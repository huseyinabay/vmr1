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
   page_header();
   echo "<br><br>";
   table_header_mavi("Hat Arama","60%");
?>
     <form name="trunk_arama" method="post" action="trunk_src.php?act=src">
    <input type="hidden" name="p" VALUE="<?echo (int)$p?>">
     <input type="hidden" name="ORDERBY" value="<?=$ORDERBY?>">
   <table cellpadding="0" cellspacing="0" border="0" width="80%" align="center">
        <tr>
           <td width="40%" class="font_beyaz">Site Adı</td>
            <td>
             <select name="SITE_ID" class="select1" style="width:250" <?if (!right_get("SITE_ADMIN")) {echo "disabled";}?>>
                 <?
                        $strSQL = "SELECT SITE_ID, SITE_NAME FROM SITES ";
                        echo $cUtility->FillComboValuesWSQL($conn, $strSQL, true, $SITE_ID);
                    ?>
              </select>
      </td>
       </tr>
        <tr> 
           <td width="30%" class="font_beyaz">Hat Kodu</td>
            <td width="70%"><input class="input1" type="text" name="MEMBER_NO" VALUE="<?echo $MEMBER_NO?>" size="10" Maxlength="15"></td>
       </tr>
       <tr> 
           <td width="30%" class="font_beyaz">Hat Adı</td>
           <td width="70%"><input class="input1" type="text" name="TRUNK_NAME" VALUE="<?echo $TRUNK_NAME?>" Maxlength="15"></td>
       </tr>
       <tr> 
          <td class="font_beyaz" width="40%">Hat Şebeke Türü</td>
           <td>
        <select class="select1" name="OptTypeid">
                    <?
                       $strSQL = "SELECT OptTypeid, OptTypeName FROM TOptTypes ";
                     echo    $cUtility->FillComboValuesWSQL($conn, $strSQL, true,  $OptTypeid);
                    ?>
               </select>
      </td>    
    </tr>
       <tr> 
           <td width="30%" class="font_beyaz">Telefon No</td>
            <td width="70%"><input class="input1" type="text" name="PHONE_NUMBER" VALUE="<?echo $PHONE_NUMBER?>" Maxlength="15"></td>
        </tr>
       <tr> 
            <td width="30%">&nbsp;</td>
           <td width="70%" class="font_beyaz">
       <a href="javascript:submit_form('trunk_arama');"><img name="Image631" border="0" src="<?=IMAGE_ROOT?>ara.gif"></a>
      </td>
        </tr>
    </table>
     <table width="100%"> 
    <tr>
      <td width="100%" align="right">
      <a href="trunk.php?act=new"><img border="0" src="<?=IMAGE_ROOT?>yeni_kayit1.gif" style="cursor:hand;"></a>
      </td>
    </tr>  
  </table>
   </form>

<?
   table_footer_mavi();
   if ($act == "src"){
         $kriter = "";
      
         if ($SITE_ID<>'-1'){
             $kriter .= $cdb->field_query($kriter, "SITES.SITE_ID",       "=",    "'$SITE_ID'");
         }
         if ($OptTypeid<>'-1'){
             $kriter .= $cdb->field_query($kriter, "TRUNKS.OPT_TYPE_ID",       "=",    "'$OptTypeid'");
         }
         $kriter .= $cdb->field_query($kriter, "TRUNKS.MEMBER_NO", "=", "'$MEMBER_NO'");
         $kriter .= $cdb->field_query($kriter, "TRUNKS.TRUNK_NAME", "LIKE", "'%$TRUNK_NAME%'");
         $kriter .= $cdb->field_query($kriter, "TRUNKS.PHONE_NUMBER", "LIKE", "'%$PHONE_NUMBER%'");
      
         $sql_str  = "SELECT SITES.SITE_ID,TRUNKS.TRUNK_ID,SITES.SITE_NAME,
                        TRUNKS.MEMBER_NO,TRUNKS.TRUNK_NAME, TOptTypes.OptTypeName,
                        TRUNKS.PHONE_NUMBER
                      FROM TRUNKS 
                        LEFT JOIN SITES
                           ON TRUNKS.SITE_ID = SITES.SITE_ID
            LEFT JOIN TOptTypes
              ON TRUNKS.OPT_TYPE_ID = TOptTypes.OptTypeid   
                      ";
         
         if ($kriter != "")
               $sql_str .= " WHERE ". $kriter;  
       
         if ($ORDERBY) {
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
        <td width="5%" align="center"><a href="javascript:submit_form('trunk_arama',1,'TRUNKS.TRUNK_ID')">ID</a></td>
        <td><a href="javascript:submit_form('trunk_arama',1,'SITES.SITE_NAME')">Site Adı</a></td>
        <td><a href="javascript:submit_form('trunk_arama',1,'TRUNKS.MEMBER_NO')">Hat Kodu</a></td>
        <td><a href="javascript:submit_form('trunk_arama',1,'TRUNKS.TRUNK_NAME')">Hat Adı</a></td>
        <td><a href="javascript:submit_form('trunk_arama',1,'TRUNKS.PHONE_NUMBER')">Telefon No</a></td>        
        <td><a href="javascript:submit_form('trunk_arama',1,'TOptTypes.OptTypeName')">Hat Şebeke Türü</a></td>
        <td>Güncelle</td>
    </tr>
<? 
      $i;
      while($row = mysqli_fetch_object($rs)){
         $i++;
      echo " <tr class=\"".bgc($i)."\">".CR;
            echo " <td height=\"20\" align=\"center\">$row->TRUNK_ID</td> ".CR;
            echo " <td>".substr($row->SITE_NAME,0,25)."</td>".CR;
            echo " <td>$row->MEMBER_NO</td>".CR;
            echo " <td>".substr($row->TRUNK_NAME,0,25)."</td>".CR;
            echo " <td>$row->PHONE_NUMBER</td>".CR;
      echo " <td>$row->OptTypeName</td>".CR;
      echo " <td><a HREF=\"trunk.php?act=upd&id=$row->TRUNK_ID\">Güncelle</td>".CR;
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
        echo $cdb->get_paging($pageCount, (int)$p, "trunk_arama", $ORDERBY);
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
