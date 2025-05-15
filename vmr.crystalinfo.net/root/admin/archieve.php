<?php
	require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
    $cdb = new db_layer();
    $cUtility = new Utility();
    require_valid_login();
  //Site Admin veya Admin Hakkı yokda bu tanımı yapamamalı
  check_right("SITE_ADMIN");
    if($act == ""){
    cc_page_meta(0);
    page_header();

?>
  <script LANGUAGE="javascript" src="/scripts/popup.js"></script>
<br><br>
<br><br>
<?table_header("Arşivleme","80%");?>
      <center>
  <form name="archieve" method="post" action="archieve.php?act=archieve">
      <table>
      <tr>
        <td colspan="2">
          Sistem Aşağıda belirteceğiniz tarihten önceki çağrıları arşivleyecektir.
          Arşivlenmiş kayıtlara arşiv raporlarına tıklayarak erişebilirsiniz.
          Aynı zamanda sistem parametreleri içerisinde yer alan "Kaç gün önceki kayıtlar arşivlensin" 
          parametresini değiştirerek kaytların otomatik arşivlenmesini sağlayabilirsiniz.
        <br><br></td>
      </tr>
      <tr>
      <TD colspan="3">
        <TABLE  BORDER=1 width="100%"><tr>
        <td colspan=4 width="" class="td1_koyu">Aylara Göre Data Oranı</td></TR>

      <?
            $qry = "SELECT MIN(MY_DATE) as MIN_DATE,MAX(MY_DATE) AS  MAX_DATE, COUNT(CDR_ID) AS CNT FROM CDR_MAIN_DATA GROUP BY YEAR(MY_DATE),MONTH(MY_DATE) ;";
            if (!($cdb->execute_sql($qry, $resultf, $error_msg))){
                    echo $error_msg;
                    exit;
            }
            while($row = mysqli_fetch_object($resultf)){
               echo "<TR><td>".$row->MIN_DATE."</td><TD>".  $row->CNT  ."</td><TD>".$row->MAX_DATE."</td><td><a href=?mn=2&act=archieve&the_date=$row->MIN_DATE&end_date=$row->MAX_DATE>Arşivle</a></TR>";
            }      
      
      ?>
     </TABLE>
     </TD>
          </tr>
      <tr>
        <td width="40%" class="td1_koyu">Arşivleme Tarihi:</td>
        <td width="60%">
      <input type="text" size=10 name="the_date" VALUE="<?echo $the_date?>" readonly><a href="javascript://"><img align="absmiddle" src="<?=IMAGE_ROOT?>takvim_icon.gif" onclick="javascript:show_calendar(document.all('the_date').name,null,null,null,event.screenX,event.screenY,1);" border="0"></a>
        </td>
     </tr>
  <tr>
      <td colspan=2 align=center><br>
           <a href="javascript:document.all.archieve.submit();"><img name="Image631" border="0" src="<?=IMAGE_ROOT?>arsivle.gif"></a>
         </td>
     </tr>      
  <tr>
      <td colspan=2 align=center><br>
           <a href="/reports/archieve/archieve_report.php">Arşiv Raporları</a>
         </td>
     </tr>      
     </table>
      <center>
     <?
     table_footer(0);?>
<br><br>
        <b></b>
<br><br>
     
<?     page_footer("");

    }elseif($act == "archieve" && !empty($the_date)){

  
        if(!empty($the_date)){
      //GIDEN ÇAĞRILAR ARŞİVLENİYOR
            $DEL_ID = 0;
           
           if($mn<>2) $the_date = convert_date_time($the_date,'start');
               
            $qry = "SELECT CDR_ID FROM CDR_MAIN_DATA WHERE MY_DATE = '$the_date' ORDER BY CDR_ID ASC LIMIT 0,1;";
            if (!($cdb->execute_sql($qry, $resultf, $error_msg))){
                    echo $error_msg;
                    exit;
            }
            $row = mysqli_fetch_object($resultf);
            $DEL_ID = $row->CDR_ID;


            if(!empty($end_date)){  
                  $qry = "SELECT CDR_ID FROM CDR_MAIN_DATA WHERE MY_DATE = '$end_date' ORDER BY CDR_ID DESC LIMIT 0,1;";
                 if (!($cdb->execute_sql($qry, $resultf, $error_msg))){
                    echo $error_msg;
                    exit;
                 }
                 $row = mysqli_fetch_object($resultf);
                 $END_ID = $row->CDR_ID;
            }  
           
            $qry = "INSERT INTO CDR_ARCHIEVE SELECT * FROM CDR_MAIN_DATA WHERE CDR_ID <= '$DEL_ID'";
            if(!empty($end_date)){
                   $qry = "INSERT INTO CDR_ARCHIEVE SELECT * FROM CDR_MAIN_DATA WHERE CDR_ID >= '$DEL_ID' AND CDR_ID <= '$END_ID'";
            }

            if (!($cdb->execute_sql($qry, $resultg3, $error_msg))){
                    echo $error_msg;
                  exit;
            }

            $qry = "DELETE FROM CDR_MAIN_DATA WHERE CDR_ID <= '$DEL_ID'";
            if(!empty($end_date)){
                 $qry = "DELETE FROM CDR_MAIN_DATA WHERE CDR_ID >= '$DEL_ID' AND CDR_ID <= '$END_ID'";
            }
            
            if (!($cdb->execute_sql($qry, $resulth, $error_msg))){
                  echo $error_msg;
                  exit;
            }
        //GELEN VE DAHILI ÇAĞRILAR ARŞİVLENİYOR
            $DEL_ID = 0;
        $the_date = convert_date_time($the_date,'start');
            $qry = "SELECT CDR_ID FROM CDR_MAIN_INB WHERE MY_DATE='$the_date' ORDER BY CDR_ID ASC LIMIT 0,1;";
            if (!($cdb->execute_sql($qry, $resultf, $error_msg))){
                    echo $error_msg;
                    exit;
            }
            $row = mysqli_fetch_object($resultf);
            $DEL_ID = $row->CDR_ID;
        
            $qry = "INSERT INTO CDR_MAIN_INB_ARCH SELECT * FROM CDR_MAIN_INB WHERE CDR_ID <= '$DEL_ID'";
            if (!($cdb->execute_sql($qry, $resultg3, $error_msg))){
                    echo $error_msg;
                  exit;
            }
            
            $qry = "DELETE FROM CDR_MAIN_INB WHERE CDR_ID <= '$DEL_ID'";
            if (!($cdb->execute_sql($qry, $resulth, $error_msg))){
                  echo $error_msg;
                  exit;
            }
//___________________________________________________________________________________________
          print_error("İşlem Başarıyla  Gerçekleştirildi..<br>");
        }else{
          print_error("Tarih Girmediniz..<br>");
        }
    }
?>
