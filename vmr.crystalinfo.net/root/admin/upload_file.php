<?
  $DOCUMENT_ROOT = "/usr/local/wwwroot/www.crystalinfo.net/root";
  require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
?>  


<?php
/**
* Upload.class.php - File Upload Class v1.1 - 02 July 2001
* Copyright Darren Beale <mail@bealers.com>
*
* The contents of this file remain the intellectual property of Darren Beale.
* It is free for Personal and non-profit use as long as this 
* entire comment block remains as-is. (Yes all of it)
*
* If you're using it commercially, please mail <mail@bealers.com> for a postal 
* address. You can then get your boss to send me a measly £10 UKP so you can 
* have unlimited use. 
*
* Either way, If you modify or extend it, please Fw: me on a copy with a 
* small note on what you've done and why.
*
* TODO: 
*  Check that source_dir is writeable
*  Allow for auto-rename option on overwrite
*  Handle exceptions and notices better
*  Extend Pear/HTML/Form??
*   Fix Macintosh Upload issues (it won't work)
*  100% Pear Compliant
*  PHPDoc commenting and bundled docs 
*
* ChangeLog 
* v1.0 -   Initial Release             01/07/01
* v1.1 -   Allowed for Multiple File uploads     02/07/01
*
* I WILL PROVIDE SUPPORT FOR THIS SCRIPT!
* This script *should* be able to handle the uploading of any file, if it
* doesn't as a first port of call please change the settings 
* UPLOAD_DEBUG_OUTPUT and UPLOAD_ENV_OUTPUT to see what is happening. 
* If you still have problems, mail
* me and I'll see what I can do. Don't expect me to code your app though!
* BTW You Need PHP > 4.0.2
*
* Useage: (bare bones)
*/

require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/upload_class.php";
//require_once(dirname($DOCUMENT_ROOT)."/cgi-bin/upload_class.php");

$upload = new Upload();
$upload->printFormStart("upload_file.php");

// put as many of these in as you want, 
// pass a string filename, else a default is used.
$upload->printFormField();
print"<br />";
$upload->printFormField();

$upload->printFormSubmit();
$upload->printFormEnd();

if ($submit) {
  $upload->setAllowedMimeTypes(
  array(
          "application/x-gzip-compressed"   => ".tar.gz, .tgz",
          "application/x-zip-compressed"     => ".zip",
          "application/x-tar"          => ".tar",
          "text/plain"            => ".php, .txt, .inc (etc)",
          "text/html"              => ".html, .htm (etc)",
          "image/bmp"             => ".bmp, .ico",
          "image/gif"             => ".gif",
          "image/pjpeg"            => ".jpg, .jpeg",
          "image/jpeg"            => ".jpg, .jpeg",
          "image/x-png"            => ".png",
          "audio/mpeg"            => ".mp3 etc",
          "audio/wav"              => ".wav",
          "application/pdf"          => ".pdf",
          "application/x-shockwave-flash"   => ".swf",
          "application/msword"        => ".doc",
          "application/vnd.ms-excel"      => ".xls",
          "application/octet-stream"      => ".exe, .fla, .psd (etc)"
        )
  );
  $upload->setUploadPath($DOCUMENT_ROOT."/temp/");
  if ($upload->doUpload()) {
    print "Files Uploaded!";
  } else {
    $errors = $upload->getUploadErrors();
    print "<strong>::Errors occured::</strong><br />\n";
    while(list($filename,$values) = each($errors)) {
      "File: " . print $filename . "<br />";
      $count = count($values);
      for($i=0; $i<$count; $i++) {
        print "==>" . $values[$i] . "<br />";
      }
    }
  }
}
?>

