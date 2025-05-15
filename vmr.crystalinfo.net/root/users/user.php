<?
     require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
	 
	 //ini_set('display_errors', 'On');
     //error_reporting(E_ALL);	 
	 
    $cUtility = new Utility();
    $cdb = new db_layer();
    session_cache_limiter('nocache');
    require_valid_login();
    $conn = $cdb->getConnection();
    
   //Site Admin veya Admin Hakkı yoksa bu tanımı yapamamalı
   if (!right_get("SITE_ADMIN") && !right_get("ADMIN") && $id != $_SESSION["user_id"]){
        print_error("Burayı Görme Hakkınız Yok");
    exit;
   }

   if ($act=="upd" && $id!=="" && is_numeric($id)){
    //Admin Hakkı versa ve Site Admin hakkı yoksa sadece kendi sitesine ait bilgiyi görebilmeli
       $site_cr = "";
    if (right_get("ADMIN") && !right_get("SITE_ADMIN")){
           $site_cr = " AND SITE_ID = ".$_SESSION['site_id'];
       }
       $sql_str = "SELECT * FROM USERS WHERE USER_ID = $id AND USER_ID<>1 ".$site_cr;
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

    cc_page_meta();
    
  //Action upd ise SITE_ID db den gelen değilse Session'dan gelen olmalı.   
    if($act=='upd')
        $SITE_ID = $row->SITE_ID;
    else
        $SITE_ID = $_SESSION['site_id'];
     
    $link_maker = Array("Anasayfa"        => "/index.php",
                        "Kullanıcılar"    => "/users/index.php",
                        "Kullanıcı Arama" => "/users/user_src.php"   
                       );
    right_toclient();
    
  echo "<center>";
    fillSecondCombo();
  page_header($link_maker1);
?>
<table  cellpadding="0" cellspacing="0" BORDER="0" width="95%">
  <tr>
       <td VALIGN="top" WIDTH="50%">
<br> 
<? 
   table_header("Kullanıcı Bilgileri", "100%");
?>
<script LANGUAGE="javascript" src="/scripts/popup.js"></script>
<script>
function submit_form() {
    if(check_form(document.users)){
      document.all("SITE_ID").disabled=false;  
        document.users.submit();
    }
}
</script>
<center>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
   <tr>
        <td width="50%">
       <form name="users" method="post" onsubmit="return check_form(this);" action="user_db.php?act=<? echo  $_GET['act'] ?>">
         <input type="hidden" name="id" value="<?=$id?>">
           <table width="100%" cellpadding="0" cellspacing="0" border="0">
           <tr class="form">
                    <td class="td1_koyu">Site Adı</td>
                    <td colspan="2">
                        <select name="SITE_ID" class="select1" style="width:200" <?if (!right_get("SITE_ADMIN")) {echo "disabled";}?> onchange="Fillothercombos(this.value)">
                        <?
                            $strSQL = "SELECT SITE_ID, SITE_NAME FROM SITES";
                            echo $cUtility->FillComboValuesWSQL($conn, $strSQL,true,  $SITE_ID);
                        ?>
                        </select>
                    </td>
                </tr>
                <tr>
                   <td width="35%" class="td1_koyu">Kullanıcı Adı</td>
                    <td width="35%"><input size="10" type="text" class="input1" name="USERNAME" VALUE="<?echo $row->USERNAME?>" Maxlength="10"></td> 
                 <td width="30%" align="center"  align=center">
                     </td>

        </tr>
              <tr>
                  <td class="td1_koyu" colspan="3"><a href="#" onclick="javascript:popup('chgpsw.php?id=<?=$id?>','user',500,500)">Şifre Değiştir</a></td>
<!--                     <td> <input size="10" type="PASSWORD" class="input1" name="PASSWORD" VALUE="<?echo $row->PASSWORD?>" Maxlength="8"></td> -->
              </tr>
               <tr> 
                   <td class="td1_koyu">Aktif</td>
                    <td colspan="2">
                      <select name="DISABLED" class="select1">
                           <OPTION VALUE="N" <?=$cUtility->is_dropdown_selected("N", $row->DISABLED)?>>Evet</OPTION>
                          <OPTION VALUE="Y" <?=$cUtility->is_dropdown_selected("Y", $row->DISABLED)?> >Hayır</OPTION>
                        </select>
                   </td>
               <tr>
                <tr>
                  <td class="td1_koyu">Adı</td>
                    <td colspan="2"><input size="10" type="text" class="input1" name="NAME" VALUE="<?echo $row->NAME?>" Maxlength="20">  </td> 
                </tr>
                <tr>
                    <td class="td1_koyu">Soyadı</td>
                    <td colspan="2"><input size="10" type="text" class="input1" name="SURNAME" VALUE="<?echo $row->SURNAME?>" Maxlength="20">  </td> 
                </tr>
               <tr>
                    <td class="td1_koyu">Departman</td>
                    <td colspan="2">
                         <select name="DEPT_ID" class="select1" style="width:200">
                              <?
                               $strSQL = "SELECT DEPT_ID, DEPT_NAME FROM DEPTS ";
                               echo    $cUtility->FillComboValuesWSQL($conn, $strSQL, true,  $row->DEPT_ID);
                             ?>
                        </select>
                    </td>
                <tr>
              <tr>
                <td class="td1_koyu">Pozisyon</td>
                 <td colspan="2"><input type="text" size="35" class="input1" name="POSITION" VALUE="<?echo $row->POSITION?>" Maxlength="50">  </td> 
              <tr>
               <tr>
                    <td class="td1_koyu">e-Mail</td>
                    <td colspan="2"><input type="text" size="35" class="input1" name="EMAIL" VALUE="<?echo $row->EMAIL?>" Maxlength="50">  </td> 
                </tr>
                 <tr>
                    <td class="td1_koyu">Dahili 1</td>
                   <td colspan="2">
                       <select name="EXT_ID1" class="select1" style="width:100">
                           <?
                               $strSQL = "SELECT EXT_ID, EXT_NO  FROM EXTENTIONS ";
                               echo $cUtility->FillComboValuesWSQL($conn, $strSQL, true,  $row->EXT_ID1);
                             ?>
                       </select>
                     </td> 
              </tr>
        <tr class="form">
                    <td class="td1_koyu">Dahili2</td>
                    <td colspan="2">
                      <select name="EXT_ID2" class="select1" style="width:100">                          <?
                              $strSQL = "SELECT EXT_ID, EXT_NO  FROM EXTENTIONS ";
                                 echo $cUtility->FillComboValuesWSQL($conn, $strSQL, TRUE,  $row->EXT_ID2);
                             ?>
                          </select>
                    </td> 
                </tr>
        <tr class="form">
                    <td class="td1_koyu">Dahili 3</td>
                    <td colspan="2">
                      <select name="EXT_ID3" class="select1" style="width:100">
                          <?
                              $strSQL = "SELECT EXT_ID, EXT_NO  FROM EXTENTIONS ";
                                 echo    $cUtility->FillComboValuesWSQL($conn, $strSQL, true,  $row->EXT_ID3);
                             ?>
                          </select>
                    </td> 
                </tr>
                 <tr>
                    <td class="td1_koyu">Auth Kodu</td>
                    <td colspan="2">
                         <select name="AUTH_CODE_ID" class="select1" style="width:100">
                              <?
                               $strSQL = "SELECT AUTH_CODE_ID, AUTH_CODE FROM AUTH_CODES ";
                               echo $cUtility->FillComboValuesWSQL($conn, $strSQL, true,  $row->AUTH_CODE_ID);
                             ?>
                       </select>
                    </td>
                </tr>
                 <tr>
                   <td class="td1_koyu">GSM</td>
                    <td colspan="2"><input type="text" class="input1" name="GSM" VALUE="<?echo $row->GSM?>" Maxlength="15">  </td> 
               </tr>
                <tr>
                   <td class="td1_koyu">Ev Tel.</td>
                   <td colspan="2"><input type="text" class="input1" name="HOME_TEL" VALUE="<?echo $row->HOME_TEL?>" Maxlength="15">  </td> 
                </tr>
               <tr class="form">
                    <td class="td1_koyu">Not</td>
                     <td colspan="2">
                       <TEXTAREA NAME="NOTE" class="textarea1" COLS=33 ROWS=3><?=$row->NOTE?></TEXTAREA>
                    </td>
                </tr> 
                <tr>
            <td></td>
          <td colspan="2"><img border="0" src="<?=IMAGE_ROOT?>kaydet.gif" style="cursor:hand;" onclick="javascript:submit_form()">
		   <?if($saved == "1"){?>
  <span style="color:red" id="spanKaydedildi">Kaydedildi!</span>
  <script>setTimeout(function(){ document.getElementById('spanKaydedildi').style.display = 'none'; }, 3000);</script>
  <?}?>
		  </td>
                </tr>
       </table><br>
    <table align="right" width="100%" border="0">
      <tr>
        <td colspan="4" width="70%"></td>
        <td align="right"><a href="user_db.php?act=del&id=<?=$id?>&SITE_ID=<?=$SITE_ID?>"><img border="0" src="<?=IMAGE_ROOT?>kayit_sil.gif" style="cursor:hand;"></a></td>
        <td align="right"><a href="user_src.php"><img border="0" src="<?=IMAGE_ROOT?>arama_yap.gif" style="cursor:hand;"></a></td>
        <td align="right"><a href="user.php?act=new"><img border="0" src="<?=IMAGE_ROOT?>yeni_kayit.gif" style="cursor:hand;"></a></td>
      </tr>
    </table>
        </form>
      <script language="javascript" src="/scripts/form_validate.js"></script>
      <script language="javascript">
            form_fields[0] = Array ("SITE_ID", "Site alanını seçmeniz gerekli.", TYP_DROPDOWN);
            form_fields[1] = Array ("USERNAME", "Kullanıcı Adı alanını girmeniz gerekli.", TYP_NOT_NULL);
            form_fields[2] = Array ("NAME", "Adı alanını girmeniz gerekli.", TYP_NOT_NULL);
            form_fields[3] = Array ("SURNAME", "Soyadı alanını girmeniz gerekli.", TYP_NOT_NULL);
            form_fields[4] = Array ("DEPT_ID", "Departman alanını seçmeniz gerekli.", TYP_DROPDOWN);
      
    function Fillothercombos(n_val){
        FillSecondCombo('DEPT_ID',      'DEPT_NAME', '01SITE_ID='+ n_val , '' , 'DEPT_ID' , n_val)
        FillSecondCombo('EXT_ID',       'EXT_NO',    '02SITE_ID='+ n_val , '' , 'EXT_ID1' , n_val)
        FillSecondCombo('EXT_ID',       'EXT_NO',    '02SITE_ID='+ n_val , '' , 'EXT_ID2' , n_val)
        FillSecondCombo('EXT_ID',       'EXT_NO',    '02SITE_ID='+ n_val , '' , 'EXT_ID3' , n_val)        
    }
  
    
  <?if($act=='upd'){?>
        FillSecondCombo('DEPT_ID',      'DEPT_NAME',    '01SITE_ID='+ '<?=$row->SITE_ID?>' , '<?=$row->DEPT_ID?>' , 'DEPT_ID' ,      '')
        FillSecondCombo('EXT_ID',       'EXT_NO',       '02SITE_ID='+ '<?=$row->SITE_ID?>' , '<?=$row->EXT_ID1?>' , 'EXT_ID1' ,       '')
        FillSecondCombo('EXT_ID',       'EXT_NO',       '02SITE_ID='+ '<?=$row->SITE_ID?>' , '<?=$row->EXT_ID2?>' , 'EXT_ID2' ,       '')
        FillSecondCombo('EXT_ID',       'EXT_NO',       '02SITE_ID='+ '<?=$row->SITE_ID?>' , '<?=$row->EXT_ID3?>' , 'EXT_ID3' ,       '<?=$row->EXT_ID3?>')        

   <?}else{?> 
        FillSecondCombo('DEPT_ID',      'DEPT_NAME',    '01SITE_ID='+ document.all('SITE_ID').value , 'DEPT_ID',  'DEPT_ID' ,      '')
        FillSecondCombo('EXT_ID',       'EXT_NO',       '02SITE_ID='+ document.all('SITE_ID').value , 'EXT_ID1' , 'EXT_ID1' ,       '')
        FillSecondCombo('EXT_ID',       'EXT_NO',       '02SITE_ID='+ document.all('SITE_ID').value , 'EXT_ID2' , 'EXT_ID2' ,       '')
        FillSecondCombo('EXT_ID',       'EXT_NO',       '02SITE_ID='+ document.all('SITE_ID').value , 'EXT_ID3' , 'EXT_ID3' ,       '')        
   <?}?>

      </script>
      </td>
  </tr>
</table>
<?   table_footer(); ?>
</td>
<td width="2%"></td>
<td valign="top" align="center" width="48%" HEIGHT="30">
<br>
<?
if($id && (right_get("ADMIN") || right_get("SITE_ADMIN"))){
        table_header("Kullanıcının Hakları", "100%");
        ?>
        <iframe FRAMEBORDER="0" SRC="user_rights.php?id=<?=$id?>" WIDTH="320" HEIGHT="120"  style="overflow: hidden;" scrolling="no" ></iframe>
        <?
        table_footer("");
    echo "<br>";
        table_header("Raporunu Alabileceği Departmanlar", "100%");
        ?>
        <iframe FRAMEBORDER="0" SRC="dept_rep_rights.php?id=<?=$id?>" WIDTH="320" HEIGHT="260" style="overflow: hidden;" scrolling="no" ></iframe>
        <?
        table_footer("");

}?>
</td>
</tr>
</table><br>
<?page_footer(0);?>

