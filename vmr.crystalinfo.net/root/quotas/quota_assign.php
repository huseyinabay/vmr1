<?
   require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
   $cUtility = new Utility();
   $cdb = new db_layer();
   require_valid_login();
   $conn = $cdb->getConnection();
//Site Admin veya Admin Hakkı yokda bu tanımı yapamamalı
     if (!right_get("SITE_ADMIN") && !right_get("ADMIN")){
        print_error("Bu sayfaya erişim hakkınız yok!");
    exit;
     }

    if ($act=="upd" && $id!="" && is_numeric($id)){
    //Admin Hakkı versa ve Site Admin hakkı yoksa sadece kendi sitesine ait bilgiyi görebilmeli
    if (right_get("ADMIN") && !right_get("SITE_ADMIN")){
           $site_crt = " AND QUOTAS.SITE_ID = ".$SESSION['site_id'];
       }
    $sql_str = "SELECT QUOTA_ASSIGNS.*, QUOTAS.SITE_ID,QUOTAS.QUOTA_NAME FROM QUOTA_ASSIGNS 
          INNER JOIN QUOTAS ON QUOTA_ASSIGNS.QUOTA_ID = QUOTAS.QUOTA_ID
          WHERE QUOTA_ASSIGN_ID = $id ".$site_crt;
    if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
          print_error($error_msg);
          exit;
       }
    if (mysqli_num_rows($result)>0){
         $row = mysqli_fetch_object($result);
        $SITE_ID  = $row->SITE_ID;
        $QUOTA_ID = $row->QUOTA_ID;
        $OBJ_ID   = $row->OBJ_ID;
        $QUOTA_NAME = $row->QUOTA_NAME;
      $QUOTA_ASSIGN_TYPE_ID = $row->QUOTA_ASSIGN_TYPE_ID;
    }else{
        print_error("Belirtilen Kayıt Bulunamadı");
          exit;
    }
  }else{
    $act="new";
    if ($q_id<>'' && $q_id!=="" && is_numeric($q_id)){
           $sql_str1 = "SELECT * FROM QUOTAS WHERE QUOTA_ID = $q_id ".$site_crt;
           if (!($cdb->execute_sql($sql_str1,$result1,$error_msg))){
               print_error($error_msg);
               exit;
           }
      if (mysqli_num_rows($result1)>0){
           $row1 = mysqli_fetch_object($result1);
          $SITE_ID  = $row1->SITE_ID;
          $QUOTA_ID = $row1->QUOTA_ID;
          $QUOTA_NAME = $row1->QUOTA_NAME;
      }else{
          print_error("İlgili Kotaya Ait Kayıt Bulunamadı");
            exit;
      }
        }
  }

  
   cc_page_meta();
   echo "<center>";
   page_header();
   fillSecondCombo();
?>
<br>
<script language="javascript">
function submit_form() {
  if(check_form(quota_assign)){
      document.quota_assign.submit();
  }
}
</script>
<?
   echo "<center>";
   table_header("KOTA ATAMALARI","75%");
?>
    <table border="0" cellspacing="0" cellpadding="0" width="100%" >
    <form name="quota_assign" action="quota_assign_db.php?act=<? echo $act ?>" method="post" onsubmit="return check_form(this);" >
     <INPUT TYPE="hidden" value="<?=$id?>" name="id"> 
     <INPUT TYPE="hidden" value="<?=$OBJ_ID?>" name="OBJ_ID"> 
     <INPUT TYPE="hidden" value="<?=$SITE_ID?>" name="SITE_ID"> 
     <INPUT TYPE="hidden" value="<?=$QUOTA_ID?>" name="QUOTA_ID"> 
     <INPUT TYPE="hidden" value="<?=$QUOTA_ASSIGN_TYPE_ID?>" name="QUOTA_ASSIGN_TYPE_ID"> 
    <tr>
          <td width="30%" valign="top">
        <table width="100%" cellspacing="0" cellpadding="0" border="0" height="60">
          <tr> 
            <td class="td1_koyu" width="25%">Site Adı</td>
             <td width="75%"><?echo get_site_name($SITE_ID);?></td>
          </tr>  
                    <tr valign="top">
            <td width="30%" class="td1_koyu">Kota Adı</td>
                        <td width="70%"><?echo $QUOTA_NAME;?></td>
          </tr>  
          <tr valign="top" >
                <td width="30%" class="td1_koyu">Kime Verilecek</td>
                        <td width="70%">
                              <select class="select1" name="QUOTA_ASSIGN_TYPE_ID" style="width:150" onchange="javascript:select_type(this.value);">
                                 <?
                                  $strSQL = "SELECT QUOTA_ASSIGN_TYPE_ID,TYPE_NAME AS USER FROM QUOTA_ASSIGN_TYPES ";
                                  echo $cUtility->FillComboValuesWSQL($conn, $strSQL, true,  $row->QUOTA_ASSIGN_TYPE_ID);
                                 ?>
                              </select>
            </td>
          </tr>
        </table>
      </td>
      <td width="5%" rowspan="3" ></td>
      <td width="65%" rowspan="4" valign="top">
        <IFRAME SRC="quota_report.php?id=<?=$QUOTA_ID?>" height="400" width="350" frameborder="0">
          <!-- Alternate content for non-supporting browsers -->
          Bu Sayfayı Düzgün Olarak Göremiyorsanız Browser'ınızı Update Ediniz.  
        </IFRAME>
      </td>
    </tr>
    <tr>
      <td width="30%" valign="top">
        <table width="100%" cellspacing="0" border="0" cellpadding="0"  height="30">
          <tr class="form" id="ext" style="display:none;" >
                        <td width="30%" class="td1_koyu">Dahili</td>
                        <td width="70%">
                            <select class="select1" name="EXT_ID" style="width:150" onclick="javascript:set_me(this.value)">
                                <OPTION value="-1">--Seçiniz--</OPTION>
                            </select>
                        </td>
            <tr>
          <tr class="form" id="dept" style="display:none;" >
                        <td width="30%" class="td1_koyu">Departman</td>
                        <td width="70%">
                            <select class="select1" name="DEPT_ID" style="width:150" onclick="javascript:set_me(this.value)"> 
                                <OPTION value="-1">--Seçiniz--</OPTION>
                            </select>
                        </td>
            <tr>
          <tr class="form"id="auth" style="display:none;" >
                        <td width="30%" class="td1_koyu">Auth.Kodu</td>
                        <td width="70%">
                            <select class="select1" name="AUTH_CODE_ID" style="width:150" onclick="javascript:set_me(this.value)">
                                <OPTION value="-1">--Seçiniz--</OPTION> 
                            </select>
                        </td>
            <tr>
        </table>
        <table width="100%" cellspacing="0" border="0" cellpadding="0"  height="40">
          <tr>
                        <td width="30%" class="td1_koyu">Açıklama</td>
                        <td width="70%">
              <textarea class="textarea1" cols="30" rows="3" name="DESCRIPTION"><?=$row->DESCRIPTION?></textarea> 
                        </td>
            <tr>
        </table>
      </td>
    </tr>
    <tr>
             <td align="center" width="100%">
                <img border="0" STYLE="cursor:hand" src="<?=IMAGE_ROOT ?>kaydet.gif" onclick="javascript:submit_form()"><br><br>
               <a href="quota_assign.php?q_id=<?=$QUOTA_ID?>">Yeni Kota Ataması Yap</a>
       </td>
      </tr>
    <tr>
      <td width="40%" align="bottom">
        <IFRAME SRC="assign_type_list.php?site_id=<?$SITE_ID?>" height="150" width="300" border="0" frameborder="0">
          <!-- Alternate content for non-supporting browsers -->
          Bu Sayfayı Düzgün Olarak Göremiyorsanız Browser'ınızı Update Ediniz.
        </IFRAME>
      </td>
    </tr>
    </form>
  </table>

  <script language="javascript">
    function check_form(my_val){
        if (window.document.all('QUOTA_ID').value=='-1'){
        alert ('Lütfen Kota Seçiniz');
        return;
      }
      if (window.document.all('QUOTA_ASSIGN_TYPE_ID').value=='-1'){
        alert ('Lütfen Atama Yapılacak Kota Tipini Seçiniz.');
        return;
      }   
        if ((window.document.all('OBJ_ID').value=="-1")||(window.document.all('OBJ_ID').value=="")){
        alert ('Kota Ataması Yapacağınız Dahili,Departman veya Auth. Kod Alanını seçiniz'); 
        return;  
      }
      document.quota_assign.submit();
    }  
      
  function select_type(m_value){
      window.document.all('ext').style.display='none';  
       window.document.all('dept').style.display='none';  
       window.document.all('auth').style.display='none';  

    
        if(m_value==1){  
             window.document.all('ext').style.display='';  
            FillSecondCombo('EXT_ID',     'EXT_NO',    '02SITE_ID=' + '<?=$SITE_ID?>' , '<?=$OBJ_ID?>' , 'EXT_ID' , '')
            
         window.frames(1).location.href ='assign_type_list.php?type=ext&site_id='+ document.all('SITE_ID').value; 
      }    
      
        if(m_value==2 || m_value==3){
        window.document.all('dept').style.display='';  
        FillSecondCombo('DEPT_ID',  'DEPT_NAME',  '01SITE_ID='+ '<?=$SITE_ID?>' , '<?=$OBJ_ID?>' , 'DEPT_ID' ,  '')
        window.frames(1).location.href ='assign_type_list.php?type=dept&site_id='+ document.all('SITE_ID').value; 
      }            
        if(m_value==4){
        window.document.all('auth').style.display='';  
            FillSecondCombo('AUTH_CODE_ID',  'AUTH_CODE',    '03SITE_ID=' + '<?=$SITE_ID?>' , '<?=$OBJ_ID?>' , 'AUTH_CODE_ID' , '')        
        window.frames(1).location.href ='assign_type_list.php?type=auth&site_id='+ document.all('SITE_ID').value; 
      }            
  }
  
  function display_quota(n_val){
    window.frames(0).location.href ='quota_report.php?id='+n_val; 
  }
  
  function set_me(myval){
    document.all('obj_id').value = myval;
  }
</script>
<script language="javascript">
    display_quota(<?=$QUOTA_ID?>);
   window.setTimeout('select_type(\'<?=$QUOTA_ASSIGN_TYPE_ID?>\')', 500);
</script>
<? table_footer();
   page_footer(0);
?>

</body>
</html>
