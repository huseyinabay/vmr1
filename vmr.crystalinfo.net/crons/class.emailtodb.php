<?
/**
 * Comments: EMAIL TO DB
 */

 #ini_set('max_execution_time', 3000);
 #ini_set('default_socket_timeout', 3000);
 #ini_set('memory_limit','512M');
 
 date_default_timezone_set('Europe/Istanbul');
 
class EMAIL_TO_DB {


 var $IMAP_host; #pop3 server
 
 var $IMAP_port; #pop3 server port
 
 var $IMAP_login;
 
 var $IMAP_pass;

 var $link;
 
 var $error = array();
 
 var $status;
 
 var $max_headers = "all";  #How much headers you want to retrive 'max' = all headers (
 
 var $filestore; 

 var $file_path = '/usr/local/sigma/crystal/recs/'; #Where to write file attachments to /var/www/files/email/
                                             # win: c://wamp//www//emailtodb//files//
                                        //     #[full/path/to/attachment/store/(chmod777)]
										 
 var $partsarray = array();
 
 var $msgid =1; 
 
 var $newid;
 
 var $logid;
 
 
 var $this_file_name = 'class.emailtodb.php'; #If mode "html" 
 
 var $mode = 'cron'; #If script run from cron "mode" =  "cron" or ""; mode "html" is using if You run script from browser
 
 var $spam_folder = 1; #Folder where moving spam (ID from DB)
  
 var $file = array(); #File in multimart message
 
 function connect($host, $port, $login, $pass){

  $this->IMAP_host = $host;
  $this->IMAP_login = $login;
  
  
  $this->link = imap_open("{". $host . $port."}INBOX", $login, $pass);
  if($this->link) {
    $this->status = 'Connected';
  } else {
    $this->error[] = imap_last_error();
    $this->status = 'Not connected';
  }
 }
 
  function set_path(){
     
    #If You need set here more parameters
    # - recognise operating systems, or something
      
      $path = $this->file_path;
      
    return $path;
  }
 
 
  function set_filestore(){
    $dir = $this->dir_name();
    $path = $this->set_path();
    $this->filestore = $path.$dir;
 
  }
 

   /**
  * Get mailbox info
  */
  function mailboxmsginfo(){
   
    //$mailbox = imap_mailboxmsginfo($this->link); #It's wery slow
     $mailbox = imap_check($this->link);
  
    if ($mailbox) {
       $mbox["Date"]    = $mailbox->Date;
       $mbox["Driver"]  = $mailbox->Driver;
       $mbox["Mailbox"] = $mailbox->Mailbox;
       $mbox["Messages"]= $this->num_message();
       $mbox["Recent"]  = $this->num_recent();
       $mbox["Unread"]  = $mailbox->Unread;
       $mbox["Deleted"] = $mailbox->Deleted;
       $mbox["Size"]    = $mailbox->Size;
    } else {
       $this->error[] = imap_last_error();
    }
    
    return $mbox;
  }
 
 /**
  * Number of Total Emails
  */
 function num_message(){
    return imap_num_msg($this->link);
 }
 
 /**
  * Number of Recent Emails
  */
  function num_recent(){
    return imap_num_recent($this->link);
  }
  
  /**
   * Type and subtype message
   */
  function msg_type_subtype($_type){
    
    if($_type > 0){
       switch($_type){
         case '0': $type = "text"; break;
         case '1': $type = "multipart"; break;
         case '2': $type = "message"; break;
         case '3': $type = "application"; break;
         case '4': $type = "audio"; break;
         case '5': $type = "image"; break;
         case '6': $type = "video"; break;
         case '7': $type = "other"; break;
       }
    }
    
    return $type;
  }
  /**
   * Flag message
   */
  function email_flag(){
    
         switch ($char) {
            case 'S':
                if (strtolower($flag) == '\\seen') {
                    $msg->is_seen = true;
                }
            break;
            case 'A':
                if (strtolower($flag) == '\\answered') {
                    $msg->is_answered = true;
                }
            break;
            case 'D':
               if (strtolower($flag) == '\\deleted') {
                    $msg->is_deleted = true;
                }
            break;
            case 'F':
                if (strtolower($flag) == '\\flagged') {
                    $msg->is_flagged = true;
                }
            break;
            case 'M':
                if (strtolower($flag) == '$mdnsent') {
                    $msg->is_mdnsent = true;
                }
            break;
                default:
            break;
            }
  }
  
  /**
  * Parse e-mail structure
  */
  function parsepart($p,$msgid,$i){
   
   $part=imap_fetchbody($this->link,$msgid,$i);
   #Multipart
   if ($p->type!=0){
       #if base64
       if ($p->encoding==3)$part=base64_decode($part);
       #if quoted printable
       if ($p->encoding==4)$part=quoted_printable_decode($part);
       #If binary or 8bit -we no need to decode
     
       #body type (to do)
       switch($p->type) {
         case '5': # image
          $this->partsarray[$i][image] = array('filename'=>imag1,'string'=>$part, 'part_no'=>$i);
        break;
       }
       
       #Get attachment
       $filename='';
       if (count($p->dparameters)>0){
           foreach ($p->dparameters as $dparam){
               if ((strtoupper($dparam->attribute)=='NAME') ||(strtoupper($dparam->attribute)=='FILENAME')) $filename=$dparam->value;
               }
           }
       #If no filename
       if ($filename==''){
           if (count($p->parameters)>0){
               foreach ($p->parameters as $param){
                   if ((strtoupper($param->attribute)=='NAME') ||(strtoupper($param->attribute)=='FILENAME')) $filename=$param->value;
                   }
               }
           }
       if ($filename!='' ){
           $this->partsarray[$i][attachment] = array('filename'=>$filename,'string'=>$part, 'encoding'=>$p->encoding, 'part_no'=>$i,'type'=>$p->type,'subtype'=>$p->subtype);
      
           }
   #end if type!=0       
   }
  
   #Text email
   else if($p->type==0){
       #decode text
       #if QUOTED-PRINTABLE
       if ($p->encoding==4) $part=quoted_printable_decode($part);
       #if base_64
       if ($p->encoding==3) $part=base64_decode($part);
      
       #if plain text
       if (strtoupper($p->subtype)=='PLAIN')1;
       #if HTML
       else if (strtoupper($p->subtype)=='HTML')1;
       $this->partsarray[$i][text] = array('type'=>$p->subtype,'string'=>$part);
   }
  
   #if subparts
   if (count($p->parts)>0){
       foreach ($p->parts as $pno=>$parr){
           $this->parsepart($parr,$this->msgid,($i.'.'.($pno+1)));           
           }
       }
   return;
  }
  
  /**
   * All email headers
   */
  function email_headers(){
    
     #$headers=imap_headers($this->link);
     if($this->max_headers == 'max'){
       $headers = imap_fetch_overview($this->link, "1:".$this->num_message(), 0);
     } else {
       $headers = imap_fetch_overview($this->link, "1:$this->max_headers", 0);
     }
     if($this->max_headers == 'max') {
         $num_headers = count($headers);
     } else {
        $count =  count($headers);
          if($this->max_headers >= $count){
              $num_headers = $count;
          } else {
              $num_headers = $this->max_headers;
          }
     }

  $size=sizeof($headers);
  for($i=1; $i<=$size; $i++){
    
  $val=$headers[$i]; 
  //while (list($key, $val) = each($headers)){
   
   $subject_s = (empty($val->subject)) ? '[No subject]' : $val->subject;
   $lp = $lp +1;
   imap_setflag_full($this->link,imap_uid($this->link,$i),'\\SEEN',SE_UID);
   $header=imap_headerinfo($this->link, $i, 80,80);
   
   if($val->seen == "0"  && $val->recent == "0") {echo  '<b>'.$val->msgno . '-' . $subject_s . '-' . $val->from .'-'. $val->date."</b><br><hr>" ;}
   else {echo  $val->msgno . '-' . $subject_s . '-' . $val->from .'-'. $val->date."<br><hr>" ;}
   }
  }

   /**
   * Get email
   */
  function email_get(){
    $email = array();
    $this->set_filestore();
    
    $header=imap_headerinfo($this->link, $this->msgid, 80,80);
    $from = $header->from;
    $udate= $header->udate;
    $to   = $header->to;
    $size = $header->Size;
    
    if ($header->Unseen == "U" || $header->Recent == "N") {
        
    #Check is it multipart messsage
    $s = imap_fetchstructure($this->link,$this->msgid);
    if (count($s->parts)>0){
       foreach ($s->parts as $partno=>$partarr){
       #parse parts of email
       $this->parsepart($partarr,$this->msgid,$partno+1);
       }
    } else { #for not multipart messages
    #get body of message
    $text=imap_body($this->link,$this->msgid);
    #decode if quoted-printable
    if ($s->encoding==4) $text=quoted_printable_decode($text);

    if (strtoupper($s->subtype)=='PLAIN') $text=$text;
    if (strtoupper($s->subtype)=='HTML') $text=$text;
  
    $this->partsarray['not multipart'][text]=array('type'=>$s->subtype,'string'=>$text);
    }
    
    if(is_array($from)){
     foreach ($from as $id => $object) {
       $fromname = $object->personal;
       $fromaddress = $object->mailbox . "@" . $object->host; // email kontrolu
     }
    }
    
    if(is_array($to)){
     foreach ($from as $id => $object) {
       $toaddress = $object->mailbox . "@" . $object->host;
     }
    }

    $email['CHARSET']    = $charset;
    $email['SUBJECT']    = $this->mimie_text_decode($header->Subject);
    $email['FROM_NAME']  = $this->mimie_text_decode($fromname);
	$email['TEXT']       = imap_body($this->link,$this->msgid);
    $email['FROM_EMAIL'] = $fromaddress;
    $email['TO_EMAIL']   = $toaddress;
    $email['DATE']       = date("Y-m-d H:i:s",strtotime($header->date));
    $email['SIZE']       = $size;
    #SECTION - FLAGS
    $email['FLAG_RECENT']  = $header->Recent;
    $email['FLAG_UNSEEN']  = $header->Unseen;
    $email['FLAG_ANSWERED']= $header->Answered;
    $email['FLAG_DELETED'] = $header->Deleted;
    $email['FLAG_DRAFT']   = $header->Draft;
    $email['FLAG_FLAGGED'] = $header->Flagged;
    
   }
   return $email;
  
  }
  
  function mimie_text_decode($string){
    
    $string = htmlspecialchars(chop($string));

    $elements = imap_mime_header_decode($string);
    if(is_array($elements)){
     for ($i=0; $i<count($elements); $i++) {
      $charset = $elements[$i]->charset;
      $txt .= $elements[$i]->text;
     }
    } else {
      $txt = $string;
    }
    if($txt == ''){
      $txt = 'No_name';
    }
    if($charset == 'us-ascii'){
     //$txt = $this->charset_decode_us_ascii ($txt);
    }
    return $txt;
	//echo $txt;
   }

   /**
   * Save messages on local disc
   */ 
  function save_files($filename, $part){

    $fp=fopen($this->filestore.$filename,"w+");
    fwrite($fp,$part);
    fclose($fp);
    chown($this->filestore.$filename, 'apache');

  }
   /**
   * Set flags
   */ 
  function email_setflag(){
    
    imap_setflag_full($this->link, "2,5","\\Seen \\Flagged"); 
  
  }
   /**
   * Mark a message for deletion 
   */ 
   
 
  function email_delete(){
    
    imap_delete($this->link, $this->msgid); 

  }
  
   /**
   * Delete marked messages 
   */ 
  
  function email_expunge(){
    
    imap_expunge($this->link);
  
  }
  
  
   /**
   * Close IMAP connection
   */ 
  function close(){
    imap_close($this->link);   
  }
 
 
  function listmailbox(){
  $list = imap_list($this->link, "{".$this->IMAP_host."}", "*");
  if (is_array($list)) {
     return $list;
   } else {
     $this->error =  "imap_list failed: " . imap_last_error() . "\n";
   }
   return array();
  }
  
/*******************************************************************************
 *                                 SPAM  DETECTION                               
 ******************************************************************************/

  function spam_detect(){
    
    $email = array();
    
    $id = $this->newid; #ID email in DB
    
    $execute = mysql_query("SELECT ID, IDEmail, EmailFrom, EmailFromP, EmailTo, Subject, Message, Message_html FROM emailtodb_email WHERE ID='".$id."'");
    $row = mysql_fetch_array($execute);
    $ID = $row['ID'];
    $email['Email']       = $row['EmailFrom'];
    $email['Subject']     = $row['Subject'];
    $email['Text']        = $row['Message'];
    $email['Text_HTML']   = $row['Message_html'];
    if($this->check_blacklist($email['Email'])){
      $this->update_folder($id, $this->spam_folder);  
    }
    if($this->check_words($email['Subject'])){
      $this->update_folder($id, $this->spam_folder);  
    }
    if($this->check_words($email['Text'])){
      $this->update_folder($id, $this->spam_folder);  
    }
    if($this->check_words($email['Text_HTML'])){
      $this->update_folder($id, $this->spam_folder);  
    }
  }
  
  
  function check_blacklist($email){
   #spam - emails 
   $execute = mysql_query("SELECT Email FROM emailtodb_list WHERE Email='".addslashes($email)."' AND Type='B'");
   $row = mysql_fetch_array($execute);
   $e_mail = $row['Email'];
   if($e_mail == $email){
    return 1;
   } else {
    return 0;
   }

  }
  
    function check_words($string){
    #spam - words
    $string = strtolower($string);
    
    $execute = mysql_query("SELECT Word FROM emailtodb_words ");
    while($row = mysql_fetch_array($execute)){
    
    $word = strtolower($row['Word']);
    
        if (eregi($word, $string)) {
          return 1;
        }
    }
  }
  
/*******************************************************************************
 *                                 DB FUNCTIONS                                 
 ******************************************************************************/

/**
* Add email to DB
*/
  function db_add_message($email){

 
 $execute = mysql_query("INSERT INTO emailtodb_email (IDEmail, EmailFrom, EmailFromP, EmailTo, DateE, DateDb, Subject, MsgSize) VALUES
      ('".$message_id."',
      '".$email['FROM_EMAIL']."',
      '".addslashes(strip_tags($email['FROM_NAME']))."',
      '".addslashes(strip_tags($email['TO_EMAIL']))."',
      '".$email['DATE']."',
      '".date("Y-m-d H:i:s")."',
      '".addslashes($email['SUBJECT'])."',
      '".$email["SIZE"]."')");
  
  $execute = mysql_query("select LAST_INSERT_ID() as UID");
  $row = mysql_fetch_array($execute);
  $this->newid = $row["UID"];

  }
 /**
 * Add attachments to DB
 **/

  // Parse message
function db_add_attach($file_orig, $filename, $email){

	$TEXT=addslashes($email['TEXT']);
	$FROM_EMAIL=addslashes($email['FROM_EMAIL']);
	$MY_DATE=addslashes($email['DATE']);
	$MY_DATE = date_create($MY_DATE);
	$MY_DATE =DATE_FORMAT($MY_DATE,'Y-m-d');
	
//echo $TEXT;	
	if($FROM_EMAIL == "ipecs@ipecs.com"){
		
		$message=$TEXT;
		$message = str_replace ("[","", $message);
		$message = str_replace ("]","", $message);
		$message = str_replace ("\"","", $message);
		$message = str_replace (":","", $message);
		$message = str_replace (",","", $message);
		$message = str_replace ("\n","", $message);
		$message = str_replace ("\r"," ", $message);
		$message = strstr($message, 'name=', true);

$messarray= explode(" ", $message); // explode
$messagenew = "";
//echo $message;
for($say=0;$say<sizeof($messarray);$say++){
    $System = ($messarray[9]);
    $Domain = ($messarray[11]);
    $Date = ($messarray[17]);
    $Time = ($messarray[19]);
    $From1 = ($messarray[23]);
    $From2 = ($messarray[24]);
    $To1 = ($messarray[26]);
    $To2 = ($messarray[27]);
    
}	
// $message = $System.$Domain.$Date.$Time.$From1.$From2.$To1.$To2;

$execute = mysql_query("INSERT INTO emailtodb_attach (IDEmail, FileNameOrg, Filename, EmailFrom, System, Domain, MY_DATE, TIME_STAMP, DateDb, Date, Time, From1, From2, To1, To2) VALUES
          ('".$this->newid."',
          '".addslashes($file_orig)."',
          '".addslashes($filename)."',
		  '".addslashes($email['FROM_EMAIL'])."',
		  '".$System."',
		  '".$Domain."',
		  '".$MY_DATE."',
		  '".$email['DATE']."',
          '".date("Y-m-d H:i:s")."',
		  '".$Date."',
		  '".$Time."',
		  '".$From1."',
		  '".$From2."',
		  '".$To1."',
		  '".$To2."')");
 }

}

/**
* Add email to DB
*/
  function db_update_message($msg, $type= 'PLAIN'){
  //echo $msg;
  if($type == 'PLAIN') $execute = mysql_query("UPDATE emailtodb_email SET Message='".addslashes($msg)."' WHERE ID= '".$this->newid."'");

  if($type == 'HTML')  $execute = mysql_query("UPDATE emailtodb_email SET Message_html='".addslashes($msg)."' WHERE ID= '".$this->newid."'");
  
  }
  
/**
 * Insert progress log
 */
  function add_db_log($email, $info){
    
    $execute = mysql_query("INSERT INTO emailtodb_log (IDemail, Email, Info, FSize, Date_start, Status) VALUES
      ('".$this->newid."',
      '".$email['FROM_EMAIL']."',
      '".addslashes(strip_tags($info))."',
      '".$email["SIZE"]."',
      '".date("Y-m-d H:i:s")."',
      '2')");
    
    $execute = mysql_query("select LAST_INSERT_ID() as UID");
    $row = mysql_fetch_array($execute);
    $this->logid = $row['UID'];
     
    return  $this->logid;
    
  }
  
 /**
 * Set folder
 */
  function update_folder($id, $folder){
 
    $execute = mysql_query("UPDATE emailtodb_email SET Type = '".addslashes($folder)."' WHERE ID = '".$id."'");
    
  }
  
 /**
 * Update progress log
 */
  function update_db_log($info, $id){
 
    $execute = mysql_query("UPDATE emailtodb_log  SET Status = '1', Info='".addslashes(strip_tags($info))."', Date_finish = '".date("Y-m-d H:i:s")."' WHERE IDlog = '".$id."'");
    
  }
  
  
 /**
 * Read log from DB
 */
  function db_read_log(){

  $email = array();
  
   $execute = mysql_query("SELECT IDlog, IDemail, Email, Info, FSize, Date_start, Date_finish, Status FROM emailtodb_log ORDER BY Date_finish DESC LIMIT 100");
   while($row = mysql_fetch_array($execute)){
    $ID = $row['IDlog'];
    $email[$ID]['IDemail']     = $row['IDemail'];
    $email[$ID]['Email']       = $row['Email'];
    $email[$ID]['Info']        = $row['Info'];
    $email[$ID]['Size']        = $row['FSize'];
    $email[$ID]['Date_start']  = $row['Date_start'];
    $email[$ID]['Date_finish'] = $row['Date_finish'];
   }
   return $email;
  }  
  
  
 /**
 * Read emails from DB
 */
  function db_read_emails(){
  if (!isset($db)) $db = new DB_WL;
  $email = array();
  
  
   $execute = mysql_query("SELECT ID, IDEmail, EmailFrom, EmailFromP, EmailTo, DateE, DateDb, Subject, Message, Message_html, MsgSize FROM emailtodb_email ORDER BY ID DESC LIMIT 25");
   while($row = mysql_fetch_array($execute)){
    $ID = $row['ID'];
    $email[$ID]['Email']     = $row['EmailFrom'];
    $email[$ID]['EmailName'] = $row['EmailFrom'];
    $email[$ID]['Subject']   = $row['Subject'];
    $email[$ID]['Date']      = $row['DateE'];
    $email[$ID]['Size']      = $row['MsgSize'];
    
   }
   return $email;
  }
  
  function dir_name() {
    
  $year  = date('Y');
  $month = date('m');
  $day = date('d');

  $dir_n = $year . '/' . $month . '/' . $day;
 
 //$dir_y = $year;
 echo $this->set_path();
  if (is_dir($this->set_path() . $dir_n)) {
    return $dir_n . '/';
  } else {
    mkdir($this->set_path() . $year, 0777);
	mkdir($this->set_path() . $year . '/' . $month, 0777);
	mkdir($this->set_path() . $year . '/' . $month . '/' . $day, 0777);
    return $dir_n . '/';
  }
 }
  
  
  
  function do_action(){
   
  if($this->num_message() >= 1) {
   
     if($this->msgid <= 0) {
       $this->msgid = 1;
      } else {
       $this->msgid = $_GET[msgid] + 1;
      }

   
    #Get first message
    $email = $this->email_get();
   
    #Get store dir
    $dir = $this->dir_name();
    
    #Insert message to db
    $ismsgdb = $this->db_add_message($email);
    
    $id_log = $this->add_db_log($email, 'Copy e-mail - start ');
    
    foreach($this->partsarray as $part){
     if($part[text][type] == 'HTML'){
       #$message_HTML = $part[text][string];
       $this->db_update_message($part[text][string], $type= 'HTML');
     }elseif($part[text][type] == 'PLAIN'){
       $message_PLAIN = $part[text][string];
       $this->db_update_message($part[text][string], $type= 'PLAIN');
     }elseif($part[attachment]){
        #Save files(attachments) on local disc
       
       // $message_ATTACH[] = $part[attachment];
        foreach(array($part[attachment]) as $attach){
            $attach[filename] = $this->mimie_text_decode($attach[filename]);
            $attach[filename] = preg_replace('/[^a-z0-9_\-\.]/i', '_', $attach[filename]);
            $this->add_db_log($email, 'Start coping file:"'.strip_tags($attach[filename]).'"');
            
            $this->save_files($this->newid.$attach[filename], $attach[string]);
            $filename =  $dir.$this->newid.$attach[filename];
            $this->db_add_attach($attach[filename], $filename, $email);
            $this->update_db_log('<b>'.$filename.'</b>Finish coping: "'.strip_tags($attach[filename]).'"', $this->logid);
        }
      //
     
     }elseif($part[image]){
        #Save files(attachments) on local disc
 
        $message_IMAGE[] = $part[image];
        
        foreach($message_IMAGE as $image){
            $image[filename] = $this->mimie_text_decode($image[filename]);
            $image[filename] = preg_replace('/[^a-z0-9_\-\.]/i', '_', $image[filename]);
            $this->add_db_log($email, 'Start coping file: "'.strip_tags($image[filename]).'"');
            
            
            $this->save_files($this->newid.$image[filename], $image[string]);
            $filename =  $dir.$this->newid.$image[filename];
            $this->db_add_attach($image[filename], $filename, $email);
            $this->update_db_log('<b>'.$filename.'</b>Finish coping:"'.strip_tags($image[filename]).'"', $this->logid);
        }

     }
    
    }
    $this->spam_detect();
    $this->email_setflag(); 
    $this->email_delete();
	$this->email_expunge();
    
    $this->update_db_log('Finish coping', $id_log);
    
    if($email <> ''){
       unset($this->partsarray);
       # echo "<meta http-equiv=\"refresh\" content=\"2; url=email.monitor.mail.php?msgid=".$this->msgid."\">";
        if($this->mode == 'html') {
          echo "<meta http-equiv=\"refresh\" content=\"2; url=".$this->this_file_name."?msgid=0\">";
          echo "E-mail extract"; 
        }
      
    }
   } else {
    # No messages
       if($this->mode == 'html') {
        echo "<meta http-equiv=\"refresh\" content=\"10; url=".$this->this_file_name."?msgid=0\">";
        echo "E-mail extract";  
       }
   }
  }
      
}#end class



?>