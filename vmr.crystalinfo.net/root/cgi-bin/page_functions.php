<?

//require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/common.php";
require_once ('/usr/local/wwwroot/multi.crystalinfo.net/root/cgi-bin/common.php');
//require_once ('/usr/local/wwwroot/multi.crystalinfo.net/root/cgi-bin/common.php');
   if (!defined("IMAGE_ROOT")){ // Note that it should be quoted
      define("IMAGE_ROOT", "/images/");
   }  
   
    //  ini_set('display_errors', 'On');
    //  error_reporting(E_ALL);
 
function cc_page_meta($BODY_MENU = 1){ 
         global $_SERVER;
		 
		 $crystalcss = "/usr/local/wwwroot/multi.crystalinfo.net/root/crystal.css";
?>
        <html>
        <head>
        <title>Crystal Info</title>
		
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<!--
        <meta http-equiv="Content-Type" content="text/html; charset=windows-1254">
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-9">
		
		// -->

        <? javascripts(); ?>
        <link rel="stylesheet" type="text/css" href="../crystal.css" />
		 <link rel="stylesheet" type="text/css" href="../bootstrap.min.css"/>
        </head>
        <body bgcolor="#FFFFFF" text="#000000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<?}
function table_header($header,$width){?>
 <table width="<?=$width?>" border="0" cellspacing="0" cellpadding="0" align="center" nowrap>
  <tr valign="bottom"> 
    <td colspan="3" > 
      <table width="100%" border="0" cellspacing="0" cellpadding="0" background="<?=IMAGE_ROOT?>bg_table05.gif" height="29">
        <tr align="right"> 
          <td width="50%" align="left" valign="bottom"> 
            <table width="100%" border="0" cellspacing="0" cellpadding="0" height="29">
              <tr> 
                <td align="left" valign="bottom" rowspan="2" width="40"><img src="<?=IMAGE_ROOT?>table_logo.gif" width="40" height="29"></td>
                <td width="100%" class="header" background="<?=IMAGE_ROOT?>bg_table08.gif" nowrap><?=$header?></td>
              </tr>
              <tr> 
                <td width="100%"><img src="<?=IMAGE_ROOT?>bg_table07.gif" width="1" height="13"></td>
              </tr>
            </table>
          </td>
          <td width="50%" valign="top"><img src="<?=IMAGE_ROOT?>bg_table06.gif" width="1" height="29"></td>
        </tr>
      </table>
    </td>
  </tr>
  <tr> 
    <td height="100%" bgcolor="929292" width="1"><img src="<?=IMAGE_ROOT?>bg_1px.gif" width="1" height="1"></td>
    <td width="100%" height="100%" bgcolor="F0F8FF" valign="top">
      <table width="100%" border="0" class="text" cellspacing="0" cellpadding="10" height="100%">
        <tr>
          <td>
          
<?} 
function table_header_main($header,$width){?>
 <table width="<?=$width?>" border="0" cellspacing="0" cellpadding="0" align="center" nowrap>
  <tr valign="bottom"> 
    <td colspan="3" > 
      <table width="100%" border="0" cellspacing="0" cellpadding="0" background="<?=IMAGE_ROOT?>bg_table05.gif" height="29">
        <tr align="right"> 
          <td width="50%" align="left" valign="bottom"> 
            <table width="100%" border="0" cellspacing="0" cellpadding="0" height="29">
              <tr> 
                <td align="left" valign="bottom" rowspan="2" width="40"><img src="<?=IMAGE_ROOT?>table_logo.gif" width="40" height="29"></td>
                <td width="100%" class="header" background="<?=IMAGE_ROOT?>bg_table08.gif" nowrap><?=$header?></td>
              </tr>
              <tr> 
                <td width="100%"><img src="<?=IMAGE_ROOT?>bg_table07.gif" width="1" height="13"></td>
              </tr>
            </table>
          </td>
          <td width="50%" valign="top"><img src="<?=IMAGE_ROOT?>bg_table06.gif" width="1" height="29"></td>
        </tr>
      </table>
    </td>
  </tr>
  <tr> 
    <td height="100%" bgcolor="929292" width="1"><img src="<?=IMAGE_ROOT?>bg_1px.gif" width="1" height="1"></td>
    <td width="100%" height="100%" bgcolor="F0F8FF" valign="top">
      <table width="100%" border="0" class="text" cellspacing="0" cellpadding="0" height="100%">
        <tr>
          <td>
          
<?} 
function table_header_mavi($header, $width){
?>
            <table width="<?=$width?>%" border="0" cellspacing="0" cellpadding="0" align="center">
              <tr> 
                <td> 
                  <table width="100%" border="0" cellspacing="0" cellpadding="0" background="<?=IMAGE_ROOT?>liste_bg_table05.gif" height="29">
                    <tr align="right"> 
                      <td width="50%" align="left" valign="bottom"> 
                        <table border="0" cellspacing="0" cellpadding="0" height="29" width="100%">
                          <tr> 
                            <td align="left" valign="bottom" rowspan="2" width="30"><img src="<?=IMAGE_ROOT?>liste_table_logo.gif" width="40" height="29"></td>
                            <td width="100%" class="header" background="<?=IMAGE_ROOT?>liste_bg_table08.gif">
                               <?=$header?>
                            </td>
                          </tr>
                          <tr> 
                            <td width="100%"><img src="<?=IMAGE_ROOT?>liste_bg_table07.gif" width="1" height="13"></td>
                          </tr>
                        </table>
                      </td>
                      <td width="50%" valign="top"><img src="<?=IMAGE_ROOT?>liste_bg_table06.gif" width="1" height="29"></td>
                    </tr>
                  </table>
                </td>
              </tr>
              <tr> 
                <td bgcolor="3F7DBC" align="center"> 

<?
}
function table_footer_mavi(){
?>
                </td>
              </tr>
            </table>
<?
}
function table_footer(){
?>
          </td>
        </tr>
      </table>
    </td>
    <td height="100%" bgcolor="929292" width="1"><img src="<?=IMAGE_ROOT?>bg_1px.gif" width="1" height="1"></td>
  </tr>
  <tr> 
    <td colspan="3" bgcolor="929292" height="1"><img src="<?=IMAGE_ROOT?>bg_1px.gif" width="1" height="1"></td>
  </tr>
</table>
            
<?}?>
<?function table_in_table_header($header,$width){?>

    <table width="<?=$width?>" border="0" cellspacing="0" cellpadding="0" background="F0F8FF">
        <tr align="right"> 
            <td class="header_mavi" colspan="3"> 
                <table border="0" cellspacing="0" cellpadding="0" bgcolor="#B3CAE3" width="99%" align="center">
                    <tr> 
                         <td width="3%" valign="bottom" align="left"><img src="<?=IMAGE_ROOT?>table3_ust.gif" width="12" height="20"></td>
                      <td width="97%" class="header_mavi"><?=$header?></td>
                   </tr>
                </table>
             </td>
         </tr>
    <tr>
            <td align="center" colspan="3">
<?}?>
<?function table_in_table_footer(){?>
            </td>
        </tr>
      <tr>
      <td></td>
      <td bgcolor="#CDDCED" height="3" width="99%" align="center"></td>
      <td></td>
      </tr>
  </table>
<?}?>
<?function table_simple_header($header,$width){?>

    <table width="<?=$width?>" border="0" cellspacing="0" cellpadding="0" background="F0F8FF">
        <tr align="right"> 
            <td class="header_mavi" colspan="3"> 
                <table border="0" cellspacing="0" cellpadding="0" bgcolor="#F0F8FF" width="98%" align="center">
                    <tr bgcolor="#CDDCED"> 
                         <td width="3%" valign="bottom" align="left"><img src="<?=IMAGE_ROOT?>ara6px.gif" width="6" height="6"></td>
                      <td width="97%" class="header_mavi"><?=$header?></td>
                   </tr>
                </table>
             </td>
         </tr>
    <tr>
            <td align="center" colspan="3">
<?}?>
<?function table_simple_footer(){?>
            </td>
        </tr>
      <tr>
      <td></td>
      <td bgcolor="#CDDCED" height="3" width="98%" align="center"></td>
      <td></td>
      </tr>
  </table>
<?}?>
<?function error_page($err_message="HATA OLUŞTU",$err_type) {
?>
   <html>
  <head>
    <title>Crystal Info</title>
    <meta http-equiv="Content-Type" content="text/html; charset=">
      <link rel="stylesheet" href="table.css" type="text/css">
   </head>
  <body bgcolor="#FFFFFF" text="#000000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
  <?page_header("","");?>
      <table width="100%" border="0" cellspacing="0" cellpadding="0" height="100%" align="left">
           <tr> 
               <td valign="middle" bgcolor="#D8E4F1" align="center"> 
          <BR><BR><BR><BR><BR><BR>&nbsp;
                </td>
           </tr>
           <tr> 
               <td valign="top" bgcolor="#D8E4F1" align="center"> 
                   <p><img src="/images/unlem.gif" width="35" height="87"></p>
                   <p class="abandon"><?=$err_message?></p>
                   <p class="abandon"><?=date('d.m.Y H:i:s')?></p>
                   <p class="abandon"><img src="/images/logo_uyari.gif" width="221" height="66"></p>
                   <p class="abandon">&nbsp;</p>
                   <p class="abandon"><a HREF="javascript:self.close();"><img src="/images/kapat.gif" width="87" height="27" BORDER="0"></a> </p>
               </td>
           </tr>
     </table>
   <?page_footer("","");?>
<?}?>
<?
function page_header ($link="Anasayfa"){ 
//global $_SESSION;

?>

   <dt id="clockDiv" class="homebox" >
     <?=date('d.m.Y H:i:s'); ?>
   </dt>
   <script>
     function placeDiv(){
       clockDiv.style.position='absolute';
       clockDiv.style.left=613;
       clockDiv.style.top=50;
	   clockDiv.style.fontSize='small';
     }
     placeDiv();
   </script>

  


<table width="100%" border="0" cellspacing="0" cellpadding="0" height="98%">
  <tr> 
    <td valign="top" height="74"> 
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td valign="bottom" height="96" rowspan="2" width="40%"> 
            <table width="775" border="0" cellspacing="0" cellpadding="0">
              <tr> 
                <td height="70" valign="bottom" width="199"><a href="http://www.crystalinfo.com.tr" target = "_blank"><img src="<?=IMAGE_ROOT?>logo1.gif" border="0"></a></td>
        
	
				 
              <td height="70" valign="bottom" width="386"> 
          
<?
   require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
     $cUtility = new Utility();
     $cdb = new db_layer();
     require_valid_login();
     //session_cache_limiter('nocache');     
     $conn = $cdb->getConnection();

	 /*
$haberler="SELECT ID, REPLACE(DATA,'&',' ') AS DATA 
                          FROM RAW_ARCHIEVE 
                          WHERE SITE_ID = '1'
                          ORDER BY ID DESC
              LIMIT 0, 5";

			  */
	$processed="SELECT CDR_ID,
				CONCAT(SITE_ID, ' ',REC_TYPE, ' ', ORIG_DN, ' ', TER_TRUNK_MEMBER, ' ', TIME_STAMP, ' ', DIGITS) AS DATA
                FROM CDR_MAIN_DATA
                ORDER BY CDR_ID DESC
              LIMIT 0, 10";		  
			  
			  
			  
    if (!($cdb->execute_sql($processed, $rs_processed, $error_msg))){
        print_error($error_msg);
                exit;
}
   echo "<marquee align='middle' scrollamount='1' height='70' width='110%' direction='down' scrolldelay='0'><font color='navy'>";      
   while($row = mysqli_fetch_array($rs_processed))
   echo "{$row['DATA']}<br>";
   echo "</font></marquee>";

?>
            </td>
				

                <td height="70" valign="bottom" rowspan="2" width="189"> 
                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr> 
                      <td align="center" valign="bottom"><img src="<?=IMAGE_ROOT?>hp_left.gif" width="14" height="27"><a href="/main.php" title="Anasayfa"><img src="<?=IMAGE_ROOT?>hp_home.gif" width="27" height="27" border="0"></a><a href="/logout.php" title="Logout"><img src="<?=IMAGE_ROOT?>hp_logout.gif" width="27" height="27" border="0"></a><a href="/index.php" title="Login"><img src="<?=IMAGE_ROOT?>hp_login.gif" width="27" height="27" border="0"></a><a href="javascript:popup('/users/chgpsw.php?id=<?=$_SESSION["user_id"]?>','pass',400,400)" title="Şifre Değiştir"><img src="<?=IMAGE_ROOT?>hp_passchange.gif" width="27" height="27" border="0"></a><a href="/reports/report_personal.php" title="Kişisel Çağrı Haritası"><img src="<?=IMAGE_ROOT?>hp_kisisel.gif" width="27" height="27" border="0"></a><a href="/admin/main.php" title="Admin"><img src="<?=IMAGE_ROOT?>hp_admin.gif" width="27" height="27" border="0"></a><img src="<?=IMAGE_ROOT?>hp_right.gif" width="13" height="27"></td>
                    </tr>
                    <tr> 
                      <td background="<?=IMAGE_ROOT?>hp_bg.gif" height="20"></td>
                    </tr>
                  </table>
                </td>
              </tr>
              <tr> 
                <td background="<?=IMAGE_ROOT?>menu8.gif" colspan="2"><img src="<?=IMAGE_ROOT?>menu1.gif" width="53" height="26"><a href="/reports/report_main.php"><img src="<?=IMAGE_ROOT?>menu3.gif" width="64" height="26" border="0"></a><img src="<?=IMAGE_ROOT?>menu2.gif" width="25" height="26" border="0"><a HREF="/news/news_list.php"><img src="<?=IMAGE_ROOT?>menu4.gif" width="76" height="26" border="0"></a><img src="<?=IMAGE_ROOT?>menu2.gif" width="25" height="26" border="0"><a HREF="/messages/mesaj_src.php"><img src="<?=IMAGE_ROOT?>menu5.gif" width="59" height="26" border="0"></a><img src="<?=IMAGE_ROOT?>menu2.gif" width="25" height="26" border="0"><a HREF="/contacts/contact_src.php"><img src="<?=IMAGE_ROOT?>menu6.gif" width="49" height="26" border="0"></a><img src="<?=IMAGE_ROOT?>menu2.gif" width="25" height="26" border="0"><a href="#"><img src="<?=IMAGE_ROOT?>menu9.gif" width="60" height="26" onmouseover="javascript:opendiv()" border="0"  onmouseout="javascript:closediv()"></a><img src="<?=IMAGE_ROOT?>menu7.gif" width="35" height="26" border="0"></td>
              </tr>
            </table>
          </td>
          <td width="65%" height="70">&nbsp;</td>
        </tr>
        <tr> 
          <td background="<?=IMAGE_ROOT?>menu8.gif" height="26" width="80%">&nbsp;</td>
        </tr>
        <tr> 
          <td height="21" colspan="2" background="<?=IMAGE_ROOT?>bg_menu_alt.gif">
          <?  
                echo write_links($_SERVER["SCRIPT_NAME"]);
            ?>
              <div id="new_sub" class="aa" STYLE="display:none;top=95;left:250;width:601;  height: 25;position:absolute">
              <table width="400" border="0" cellspacing="0" cellpadding="0" onmouseover="javascript:opendiv()"  onmouseout="javascript:closediv()">
              <tr> 
                <td rowspan="2" valign="bottom" width="15" align="right"><img src="<?=IMAGE_ROOT?>yeni01.gif" width="12" height="33"></td>
                <td valign="bottom" align="center"> 
                  <table width="100%" border="0" cellspacing="0" cellpadding="0" height="9">
                    <tr> 
                      <td width="48%"></td>
                      <td width="2%" valign="bottom" align="center"><img src="<?=IMAGE_ROOT?>yeni04.gif" width="9" height="9"></td>
                      <td width="50%"></td>
                    </tr>
                    <tr> 
                      <td width="48%" bgcolor="003461" height="1"></td>
                      <td width="2%" valign="bottom" align="center" height="1"></td>
                      <td width="50%" bgcolor="003461" height="1"></td>
                    </tr>
                  </table>
                </td>
                <td rowspan="2" valign="bottom" width="15" align="left"><img src="<?=IMAGE_ROOT?>yeni02.gif" width="15" height="33"></td>
              </tr>
              <tr> 
                <td width="375" background="<?=IMAGE_ROOT?>yeni03.gif" align="center" height="32" valign="top">
                  <table width="100%" border="0" cellspacing="0" cellpadding="0" height="27">
                    <tr align="center"> 
                      <td class="yeni" width="48"><a HREF="/users/user.php">Kullanici</a></td>
                      <td class="yeni" width="11"><img src="<?=IMAGE_ROOT?>yeni05.gif" width="11" height="27"></td>
                      <td class="yeni" width="36"><a HREF="/news/news_entry_form.php">Haber</a></td>
                      <td class="yeni" width="11"><img src="<?=IMAGE_ROOT?>yeni05.gif" width="11" height="27"></td>
                      <td class="yeni" width="75"><a HREF="/messages/mesaj.php">Mesaj</a></td>
                      <td class="yeni" width="11"><img src="<?=IMAGE_ROOT?>yeni05.gif" width="11" height="27"></td>
                      <td class="yeni" width="49"><a HREF="/quotas/quota.php">Kota</a></td>
                      <td class="yeni" width="11"><img src="<?=IMAGE_ROOT?>yeni05.gif" width="11" height="27"></td>
                      <td class="yeni" width="47"><a HREF="/contacts/contacts.php">Fihrist</a></td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
            
              </div> 
          </td>
        </tr>
      </table>
<script>
      function opendiv(){
            var xx = document.all('new_sub').style.display;
            if(xx =="none" )
                  document.all('new_sub').style.display = '';
      }
      function closediv(){
            var xx = document.all('new_sub').style.display;
            if(xx =='' )
                  document.all('new_sub').style.display = 'none';
      }
</script>  
    </td>
  </tr>
  <tr> 
    <td valign="top" align="center" height="95%" background="<?=IMAGE_ROOT?>hp_bg1.gif"> 
      <table width="777" border="0" cellspacing="0" cellpadding="0" height="100%" align="left">
        <tr>
          <td valign="top" background="<?=IMAGE_ROOT?>hp_bg2.gif" width="4"></td>
          <td valign="top" bgcolor="#D8E4F1" width="769" align="center"> 
            <center> 

<?
 }

function page_footer($header,$width="100%"){ 
?>

 </td>
     <td   WIDTH="4" BACKGROUND="<?=IMAGE_ROOT?>hp_bg2.gif" >
     </td>
  </tr>
</TABLE>
  <tr> 
    <td valign="top" background="<?=IMAGE_ROOT?>bg_menu_alt.gif" height="21" width="100%"></td>
  </tr>

  <tr> 
    <td valign="CENTER" bgcolor="#336699" height="25" width="100%" align=LEFT class="text">
     &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<font color=#FFFFFF>
    Copyright © 2003 Crystalinfo bir <a href="http://www.crystalinfo.com.tr" target = _blank>Netser Komünikasyon</a> Ürünüdür.&nbsp;
    Destek için : <a href="mailto:crystalinfo@netser.com.tr">crystalinfo@netser.com.tr</a>
    </font>
    </td>
  </tr>
</table>
</body>
</html>

<? }


function popup_footer($width="100%"){ 
 ?><?
 }

function table_arama_header($width){ ?>
<center>
    <table width="<?=$width?>" border="0" cellspacing="0" cellpadding="0" align="center" bordercolor="60ADD1">
       <tr> 
           <td align=left>
               <table width="100%" border="0" cellspacing="0" cellpadding="0" align="left" bordercolor="60ADD1">
                   <tr> 
                       <td colspan="8" class="header_mavi"><img src="<?=IMAGE_ROOT?>liste_bg_table09.gif" width="124" height="11"></td>
                   </tr>
                </table>
           </td >
         </tr> 
        <tr> 
            <td align=left>
                      
<? } 
function table_arama_footer(){
?>
           </td>
         </tr>
        <tr>
          <td>
               <table BORDER="0" WIDTH="100%" CELLPADDING="0" CELLSPACING="0">  
                   <tr> 
                      <td height="5" width="100%" class="text_beyaz" bgcolor="#6699CC"></td>
                   </tr>
                   <tr> 
                       <td height="2" width="100%" class="text_beyaz" bgcolor="#3F7DBC"></td>
                    </tr>
               </table>
            </td>
      </tr>
  </table>
<? } 

function list_line($colspan){?>
          <tr> 
            <td colspan="<?=$colspan?></td>" height="1" background="<?=IMAGE_ROOT ?>bg_table1.gif">
            <img src="<?=IMAGE_ROOT ?>bg_table1.gif" width="4" height="1"><img src="<?=IMAGE_ROOT ?>bg_table2.gif" width="1" height="1"></td>
          </tr>

<?}

function javascripts() {
?>
<script language="JavaScript">
<!--

function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_findObj(n, d) { //v4.0
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && document.getElementById) x=document.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}

// -->
</script>
<script LANGUAGE="javascript">
function popup(popup_url, popup_name, winWidth, winHeight){
  scrL = (screen.width - winWidth) / 2;
  scrT = ((screen.height - winHeight) / 2);
  yeni = window.open(popup_url, popup_name, "width="+winWidth+",height="+winHeight+",status=yes,resizable=yes,scrollbars=yes,menubar=yes,top="+scrT+",left="+scrL);
  yeni.focus();
}
</script>
<?
}

 function menu(){
}

?>