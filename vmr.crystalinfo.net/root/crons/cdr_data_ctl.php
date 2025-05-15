<?
  require_once("doc_root.cnf");
  require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/class.phpmailer.php";
  $cUtility = new Utility();
  $cdb = new db_layer();
  $conn = $cdb->getConnection();
  include("mail_send.php");


  $sql_str = "SELECT * FROM SITES ";
  if (!($cdb->execute_sql($sql_str, $rs, $error_msg))){
    print_error($error_msg);
    exit;
  }
  while($row = mysqli_fetch_object($rs)){//Bütün siteler taranmalı Site döngüsü start
      $SITE_ID = $row->SITE_ID;
      $SITE_NAME = $row->SITE_NAME;
      /////////////////////////////////////////////////////////////////
      $sql1 = "SELECT DATE_SUB((SELECT FROM_UNIXTIME(DATE, '%Y-%m-%d %H:%i:%s') AS DATE FROM RAW_ARCHIEVE WHERE SITE_ID = '".$SITE_ID."'
               ORDER BY ID DESC LIMIT 1), INTERVAL 2 HOUR) >=NOW() as SONUC, 
							 DATE_FORMAT(NOW(), '%H:%i:%s')>='08:00:00' as START_DAY,
							 DATE_FORMAT(NOW(), '%H:%i:%s')<='22:00:00' as END_DAY " ;
      //echo $sql1;exit;
      if (!($cdb->execute_sql($sql1, $rs1, $error_msg))){
        print_error($error_msg);
        exit;
      }
      $row1 = mysqli_fetch_object($rs1);
      if($rs1->SONUC=='1' && $rs1->START_DAY=='1' && $rs1->END_DAY=='1'){
          $DATA="";
          $DATA = "Santral Uyarılarında belirtilen kriterlere uyan aşağıdaki kayıt gelmiştir.<br>";
          $DATA .= "Uyarı Türü = SANTRAL CDR DATA KONTROLÜ<br>";
          $DATA .= "Site Adı = ".$SITE_NAME."<br>";
          $DATA .= "Açıklama = Belirtilen siteden 2 saatten fazla bir süredir data gelmemektedir.<br>";
          //////////////package the data to be sent
          mail_send("skaya@vodasoft.com.tr","CDR Data Kontrolü",$DATA);
          mail_send("yarslan@vodasoft.com.tr","CDR Data Kontrolü",$DATA);
          mail_send("ckaraman@vodasoft.com.tr","CDR Data Kontrolü",$DATA);
      }
  }    
?>