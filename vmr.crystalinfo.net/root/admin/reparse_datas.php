<?php
    require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";    
    $cdb = new db_layer();
     $cUtility = new Utility();
    require_valid_login();
   //Site Admin veya Admin Hakkı yokda bu tanımı yapamamalı
   if (!right_get("SITE_ADMIN") && !right_get("ADMIN")){
        print_error("Burayı Görme Hakkınız Yok");
    exit;
   }
   $conn = $cdb->getConnection();
   if (!right_get("SITE_ADMIN"))
       $SITE_ID = $_SESSION['site_id'];
   if (right_get("SITE_ADMIN") && ($SITE_ID=="" || $SITE_ID=="-1"))
       $SITE_ID = $_SESSION['site_id'];

    if($act == ""){
    cc_page_meta(0);
    page_header();

?>
  <script LANGUAGE="javascript" src="/scripts/popup.js"></script>
    <script language=javascript>
function submit_form(){
    document.all('SITE_ID').disabled=false;
    document.all('reparse').action = 'reparse_datas.php?act=reparse';
        document.all('reparse').submit();
  }
    </script>
<br><br>
<br><br>
<?table_header("Yeniden Ücretlendir","50%");?>
      <center>
  <form name="reparse" method="post" action="reparse_datas.php?act=reparse">
      <table>
      <tr>
        <td colspan="2">
            <select name="SITE_ID" class="select1" style="width:200" <?if (!right_get("SITE_ADMIN")) {echo "disabled";}?>>
                <?
                 if (right_get("SITE_ADMIN"))
            $strSQL = "SELECT SITE_ID, SITE_NAME FROM SITES";
                 else
            $strSQL = "SELECT SITE_ID, SITE_NAME FROM SITES WHERE SITE_ID='$SITE_ID'";
                    echo $cUtility->FillComboValuesWSQL($conn, $strSQL, true,  $SITE_ID);
                ?>
            </select>
        <br><br></td>
      </tr>
      <tr>
        <td colspan="2">
          Sistem Aşağıda belirteceğiniz tarihten sonraki çağrıları yeniden ücretlendirecektir.
          Bu işlem eğer bu dataların ham dataları silinmemişse yapılabilir. 
        <br><br></td>
      </tr>
      <tr>
        <td width="40%" class="td1_koyu">Başlangıç Tarihi:</td>
        <td width="60%">
      <input type="text" size=10 name="the_date" VALUE="<?echo $the_date?>" readonly><a href="javascript://"><img align="absmiddle" src="<?=IMAGE_ROOT?>takvim_icon.gif" onclick="javascript:show_calendar(document.all('the_date').name,null,null,null,event.screenX,event.screenY,1);" border="0"></a>
        </td>
     </tr>
  <tr>
      <td colspan=2 align=center><br>
           <a href="javascript:document.all.reparse.submit();"><img name="Image631" border="0" src="<?=IMAGE_ROOT?>arsivle.gif"></a>
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

    }elseif($act == "reparse" && !empty($the_date)){

  
        if(!empty($the_date)){
      //GIDEN ÇAĞRILAR BULUNUYOR
            $OUTBOUND_DEL_ID = 0;
            $OUTBOUND_SEMI_ID = 0;
        $the_date = convert_date_time($the_date,'start');
            $qry = "SELECT CDR_ID, MY_DATE, SEMI_FINISHED_ID FROM CDR_MAIN_DATA WHERE MY_DATE = '$the_date' AND SITE_ID=$SITE_ID ORDER BY CDR_ID ASC LIMIT 0,1;";

            if (!($cdb->execute_sql($qry, $resultf, $error_msg))){
                    echo $error_msg;
                    exit;
            }
            echo "<table border=1>\n";
                echo "<tr>\n";
                echo "<td>CDR_ID</td><td>MY_DATE</td>\n";
                echo "</tr>\n";
            while($row = mysqli_fetch_object($resultf)){
//            $DEL_ID = $row->CDR_ID;
                $OUTBOUND_DEL_ID = $row->CDR_ID;
                $OUTBOUND_SEMI_ID = $row->SEMI_FINISHED_ID;
                echo "<tr>\n";
                echo "<td>".$row->CDR_ID."</td>\n";
                echo "<td>".$row->MY_DATE."</td>\n";
                echo "</tr>\n";
                
            }
            ECHO "</table>\n";
            ECHO "<BR>\n";
      //GELEN ÇAĞRILAR BULUNUYOR
            $INBOUND_DEL_ID = 0;
            $INBOUND_SEMI_ID = 0;
        $the_date = convert_date_time($the_date,'start');
            $qry = "SELECT CDR_ID, MY_DATE, SEMI_FINISHED_ID FROM CDR_MAIN_INB WHERE MY_DATE = '$the_date' AND SITE_ID=$SITE_ID ORDER BY CDR_ID ASC LIMIT 0,1;";

            if (!($cdb->execute_sql($qry, $resultf, $error_msg))){
                    echo $error_msg;
                    exit;
            }
            echo "<table border=1>\n";
                echo "<tr>\n";
                echo "<td>CDR_ID</td><td>MY_DATE</td>\n";
                echo "</tr>\n";
            while($row = mysqli_fetch_object($resultf)){
//            $DEL_ID = $row->CDR_ID;
                $INBOUND_DEL_ID = $row->CDR_ID;
                $INBOUND_SEMI_ID = $row->SEMI_FINISHED_ID;
                echo "<tr>\n";
                echo "<td>".$row->CDR_ID."</td>\n";
                echo "<td>".$row->MY_DATE."</td>\n";
                echo "</tr>\n";
                
            }
            ECHO "</table>";
            if($INBOUND_SEMI_ID>$OUTBOUND_SEMI_ID){
                $SEMI_ID = $OUTBOUND_SEMI_ID;
            }else{
                $SEMI_ID = $INBOUND_SEMI_ID;
            }

            $qry = "SELECT * FROM SEMI_ARCHIEVE WHERE ID = $SEMI_ID AND SITE_ID=$SITE_ID ORDER BY ID ASC LIMIT 0,1;";

            if (!($cdb->execute_sql($qry, $resultf, $error_msg))){
                    echo $error_msg;
                    exit;
            }
            
//___________________________________________________________________________________________
        }else{
          print_error("Tarih Girmediniz..<br>");
        }
    }
?>
