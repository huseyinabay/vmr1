<?  require_once(dirname($DOCUMENT_ROOT)."/cgi-bin/functions.php");
     $cUtility = new Utility();
     $cdb = new db_layer();
     require_valid_login();

  //Site Admin veya Admin Hakký yokda bu tanýmý yapamamalý
     if (!right_get("SITE_ADMIN") && !right_get("ADMIN")){
        print_error("Bu sayfaya eriþim hakkýnýz yok!");
    exit;
     }
  //Site Admin Hakký Yoksa sadece kendisine baðlý kayýtlarý görsün   
     if (right_get("ADMIN") && !right_get("SITE_ADMIN")){
         $SITE_ID = $SESSION['site_id'];
     }
      
     if ($p=="" || $p < 1)
       $p = 1;

     $start = $cUtility->myMicrotime();

     cc_page_meta();
     echo "<center>";
     page_header();
     echo "<br><br>";
     table_header_mavi("Hesap no Arama","65%");
?>
<center>
       <form name="auth_code_src" method="post" action="auth_mail_src.php?act=src">
         <input type="hidden" name="p" VALUE="<?echo $p?>">
         <input type="hidden" name="ORDERBY" value="<?=$ORDERBY?>">
         <table cellpadding="0" cellspacing="0" align="center" width="75%">

            <tr> 
                <td width="30%" nowrap class="font_beyaz">Hesap No</td>
                <td width="70%"><input type="text" class="input1" size="10" name="ACCOUNT_NO" VALUE="<?echo $ACCOUNT_NO?>" Maxlength="15"></td>
            </tr>
                <tr class="form">
                  <td width="30%" class="font_beyaz">E mail</td>
                   <td width="70%"><input type="text" class="input1" size="50" name="EMAIL" VALUE="<?echo $EMAIL?>" Maxlength="25"></td> 
               </tr>
            <tr>
                <td colspan=2 align=center><br>
                    <a href="javascript:submit_form('auth_code_src');"><img name="Image631" border="0" src="<?=IMAGE_ROOT?>ara.gif"></a>
                </td>
            </tr>
        </table>
       </form>
    <table width="100%"> 
      <tr>
        <td width="100%" align="right">
        <a href="auth_mail.php?act=new"><img border="0" src="<?=IMAGE_ROOT?>yeni_kayit1.gif" style="cursor:hand;"></a>
        </td>
      </tr>  
    </table>
<?
   table_footer_mavi();
   if ($act == "src") {
         $kriter = "";   


         $kriter .= $cdb->field_query($kriter, "ACCOUNT_NO",     "LIKE", "'%$ACCOUNT_NO%'");
         $kriter .= $cdb->field_query($kriter, "EMAIL",     "LIKE", "'%$EMAIL%'");
      
         $sql_str  = "SELECT * 
                      FROM ACCOUNT_EMAIL";
         
         if ($kriter != "")
               $sql_str .= " WHERE ". $kriter;  
       
         if ($ORDERBY) {
               $sql_str .= " ORDER BY ". $ORDERBY ;      
         }
	// echo $sql_str;
         $rs = $cdb->get_Records($sql_str, $p, $page_size,  $pageCount, $recCount);    
         $stop = $cUtility->myMicrotime();

?>
<br><br>
<?
table_arama_header("75%");
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td class="sonuc" width="50%" height="20"><? echo $cdb->calc_current_page($p, $recCount, $page_size);?></td>
    <td class="sonuc" align="right" width="50%" height="20"><? $cdb->show_time(($stop -$start)); ?></td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr class="header_beyaz">
        <td><a href="javascript:submit_form('auth_code_src',1, 'AUTH_CODES.AUTH_CODE')">Auth.Kodu</a></td>
        <td><a href="javascript:submit_form('auth_code_src',1, 'AUTH_CODES.ACCOUNT_NO')">E mail</a></td>
        <td>Güncelle</td>
    </tr>
<? 
   $i;
   while($row = mysql_fetch_object($rs)){
      $i++;
    echo " <tr class=\"".bgc($i)."\">".CR;
         echo " <td>$row->ACCOUNT_NO</td>".CR;
         echo " <td>$row->EMAIL</td>".CR;
         echo " <td><a HREF=\"auth_mail.php?act=upd&id=$row->Id\">Güncelle</td>".CR;
        echo " </tr>".CR;
       list_line(18);
   }
?>
</table>
<?table_arama_footer();   }?>
<table width="80%" align="center">
    <tr>
        <td align="center">
        <?
        echo $cdb->get_paging($pageCount, $p, "auth_code_src", $ORDERBY);
        ?></td>
    </tr>
</table>
<?page_footer(0);?>

<script language="javascript" src="/scripts/form_validate.js"></script>
<script language="JavaScript" type="text/javascript">
<!--

    function submit_form(form_name, page, sortby){
          document.all("ORDERBY").value = sortby;
          if (!sortby)
                document.all("ORDERBY").value = '';

          document.all("p").value = page;
          document.all(form_name).submit();
    }
//-->
</script>
</html>
