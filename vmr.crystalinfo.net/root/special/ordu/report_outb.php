﻿<?
     require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
     $cUtility = new Utility();
     $cdb = new db_layer();
     require_valid_login();
     session_cache_limiter('nocache');     
     $conn = $cdb->getConnection();    

    //Hak Kontrolü
    if (right_get("SITE_ADMIN")){
      if(!$SITE_ID){$SITE_ID = $_SESSION['site_id'];}
      $site_crt="SITE_ID =".$SITE_ID;
      $dept_crt = " WHERE 1=1";
        //Site admin hakkı varsa herşeyi görebilir.
    }elseif(right_get("ADMIN") || right_get("ALL_REPORT")){
    // Admin vaye ALL_REPORT hakkı varsa kendi sitesindeki herşeyi görebilir.
      $SITE_ID = $_SESSION['site_id'];
      $site_crt="SITE_ID =".$SITE_ID;
      $dept_crt = " WHERE 1=1";
    }elseif(got_dept_right($_SESSION["user_id"])==1){
    //Bir departmanın raporunu görebiliyorsa kendi sitesindekileri girebilir.
      $SITE_ID = $_SESSION['site_id'];
      $site_crt="SITE_ID =".$SITE_ID;
      $dept_id_string = get_dept_list($_SESSION["user_id"]);
      $dept_crt = " WHERE DEPT_ID IN($dept_id_string)";
    }else{
      $SITE_ID = $_SESSION['site_id'];
      $site_crt="SITE_ID =".$SITE_ID;
      $dept_crt = " WHERE DEPT_ID IN('')";
    } 
    $kriter = $dept_crt." AND ".$site_crt;
    $start = $cUtility->myMicrotime();
	if ($CountryCode ==''){
	  $CountryCode = get_country_code($SITE_ID);
	}

  cc_page_meta();
  fillsecondcombo();
  echo "<center>";
     page_header();
?>
<form name="outb_search" method="post" action="report_outb_prn.php?act=src">
<input type="hidden" name="country_code" value="<?if ($CountryCode ==''){echo get_country_code($SITE_ID);}else{echo $CountryCode;}?>">
<table cellpadding="0" cellspacing="0" align="center" border="0" width="98%">
  <tr height="8"><td colspan="3"></td></tr>
  <tr>
    <td colspan="3">
      <table cellpadding="0" cellspacing="0" align="center" border="0" width="100%">
      <tr>  
         <td width="35%" align="right" class="td1_koyu">Site Adı</td>
           <td width="5%"></td>
             <td>
                 <select name="SITE_ID" class="select1" style="width:200" <?if (!right_get("SITE_ADMIN")) {echo "disabled";}?> onchange="Fillothercombos(this.value)">
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
    <td nowrap width="50%">
    <?table_header("Giden Çağrı Raporu","100%");?>
    <center>
      <table cellpadding="0" cellspacing="0" align="center" border="0" width="100%">
        <tr> 
        <td width="45%" class="td1_koyu">Dahili</td>
               <td width="55%"><input class="input1" type="text" name="ORIG_DN" VALUE="<?echo $ORIG_DN?>" size="30">
                </td>
      </tr>
      <tr> 
               <td width="45%" class="td1_koyu">Departman</td>
               <td width="55%">
          <select name="DEPT_ID" class="select1" style="width:250;" multiple>
                      <?  
                        $strSQL = "SELECT DEPT_ID, DEPT_NAME FROM DEPTS ".$kriter. " ORDER BY DEPT_NAME";
                          echo  $cUtility->FillComboValuesWSQL($conn, $strSQL, true,  $DEPT_ID);
                        ?>
                   </select>
        </td>  
      </tr>
      <tr>
        <td width="45%" class="td1_koyu">Çağrı Tipi</td>
               <td width="55%">
                <select name="LocationTypeid" class="select1">
                      <?
                        $strSQL = "SELECT LocationTypeid, LocationType FROM TLocationType ";
                          echo $cUtility->FillComboValuesWSQL($conn, $strSQL, true,  $LocationTypeid);
                        ?>
                    </select>
               </td>
      </tr>    
      <tr>
        <td width="45%" class="td1_koyu">Aranan Şebeke</td>
               <td width="55%">
                <select name="TelProviderid" class="select1">
                      <?
                        $strSQL = "SELECT TelProviderid, TelProvider FROM TTelProvider";
                        echo $cUtility->FillComboValuesWSQL($conn, $strSQL, true,  $TelProviderid);
                        ?>
                    </select>
               </td>
      <tr>
 <!--       <td width="19%" class="td1_koyu">Hat No</td>
               <td width="30%">
                <select name="TRUNK" class="select1">
                      <?
                        $strSQL = "SELECT MEMBER_NO, CONCAT(TRUNK_NAME,'(',MEMBER_NO,')') FROM TRUNKS WHERE SITE_ID=".$SITE_ID;
                          echo  $cUtility->FillComboValuesWSQL($conn, $strSQL, true,  $TRUNK);
                        ?>
                    </select>
                    <input type=hidden name=SUMM value="">
               </td>  -->
       <td width="19%" class="td1_koyu">Çıkış Şebekesi</td>
               <td width="30%">
                <select name="FROM_PROVIDER_ID" class="select1">
                      <?
                        $strSQL = "SELECT TelProviderid, TelProvider FROM TTelProvider ";
                          echo  $cUtility->FillComboValuesWSQL($conn, $strSQL, true,  $FROM_PROVIDER_ID);
                        ?>
                    </select>
                    <input type=hidden name=SUMM value="">
               </td>  
               
      </tr>
      <tr> 
        <td width="100%" class="td1_koyu" colspan="2">
                   <table width="100%">
                        <tr>
                       <td width="5%" class="td1_koyu">Opt.Kodu</td>
                         <td width="20%"><input class="input1" size="5" type="text" name="LocalCode" VALUE="<?echo $LocalCode?>" Maxlength="5">
                            <a href="#"><span onclick="javascript:popup('/contacts/phone_list.php?type=sehir&country_code=' + document.all('country_code').value,'phone',500,500)">Bak</span></a></td>
                       <td width="25%" class="td1_koyu">Ülke Kodu</td>
                      <td width="50%"><input class="input1" size="5" type="text" name="CountryCode" VALUE="" Maxlength="5" onblur="set_country();">
                            <a href="#"><span onclick="javascript:popup('/contacts/phone_list.php?type=ulke','phone',500,500)">Bak</span></a></td>
                        </tr>
              <tr> 
                <td width="30%" class="td1_koyu">Aranan No</td>
                     <td width="70%" colspan="3"><input class="input1" type="text" name="DIGITS" VALUE="<?echo $DIGITS?>" size="15"></td>
            </tr>
                        <tr>
                       <td class="td1_koyu">Auth. Kod</td>
                          <td><input class="input1" type="text" name="AUTH_CODE" VALUE="<?echo $AUTH_CODE?>" size="5"></td>
                       <td class="td1_koyu">Çıkış Kodu</td>
                        <td><input class="input1" type="text" name="ACCESS_CODE" VALUE="<?echo $ACCESS_CODE?>" size="5"></td>
                        </tr>
                        <tr>
              <td class="td1_koyu">Min. Ücret</td>
                      <td><input class="input1" size="10" type="text" name="PRICE" VALUE="<?if($PRICE==""){echo 0.01;}else{echo $PRICE;}?>" Maxlength="15"></td>
              <td class="td1_koyu">Min. Süre(Dk)</td>
                      <td><input class="input1" size="5" type="text" name="DURATION" VALUE="<?echo $DURATION?>" Maxlength="5"></td>
                        </tr>
                   </table>
               </td>
      </tr>
       <tr> 
        <td width="45%" class="td1_koyu">Fihriste </td>
               <td width="55%">
                <input  type="checkbox" name="IN_FIHRIST" VALUE="1" size="15" onclick="javascript:contact_type_display()">Kayıtlı Olanlar 
                <input  type="checkbox" name="OUT_FIHRIST" VALUE="1" size="15" onclick="javascript:contact_type_display()">Kayıtlı Olmayanlar 
                </td>
      </tr>
       <tr id="cont_type_sel" style="display:none"> 
        <td width="45%" class="td1_koyu">Kontakt Tipi</td>
               <td width="55%">
                <input type="radio" name="CONTACT_TYPE" VALUE="1" size="15">Şirket
                <input type="radio" name="CONTACT_TYPE" VALUE="0" size="15">Özel 
                <input type="radio" name="CONTACT_TYPE" VALUE="2" size="15">Tümü 
                </td>
      </tr>
      <tr> 
       <td colspan=2>
       <table border=0 width="100%" cellspacing=0 cellpadding=0>
        <tr>
               <td  width="29%"class="td1_koyu">Şifreli Çıkışlar</td>
               <td width="16%"><input type="radio" name="AUTH_CODE_CNTL" VALUE="1" size="15">Şifreli</td>
                <td width="25%"><input type="radio" name="AUTH_CODE_CNTL" VALUE="2" size="15">Şifresiz</td>
                <td width="30%"><input type="radio" name="AUTH_CODE_CNTL" VALUE="0" size="15" CHECKED>Tümü</td>
           </tr>
        </table>        
        </td>
        </tr>
       <tr id="" style=""> 
       <td colspan=2>
       <table border=0 width="100%" cellspacing=0 cellpadding=0>
        <tr>
        <td  width="29%" class="td1_koyu">Çıktı Formatı</td>
               <td width="16%">
                <input type="radio" name="CSV_EXPORT" VALUE="0" size="15" CHECKED>Html
                </td>
               <td width="25%">
                <input type="radio" name="CSV_EXPORT" VALUE="1" size="15">Csv Export
                </td>
               <td width="30%">
                <input type="radio" name="CSV_EXPORT" VALUE="2" size="15">Excel Export
                </td>
         </tr>
        </table>        
        </td>
        </tr>
           <tr id=div1> 
               <td width="45%" class="td1_koyu">Ana Tablo</td>
               <td width="55%"><input type="checkbox" name="forceMainTable" VALUE="1" size="15"></td>
           </tr>
      </table>
    <?table_footer();?>
  </td>
  <td width="2%"></td>
  <td nowrap width="48%" valign="top">
    <?table_header("Kriterler","100%");?>
    <center>
    <table cellspacing="0" cellpadding="0" border="0">
      <tr>
        <td colspan="1" class="td1_koyu">Kayıt Adedi</td>
        <td colspan="6"><input type="text" class="input1" size="5" name="record" VALUE="<?echo $record?>"></td>
      </tr>
      <tr>
        <td colspan="1" class="td1_koyu">Son</td>
        <td colspan="6">
          <select name ="last" style="width:45" class="select1" onchange="javascript:set_date1(this.value);" >
            <option value="-1"></option>
            <?for ($i=1;$i<21;$i++){
              echo "<option value=\"$i\">".$i."</option>";
            }?>
          </select> gün
        </td>
      </tr>
      <tr align="left">
        <td colspan="1" align="left" class="td1_koyu">Tarih:&nbsp&nbsp
              <td colspan="6">
          <select name="MY_DATE" style="width:100;" class="select1" onchange="javascript:set_date(this.value);">
            <option  value="-1" selected></option>            
                     <option  value="b">Bugün</option>
            <option  value="f">Dün</option>
            <option  value="h">Bu Hafta</option>
            <option  value="i">Geçen Hafta</option>            
            <option  value="c" selected>Bu Ay</option>
            <option  value="g">Geçen Ay</option>
            <option  value="d">------</option>
            <option  value="e">Tarih Seç</option>
                </select>
             </td>
      </tr>
      <tr height="60">
        <td colspan="7">
          <table>
            <tr id="tarih_bas" style="display:none;"> 
                       <td width="40%" colspan="1" class="td1_koyu" tip="Tarih formatini uygun şekilde giriniz.">Baş.Tarihi:(gg/aa/yyyy)</td>
              <td width="40%" colspan="6">
                  <input type="text" size=17 name="t0" VALUE="<?echo $t0?>"><a href="javascript://"><img align="absmiddle" src="<?=IMAGE_ROOT?>takvim_icon.gif" onclick="javascript:show_calendar(document.all('t0').name,null,null,null,event.screenX,event.screenY,1);" border="0"></a>
              </td>
            </tr>
            <tr id="tarih_bit" style="display:none;">
              <td width="40%" colspan="1" class="td1_koyu">Bit. Tarihi:(gg/aa/yyyy)</td>
              <td width="40%" colspan="6">
                <input type="text" size=17 name="t1" VALUE="<?echo $t1?>"><a href="javascript://"><img align="absmiddle" src="<?=IMAGE_ROOT?>takvim_icon.gif" onclick="javascript:show_calendar(document.all('t1').name,null,null,null,event.screenX,event.screenY,1);" border="0"></a>
              </td>
            </tr>  
          </table>  
        </td>        
      <tr>
        <td colspan="1" class="td1_koyu">Hafta İçi:</td>
        <td colspan="1">
          <input type="radio" name="hafta" value="1">
        </td>  
        <td colspan="2" class="td1_koyu" nowrap>Hafta Sonu</td>
        <td colspan="1">
          <input type="radio" name="hafta" value="2">
        </td>
        <td colspan="1" class="td1_koyu">Tümü</td>
        <td colspan="1">
          <input type="radio" name="hafta" value="3" checked>
        </td>
      </tr>
      <tr>
        <td colspan="1" class="td1_koyu">Mesai İçi:</td>
        <td colspan="1">
          <input type="radio" name="mesai" value="1" onclick="javascript:set_hours(this.value)">
        </td>  
        <td colspan="2" class="td1_koyu">Mesai Dışı</td>
        <td colspan="1">
          <input type="radio" name="mesai" value="2" onclick="javascript:set_hours(this.value)">
        </td>
        <td colspan="1" class="td1_koyu">Tümü</td>
        <td colspan="1">
          <input type="radio" name="mesai" value="3" onclick="javascript:set_hours(this.value)" checked>
        </td>
      </tr>
      <tr>
        <td width="25%" class="td1_koyu">Saat Dilimi:</td>
          <td width="10%">
          <select name ="hh0" style="width:40" class="select1" onchange="javascript:set_my_min('a')">
            <option value="-1"></option>
            <?for ($i=0;$i<24;$i++){
              echo "<option value=\"$i\">".format_time($i)."</option>";
            }?>
          </select>
        </td>
        <td width="10%">  
          <select name ="hm0" style="width:40" class="select1" onchange="javascript:set_my_hour('a')">
            <option value="-1"></option>
            <?for ($i=0;$i<=59;$i++){
              echo "<option value=\"$i\">".format_time($i)."</option>";
            }?>
          </select>
        </td>
        <td width="10%" class="td1_koyu">'dan</td>
          <td width="10%">
          <select name ="hh1" style="width:40" class="select1" onchange="javascript:set_my_min('b')">
            <option value="-1"></option>
            <?for ($i=0;$i<24;$i++){
              echo "<option value=\"$i\">".format_time($i)."</option>";
            }?>
          </select>
        </td>  
        <td width="10%">  
          <select name ="hm1" style="width:40" class="select1" onchange="javascript:set_my_hour('b')">
            <option value="-1"></option>
            <?for ($i=0;$i<=59;$i++){
              echo "<option value=\"$i\">".format_time($i)."</option>";
            }?>
          </select>
        </td>
        <td width="10%" class="td1_koyu">'a</td>      
      </tr>
    </table>
    <?table_footer();?>
    <table>
      <tr>
        <td class="td1_koyu">Not : </td>
      </tr>
      <?if(right_get("ADMIN")){?>      
      <tr>
        <td class="td1_koyu">
                    - Birden fazla dahiliyi aralarına virgül(,) koyarak yazabilirsiniz!!!
        </td>
      </tr>
      <?}?>
      <?if(right_get("ADMIN") || got_dept_right($_SESSION["user_id"]) || right_get("SITE_ADMIN") || right_get("ALL_REPORT")){?>      
      <tr>
        <td class="td1_koyu">
                    - CTRL tuşuna basarak birden fazla departman seçebilisiniz!!!
        </td>
      </tr>
      <?}?>
    </table>
  </td>
  </tr>
  <tr>
        <td colspan=4 align=center><br>
            <a href="javascript:submit_form('outb_search');"><img name="Image631" border="0" src="<?=IMAGE_ROOT?>raporal.gif"></a>
       </td>
    </tr>    
</table>    
   </form>
   <a href="report_outb.php"><img border="0" src="<?=IMAGE_ROOT?>kriter_temizle.gif"></a>
<?page_footer(0);?>
<script LANGUAGE="javascript" src="/scripts/popup.js"></script>
<script language="javascript" src="/scripts/form_validate.js"></script>
<script language="JavaScript" type="text/javascript">
//Time functions starts here
  function Fillothercombos(my_val){
    FillSecondCombo('DEPT_ID',      'DEPT_NAME',    '01SITE_ID='+ my_val               , ''                   , 'DEPT_ID' , '');
  }
  
  function set_my_hour(my_val){
    if (my_val=='a'){
      if ((document.all('hm0').value == '-1') && (document.all('hh0').value != '-1'))
        document.all('hh0').value = '-1';
      else if ((document.all('hm0').value != '-1')&&(document.all('hh0').value == '-1'))
        document.all('hh0').value = '0';
    }else if (my_val=='b'){
        if ((document.all('hm1').value == '-1')&&(document.all('hh1').value != '-1'))
          document.all('hh1').value = '-1'
        else if ((document.all('hm1').value != '-1')&&(document.all('hh1').value == '-1'))
          document.all('hh1').value = '0';  
    }
  }  

  function set_my_min(my_val){
    if (my_val=='a'){
      if ((document.all('hh0').value == '-1')&&(document.all('hm0').value != '-1'))
        document.all('hm0').value = '-1';
      else if ((document.all('hh0').value != '-1')&&(document.all('hm0').value == '-1'))
        document.all('hm0').value = '0';
    }else if (my_val=='b'){
      if ((document.all('hh1').value == '-1')&&(document.all('hm1').value != '-1'))
        document.all('hm1').value = '-1';
      else if ((document.all('hh1').value != '-1')&&(document.all('hm1').value == '-1'))
        document.all('hm1').value = '0';
    }
  }

  function set_hours(my_val){
    if (my_val=='1') {
      document.all('hh0').value = '9';
      document.all('hm0').value = '0';
      document.all('hh1').value = '18';
      document.all('hm1').value = '0';
      }
    else if (my_val=='2') {
      document.all('hh0').value = '18';
      document.all('hm0').value = '0';
      document.all('hh1').value = '9';
      document.all('hm1').value = '0';
      }
    else if (my_val=='3') {
      document.all('hh0').value = '-1';
      document.all('hm0').value = '-1';
      document.all('hh1').value = '-1';
      document.all('hm1').value = '-1';
      }
      else return;                      
    }

  
    function set_date(n_value){
      document.all("tarih_bas").style.display='none';
      document.all("tarih_bit").style.display='none';
      document.all('last').value='-1'
      if (n_value == 'e'){
        document.all("tarih_bas").style.display='';
        document.all("tarih_bit").style.display='';
      }else if (n_value > 0){
        document.all('MY_DATE').value='-1'
      }else return;
    }  
    function set_date1(n_value){
      document.all("tarih_bas").style.display='none';
      document.all("tarih_bit").style.display='none';
      if (n_value > 0){
        document.all('MY_DATE').value='-1'
      }else return;
    }
//Time functions ends here

    function contact_type_display(){
            if(document.all("IN_FIHRIST").checked && !document.all("OUT_FIHRIST").checked){
          document.all("cont_type_sel").style.display='';
            }else{
          document.all("cont_type_sel").style.display='none';
            }    
    }

  function submit_form(form_name){
        popup('','report_screen',800,600)
    document.all('SITE_ID').disabled=false;  
    document.all('DEPT_ID').name = 'DEPT_ID[]';    
        document.all(form_name).target= 'report_screen';
/*        if(document.all('TRUNK').value != '-1'){
        document.all('SUMM').value = 'trunk';            
        }*/
        if(document.all('SITE_ID').disabled){
           document.all('SITE_ID').disabled=false; 
           var closeIt=2;
        }   
        document.all(form_name).submit();
        
        if(closeIt==2)
            document.all('SITE_ID').disabled=true; 
       }

       function set_country(){
	     document.all('country_code').value = document.all('CountryCode').value;
	   }

//-->
</script>