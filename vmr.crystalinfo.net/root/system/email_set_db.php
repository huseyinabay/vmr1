<?
      require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
      $cDB = new db_layer();
      require_valid_login();
      $args = Array();

	 //ini_set('display_errors', 'On');
     //error_reporting(E_ALL);	
	  
	  
     //Site Admin veya Admin Hakkı yokda bu tanımı yapamamalı
  check_right("SITE_ADMIN");

      $args[] = array("set_name",             $set_name,        cFldWQuote);
	  $args[] = array("set_server",           $set_server,      cFldWQuote);
      $args[] = array("set_user",             $set_user,        cFldWQuote);
	  $args[] = array("set_pass",             $set_pass,        cFldWQuote);
      $args[] = array("set_port",             $set_port,        cFldWQuote);
	  $args[] = array("set_secure",           $set_secure,      cFldWQuote);
	  $args[] = array("set_auth",             $set_auth,        cFldWQuote);
      $args[] = array("set_protokol",         $set_protokol,    cFldWQuote);
      $args[] = array("set_type",             $set_type,        cFldWQuote);
	  $args[] = array("set_fromname",         $set_fromname,    cFldWQuote);
	  $args[] = array("set_from",             $set_from,        cFldWQuote);
	  $args[] = array("set_desc",             $set_desc,        cFldWQuote);
    
   
    //  if($act == "upd" && $id =="1"){
		
            $args[] = array("set_id",$id, cReqWoQuote);
            $sql_str =  $cDB->UpdateString("email_sets", $args);
      if (!($cDB->execute_sql($sql_str,$result,$error_msg))){
                  print_error($error_msg);
                  exit;
            }
			
		
			// cnf dosyasinin guncellendigi yer..
			
      $local_root = "/usr/local/wwwroot/multi.crystalinfo.net/root/cgi-bin/";  //cfg dosyasinin oldugu yer

	  $file_name =  "site.cnf";
	  unlink ($local_root.$file_name); 		// eski dosyayi siler
		
		$handle = fopen($local_root.$file_name, 'a+');

		$code1 = '<?  
define ("DB_IP", "localhost");  
define ("DB_NAME", "MCRYSTALINFONE"); 
define ("DB_USER", "crinfo");  
define ("DB_PWD", "SiGmA*19"); 
define ("MYSQLSOCK", "/tmp/mysql.sock"); 
define ("SMTP_SERVER", "';
		$code2 = '");';
		$code3 = 'define ("SMTP_PORT", "';
		$code4 = 'define ("SMTP_PROTOCOL", "';
		$code5 = 'define ("SMTP_SECURE", "';
		$code6 = 'define ("SMTP_AUTH", ';
		$code7 = 'define ("SMTP_USER", "'; 
		$code8 = 'define ("SMTP_PWD", "'; 
		$code9 = 'define ("SMTP_FROMNAME", "';
		$code10 = 'define ("SMTP_FROM", "';
		$code11 = '
define ("SMTP_PMASTER", "crystalinfo@netser.com.tr");
define ("SMTP_CHARSET", "iso-8859-9");
define ("SMTP_HTML", "true");
?>';


$buffer = $code1.$set_server.$code2."\n".$code3.$set_port.$code2."\n".$code4.$set_protokol.$code2."\n".$code5.$set_secure.$code2."\n".$code6.$set_auth.");\n".$code7.$set_user.$code2."\n".$code8.$set_pass.$code2."\n".$code9.$set_fromname.$code2."\n".$code10.$set_from.$code2.$code11;


      fwrite($handle, $buffer);     //DOSYA DİĞER DOSYAYA YAZILIYOR.	
		
	  fclose($handle);
		  
      header("Location:email_set.php?act=upd&id=".$id);
	  
	//  }
?>
