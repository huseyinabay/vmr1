<?
     require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
     $cUtility = new Utility();
     $cdb = new db_layer();
     session_cache_limiter('nocache');     
     require_valid_login();
    //Hak Kontrol
    if (right_get("SITE_ADMIN")){
        //Site admin hakk varsa hereyi g�ebilir.  
      if(!$SITE_ID){$SITE_ID = $_SESSION['site_id'];}
      $site_crt="SITE_ID =".$SITE_ID;
      $dept_crt = " WHERE 1=1";
    }elseif(right_get("ADMIN") || right_get("ALL_REPORT")){
    // Admin vaye ALL_REPORT hakk varsa kendi sitesindeki hereyi g�ebilir.
      $SITE_ID = $_SESSION['site_id'];
      $site_crt="SITE_ID =".$SITE_ID;
      $dept_crt = " WHERE 1=1";
    }elseif(got_dept_right($_SESSION["user_id"])==1){
    //Bir departmann raporunu g�ebiliyorsa kendi sitesindekileri girebilir.
      $SITE_ID = $_SESSION['site_id'];
      $site_crt="SITE_ID =".$SITE_ID;
      $dept_id_string = get_dept_list($_SESSION["user_id"]);
      $dept_crt = " WHERE DEPT_ID IN($dept_id_string)";
    }else{
            print_error("Bu sayfay G�me Hakknz Yok!!!");
      exit;
    } 
  //Hak kontrol sonu  
   $kriter = $dept_crt." AND ".$site_crt;
   $start = $cUtility->myMicrotime();

  cc_page_meta();
  fillsecondcombo();
  echo "<center>";
  page_header();
?>
<script LANGUAGE="javascript" src="/scripts/popup.js"></script>
<form name="monthly_analyse" method="post"
      action="monthly_analyse_show.php">
<table cellpadding="0" cellspacing="0" align="center" border="0" width="98%">
  <tr height="8"><td colspan="3"></td></tr>
  <tr>
    <td colspan="3">
      <table cellpadding="0" cellspacing="0" align="center" border="0" width="100%">
      <tr>  
         <td width="35%" align="right" class="td1_koyu">Site Ad</td>
           <td width="5%"></td>
             <td>
                 <select name="SITE_ID" class="select1" style="width:200" <?if (!right_get("SITE_ADMIN")) {echo "disabled";}?>  onchange="FillSecondCombo('DEPT_ID', 'DEPT_NAME', '01SITE_ID='+ this.value , '' , 'DEPT_ID' , this.value)">
                     <?
                         $strSQL = "SELECT SITE_ID, SITE_NAME FROM SITES ORDER BY SITE_NAME";
                         echo $cUtility->FillComboValuesWSQL($conn, $strSQL, false,  $SITE_ID);
                     ?>
                 </select>
             </td>
      </tr>
     </table>
     </td>
     </tr>
  <tr height="8"><td colspan="3"></td></tr>   
  <tr>
    <input type="hidden" name="myrep_type" value="">
    <td width="50%">
    <?table_header("Aylik Analizler","100%");?>
    <center>
    <table cellpadding="10" cellspacing="0"
            align="center"border="0"width="100%">
      <tr height="40"> 
        <td width="40%" ><b>Rapor Turu</b></td> 
      </tr>
     </table>
     <table cellpadding="0" cellspacing="0"
            align="center"border="0"width="100%">
      <tr>
        <td width="10%"><input type="radio" id="month_rep" class="inputrpt"
            name="report_type"
            value="month" onclick="javascript:show_last_date(false)"></td>
        <td width="90%" style="font-size:16px"class="td1_koyu">Belli Bir Donem
Raporu</td>
      </tr>
      <tr>
        <td width="10%"><input type="radio" id="time_int_rep" class="inputrpt"
            name="report_type"
            value="time_int" onclick="javascript:show_last_date(true)"></td>
        <td width="90%" style="font-size:16px"class="td1_koyu">Zaman Araligi
Raporu</td>
      </tr>
     </table>
     <table cellpadding="10" cellspacing="0"
            align="center"border="0"width="100%">
      <tr height="40"> 
        <td width="40%" ><b>Raporu Istenen</b></td> 
      </tr>
     </table>
     <table cellpadding="0" cellspacing="0"
            align="center"border="0"width="100%">
      <tr>
        <td width="100%" class="td1_koyu" colspan=2>
          <input id="ext_id" type="radio" class="input1" name="analysis_type" 
                 value="ext" onclick="javascript:show_inputs(1);">Dahili</td>
        <td id="my_dahili_row" style="display:none" class="td1_koyu"
              align="left">
           <input type="text" id="my_dahili" name="my_dahili"
                  style="width:250;">
         <td>
      </tr>
      
      <tr> 
        <td width="100%" class="td1_koyu" colspan=2><input id="dept_id"
            type="radio"
            name="analysis_type" value="dept"
            onclick="javascript:show_inputs(2);">Departman</td>
        <td id="dept_select" style="display:none"width="45%" class="td1_koyu"
             colspan=2>
          <select name="DEPT_ID" class="select1" style="width:250;">
              <?  
                $strSQL = "SELECT DEPT_ID, DEPT_NAME FROM DEPTS "
                          .$kriter. " ORDER BY DEPT_NAME";
                echo $cUtility->FillComboValuesWSQL($conn,$strSQL,
                                                     true,$DEPT_ID);
              ?>
          </select>
        </td>  
      </tr>
      <tr> 
        <td width="100%" class="td1_koyu">
           <input id="trunk_id" type="radio" class="input1"
                 name="analysis_type" value="trunk"
            onclick="javascript:show_inputs(3);">Hat</td>
       <td id ="trunk_row" style="display:none" width="45%" class="td1_koyu"
           colspan=2>
          <select name="hat" class="select1" style="width:250;">
              <?  
                $strSQL = "SELECT TRUNK_ID, TRUNK_NAME FROM TRUNKS "
                          .$kriter. " ORDER BY TRUNK_NAME";
                echo $cUtility->FillComboValuesWSQL($conn,$strSQL,
                                                     true,0);
              ?>
          </select>
        </td>  
      </tr>
      <tr id ="general_row"> 
        <td width="100%" class="td1_koyu">
           <input id="general_id" type="radio" class="input1"
                  name="analysis_type" value="general"
            onclick="javascript:show_inputs(4);">Genel</td>
      </tr>
      <tr height="40"> 
        <td width="40%" ><b>&nbsp Rapor Icerigi</b></td> 
      </tr>
      <tr id ="show_price"> 
        <td width="100%" class="td1_koyu">
           <input id="tutar_radio" type="radio" class="input1"
                  name="show_type" value="price">Tutar</td>
      </tr>
      <tr id ="show_dur_cnt"> 
        <td width="100%" class="td1_koyu">
           <input id="sure_adet_radio" type="radio" class="input1"
                  name="show_type" value="dur_count">Sure-Adet
        </td>
      </tr>
      </table>
    <?table_footer();?>
  </td>
  <td width="2%"></td>
  <td nowrap width="48%" valign="top">
    <?table_header("Donem Kriteri","100%");?>
    <center>
    <table cellspacing="0" cellpadding="0" border="0">
      <tr>
        <td colspan="6" class="td1_koyu">Baslangic Ay ve Yil&nbsp</td>
        <td colspan="">
             <select name="month1" >
               <?for ($i=1;$i<=12;$i++){
                    echo "<option value=\"$i\">".$i."</option>";
                 }
               ?>
             </select>
        </td>
        <td colspan="">
             <select name="year1" >
               <?for ($i= date("Y",time(NULL));$i>=2000;$i--){
                    echo "<option value=\"$i\">".$i."</option>";
                 }
               ?>
             </select>
        </td>
      </tr>
      <tr id="last_date"class="td1_koyu" style="display:none" >
        <td colspan="6" class="td1_koyu" >Bitis Ay ve Yil&nbsp</td>
        <td colspan="">
             <select name="month2" >
               <?for ($i=1;$i<=12;$i++){
                    echo "<option value=\"$i\">".$i."</option>";
                 }
               ?>
             </select>
        </td>
        <td colspan="">
             <select name="year2" >
               <?for ($i= date("Y",time(NULL));$i>=2003;$i--){
                    echo "<option value=\"$i\">".$i."</option>";
                 }
               ?>
             </select>
        </td>
      </tr>
    </table>
    <?table_footer();?>
  </td>
  </tr>
  <tr>
      <td colspan=4 align=center><br>
           <a href="javascript:submit_form();">
             <img name="Image631" border="0"
                  src="<?=IMAGE_ROOT?>raporal.gif"></a>
         </td>
     </tr>      
</table>    
   </form>
   <a href="monthly_analyse_sel.php"><img border="0"
src="<?=IMAGE_ROOT?>kriter_temizle.gif"></a>
<?page_footer(0);?>
<script language="javascript" src="/scripts/form_validate.js"></script>
<script language="JavaScript" type="text/javascript">
<!--
  
  show_last_date();
  function show_last_date(bool){
      
      bool = document.getElementById('time_int_rep').checked;
      my_ref = document.getElementById('last_date');
      my_ref2 = document.getElementById('my_dahili_row');
      my_ref3 = document.getElementById('dept_select');
      my_ref4 = document.getElementById('trunk_row');
      my_ref5 = document.getElementById('general_row');
   
      ref1 = document.getElementById('ext_id');
      ref2 = document.getElementById('dept_id');
      ref3 = document.getElementById('trunk_id');
      ref4 = document.getElementById('general_id');
      if (bool){
         my_ref.style.display = '';
         my_ref2.style.display = 'none';
         my_ref3.style.display = 'none';
         my_ref4.style.display = 'none';
      }
      else{
         my_ref.style.display = 'none';
         if (ref1.checked)
             show_inputs(1);
         else if (ref2.checked)
             show_inputs(2);
         else if (ref3.checked)
             show_inputs(3);
         else if (ref4.checked)
             show_inputs(4);
      }
  }
  function show_inputs(value){
     my_val = "";
     my_val = document.getElementById('month_rep').checked;
     my_ref1 = document.getElementById('my_dahili_row');
     my_ref2 = document.getElementById('dept_select');
     my_ref3 = document.getElementById('trunk_row');
     my_ref4 = document.getElementById('general_row');

     if (my_val){
        if (value == 1){
           my_ref1.style.display = '';
           my_ref2.style.display = 'none';
           my_ref3.style.display = 'none';
        }else if(value == 2){
           my_ref2.style.display = '';
           my_ref1.style.display = 'none';
           my_ref3.style.display = 'none';
        }
        else if (value == 3){
           my_ref3.style.display = '';
           my_ref2.style.display = 'none';
           my_ref1.style.display = 'none';
        }
        else if (value == 4){
           my_ref3.style.display = 'none';
           my_ref2.style.display = 'none';
           my_ref1.style.display = 'none';
        }
     }
  }
 
  function submit_form(){
    document.forms['monthly_analyse'].submit();
  }
</script>

