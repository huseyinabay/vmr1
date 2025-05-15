<?
  require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
 //require_once $_SERVER['DOCUMENT_ROOT']"/usr/local/wwwroot/multi.crystalinfo.net/root/cgi-bin/functions.php";	
  //require_once "/usr/local/wwwroot/multi.crystalinfo.net/root/cgi-bin/functions.php";	
    //ini_set('display_errors', 'On');
	//error_reporting(E_ALL ^ E_NOTICE);
   
   /*   
  $Halil = "Halil";
  echo $Halil;exit;
   */
 ?>
<HTML>
<HEAD>
<TITLE>CrystalInfo Login</TITLE>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-9">
<link rel="stylesheet" href="crystal.css" type="text/css">
</HEAD>
<BODY BGCOLOR=#FFFFFF leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" background="/images/hp_bg1.gif">
<?cc_page_meta(0);?>
<body bgcolor="#FFFFFF" text="#000000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<form name="giris" method="post" action="login.php" >
<table width="100%" border="0" cellspacing="0" cellpadding="0" height="100%">
  <tr>
    <td align="center" valign="middle"> 
      <table width="478" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td align="right" valign="bottom"><img src="/images/login_ust.gif" width="222" height="14"></td>
        </tr>
        <tr> 
          <td colspan="2"> 
            <table width="478" border="0" cellspacing="0" cellpadding="0" height="222">
              <tr> 
                <td width="1" valign="top" background="/images/bg_border.gif"><img src="/images/bg_border.gif" width="1" height="1"></td>
                <td width="199" background="/images/bg_login.gif" align="center" valign="middle"> 
                  <table width="190" border="0" cellspacing="6" cellpadding="0">
              <tr><td colspan="3"><img src="/images/logo1.gif"></td></tr>
			        <?if($act == "retry"){?>
					<tr>
					  <td colspan="3"><span style="color:red;">Kullanıcı adı veya şifreniz yanlış!</span></td>
					</tr>
					<?}?>
                    <tr> 
                      <td class="header" nowrap><img SRC="/images/ok.gif"> Kullanıcı Adı</td><td>:</td>
                      <td width="100"><input type="text" name="user_name" size="10"></td>
                    </tr>
                    <tr> 
                      <td class="header" nowrap><img SRC="/images/ok.gif"> Şifre</td><td>:</td>
                      <td width="100"><input type="password" name="user_pass" size="10"></td>
                    </tr>
                    <tr> 
                      <td colspan="2">&nbsp;</td>
                      <td width="91"><input tabindex="4" type="image" onclick="javascript:return send_form()" name="Image6" border="0" src="/images/login_button.gif"></td>
                    </tr>
                  </table>
                </td>
                <td width="279" valign="top" align="left" style="white-space: nowrap;"><img src="/images/login_img01.gif" width="135" height="222"><img src="/images/login_img02.gif" width="144" height="222"></td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</form>

<script language="JavaScript" type="text/javascript">
<!--
   function send_form()
   {
      if (giris.user_name.value ==""){
        alert("Kullanıcı kodunuzu giriniz");
        giris.user_name.focus();
        return false;
      }
      if (giris.user_pass.value ==""){
        alert("Kullanıcı şifrenizi giriniz");
        giris.user_pass.focus();
        return false ;
      }
      document.giris.submit();
   }
//-->
</script>

