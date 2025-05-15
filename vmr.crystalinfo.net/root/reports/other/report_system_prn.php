<?
     require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
     $cUtility = new Utility();
     $cdb = new db_layer();
     require_valid_login();
  if (right_get("SITE_ADMIN")){
       //Site admin hakkı varsa herşeyi görebilir.  
  //Site id gelmemişse kişinin bulunduğu site raporu alınır.
    if(!$SITE_ID){$SITE_ID = $_SESSION['site_id'];}
  }elseif(right_get("ADMIN") || right_get("ALL_REPORT")){
  // Admin vaye ALL_REPORT hakkı varsa kendi sitesindeki herşeyi görebilir.
    $SITE_ID = $_SESSION['site_id'];
  }else{
        print_error("Bu sayfayı Görme Hakkınız Yok!!!");
    exit;
  } 

   $conn = $cdb->getConnection();
?>
  <form name="sort_me" method="post" action="">
    <input type="hidden" name="type" value="<?=$type?>">
        <input type="hidden" name="SITE_ID" value="<?=$SITE_ID?>">    
        <input type="hidden" name="sort_type" value="<?=($sort_type=="asc")?"desc":"asc"?>">  
  </form>
<?
  $local_country_code = get_country_code($SITE_ID);
  switch ($type){
       case 'ext':
      $sql_str = "SELECT EXTENTIONS.EXT_NO, DEPTS.DEPT_NAME, EXTENTIONS.DESCRIPTION 
                    FROM EXTENTIONS LEFT JOIN DEPTS ON EXTENTIONS.DEPT_ID = DEPTS.DEPT_ID 
                  WHERE EXTENTIONS.SITE_ID = ".$SITE_ID; 
      $header="Dahili Listesi";$head_width="60%";
      $field1="EXT_NO";$field1_name="Dahili";$width1="20%";$field1_ord="EXT_NO";
      $field2="DEPT_NAME";$field2_name="Departman";$width2="40%";$field2_ord="DEPT_NAME";
      $field3="DESCRIPTION";$field3_name="Açıklama";$width3="40%";$field3_ord="DESCRIPTION";
      break;  
      case 'user':
      $sql_str = "SELECT USERS.NAME,USERS.SURNAME,DEPTS.DEPT_NAME 
                  FROM USERS LEFT JOIN DEPTS ON USERS.DEPT_ID = DEPTS.DEPT_ID 
                  WHERE USERS.SITE_ID = ".$SITE_ID; 
      $header="Kullanıcı Listesi";$head_width="60%";
      $field1="NAME";$field1_name="Adı";$width1="30%";$field1_ord="NAME";
      $field2="SURNAME";$field2_name="Soyadı";$width2="30%";$field2_ord="SURNAME";
      $field3="DEPT_NAME";$field3_name="Departman";$width3="40%";$field3_ord="DEPT_NAME";
      break;
    case 'dept':
      $sql_str = "SELECT DEPT_NAME FROM DEPTS WHERE DEPTS.SITE_ID = ".$SITE_ID; 
      $header="Departman Listesi";$head_width="35%";
      $field1="DEPT_NAME";$field1_name="Departman Adı";$width1="80%";$field1_ord="DEPT_NAME";
      break;
    case 'addr':
      $sql_str = "SELECT NAME, SURNAME,COMPANY,POSITION FROM CONTACTS WHERE IS_GLOBAL=1 AND CONTACTS.SITE_ID = ".$SITE_ID;
      $header="Global Fihrist";$head_width="60%";
      $field1="NAME";$field1_name="Adı";$width1="20%";$field1_ord="NAME";
      $field2="SURNAME";$field2_name="Soyadı";$width2="20%";$field2_ord="SURNAME";
      $field3="COMPANY";$field3_name="Firma";$width3="30%";$field3_ord="COMPANY";
      break;
    case 'trunk':
      $sql_str = "SELECT TRUNKS.MEMBER_NO AS TRUNK, TRUNKS.TRUNK_NAME, 
                    TTelProvider.TelProvider, TRUNKS.PHONE_NUMBER 
                  FROM TRUNKS 
                  LEFT JOIN TTelProvider ON TRUNKS.TEL_PROVIDER_ID = TTelProvider.TelProvider
                  WHERE TRUNKS.SITE_ID = ".$SITE_ID; 
      $header="Hat Listesi";$head_width="70%";
      $field1="TRUNK";$field1_name="Hat";$width1="10%";$field1_ord="TRUNK";
      $field2="TRUNK_NAME";$field2_name="Hat Adı";$width2="25%";$field2_ord="TRUNK_NAME";
      $field3="TelProvider";$field3_name="Şebeke";$width3="20%";$field3_ord="TelProvider";
      $field4="PHONE_NUMBER";$field4_name="Telefon No";$width4="20%";$field4_ord="PHONE_NUMBER";
      break;
    case 'city':
      $sql_str = "SELECT LocalCode, LocationName FROM TLocation WHERE LocationTypeid = 1 "; 
      $header="Şehir Kodları";$head_width="40%";
      $field1="LocalCode";$field1_name="Şehir Kodu";$width1="30%";$field1_ord="LocalCode";
      $field2="LocationName";$field2_name="Şehir";$width2="70%";$field2_ord="LocationName";
      break;
    case 'country':
      $sql_str = "SELECT CountryCode, LocationName FROM TLocation WHERE LocationTypeid = 3"; 
      $header="Ülke Kodları";$head_width="40%";
      $field1="CountryCode";$field1_name="Ülke Kodu";$width1="30%";$field1_ord="CountryCode";
      $field2="LocationName";$field2_name="Ülke";$width2="70%";$field2_ord="LocationName";
      break;
    case 'special':
      $sql_str = "SELECT LocalCode, LocationName FROM TLocation WHERE LocationTypeid = 7"; 
      $header="Özel Numaralar";$head_width="40%";
      $field1="LocalCode";$field1_name="Kodu";$width1="30%";$field1_ord="LocalCode";
      $field2="LocationName";$field2_name="Adı";$width2="70%";$field2_ord="LocationName";
      break;
    default:  
     }

    switch ($order){
    case '1':
      $sql_str .= " ORDER BY ".$field1_ord." ".$sort_type; 
      break;
    case '2':
      $sql_str .= " ORDER BY ".$field2_ord." ".$sort_type; 
      break;
    case '3':
      $sql_str .= " ORDER BY ".$field3_ord." ".$sort_type; 
      break;
    case '4':
      $sql_str .= " ORDER BY ".$field4_ord." ".$sort_type; 
      break;
    case '5':
      $sql_str .= " ORDER BY ".$field5_ord." ".$sort_type; 
      break;
    default:
         }
     
   if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
        print_error($error_msg);
        exit;
     }
    
     if (!($cdb->execute_sql("SELECT * FROM PHONES",$resultPhones,$error_msg))){
      print_error($error_msg);
      exit;
   }

  

?>
<html>
<body>
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
	 
  function mailPage(page){
      var keyword = prompt("Lütfen bir mail adresi giriniz.", "")
      if(CheckEmail(keyword)){
          var pagename = "/reports/htmlmail.php?page=/temp/"+page+  "&email="+ keyword;
          this.location.href = pagename;
      }    
   }   

   $(document).ready(function() {
    $('#export').DataTable( {
		//scrollY: 600,
        //paging: false,
        dom: 'Bfrtip',
		"pageLength": 100,
        buttons: ['copy', 
        {
            extend: 'excelHtml5',
            title: "<? echo $header; ?>"
        },
        {
            extend: 'csvHtml5',
            title: "<? echo $header; ?>"
        }, 
            'print', {text: "email",
        action: function (e ,dt, node, config){
          location.href = "javascript:mailPage('inbound.html')";
        }} ]
		
    } );
	} );
	  
	</script>	


<table id="export" class="display nowrap" style="width:100%">
      <thead>
       <tr>
      <th><?echo $field1_name;?></th>
      <?if ($field2_name <> ''){?>
        <th><?echo $field2_name;?></th>
      <?}?>
      <?if ($field3_name <> ''){?>
        <th><?echo $field3_name;?></th>
        <?}?>
      <?if ($field4_name <> ''){?>
        <th><?echo $field4_name;?></th>
        <?}?>
      <?if ($field5_name <> ''){?>
        <th><?echo $field5_name;?></th>
        <?}?>
        <?php 
          if ($_GET['type'] == "addr") {
            echo "<th>Telefon 1</th><th>Telefon 2</th><th>Telefon 3</th>";
          }
        ?>
    </tr>
    </thead>
    <tbody>
      <?while($row = mysqli_fetch_array($result)){
      $i++;
      echo " <tr>";
      echo " <td>".$row["$field1"]."</td>";
      echo " <td>".$row["$field2"]."</td>";
        if($field3_name <> ''){  
          echo " <td>".$row["$field3"]."</td>";
        }
        if($field4_name <> ''){  
          echo " <td>".$row["$field4"]."</td>";  
        }
        if($field5_name <> ''){      
          echo " <td>".$row["$field5"]."</td>";  
        }
        if ($_GET['type'] == "addr") {
          $telControl = 0;
          while($row2 = mysqli_fetch_array($resultPhones)){
            if($row2['CONTACT_ID'] == $row['CONTACT_ID'] || $wControl != 3){
              echo "<td>" . $row2['COUNTRY_CODE'] . "-" .  $row2['CITY_CODE']. "-" .$row2['PHONE_NUMBER'] ."</td>";
              $wControl++;
            }
          }
        }
    }?>
    </tbody>
    <tfoot>
             <tr>
      <th><?echo $field1_name;?></th>
      <?if ($field2_name <> ''){?>
        <th><?echo $field2_name;?></th>
      <?}?>
      <?if ($field3_name <> ''){?>
        <th><?echo $field3_name;?></th>
        <?}?>
      <?if ($field4_name <> ''){?>
        <th><?echo $field4_name;?></th>
        <?}?>
      <?if ($field5_name <> ''){?>
        <th><?echo $field5_name;?></th>
        <?}?>
        <?php 
          if ($_GET['type'] == "addr") {
            echo "<th>Telefon 1</th><th>Telefon 2</th><th>Telefon 3</th>";
          }
        ?>
    </tr>
    </tfoot>
   </table>
 <?table_footer();?>
<script language="JavaScript">
  function submit_form(sortby){
    document.all('sort_me').action='report_system_prn.php?type=<?=$type?>&order=' + sortby;    
    document.all('sort_me').submit();
  }
</script>
</script>

</body>
</html>   