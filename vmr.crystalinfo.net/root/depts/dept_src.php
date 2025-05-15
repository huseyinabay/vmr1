<?
   require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
   
   	 //ini_set('display_errors', 'On');
     //error_reporting(E_ALL);	
 
   
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
     table_header_mavi("Departman Arama","60%");
?>
<center>
       <form name="departman_arama" method="post" action="dept_src.php?act=src">
         <input type="hidden" name="p" VALUE="<?echo $p?>">
         <input type="hidden" name="ORDERBY" value="<?=$ORDERBY?>">
         <table cellpadding="0" cellspacing="0" border="0" width="65%">
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
                    <td width="50%" class="font_beyaz">Departman Adı</td>
                    <td width="50%"><input type="text" class="input1" name="DEPT_NAME" VALUE="<?echo $DEPT_NAME?>" size="20" Maxlength="20"></td>
            </tr>
            <tr>
                 <td colspan=2 align=center><br>
                    <a href="javascript:submit_form('departman_arama');"><img name="Image631" border="0" src="<?=IMAGE_ROOT?>ara.gif"></a>
                </td>
            </tr>
            </tr>
        </table>
       </form>
    <table width="100%"> 
      <tr>
        <td width="100%" align="right">
        <a href="dept.php?act=new"><img border="0" src="<?=IMAGE_ROOT?>yeni_kayit1.gif" style="cursor:hand;"></a>
        </td>
      </tr>  
    </table>
<?
   table_footer_mavi();
   if ($act == "src") {
         $kriter = "";   

         if ($SITE_ID<>'-1'){
             $kriter .= $cdb->field_query($kriter, "SITES.SITE_ID",       "=",    "'$SITE_ID'");
         }

         $kriter .= $cdb->field_query($kriter, "DEPT_NAME", "LIKE", "'%$DEPT_NAME%'");
    
         $sql_str  = "SELECT DEPTS.DEPT_ID, DEPTS.DEPT_NAME, DEPTS.DEPT_RSP_EMAIL,SITES.SITE_NAME
                      FROM DEPTS
                      INNER JOIN SITES ON DEPTS.SITE_ID = SITES.SITE_ID";
         
         if ($kriter != "")
               $sql_str .= " WHERE ". $kriter;  
       
         if ($ORDERBY) {
               $sql_str .= " ORDER BY ". $ORDERBY ;      
         }
//echo $sql_str;die;
         $rs = $cdb->get_Records($sql_str, (int)$p, $page_size,  $pageCount, $recCount);    
         $stop = $cUtility->myMicrotime();

?>
<br><br>
<?
table_arama_header("60%");
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td class="sonuc" width="50%" height="20"><? echo $cdb->calc_current_page((int)$p, $recCount, $page_size);?></td>
    <td class="sonuc" align="right" width="50%" height="20"><? $cdb->show_time(($stop -$start)); ?></td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr class="header_beyaz">
        <td><a href="javascript:submit_form('departman_arama',1, 'DEPTS.DEPT_ID')">ID</a></td>
        <td><a href="javascript:submit_form('departman_arama',1,'SITES.SITE_NAME')">Site Adı</a></td>       
        <td><a href="javascript:submit_form('departman_arama',1, 'DEPTS.DEPT_NAME')">Departman</a></td>
        <td><a href="javascript:submit_form('departman_arama',1, 'DEPTS.DEPT_RSP_EMAIL')">Sorumlu Email'i</a></td>
        <td>Güncelle</td>
    </tr>
<? 
   $i="";
 
	if(mysqli_num_rows($rs)>0){
       while($row = mysqli_fetch_object($rs)){
          $i++;
           echo " <tr class=\"".bgc($i)."\">".CR;
           echo " <td height=\"20\">$row->DEPT_ID</td> ".CR;
           echo " <td>".substr($row->SITE_NAME,0,25)."</td>".CR;
           echo " <td>".substr($row->DEPT_NAME,0,25)."</td>".CR;
           echo " <td>".substr($row->DEPT_RSP_EMAIL,0,25)."</td>".CR;
           echo " <td><a HREF=\"dept.php?act=upd&id=$row->DEPT_ID\">Güncelle</td>".CR;
           echo "</tr>".CR;
           list_line(18);
       }

    }

?>
</table>
<?table_arama_footer();   }?>
<table width="80%" align="center">
    <tr>
        <td align="center">
        <?
        echo $cdb->get_paging($pageCount, (int)$p, "departman_arama", $ORDERBY);
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
