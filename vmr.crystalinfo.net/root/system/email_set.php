<?
   require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
   $cUtility = new Utility();
   $cdb = new db_layer();
   session_cache_limiter('nocache');
   require_valid_login();
   $conn = mysqli_connect("localhost","crinfo","SiGmA*19","MCRYSTALINFONE");

   check_right("SITE_ADMIN");
//$id ='1';

     $sql_str = "SELECT * FROM email_sets WHERE set_id = 1" ; 
     $result = mysqli_query($conn,$sql_str);
	 
   if (mysqli_num_rows($result)>0){
       $row = mysqli_fetch_object($result);
   }else{
      print_error("Belirtilen Kayıt Bulunamadı");
        exit;    
   }
  
   
  cc_page_meta();
     echo "<center>";
     page_header();
     echo "<center><br>";
     table_header("E-Posta Sunucu Tanımları","65%");
	 
	 
?>
<script>
function submit_form() {
   if(check_form(document.email_set_frm)){
    document.email_set_frm.submit();
   }
}
</script>
<table width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
  <tr>
      <td>
      <center>
            <table class="formbg">
            <form name="email_set_frm" method="post" onsubmit="return check_form(this);" action="email_set_db.php?act=upd">
            <input type="hidden" name="id" value="<?=$id?>">
         <tr>
          <td colspan="2" class="td1_koyu">
           E-mail Posta Bilgilerini Giriniz!
          </td>
        </tr>
		
		<tr class="form">
                  <td width="50%" class="td1_koyu">Kayıt İsmi</td>
                   <td width="50%" ><input type="text" class="input1" size="60" name="set_name" VALUE="<?echo $row->set_name?>" Maxlength="60"></td> 
               </tr>
        <tr class="form" action="/email_set.php">
                  <td width="50%" class="td1_koyu">Sunucu Adı-ip</td>
                   <td width="50%" ><input type="text" class="input1" size="31" name="set_server" VALUE="<?echo $row->set_server?>" Maxlength="31"> 

               </tr>
		<tr class="form" action="/email_set.php">
                  <td width="50%" class="td1_koyu">Port</td>
                   <td width="50%" ><input type="text" class="input1" size="4" name="set_port" VALUE="<?echo $row->set_port?>" Maxlength="4"></td> 
               </tr>	   
        <tr class="form">
                  <td class="td1_koyu" title="e-mail kullanıcı adı">Kullanıcı Adı</td>
                   <td ><input type="text" class="input1" size="60" name="set_user" VALUE="<?echo $row->set_user?>" Maxlength="60"></td> 
               </tr>
		<tr class="form">
                  <td class="td1_koyu" title="e-mail şifresi">Şifre</td>
                   <td ><input type="password" class="input1" size="60" name="set_pass" VALUE="<?echo $row->set_pass?>" Maxlength="60"></td> 
               </tr>	   
			   
	   	<tr class="form">
                    <td class="td1_koyu" title="Kimlik Doğrulama">Kimlik Doğrulama</td>
                    <td>
                        <select name="set_auth" class="select1" Maxlength="15" style="width: 100px;">
							<option VALUE="<?echo $row->set_auth?>"><?echo $row->set_auth?></option> 
							<option value="true">true</option> 
							<option value="false">false</option>
                        </select>
                    </td>
                </tr>

		<tr class="form">
                    <td class="td1_koyu" title="e-mail protokolü">Protokol</td>
                    <td>
                        <select name="set_protokol" class="select1" Maxlength="15" style="width: 100px;">
							<option VALUE="<?echo $row->set_protokol?>"><?echo $row->set_protokol?></option> 
							<option value="smtp">smtp</option> 
							<option value="pop3">pop3</option>
							<option value="Imap">Imap</option>
                        </select>
                    </td>
                </tr>
		
				<tr class="form">
                    <td class="td1_koyu" title="e-mail protokolü">Secure</td>
                    <td>
                        <select name="set_secure" class="select1" Maxlength="15" style="width: 100px;">
							<option VALUE="<?echo $row->set_secure?>"><?echo $row->set_secure?></option> 
							<option value=""></option> 
							<option value="ssl">ssl</option> 
							<option value="tls">tls</option>
                        </select>
                    </td>
                </tr>
				
				
		<tr class="form">
                    <td class="td1_koyu" title="e-mail tipi">E-posta Tipi</td>
                    <td>
                        <select name="set_type" class="select1" Maxlength="15" style="width: 100px;">
							<option VALUE="<?echo $row->set_type?>"><?echo $row->set_type?></option> 
							<option value="gelen">gelen</option> 
							<option value="giden">giden</option>
                        </select>
                    </td>
                </tr>
				
		  
		  <tr class="form">
                  <td class="td1_koyu" title="Açıklama">Kimden / FromName</td>
                   <td ><input type="text" class="input1" size="60" name="set_fromname" VALUE="<?echo $row->set_fromname?>" Maxlength="60"></td> 
               </tr>
		  
		  
		  <tr class="form">
                  <td class="td1_koyu" title="Açıklama">Kimden E-posta</td>
                   <td ><input type="text" class="input1" size="60" name="set_from" VALUE="<?echo $row->set_from?>" Maxlength="60"></td> 
               </tr>	
	
         <tr class="form">
                  <td class="td1_koyu" title="Açıklama">Açıklama</td>
                   <td ><input type="text" class="input1" size="60" name="set_desc" VALUE="<?echo $row->set_desc?>" Maxlength="60"></td> 
               </tr>
        
              <tr>
          <td></td>
          <td><img border="0" src="<?=IMAGE_ROOT?>kaydet.gif" style="cursor:hand;" onclick="javascript:submit_form()">
		  <?if($id == "1"){?>
		  <span style="color:red" id="spanKaydedildi">Kaydedildi!</span>
		  <script>setTimeout(function(){ document.getElementById('spanKaydedildi').style.display = 'none'; }, 3000);</script>
		  <?}?>
		  </td>
		  
        </tr>
          </form>
            </table>
      </td>
  </tr>
</table><br>


<table align="right" width="100%" border="0">
  <tr>
    <td colspan="4" width="70%"></td>
	
	
	
  </tr>
</table>

      <script language="javascript" src="/scripts/form_validate.js"></script>
      <script language="javascript">
            form_fields[0] = Array ("set_name", "Kayıt Adını Girmelisiniz.", TYP_NOT_NULL);
			form_fields[1] = Array ("set_server", "Sunucu Adını veya Ip Adresisni Girmelisiniz.", TYP_NOT_NULL);
			form_fields[2] = Array ("set_port", "E-posta port Bilgisini Girmelisiniz.", TYP_NOT_NULL);
            form_fields[3] = Array ("set_user", "E-posta Kullanıcı Adını Girmelisiniz.", TYP_NOT_NULL);
			form_fields[4] = Array ("set_pass", "E-posta Şifresini Girmelisiniz.", TYP_NOT_NULL);
			form_fields[5] = Array ("set_auth", "E-posta Kimlik Doğrulama Bilgisini Seçmelisiniz.", TYP_DROPDOWN);
			form_fields[6] = Array ("set_protokol", "E-posta protokol Bilgisini Seçmelisiniz.", TYP_DROPDOWN);
			form_fields[7] = Array ("set_secure", "E-posta Güvenli Bağlantı Tipini Seçmelisiniz.", TYP_DROPDOWN);
			form_fields[8] = Array ("set_type", "E-posta Tipini Seçmelisiniz.", TYP_DROPDOWN);
			form_fields[9] = Array ("set_fromname", "E-posta Kimden İsim Bilgisi Girmelisiniz.", TYP_NOT_NULL);
			form_fields[10] = Array ("set_from", "E-posta Kimden e-posta Girmelisiniz.", TYP_EMAIL);
            form_fields[11] = Array ("set_desc", "E-posta Açıklaması Girmelisiniz.", TYP_NOT_NULL);

      </script>
<?
table_footer();
page_footer(0);?>