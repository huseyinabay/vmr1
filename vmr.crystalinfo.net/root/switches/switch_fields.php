<?
   require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
   $cUtility = new Utility();
   $cdb = new db_layer();
   require_valid_login();
   $conn = $cdb->getConnection();

//Site Admin değilse bu tanımı yapamamalı
   if (!right_get("SITE_ADMIN")){
        print_error("Bu sayfaya erişim hakkınız yok!");
    exit;
   }

   if ($act=="upd" && $id!=="" && is_numeric($id)){
       $sql_str = "SELECT * FROM SANT_ALANLARI WHERE ID = $id";
       if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
          print_error($error_msg);
          exit;
       }
    if (mysqli_num_rows($result)>0){
         $row = mysqli_fetch_object($result);
    }else{

	header('Location: ' . $_SERVER['HTTP_REFERER']);  // bir önceki sayfaya git, halil

	$url1=$_SERVER['REQUEST_URI'];   
    header("Refresh: 5; URL=$url1");    // sayfayayı refresh et, halil
          exit;    
    }
   }

   if (empty($sw_id)){
        $sw_id = -1;
   }
   if (empty($log_id)){
        $log_id = -1;
   }

  cc_page_meta();
     echo "<center>";
     page_header();
    fillSecondCombo();
     echo "<center><br>";
     table_header("Alan Tanımları","50%");
?>
<script>function submit_form() {
    if (check_form(document.log_types))
        document.log_types.submit();
  }
    function change_santral(sw_id){
        window.location.href = 'switch_fields.php?act=new&sw_id='+sw_id
    }    
    function change_log_type(log_id){
        window.location.href = 'switch_fields.php?act=new&sw_id=<?=$sw_id?>&log_id='+log_id
    }    
</script>
  <center>
  <table width="100%" cellpadding="0" cellspacing="0">
    <form name="log_types" method="post" onsubmit="return check_form(this);" action="switch_fields_db.php?act=<? echo  $_GET['act'] ?>">
    <input type="hidden" name="id" value="<?=$id?>">
       <tr class="form">
           <td class="td1_koyu">Santral Adı</td>
           <td colspan=2>
                <select name="sw_id" class="select1" style="width:200" onchange="javascript:change_santral(this.value)">
                <?
                    $strSQL = "SELECT ID, NAME FROM SANTRAL ";
                    echo $cUtility->FillComboValuesWSQL($conn, $strSQL, true,  $sw_id);
                ?>
                </select>
          </td>
    </tr>
       <tr class="form">
           <td class="td1_koyu">Log Tipi Adı</td>
           <td colspan=2>
                <select name="log_id" class="select1" style="width:200" onchange="javascript:change_log_type(this.value)">
                <?
                    $strSQL = "SELECT ID, NAME FROM SANTRAL_LOG_TIPLERI WHERE SANTRAL_ID=".$sw_id;
                    echo $cUtility->FillComboValuesWSQL($conn, $strSQL, true,  $log_id);
                ?>
                </select>
          </td>
    </tr>
       <tr class="form">
           <td class="td1_koyu">Alan Adı</td>
           <td colspan=2>
                <select name="FIELD_ID" class="select1" style="width:190">
                <?
                    $strSQL = "SELECT ID, DESCRIPTION FROM SANT_ALAN_TANIMLARI ";
                    echo $cUtility->FillComboValuesWSQL($conn, $strSQL, true,  $row->FIELD_ID);
                ?>
                </select>
          </td>
    </tr>
       <tr class="form">
           <td class="td1_koyu">Satır No :
                <input type="text" name="ROW_NUM" class="input1" size="5" value="<?=$row->ROW_NUM?>"></td>
                <td class="td1_koyu">Başlangıç :
                <input type="text" name="START" class="input1" size="5" value="<?=$row->START?>"></td>
                <td class="td1_koyu">Uzunluk :
                <input type="text" name="LENGTH" class="input1" size="5" value="<?=$row->LENGTH?>"></td>
          </td>
    </tr>
    <tr height="15">
      <td></td>
    </tr>
       <tr>
        <td></td>
      <td><img border="0" src="<?=IMAGE_ROOT?>kaydet.gif" style="cursor:hand;" onclick="javascript:submit_form()"></td>
        </tr>
  </table><br>
  <table align="right" width="100%" border="0">
    <tr>
      <td colspan="4" width="40%"></td>
      <td  width="30%"><a href="switch_fields.php?act=new&log_id=<?=$log_id?>&sw_id=<?=$sw_id?>"><img border="0" src="<?=IMAGE_ROOT?>yeni_kayit.gif" style="cursor:hand;"></a></td>
      <td align="right"><?if($sw_id>0 && $log_id>0 && $id>0){?>
            <a HREF="switch_fields_db.php?act=del&log_id=<?=$log_id?>&sw_id=<?=$sw_id?>&FIELD_ID=<?=$row->FIELD_ID?>"><img border="0" src="<?=IMAGE_ROOT?>kayit_sil.gif" style="cursor:hand;"></a><?}?></td>
        </tr>
  </table>

  </form>
<?table_footer();


      
         $sql_str  = "SELECT SANT_ALANLARI.ID AS ID, SANT_ALAN_TANIMLARI.DESCRIPTION AS DESCRIPTION,
                        SANT_ALANLARI.ROW_NUM AS ROW_NUM, SANT_ALANLARI.START AS START,
                        SANT_ALANLARI.LENGTH AS LENGTH
                            FROM SANT_ALANLARI
                                LEFT JOIN SANT_ALAN_TANIMLARI ON SANT_ALANLARI.FIELD_ID = SANT_ALAN_TANIMLARI.ID
                                    WHERE SANT_ALANLARI.LOG_TYPE_ID='$log_id' ORDER BY ID ";

       if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
          print_error($error_msg);
          exit;
       }
?>
<br><br>
<?
table_arama_header("85%");
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td class="sonuc" width="50%" height="20"><? echo $cdb->calc_current_page($p, $recCount, $page_size);?></td>
    <td class="sonuc" align="right" width="50%" height="20"><? $cdb->show_time(($stop -$start)); ?></td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr class="header_beyaz">
        <td>ID</td>
        <td>Alan Adı</td>       
        <td>Satır No</td>
        <td>Başlangıç</td>
        <td>Uzunluk</td>
        <td>Güncelle</td>
    </tr>
<? 
   $i;
   if(mysqli_num_rows($result)>0){
       while($row = mysqli_fetch_object($result)){
          $i++;
           echo " <tr class=\"".bgc($i)."\">".CR;
          echo " <td height=\"20\">$row->ID</td> ".CR;
             echo " <td>".substr($row->DESCRIPTION,0,25)."</td>".CR;
           echo " <td>".$row->ROW_NUM."</td>".CR;
           echo " <td>".$row->START."</td>".CR;
           echo " <td>".$row->LENGTH."</td>".CR;
           echo " <td><a HREF=\"switch_fields.php?act=upd&log_id=$log_id&id=$row->ID&sw_id=$sw_id\">Güncelle</a></td>".CR;
             echo "</tr>".CR;
           list_line(18);
       }
    }
?>
</table>
<?table_arama_footer();   
page_footer(0);?>  

<script language="javascript" src="/scripts/form_validate.js"></script>
<script language="javascript">
  form_fields[0] = Array ("ROW_NUM",   "Satır Numarası Sayısal Olmalı.", TYP_DIGIT);
  form_fields[1] = Array ("START",   "Başlangıç Sayısal Olmalı.", TYP_DIGIT);
  form_fields[2] = Array ("LENGTH",   "Uzunluk Sayısal Olmalı.", TYP_DIGIT);
    form_fields[3] = Array ("log_id", "Log Tipini seçmeniz gerekli.", TYP_DROPDOWN);
    form_fields[4] = Array ("FIELD_ID", "Alan Adını seçmeniz gerekli.", TYP_DROPDOWN);
  form_fields[5] = Array ("ROW_NUM",   "Satır Numarasını girmeniz gerekli.", TYP_NOT_NULL);
  form_fields[6] = Array ("START",   "Başlangıcı girmeniz gerekli.", TYP_NOT_NULL);
  form_fields[7] = Array ("LENGTH",   "Uzunluğu girmeniz gerekli.", TYP_NOT_NULL);
</script>
