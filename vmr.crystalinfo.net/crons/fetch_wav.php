<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL);

$hostname = '{imap.gmail.com:993/imap/ssl/novalidate-cert}INBOX';
$username = 'santral.seskayit1@gmail.com';
$password = 'ymni shtc gczl edau';

include_once "/usr/local/wwwroot/vmr.crystalinfo.net/config.php";
require_once("/usr/local/wwwroot/vmr.crystalinfo.net/lib/db.class.php");

$db = new DB($config['host'], $config['username'], $config['password'], $config['database']);

$inbox = imap_open($hostname, $username, $password) or die('❌ IMAP bağlantı hatası: ' . imap_last_error());
$emails = imap_search($inbox, 'UNSEEN');

if (!$emails) {
    echo "📭 İşlenecek e-posta yok.<br>";
    imap_close($inbox);
    exit;
}

function findWavAttachments($structure, $prefix = '', &$results = []) {
    if (isset($structure->parts)) {
        foreach ($structure->parts as $i => $part) {
            $newPrefix = $prefix === '' ? ($i + 1) : "$prefix." . ($i + 1);
            findWavAttachments($part, $newPrefix, $results);
        }
    } else {
        if (isset($structure->disposition) && strtolower($structure->disposition) === 'attachment') {
            if (isset($structure->dparameters)) {
                foreach ($structure->dparameters as $dp) {
                    if (strtolower($dp->attribute) === 'filename') {
                        $filename = imap_utf8($dp->value);
                        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                        if ($ext === 'wav') {
                            $results[] = [
                                'partNo' => $prefix,
                                'filename' => $filename,
                                'encoding' => $structure->encoding
                            ];
                        }
                    }
                }
            }
        }
    }
}

foreach ($emails as $emailNo) {
    $overview = imap_fetch_overview($inbox, $emailNo, 0)[0];
    $structure = imap_fetchstructure($inbox, $emailNo);
    
    // Varsayılan alınan tarih
    $dateHeader = date("Y-m-d H:i:s", strtotime($overview->date));

    // E-posta içeriği (HTML body)
    $body = imap_fetchbody($inbox, $emailNo, 1.2);
    if (empty($body)) {
        $body = imap_fetchbody($inbox, $emailNo, 1);
    }
    $body = quoted_printable_decode($body);

    libxml_use_internal_errors(true);
    $dom = new DOMDocument();
    $dom->loadHTML($body);
    $text = $dom->textContent;

    // Bilgileri parçala
    preg_match('/Date\s+([A-Z]{3}\/\d{1,2}\/\d{4})/i', $text, $dateMatch);
    preg_match('/Time\s+([\d:]+)/i', $text, $timeMatch);
    preg_match('/Caller\s+\[(.*?)\]/i', $text, $callerMatch);
    preg_match('/Callee\s+\[(.*?)\]/i', $text, $calleeMatch);

    // Parçalanan değerleri hazırla
    $rawDate = $dateMatch[1] ?? date("M/d/Y");
    $rawTime = $timeMatch[1] ?? date("H:i:s");
    $rawDate = str_replace("/", " ", $rawDate); // 'DEC 20 2024' formatına çevir
    $caller = $callerMatch[1] ?? 'unknown';
    $callee = $calleeMatch[1] ?? 'unknown';

    $callDateTime = date('Y-m-d H:i:s', strtotime("$rawDate $rawTime"));
    $receivedAt = $callDateTime;

    // Ekleri al
    $attachments = [];
    findWavAttachments($structure, '', $attachments);
foreach ($attachments as $attachment) {
    $filename = $attachment['filename'];
    $partNo = $attachment['partNo'];
    $encoding = $attachment['encoding'];

    $content = imap_fetchbody($inbox, $emailNo, $partNo);
    if ($encoding == 3) $content = base64_decode($content);
    elseif ($encoding == 4) $content = quoted_printable_decode($content);

    
    // 📁 received_at'e göre yıl ve ay klasörü oluştur
// 📁 received_at'e göre yıl ve ay klasörü oluştur
$year = date('Y', strtotime($receivedAt));
$month = date('m', strtotime($receivedAt));
$folderPath = "/usr/local/wwwroot/vmr.crystalinfo.net/uploads/$year/$month";
$webPathBase = "uploads/$year/$month";

// Klasör yoksa oluştur ve 0777 ver
if (!is_dir($folderPath)) {
    mkdir($folderPath, 0777, true);
    chmod("/usr/local/wwwroot/vmr.crystalinfo.net/uploads/$year", 0777); // yıl klasörü
    chmod($folderPath, 0777); // ay klasörü
}


// 📁 Dosya adı formatı: 202412202321_from_7731_to_1000.wav
$filenameDate = date('YmdHi', strtotime($callDateTime));
$callerClean = preg_replace('/\D/', '', $caller);
$calleeClean = preg_replace('/\D/', '', $callee);
$safeName = "{$filenameDate}_from_{$callerClean}_to_{$calleeClean}.wav";

// 📍 Tam yollar
$absolutePath = "$folderPath/$safeName";
$webPath = "$webPathBase/$safeName";

// Kaydet
file_put_contents($absolutePath, $content);
chmod($absolutePath, 0777); 
echo "✅ Kaydedildi: $webPath<br>";


    // Veritabanına ekle
    $sql = sprintf(
        "INSERT INTO voice_records (record_path, source, received_at, call_date, caller, callee, duration, call_type, status)
         VALUES ('%s', 'fetch', '%s', '%s', '%s', '%s', 0, 'incoming', 'completed')",
        $db->getConnection()->real_escape_string($webPath),
        $db->getConnection()->real_escape_string($receivedAt),
        $db->getConnection()->real_escape_string($callDateTime),
        $db->getConnection()->real_escape_string($caller),
        $db->getConnection()->real_escape_string($callee)
    );

    $db->query($sql);
}
    imap_setflag_full($inbox, $emailNo, "\\Seen");
}

imap_close($inbox);
?>
