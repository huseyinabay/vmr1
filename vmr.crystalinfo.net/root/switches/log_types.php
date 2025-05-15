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
       $sql_str = "SELECT * FROM SANTRAL_LOG_TIPLERI WHERE ID = '$id' GROUP BY ID ";
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

   if (empty($sw_id)){
        $sw_id = -1;
   }
     
  cc_page_meta();
     echo "<center>";
     page_header();
    fillSecondCombo();
     echo "<center><br>";
     table_header("Santral Log Tipleri","50%");
?>
<script>function submit_form() {
    if (check_form(document.log_types))
        document.log_types.submit();
  }
    function change_santral(sw_id){
        window.location.href = 'log_types.php?act=new&sw_id='+sw_id
    }    
</script>
  <center>
  <table width="100%" cellpadding="0" cellspacing="0">
    <form name="log_types" method="post" onsubmit="return check_form(this);" action="log_types_db.php?act=<? echo  $_GET['act'] ?>">
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
           <td class="td1_koyu">Log Tipi Adı :</td>
           <td colspan=2>
                <input type="text" name="LOG_NAME" class="input1" size="25" value="<?=$row->NAME?>">
          </td>
    </tr>
       <tr class="form">
           <td class="td1_koyu">Çağrı Tipi</td>
           <td>
                <select name="CALL_TYPE" class="select1" style="width:100">
                <?
                    $strSQL = "SELECT ID, NAME FROM CALL_TYPES ";
                    echo $cUtility->FillComboValuesWSQL($conn, $strSQL, true,  $row->CALL_TYPE);
                ?>
                </select>
          </td>
           <td class="td1_koyu">Satır No :
                <input type="text" name="ROW_NUM" class="input1" size="5" value="<?=$row->ROW_NUM?>"></td>
    </tr>
       <tr class="form">
          <td class="td1_koyu" colspan=2>1. Kriter</td>
        </tr>
       <tr class="form">
                <td class="td1_koyu">Başlangıç :</td><td class="td1_koyu">
                <input type="text" name="START" class="input1" size="5" value="<?=$row->START?>"></td>
                <td class="td1_koyu">Uzunluk :
                <input type="text" name="LENGTH" class="input1" size="5" value="<?=$row->LENGTH?>"></td>
          </td>
    </tr>
       <tr class="form">
           <td class="td1_koyu">Değer :</td><td class="td1_koyu" colspan=2>
                <input type="text" name="VALUE" class="input1" size="25" value="<?=$row->VALUE?>">
            </td>
    </tr>
       <tr class="form">
          <td class="td1_koyu" colspan=2>2. Kriter</td>
        </tr>
       <tr class="form">
                <td class="td1_koyu">Başlangıç :</td><td class="td1_koyu">
                <input type="text" name="START1" class="input1" size="5" value="<?=$row->START1?>"></td>
                <td class="td1_koyu">Uzunluk :
                <input type="text" name="LENGTH1" class="input1" size="5" value="<?=$row->LENGTH1?>"></td>
          </td>
    </tr>
       <tr class="form">
           <td class="td1_koyu">Değer :</td><td class="td1_koyu" colspan=2>
                <input type="text" name="VALUE1" class="input1" size="25" value="<?=$row->VALUE1?>">
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
      <td colspan="5" width="70%"></td>
      <td align="right"><a href="log_types.php?act=new&sw_id=<?=$sw_id?>"><img border="0" src="<?=IMAGE_ROOT?>yeni_kayit.gif" style="cursor:hand;"></a></td>
    </tr>
  </table>

  </form>
<?table_footer();


      
         $sql_str  = "SELECT SANTRAL_LOG_TIPLERI.ID AS ID, SANTRAL_LOG_TIPLERI.NAME AS NAME, SANTRAL_LOG_TIPLERI.ROW_NUM AS ROW_NUM,
                        SANTRAL_LOG_TIPLERI.START AS START, SANTRAL_LOG_TIPLERI.LENGTH AS LENGTH, SANTRAL_LOG_TIPLERI.VALUE AS VALUE, 
                        CALL_TYPES.NAME AS CALL_TYPE, SANTRAL_LOG_TIPLERI.CRITERIA AS CRITERIA, START1, LENGTH1, VALUE1
                            FROM SANTRAL_LOG_TIPLERI
                                LEFT JOIN CALL_TYPES ON SANTRAL_LOG_TIPLERI.CALL_TYPE = CALL_TYPES.ID
                                WHERE SANTRAL_LOG_TIPLERI.SANTRAL_ID='$sw_id' GROUP BY ID ORDER BY ID";

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
        <td width="5%">ID</td>
        <td width="15%">Adı</td>       
        <td width="15%">Çağrı Tipi</td>
        <td width="10%">Satır No</td>
        <td width="40%">Kriterler</td>
        <td width="15%">Alan Tanımları</td>
    </tr>
<? 
   $i;
   if(mysqli_num_rows($result)>0){
       while($row = mysqli_fetch_object($result)){
          $i++;
           echo " <tr class=\"".bgc($i)."\">".CR;
          echo " <td height=\"20\">$row->ID</td> ".CR;
             echo " <td NOWRAP><a HREF=\"log_types.php?act=upd&id=$row->ID&sw_id=$sw_id\">".substr($row->NAME,0,25)."</a></td>".CR;
           echo " <td>".$row->CALL_TYPE."</td>".CR;
           echo " <td>".$row->ROW_NUM."</td>".CR;

            echo "<td><table border=1 width=\"100%\" cellspacing=0 cellpadding=0>".CR;
           echo " <tr><td width='30%'>1. Kriter</td>".CR;
           echo " <td width='10%'>".$row->START."</td>".CR;
           echo " <td width='10%'>".$row->LENGTH."</td>".CR;
           echo " <td width='50%'>".$row->VALUE."</td></tr>".CR;
           echo " <tr><td>2. Kriter</td>".CR;
           echo " <td>".$row->START1."</td>".CR;
           echo " <td>".$row->LENGTH1."</td>".CR;
           echo " <td>".$row->VALUE1."</td></tr>".CR;
            echo "</table></td>".CR; 
           echo " <td><a HREF=\"switch_fields.php?act=new&log_id=$row->ID&sw_id=$sw_id\">Alan Tanımları</td>".CR;
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
  form_fields[0] = Array ("LOG_NAME", "Log Tipi Adını girmeniz gerekli.", TYP_NOT_NULL);
  form_fields[1] = Array ("ROW_NUM",   "Satır Numarası Sayısal Olmalı.", TYP_DIGIT);
  form_fields[2] = Array ("START",   "Başlangıç Sayısal Olmalı.", TYP_DIGIT);
  form_fields[3] = Array ("LENGTH",   "Uzunluk Sayısal Olmalı.", TYP_DIGIT);
    form_fields[4] = Array ("CALL_TYPE", "Çağrı Tipini seçmeniz gerekli.", TYP_DROPDOWN);
    form_fields[5] = Array ("ROW_NUM", "Satır Numarasını girmeniz gerekli.", TYP_NOT_NULL);
  form_fields[6] = Array ("LENGTH1",   "Uzunluğu girmeniz gerekli.", TYP_NOT_NULL);
  form_fields[7] = Array ("START",   "Başlangıcı girmeniz gerekli.", TYP_NOT_NULL);
  form_fields[8] = Array ("LENGTH",   "Uzunluğu girmeniz gerekli.", TYP_NOT_NULL);
  form_fields[9] = Array ("START1",   "Başlangıcı girmeniz gerekli.", TYP_NOT_NULL);
</script>
