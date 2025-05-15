<?
   require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
   $cUtility = new Utility();
   $cdb = new db_layer();
   session_cache_limiter('nocache');
   require_valid_login();
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
     $sql_str = "SELECT * FROM TRUNKS WHERE TRUNK_ID = $id".$site_cr;
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
   echo "<center>";
   echo "<br>";
   table_header("Hat Tanımı","80%");
?>
<script>
    function submit_form() {
        if(check_form(document.trunk)){
        document.all("SITE_ID").disabled=false;
            document.trunk.submit();
        }
    }
</script>
       <form name="trunk" method="post" onsubmit="return check_form(this);" action="trunk_db.php?act=<? echo  $_GET['act'] ?>">
      <input type="hidden" name="id" value="<?=$id?>">
       <table width="100%" cellpadding="0" cellspacing="0" border="0">
          <tr class="form">
          <td class="td1_koyu">Site Adı</td>
             <td>
                <select name="SITE_ID" class="select1" style="width:200" <?if (!right_get("SITE_ADMIN")) {echo "disabled";}?>>
                     <?
                    $strSQL = "SELECT SITE_ID, SITE_NAME FROM SITES";
                       echo $cUtility->FillComboValuesWSQL($conn, $strSQL,true,  $SITE_ID);
                    ?>
                   </select>
              </td>
          </tr>
             <tr>
                <td width="%" class="td1_koyu">Hat Kodu</td>
               <td width="%"><input class="input1" type="text" size="10" name="MEMBER_NO" VALUE="<?echo $row->MEMBER_NO?>" Maxlength="15">  </td> 
            </tr>
          <tr>
              <td width="%" class="td1_koyu">Hat Adı</td>
                <td width="%"><input class="input1" type="text" size="20" name="TRUNK_NAME" VALUE="<?echo $row->TRUNK_NAME?>" Maxlength="30">  </td> 
             </tr>
             <tr>
              <td width="%" class="td1_koyu">Şebeke Tipi</td>
               <td width="%">
          <select class="select1" name="OptTypeid">
                    <?
                        $strSQL = "SELECT OptTypeid, OptTypeName FROM TOptTypes ";
                        echo $cUtility->FillComboValuesWSQL($conn, $strSQL, true,  $row->OPT_TYPE_ID);
                     ?>
                </select>
        </td>  
      </tr>
            <tr>
        <td width="%" class="td1_koyu">Hat Türü</td>
        <td width="%">
               <select class="input1" name="TRUNK_TYPE">
                  <option value="1" <?if ($row->TRUNK_TYPE==1)echo "selected";?>>Normal</option>
            <option value="2" <?if ($row->TRUNK_TYPE==2)echo "selected";?>>Kontörlü</option>
            <option value="3" <?if ($row->TRUNK_TYPE==3)echo "selected";?>>Ücretsiz</option>
                </select>
        </td>
      </tr>
      <tr>
                <td  width="%" class="td1_koyu">Telefon No</td>
                <td  width="%"><input class="input1" type="text" size="20" name="PHONE_NUMBER" VALUE="<?echo $row->PHONE_NUMBER?>" Maxlength="15">  </td> 
             </tr>
             <tr>
               <td width="%" class="td1_koyu">Açıklama</td>
                <td width=%">
                   <TEXTAREA class="textarea1" NAME="TRUNK_DESC" COLS=35 ROWS=3><?=$row->TRUNK_DESC?></TEXTAREA>
                 </td>
             </tr>
      <tr>
        <td width="%" class="td1_koyu">Giriş-Çıkış</td>
        <td width="%">
               <select class="input1" name="TRUNK_IO_TYPE">
                  <option value="1" <?if ($row->TRUNK_IO_TYPE==1)echo "selected";?>>Giriş</option>
            <option value="2" <?if ($row->TRUNK_IO_TYPE==2)echo "selected";?>>Çıkış</option>
            <option value="3" <?if ($row->TRUNK_IO_TYPE==3)echo "selected";?>>Giriş-Çıkış</option>
                </select>
        </td>
      </tr>
      <tr>
          <td></td>
        <td><img border="0" src="<?=IMAGE_ROOT?>kaydet.gif" style="cursor:hand;" onclick="javascript:submit_form()">
		<?if($saved == "1"){?>
  <span style="color:red" id="spanKaydedildi">Kaydedildi!</span>
  <script>setTimeout(function(){ document.getElementById('spanKaydedildi').style.display = 'none'; }, 3000);</script>
  <?}?></td>
           </tr>


     <? if ($act=="upd" && $id!=""){ ?>
     <tr>
       <td colspan=2> <iframe FRAMEBORDER="0" SRC="trunk_providers.php?id=<?=$id?>" WIDTH="580" HEIGHT="150" ></iframe></td>
     </tr>  
     <?}?>


    </table>
        </form>
    <table align="right" width="100%" border="0">
      <tr>
        <td colspan="4" width="70%"></td>
        <td align="right"><a href="trunk_db.php?act=del&id=<?=$id?>&SITE_ID=<?=$SITE_ID?>"><img border="0" src="<?=IMAGE_ROOT?>kayit_sil.gif" style="cursor:hand;"></a></td>
        <td align="right"><a href="trunk_src.php"><img border="0" src="<?=IMAGE_ROOT?>arama_yap.gif" style="cursor:hand;"></a></td>
        <td align="right"><a href="trunk.php?act=new"><img border="0" src="<?=IMAGE_ROOT?>yeni_kayit.gif" style="cursor:hand;"></a></td>
      </tr>
    </table>
    

<?table_footer();
page_footer(0);?>

<script language="javascript" src="/scripts/form_validate.js"></script>
<script language="javascript">
  form_fields[0]  = Array ("SITE_ID", "Hattın Bağlı Olduğu Siteyi seçmelisiniz.", TYP_DROPDOWN);
   form_fields[1] = Array ("MEMBER_NO", "Hat Kodu'nu Girmelisiniz.", TYP_NOT_NULL);
  form_fields[2]  = Array ("OptTypeid", "Hattın şebeke türünü seçmelisiniz.", TYP_DROPDOWN);
</script>

