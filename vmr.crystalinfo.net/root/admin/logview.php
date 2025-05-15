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
   $conn = $cdb->getConnection();
   if ($p=="" || $p < 1)
       $p = 1;

   $start = $cUtility->myMicrotime();

   cc_page_meta();
   echo "<center>";
   page_header();
   echo "<BR>";
   table_header_mavi("Santral Log'u Arama",'50%');
?>
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

       <form name="log_arama" method="post" action="logview.php?act=src">
         <input type="hidden" name="p" VALUE="<?echo $p?>">
         <input type="hidden" name="ORDERBY" value="ID DESC">
         <table cellpadding=0 cellspacing=0 ALIGN="center" border="0">
            <tr class="form">
        <td></td>      
                <td class="font_beyaz">Site Adı</td>
                <td>
                    <select name="SITE_ID" class="select1" style="width:200" <?if (!right_get("SITE_ADMIN")) {echo "disabled";}?>>
                    <?
                        $strSQL = "SELECT SITE_ID, SITE_NAME FROM SITES ";
                        echo $cUtility->FillComboValuesWSQL($conn, $strSQL, true, $SITE_ID);
                    ?>
                    </select>
                   </td>
        <td width="15%"></td>           
            </tr>
           <tr> 
        <td width="15%"></td>  
              <td width="35%" class="font_beyaz">ID&nbsp;&nbsp;</td>
              <td width="45%"><input type="text" class="input1" name="ID" VALUE="<?echo $ID?>" Maxlength="7"></td>
        <td width="15%"></td>
          </tr>
           <tr> 
        <td></td>      
              <td class="font_beyaz">İçerik&nbsp;&nbsp;</td>
               <td><input type="text" class="input1" name="NAME" VALUE="<?echo $NAME?>" Maxlength="30"></td>
        <td width="15%"></td>
           </tr>
        <tr> 
              <td height="35" class="font_beyaz" align="center" colspan="4">Aranacak Yer&nbsp;&nbsp;</td>
           </tr>
      <tr> 
        <td></td>
        <td class="font_beyaz" colspan="2"><input type="radio" class="input1" name="src_place" value="raw_archieve" <?if($src_place=='raw_archieve'){echo "checked";}?>> Ham Data Arşiv</td>
        <td></td>
      </tr>
      <tr> 
        <td></td>      
        <td class="font_beyaz" colspan="2"><input type="radio" class="input1" name="src_place" value="semi_archieve" <?if($src_place=='semi_archieve'){echo "checked";}?>> Birleşik Data Arşiv</td>
        <td></td>
      </tr>
          <tr>
              <td colspan=4 align=center><br>
                  <a href="javascript:submit_form('log_arama');"><img name="Image631" border="0" src="<?=IMAGE_ROOT?>ara.gif"></a>
               </td>
           </tr>
       </form>
       </table>
<?
   table_footer_mavi();

   if ($act == "src") {
         $kriter = "";   

         if ($SITE_ID<>'-1'){
             $kriter .= $cdb->field_query($kriter, "SITE_ID",       "=",    "'$SITE_ID'");
         }

     switch($src_place){
       case 'raw_archieve':
        $table_name = "RAW_ARCHIEVE";
             $kriter .= $cdb->field_query($kriter, "DATA", "LIKE", "'%$NAME%'");
             $kriter .= $cdb->field_query($kriter, "ID", "=", "'$ID'");
           $kayit="DATA";
           $ORDERBY = "ID DESC";
        break;
       case 'semi_archieve':
        $table_name = "SEMI_ARCHIEVE";
             $kriter .= $cdb->field_query($kriter, "LINE1", "LIKE", "'%$NAME%'");
             $kriter .= $cdb->field_query($kriter, "LINE1_ID", "=", "'$ID'");
           $kayit="LINE1";
           $ORDERBY = "LINE1_ID DESC";
        break;
       default :
        $table_name = "RAW_ARCHIEVE";
             $kriter .= $cdb->field_query($kriter, "DATA", "LIKE", "'%$NAME%'");
             $kriter .= $cdb->field_query($kriter, "ID", "=", "'$ID'");
           $kayit="DATA";
           $ORDERBY = "ID DESC";
        break;
     }
      
         $sql_str  = "SELECT * FROM ".$table_name;
         
         if ($kriter != "")
               $sql_str .= " WHERE ". $kriter;
       
         if ($ORDERBY) {
               $sql_str .= " ORDER BY ". $ORDERBY ;
         }
//echo $sql_str;
         $rs = $cdb->get_Records($sql_str, $p, $page_size,  $pageCount, $recCount);    
         $stop = $cUtility->myMicrotime();

?>
<BR><BR>
<?
table_arama_header("93%");
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" bordercolor="60ADD1">
  <tr > 
    <td class="sonuc" HEIGHT="20" width="40%"  ><? echo $cdb->calc_current_page($p, $recCount, $page_size);?></td>
    <td class="sonuc" align="right" width="50%"><? $cdb->show_time(($stop -$start)); ?></td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0" ALIGN="left">
    <tr class="header_beyaz" >
        <td HEIGHT="20">ID</td>
        <td>Kayıt</td>
        <td></td>
    </tr>
<? 
     $i;
     while($row = mysqli_fetch_object($rs)){
    $i++;
       echo "<tr class=\"".bgc($i)."\">".CR;
      echo "  <td HEIGHT=20>$row->ID</td> ".CR;
      echo "  <td HEIGHT=20>".get_site_name($row->SITE_ID)."</td> ".CR;
      echo "  <td BGCOLOR=#00FF00 ><B>".$row->$kayit."</B></td>".CR;
       echo "  <td></td>".CR;
       echo "</tr>".CR;
       list_line(18);
   }
?>
</table>
<?table_arama_footer();}?>
<table border="0">
    <tr>
        <td align="center">
        <?
        echo $cdb->get_paging($pageCount, $p, "log_arama", $ORDERBY);
        ?></td>
    </tr>
</table>
<? page_footer(0);

function row_line(){?>
  <td><font COLOR="#3F7DBC">|</font></td>
<?}
?>
