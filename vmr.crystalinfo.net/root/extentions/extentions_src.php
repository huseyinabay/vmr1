<?
   require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
   $cUtility = new Utility();
   $cdb = new db_layer();
   require_valid_login();

   if ($p=="" || $p < 1)
       $p = 1;
   
   
   $page_size ="10000";

//Site Admin veya Admin Hakkı yokda bu tanımı yapamamalı
   if (!right_get("SITE_ADMIN") && !right_get("ADMIN")){
        print_error("Bu sayfaya erişim hakkınız yok!");
    exit;
   }
//Site Admin Hakkı Yoksa sadece kendisine bağlı kayıtları görsün   
     if (right_get("ADMIN") && !right_get("SITE_ADMIN")){
         $SITE_ID = $_SESSION['site_id'];
     }

    $start = $cUtility->myMicrotime();
     cc_page_meta();
     echo "<center>";
     page_header();
    fillSecondCombo();
     echo "<br><br>";
     table_header_mavi("Dahili Arama","60%");

         //csv import
         if(isset($_POST['import'])){
            if ($_POST['veriSil'] == 'veriSil') {
                mysqli_query($conn, "DELETE FROM EXTENTIONS");
                echo "e";
            }
            $fileName = $_FILES['file']['tmp_name'];
            if ($_FILES['file']['size'] > 0) {
                $file = fopen($fileName, "r");
    
                while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {
                    $sqlInsert = "INSERT INTO EXTENTIONS (SITE_ID, EXT_NO, EMAIL, DEPT_ID, DESCRIPTION, RESIDE_IN_EXTEN, SICIL_NO) values (".$column[0].", '".$column[1]."', '".$column[2]."', ".$column[3].", '".$column[4]."', ".$column[5].", '".$column[6]."') ";
                    mysqli_query($conn, $sqlInsert);
                }
            }
        }
?>
<script>
function submit_form() {
   if(check_form(document.extention_arama)){
    document.all("SITE_ID").disabled=false;
  document.extention_arama.submit();
   }
}
</script>
<center>
       <form name="extention_arama" method="post" onsubmit="return check_form(this);" action="extentions_src.php?act=src">
         <input type="hidden" name="p" VALUE="<?echo (int)$p?>">
         <input type="hidden" name="ORDERBY" value="<?=$ORDERBY?>">
         <table cellpadding="0" cellspacing="0" border="0" width="60%">
            <tr class="form">
                <td class="font_beyaz">Site Adı</td>
                <td>
                    <select name="SITE_ID" class="select1" style="width:250" <?if (!right_get("SITE_ADMIN")) {echo "disabled";}?> onchange="FillSecondCombo('DEPT_ID', 'DEPT_NAME', '01SITE_ID='+ this.value , '' , 'DEPT_ID' , this.value)">
                    <?
                        $strSQL = "SELECT SITE_ID, SITE_NAME FROM SITES ";
                        echo $cUtility->FillComboValuesWSQL($conn, $strSQL, true, $SITE_ID);
                    ?>
                    </select>
                   </td>
            </tr>
            <tr> 
                <td width="50%" class="font_beyaz">Dahili No</td>
                <td width="50%"><input type="text" class="input1" name="EXT_NO" VALUE="<?echo $EXT_NO?>" size="10" Maxlength="10"></td>
            </tr>
            <tr>
          <td width="50%" class="font_beyaz">Departman</td>
		  
		            <td>
                    <select name="DEPT_ID" class="select1" style="width:250" <?if (!right_get("SITE_ADMIN")) {echo "disabled";}?> onchange="FillSecondCombo('DEPT_ID', 'DEPT_NAME', '01SITE_ID='+ this.value , '' , 'DEPT_ID' , this.value);return false;">
                    <?
                        $strSQL = "SELECT DEPT_ID, DEPT_NAME FROM DEPTS ";
                        echo $cUtility->FillComboValuesWSQL($conn, $strSQL, true, $DEPT_ID);
                    ?>
                    </select>
                   </td>
		  
		  <!--
        <td>
                    <select name="DEPT_ID" class="select1" style="width:250">
                        <OPTION value="-1">--Seçiniz--</OPTION>
                    </select>
        </td> 

-->		
             </tr>
            <tr> 
                <td width="50%" class="font_beyaz">Açıklama</td>
                 <td width="50%"><input type="text" class="input1" name="DESCRIPTION" VALUE="<?echo $DESCRIPTION?>" Maxlength="30"></td>
             </tr>
            <tr>
                 <td colspan=2 align=center><br>
                    <a href="javascript:submit_form('extention_arama');"><img name="Image631" border="0" src="<?=IMAGE_ROOT?>ara.gif"></a>
                </td>
            </tr>
            </tr>
        </table>
       </form>
  <table width="100%"> 
    <tr>
      <td width="100%" align="right">
      <a href="extentions.php?act=new"><img border="0" src="<?=IMAGE_ROOT?>yeni_kayit1.gif" style="cursor:hand;"></a>
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
         $kriter .= $cdb->field_query($kriter, "EXTENTIONS.EXT_NO", "=", "'$EXT_NO'");
         $kriter .= $cdb->field_query($kriter, "EXTENTIONS.DEPT_ID", "=", "$DEPT_ID");
         $kriter .= $cdb->field_query($kriter, "EXTENTIONS.DESCRIPTION", " LIKE ", "'%$DESCRIPTION%'");
      
         $sql_str  = "SELECT EXTENTIONS.EXT_ID,
                             EXTENTIONS.EXT_NO,EXTENTIONS.DEPT_ID,
                             EXTENTIONS.DESCRIPTION,
                             DEPTS.DEPT_NAME,SITES.SITE_NAME
                      FROM EXTENTIONS
                      INNER JOIN SITES ON EXTENTIONS.SITE_ID = SITES.SITE_ID                      
                      LEFT JOIN DEPTS  ON DEPTS.DEPT_ID = EXTENTIONS.DEPT_ID
                      ";
         
         if ($kriter != "")
               $sql_str .= " WHERE ". $kriter;  
       
         if ($ORDERBY) {
               $sql_str .= " ORDER BY ". $ORDERBY ;      
         }

         $rs = $cdb->get_Records($sql_str, (int)$p, $page_size,  $pageCount, $recCount);    
		 
		 //echo $page_size; exit;
		 
         $stop = $cUtility->myMicrotime();

?>
<br><br>
<script LANGUAGE="javascript" src="/reports/scripts/jquery-3.5.1.js"></script>
	<script LANGUAGE="javascript" src="/reports/scripts/jquery.dataTables.min.js"></script>
	<script LANGUAGE="javascript" src="/reports/scripts/dataTables.buttons.min.js"></script>
	<script LANGUAGE="javascript" src="/reports/scripts/jszip.min.js"></script>
	<script LANGUAGE="javascript" src="/reports/scripts/pdfmake.min.js"></script>
	<script LANGUAGE="javascript" src="/reports/scripts/vfs_fonts.js"></script>
	<script LANGUAGE="javascript" src="/reports/scripts/buttons.html5.min.js"></script>
	<script LANGUAGE="javascript" src="/reports/scripts/buttons.print.min.js"></script>
	
		<link rel="stylesheet" type="text/css" href="/reports/scripts/jquery.dataTables.min.css" />
		<link rel="stylesheet" type="text/css" href="/reports/scripts/buttons.dataTables.min.css"/>
		 
	<script language="JavaScript">
	//https://datatables.net/extensions/buttons/examples/initialisation/export.html
	//https://datatables.net/reference/option/
	 
  $(document).ready(function() {
    // 03.03.2022 / Stajyer Berk(Wiro)
    function wiroCSV() {
        var array = []
        var tableDepartman;
        var departman;
        var tableSite;
        var site;
        $("#export > tbody > tr").each(function () {
            var anlikTD = $(this).find('td');
            tableDepartman = anlikTD.eq(3).text()
            tableSite= anlikTD.eq(1).text()
            <?php
                $dResults = mysqli_query($conn, "SELECT * from DEPTS");
                foreach ($dResults as $k) {
                    echo "
                    if(tableDepartman == "; 
                    echo '"' . $k['DEPT_NAME'] . '"';
                    echo ') departman = "'; 
                    echo $k['DEPT_ID'] . '";';
                    
                }

                $sResults = mysqli_query($conn, "SELECT * from SITES");
                foreach ($sResults as $k) {
                    echo "
                    if(tableSite == "; 
                    echo '"' . $k['SITE_NAME'] . '"';
                    echo ') site = "'; 
                    echo $k['SITE_ID'] . '";';
                    
                }
            ?>
            if(tableSite == "YKY IPECS"){
                site = "1";
            } 
            array.push([site, anlikTD.eq(2).text(), "",departman, anlikTD.eq(4).text(), 1, ""]);
    }   );
    let csvContent = "data:text/csv;charset=utf-8," + array.map(e => e.join(",")).join("\n");
    var encodedUri = encodeURI(csvContent);
    window.open(encodedUri);
    }

    $('#export').DataTable( {
		//scrollY: 600,
        //paging: false,
        dom: 'Bfrtip',
		"pageLength": 400,
        buttons: ['copy', 
        {
            extend: 'excelHtml5',
            title: "<? echo $header; ?>"
        },
            'print', {text: "email",
        action: function (e ,dt, node, config){
          location.href = "javascript:mailPage('inbound.html')";
        }}, 
        {text: "CSV",
        action: function (e ,dt, node, config){
          wiroCSV();
        }} ]
		
    } );
	} );
	  
	</script>	

<?
table_arama_header("75%");
?>
<form method="POST" enctype="multipart/form-data">
<input type="file" name="file" accept=".csv">
<input type="checkbox" id="veriSil" name="veriSil" value="veriSil">
<label for="veriSil">Şuan ki tüm kayıtları sil</label>
<button type="submit" name="import">Import</button>
</form>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td class="sonuc" width="50%" height="20"><? echo $cdb->calc_current_page((int)$p, $recCount, $page_size);?></td>
    <td class="sonuc" align="right" width="50%" height="20"><? $cdb->show_time(($stop -$start)); ?></td>
  </tr>
</table>
<table id="export" width="100%" border="0" cellspacing="0" cellpadding="0">
    <thead>
    <tr>
        <th>ID</th>
        <th>Site Adı</th>
        <th>Dahili No</th>
        <th>Departman</th>
        <th>Açıklama</th>
        <th>Güncelle</th>
    </tr>
    </thead>
    <tbody>
<? 
   $i;
   while($row = mysqli_fetch_object($rs)){
      $i++;
       echo " <tr>".CR;
      echo " <td >$row->EXT_ID</td> ".CR;
         echo " <td>".substr($row->SITE_NAME,0,25)."</td>".CR;       
        echo " <td>$row->EXT_NO</td>".CR;
        echo " <td>".substr($row->DEPT_NAME,0,25)."</td>".CR;
       echo " <td>".substr($row->DESCRIPTION,0,25)."</td>".CR;
       echo " <td><a HREF=\"extentions.php?act=upd&id=$row->EXT_ID\">Güncelle</td>".CR;
         echo "</tr>".CR;
   }
?>
</tbody>
</table>
<?table_arama_footer();   }?>
<?page_footer(0);?>

<script language="javascript" src="/scripts/form_validate.js"></script>
<script language="JavaScript" type="text/javascript">
<!--
    FillSecondCombo('DEPT_ID', 'DEPT_NAME', '01SITE_ID='+ '<?=$SITE_ID?>' , '<?=$DEPT_ID?>' , 'DEPT_ID' , '<?= $DEPT_ID?>')

    function submit_form(form_name, page, sortby){
          document.all("ORDERBY").value = sortby;
          if (!sortby)
                document.all("ORDERBY").value = '';

          document.all("p").value = page;
          document.all(form_name).submit();
    }
//-->
</script>
