<?php
  require_once("doc_root.cnf");
  require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
  //require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/class.phpmailer.php";
  include("mail_send.php");
  mail_send('hmutlu@netser.com.tr',"CrystalInfo Sistemi Özet Raporu.-Test","Deneme");
?>
 
