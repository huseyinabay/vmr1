<?
     require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
     $cUtility = new Utility();
     $cdb = new db_layer();
     require_valid_login();

//Site Admin veya Admin Hakkı yokda bu tanımı yapamamalı
   if (!right_get("SITE_ADMIN") && !right_get("ADMIN")){
        print_error("Bu sayfaya erişim hakkınız yok!");
    exit;
   }
//Site Admin Hakkı Yoksa sadece kendisine bağlı kayıtları görsün   
     if (right_get("ADMIN") && !right_get("SITE_ADMIN")){
         $SITE_ID = $_SESSION['site_id'];
     }
   
     if ($p=="" || $p < 1)
       $p = 1;

     $start = $cUtility->myMicrotime();

     cc_page_meta();
     echo "<center>";
   $link["Ana Sayfa"]= "../main.php";
   $link["Admin Ana Sayfa"]= "../admin/main.php";
   $link["Erişim Kodları"]= "access_code_src.php";
     page_header($link);
     echo "<br><br>";
     table_header_mavi("Erişim Kodu Arama","65%");
?>
<center>
       <form name="access_code_src" method="post" action="access_code_src.php?act=src">
         <input type="hidden" name="p" VALUE="<?echo (int)$p?>">
         <input type="hidden" name="ORDERBY" value="<?=$ORDERBY?>">
         <table cellpadding="0" cellspacing="0" border="0" width="75%">
            <tr class="form">
                <td class="font_beyaz">Site Adı</td>
                <td>
                    <select name="SITE_ID" class="select1" style="width:200" <?if (!right_get("SITE_ADMIN")) {echo "disabled";}?>>
                    <?
                        $strSQL = "SELECT SITE_ID, SITE_NAME FROM SITES ";
                        echo $cUtility->FillComboValuesWSQL($conn, $strSQL, true, $SITE_ID);
                    ?>
                    </select>
                   </td>
            </tr>
            <tr> 
                <td width="30%" class="font_beyaz">Erişim Kodu</td>
                <td width="70%"><input type="text" class="input1" size="10" name="ACCESS_CODE" VALUE="<?echo $ACCESS_CODE?>" Maxlength="15"></td>
            </tr>
            <tr> 
                <td width="30%" class="font_beyaz">Açıklama</td>
                <td width="70%"><input type="text" class="input1" name="ACCESS_CODE_DESC" VALUE="<?echo $ACCESS_CODE_DESC?>" Maxlength="30"></td>
            </tr>
            <tr>
                <td colspan=2 align=center><br>
                    <a href="javascript:submit_form('access_code_src');"><img name="Image631" border="0" src="<?=IMAGE_ROOT?>ara.gif"></a>
                </td>
            </tr>
        </table>
      <table width="100%"> 
        <tr>
          <td width="100%" align="right">
          <a href="access_code.php?act=new"><img border="0" src="<?=IMAGE_ROOT?>yeni_kayit1.gif" style="cursor:hand;"></a>
          </td>
        </tr>  
      </table>
       </form>
<?
   table_footer_mavi();
   if ($act == "src") {
         $kriter = "";

         if ($SITE_ID<>'-1'){
             $kriter .= $cdb->field_query($kriter, "SITES.SITE_ID",       "=",    "'$SITE_ID'");
         }
         $kriter .= $cdb->field_query($kriter, "ACCESS_CODES.ACCESS_CODE",   "=", "'$ACCESS_CODE'");
         $kriter .= $cdb->field_query($kriter, "ACCESS_CODES.ACCESS_CODE_DESC", "LIKE", "'%$ACCESS_CODE_DESC%'");
      
         $sql_str  = "SELECT ACCESS_CODES.ACCESS_CODE_ID,ACCESS_CODES.ACCESS_CODE,
                            ACCESS_CODES.ACCESS_CODE_DESC,SITES.SITE_NAME
                      FROM ACCESS_CODES
                      INNER JOIN SITES ON ACCESS_CODES.SITE_ID = SITES.SITE_ID
                      ";

         if ($kriter != ""){
               $sql_str .= " WHERE ". $kriter;  
         }
         if ($ORDERBY){
               $sql_str .= " ORDER BY ". $ORDERBY ;      
         }

         $rs = $cdb->get_Records($sql_str, (int)$p, $page_size,  $pageCount, $recCount);    
         $stop = $cUtility->myMicrotime();

?>
<br><br>
<?
table_arama_header("75%");
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td class="sonuc" width="50%" height="20"><? echo $cdb->calc_current_page((int)$p, $recCount, $page_size);?></td>
    <td class="sonuc" align="right" width="50%" height="20"><? $cdb->show_time(($stop -$start)); ?></td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr class="header_beyaz">
        <td><a href="javascript:submit_form('access_code_src',1,'ACCESS_CODES.ACCESS_CODE_ID')">ID</a></td>
        <td><a href="javascript:submit_form('access_code_src',1,'SITES.SITE_NAME')">Site Adı</a></td>
        <td><a href="javascript:submit_form('access_code_src',1,'ACCESS_CODES.ACCESS_CODE')">Erişim Kodu</a></td>
        <td><a href="javascript:submit_form('access_code_src',1,'ACCESS_CODES.ACCESS_CODE_DESC')">Açıklama</a></td>
        <td>Güncelle</td>
    </tr>
<? 
   $i;
   while($row = mysqli_fetch_object($rs)){
      $i++;
        echo " <tr class=\"".bgc($i)."\">".CR;
        echo " <td height=\"20\">$row->ACCESS_CODE_ID</td> ".CR;
         echo " <td>".substr($row->SITE_NAME,0,25)."</td>".CR;
         echo " <td>$row->ACCESS_CODE</td>".CR;
       echo " <td>".substr($row->ACCESS_CODE_DESC,0,30)."</td>".CR;
         echo " <td><a HREF=\"access_code.php?act=upd&id=$row->ACCESS_CODE_ID\">Güncelle</td>".CR;
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
        echo $cdb->get_paging($pageCount, (int)$p, "access_code_src", $ORDERBY);
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

