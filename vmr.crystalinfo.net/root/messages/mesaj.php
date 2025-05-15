<?
   require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
   $cUtility = new Utility();
   $cdb = new db_layer();
   require_valid_login();
   $conn = $cdb->getConnection();
   $SITE_ID = $_SESSION["site_id"];
?>
<script>
function submit_form() {
    if(check_form(document.yeni_mesaj)){
    document.all('USER_ID').name = 'USER_ID[]';
        document.mesaj.submit();
    }
}
</script>
<center>
<?
cc_page_meta();
fillSecondCombo();
page_header();
echo "<br>";
table_header("Mesajlar","75%"); ?>
  <center>
  <table border="0" cellspacing="1" cellpadding="0" align="center" width="100%">
    <form name="mesaj" action="mesaj_db.php?act=ins" method="post">
      <INPUT TYPE="hidden" name=back value=back> 
        <tr> 
           <td class="td1_koyu" width="25%">Kaydeden</td>
           <td class="td1" width="75%"><? echo $_SESSION["adi"]." ".$_SESSION["soyadi"];?></td>
        </tr>
         <tr class="form">
              <td class="td1_koyu">Site Adı</td>
              <td>
              <select name="SITE_ID" class="select1" style="width:200" <?if (!right_get("SITE_ADMIN")) {echo "disabled";}?> onchange="FillSecondCombo('USER_ID', 'ADI', '05SITE_ID='+ this.value , '' , 'USER_ID' , this.value)">
                  <?
                      $strSQL = "SELECT SITE_ID, SITE_NAME FROM SITES";
                      echo $cUtility->FillComboValuesWSQL($conn, $strSQL,true,  $SITE_ID);
                  ?>
                  </select>
              </td>
          </tr>
        <tr> 
          <td class="td1_koyu" width="25%">Kime</td>
          <td width="75%"> 
                        <?        
                          /////////////////////////////////////////////////////////////////////////////////////
                          //Bu kodu mesajlara direk cevap vermek için ekledim..      
                          //      
                          ////////////////////////////////////////////////////////////////////////////////////
                  if (($type=="reply")){
                          ?>
                      <select class="select1" multiple size="5" name="USER_ID">
                         
                         <? 
                            $lookup = "SELECT USER_ID, NAME, SURNAME FROM USERS WHERE DISABLED = 'N' ORDER BY USER_ID;";
                
                            if ($cdb->execute_sql($lookup,$result,$error_msg)){
                               while ($row=mysqli_fetch_object($result)){
                                   
                                   if ($row->NAME==$from_who){
                                     printf("<option selected value=\"%d\">%s</option>%s",
                                     $row->USER_ID,$row->NAME." ".$row->SURNAME,CR);
                                   }//if in sonu
                                   else
                                      printf("<option value=\"%d\">%s</option>%s",
                                          $row->USER_ID,$row->NAME." ".$row->SURNAME,CR);
                                 }
                             }
                         ?> 
                         </select>
                         <? } 
                    else{
                          ?>    
                            <select class="select1" multiple style="height:75px;width=150;" size="5" name="USER_ID">
                             </select>
                         <?  } ?>  
                         <br>
                         <nobr>(Ctrl Tuşuna Basılı Tutarak Çoklu Seçim Yapabilirsiniz.)</nobr>
           </td>
       </tr>
      <tr> 
           <td class="td1_koyu"  width="25%">Konu</td>
           <td width="75%"> 
               <?
                   if ($type=="reply") 
                      {$msg_value="RE > ".$subject; }
                   else $msg_value="";
               ?>
                 <input class="input1" type="text" value="<? echo $msg_value; ?>" name="frm_konu" size="80" maxlength="100">
           </td>
       </tr>
        <tr> 
           <td class="td1_koyu" width="25%">Detay</td>
           <td width="75%"><textarea rows="10" name="frm_detay" class="textarea1" cols="60"></textarea></td>
        </tr>
        <tr>
        <td align="center" width="100%" colspan="3">
             <p align="center"><img border="0" src="<?=IMAGE_ROOT?>kaydet.gif" style="cursor:hand;" onclick="javascript:submit_form()"></td>
        </tr>
    </form>
      <script language="javascript" src="/scripts/form_validate.js"></script>
      <script language="javascript">
            form_fields[0] = Array ("frm_konu", "Site'yi seçmeniz gerekli.", TYP_DROPDOWN);
            form_fields[1] = Array ("frm_konu", "Başlık alanını girmeniz gerekli.", TYP_NOT_NULL);
            form_fields[2] = Array ("frm_detay", "Detay alanını girmeniz gerekli.", TYP_NOT_NULL);
      FillSecondCombo('USER_ID', 'ADI', '05SITE_ID='+ document.all('SITE_ID').value , '' , 'USER_ID' , document.all('SITE_ID').value)     
    </script>
  </table>
<? table_footer();
   page_footer(0);
?>
