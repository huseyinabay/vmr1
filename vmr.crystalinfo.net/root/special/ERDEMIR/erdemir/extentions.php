<?
   require_once(dirname($DOCUMENT_ROOT)."/cgi-bin/functions.php");
   $cUtility = new Utility();
   $cdb = new db_layer();
   require_valid_login();
   session_cache_limiter('nocache');
  
   $conn = $cdb->getConnection();

//Site Admin veya Admin Hakk� yokda bu tan�m� yapamamal�
   if (!right_get("SITE_ADMIN") && !right_get("ADMIN")){
        print_error("Bu sayfaya eri�im hakk�n�z yok!");
    exit;
   }

  if ($act=="upd" && $id!="" && is_numeric($id)){
  //Admin Hakk� versa ve Site Admin hakk� yoksa sadece kendi sitesine ait bilgiyi g�rebilmeli
     if (right_get("ADMIN") && !right_get("SITE_ADMIN")){
         $site_cr = " AND SITE_ID = ".$SESSION['site_id'];
     }
     $sql_str = "SELECT * FROM EXTENTIONS WHERE EXT_ID = $id".$site_cr;
   if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
        print_error($error_msg);
        exit;
     }
  if (mysql_numrows($result)>0){
       $row = mysql_fetch_object($result);
  }else{
      print_error("Belirtilen Kay�t Bulunamad�");
        exit;    
  }
   }
//Action upd ise SITE_ID db den gelen de�ilse Session'dan gelen olmal�.
    if($act=='upd')
        $SITE_ID = $row->SITE_ID;
    else
        $SITE_ID = $SESSION['site_id'];
        
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
              <form name="extention" method="post" onsubmit="return check_form(this);" action="extentions_db.php?act=<? echo  $HTTP_GET_VARS['act'] ?>">
              <input type="hidden" name="id" value="<?=$id?>">
              <table class="formbg">
                   <tr class="form">
                        <td class="td1_koyu">Site Ad�</td>
                        <td>
                        <select name="SITE_ID" class="select1" style="width:200" <?if (!right_get("SITE_ADMIN"))
                        {echo "disabled";}?> onchange="FillSecondCombo('DEPT_ID', 'DEPT_NAME', '01SITE_ID='+ this.value , '' , 'DEPT_ID' , this.value)">
                            <?
                                $strSQL = "SELECT SITE_ID, SITE_NAME FROM SITES";
                                echo $cUtility->FillComboValuesWSQL($conn, $strSQL,true,  $SITE_ID);
                            ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                    <td class="td1_koyu">Dahili No</td>
                    <td class="td1_koyu"><input type="text" class="input1" name="EXT_NO" VALUE="<?echo $row->EXT_NO?>" size="10" Maxlength="7">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="checkbox" class="checkbox" name="RESIDE_IN_EXTEN" VALUE="1" <?if ($row->RESIDE_IN_EXTEN==1 || $act=="new"){echo checked;}?> >											
						CI Extentions Sisteminde G�r�ls�n
					</td> 
                    </tr>
                    <tr>
                    <td class="td1_koyu">Hesap No</td>
                    <td class="td1_koyu"><input type="text" class="input1" name="ACCOUNT_NO" VALUE="<?echo $row->ACCOUNT_NO?>" size="10" Maxlength="10">
					</td> 
                    </tr>
                    <tr class="form">
                        <td class="td1_koyu">Departman</td>
                        <td>
                         <select name="DEPT_ID" class="select1">
                               <OPTION value="-1">--Se�iniz--</OPTION>
                         </select>
                        </td>
                            </tr>
                        

        	    <tr>
                    <td class="td1_koyu">E-Posta</td>
                      <td><input type="text" class="input1" name="EMAIL" VALUE="<?echo $row->EMAIL?>" size="37" Maxlength="100">  </td> 
                    </tr>
                    
			<tr class="form">
                        <td class="td1_koyu">�sim</td>
                        <td>
                              <TEXTAREA NAME="DESCRIPTION" class="textarea1" COLS=51 ROWS=2><?=$row->DESCRIPTION?></TEXTAREA>
                        </td>
                    <tr>

			<tr class="form">
                        <td class="td1_koyu">PER NO</td>
                        <td>
                              <TEXTAREA NAME="PER_NO" class="textarea1" COLS=51 ROWS=2><?=$row->PER_NO?></TEXTAREA>
                        </td>
                    <tr>

                    
			<tr class="form">
                        <td class="td1_koyu">TN</td>
                        <td>
                              <TEXTAREA NAME="TN" class="textarea2" COLS=38 ROWS=2><?=$row->TN?></TEXTAREA>
                        </td>
                    <tr>
    		    <tr>
                    <td class="td1_koyu">YER</td>
                      <td><input type="text" class="input2" name="YER" VALUE="<?echo $row->YER?>" size="48" Maxlength="100">  </td> 
                    </tr>
                	
       
        
            <td></td>
          <td><img border="0" src="<?=IMAGE_ROOT?>kaydet.gif" style="cursor:hand;" onclick="javascript:submit_form()"></td>
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
  </table>
      <script language="javascript" src="/scripts/form_validate.js"></script>
      <script language="javascript">
            form_fields[0] = Array ("EXT_NO", "Dahili alan�n� girmeniz gerekli.", TYP_NOT_NULL);
            form_fields[1] = Array ("DEPT_ID", "Departman alan�n� se�meniz gerekli.", TYP_DROPDOWN);
            form_fields[2] = Array ("SITE_ID", "Site alan�n� girmeniz gerekli.", TYP_DROPDOWN);            
         <?if($act=='upd'){?>   
            FillSecondCombo('DEPT_ID', 'DEPT_NAME', '01SITE_ID='+ '<?=$row->SITE_ID?>' , '<?=$row->DEPT_ID?>' , 'DEPT_ID' , '<?=$row->DEPT_ID?>')
         <?}else{?>
            FillSecondCombo('DEPT_ID', 'DEPT_NAME', '01SITE_ID='+ document.all('SITE_ID').value , '<?=$DEPT_ID?>' ,'DEPT_ID' , document.all('DEPT_ID').value)         
         <?}?>
    
  </script>
  <script>  
    function goDel(my_id,site_id){
            if(confirm('Bu Kayd� silmek istedi�inizden emin misiniz?')){
        document.location.href = 'extentions_db.php?act=del&id=' + my_id + '&SITE_ID=' + site_id
            }else{
              return false;
      }
  }
  </script>  
    
<?table_footer();
page_footer(0);?>  
