<?
   require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
   
    //ini_set('display_errors', 'On');
   // error_reporting(E_ALL);
   
   $cUtility = new Utility();
   $cdb = new db_layer();
   require_valid_login();
   session_cache_limiter('nocache');
  
   $conn = $cdb->getConnection();

//Site Admin veya Admin Hakkı yokda bu tanımı yapamamalı
   if (!right_get("SITE_ADMIN") && !right_get("ADMIN")){
        print_error("Bu sayfaya erişim hakkınız yok!");
    exit;
   }

  if ($act=="upd" && $id!="" && is_numeric($id)){
  //Admin Hakkı versa ve Site Admin hakkı yoksa sadece kendi sitesine ait bilgiyi görebilmeli
     if (right_get("ADMIN") && !right_get("SITE_ADMIN")){
         $site_cr = " AND SITE_ID = ".$_SESSION['site_id'];
     }
     $sql_str = "SELECT * FROM EXTENTIONS WHERE EXT_ID = $id".$site_cr;
   if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
        print_error($error_msg);
        exit;
     }
  if (mysqli_num_rows($result)>0){
       $row = mysqli_fetch_object($result);
  }else{
      print_error("Belirtilen Kayıt Bulunamadı");
        exit;    
  }
   }
//Action upd ise SITE_ID db den gelen değilse Session'dan gelen olmalı.
    if($act=='upd')
        $SITE_ID = $row->SITE_ID;
    else
        $SITE_ID = $_SESSION['site_id'];
        
   cc_page_meta();
   echo "<center>";
   page_header();
   fillSecondCombo();
   echo "<center><br>";
   table_header("Dahililer","75%");

?>
<script>
function submit_form() {
    if (check_form(document.extention))
      document.all("SITE_ID").disabled=false;
        document.extention.submit();
}
</script>
  <center>
  <table width="100%" cellpadding="0" cellspacing="0">
      <tr>
          <td>
              <form name="extention" method="post" onsubmit="return check_form(this);" action="extentions_db.php?act=<? echo  $_GET['act'] ?>">
              <input type="hidden" name="id" value="<?=$id?>">
              <table class="formbg">
                   <tr class="form">
                        <td class="td1_koyu">Site Adı</td>
                        <td>
                        <select name="SITE_ID" class="select1" style="width:200" <?if (!right_get("SITE_ADMIN")) {echo "disabled";}?> onchange="FillSecondCombo('DEPT_ID', 'DEPT_NAME', '01SITE_ID='+ this.value , '' , 'DEPT_ID' , this.value)">
                            <?
                                $strSQL = "SELECT SITE_ID, SITE_NAME FROM SITES";
                                echo $cUtility->FillComboValuesWSQL($conn, $strSQL,true,  $SITE_ID);
                            ?>
                            </select>
                        </td>
                    </tr>

                    <tr class="form">
                    <td class="td1_koyu">Dahili No</td>
                    <td class="td1_koyu"><input type="text" class="input1" name="EXT_NO" VALUE="<?echo $row->EXT_NO?>" size="7" Maxlength="7">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="checkbox" class="checkbox" name="RESIDE_IN_EXTEN" VALUE="1" <? if ($row->RESIDE_IN_EXTEN==1 || $act=="new"){echo "checked";}  ?> >											
						CI Extentions Sisteminde Görülsün
					</td> 
                    </tr>

                    <tr class="form">
                        <td class="td1_koyu">Departman</td>
						
						<td>
                        <select name="DEPT_ID" class="select1" style="width:200" <?if (!right_get("SITE_ADMIN")) {echo "disabled";}?> onchange="FillSecondCombo('DEPT_ID', 'DEPT_NAME', '01SITE_ID='+ this.value , '' , 'DEPT_ID' , this.value)">
                            <?
                                $strSQL = "SELECT DEPT_ID, DEPT_NAME FROM DEPTS";
                                echo $cUtility->FillComboValuesWSQL($conn, $strSQL,true,  $row->DEPT_ID);
                            ?>
                            </select>
                        </td>
						
						
                    </tr>
          <tr>
                    <td class="td1_koyu">E-Posta</td>
                      <td><input type="text" class="input1" name="EMAIL" VALUE="<?echo $row->EMAIL?>" size="30" Maxlength="100">  </td> 
                    </tr>
                    <tr class="form">
                        <td class="td1_koyu">Açıklama</td>
                        <td>
                              <TEXTAREA NAME="DESCRIPTION" class="textarea1" COLS=50 ROWS=5><?=$row->DESCRIPTION?></TEXTAREA>
                        </td>
                    <tr>
            <td></td>
          <td><img border="0" src="<?=IMAGE_ROOT?>kaydet.gif" style="cursor:hand;" onclick="javascript:submit_form()">
		  <?if($saved == "1"){?>
		  <span style="color:red" id="spanKaydedildi">Kaydedildi!</span>
		  <script>setTimeout(function(){ document.getElementById('spanKaydedildi').style.display = 'none'; }, 3000);</script>
		  <?}?>
		  </td>
                    </tr>
              </table>
            </form>
          </td>
    </tr>
  </table><br>
  <table align="right" width="100%" border="0">
    <tr>
      <td colspan="4" width="70%"></td>
            <td align="right"><a href="extentions_db.php?act=del&id=<?=$id?>&SITE_ID=<?=$SITE_ID?>"><img border="0" src="<?=IMAGE_ROOT?>kayit_sil.gif" style="cursor:hand;"></a></td>
      <td align="right"><a href="extentions_src.php"><img border="0" src="<?=IMAGE_ROOT?>arama_yap.gif" style="cursor:hand;"></a></td>
      <td align="right"><a href="extentions.php?act=new"><img border="0" src="<?=IMAGE_ROOT?>yeni_kayit.gif" style="cursor:hand;"></a></td>
    </tr>
        <tr>
      <td align="center" colspan="6" width="100%"><a href="unrep_exts_src.php">Raporlanmak İstenmeyen Dahili Ekle</a></td>
    </tr>
  </table>
      <script language="javascript" src="/scripts/form_validate.js"></script>
      <script language="javascript">
            form_fields[0] = Array ("EXT_NO", "Dahili alanını girmeniz gerekli.", TYP_NOT_NULL);
            form_fields[1] = Array ("DEPT_ID", "Departman alanını seçmeniz gerekli.", TYP_DROPDOWN);
            form_fields[2] = Array ("SITE_ID", "Site alanını girmeniz gerekli.", TYP_DROPDOWN);            
         <?if($act=='upd'){?>   
            FillSecondCombo('DEPT_ID', 'DEPT_NAME', '01SITE_ID='+ '<?=$row->SITE_ID?>' , '<?=$row->DEPT_ID?>' , 'DEPT_ID' , '<?=$row->DEPT_ID?>')
         <?}else{?>
            FillSecondCombo('DEPT_ID', 'DEPT_NAME', '01SITE_ID='+ document.all('SITE_ID').value , '<?=$DEPT_ID?>' ,'DEPT_ID' , document.all('DEPT_ID').value)         
         <?}?>
    
  </script>
  <script>  
    function goDel(my_id,site_id){
            if(confirm('Bu Kaydı silmek istediğinizden emin misiniz?')){
        document.location.href = 'extentions_db.php?act=del&id=' + my_id + '&SITE_ID=' + site_id
            }else{
              return false;
      }
  }
  </script>  
    
<?table_footer();
page_footer(0);?>  
