<?
     require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
     $cUtility = new Utility();
     $cdb = new db_layer();
     require_valid_login();

    //Hak Kontrolü
    if (right_get("SITE_ADMIN")){
        //Site admin hakkı varsa herşeyi görebilir.  
    }elseif(right_get("ADMIN") || right_get("ALL_REPORT")){
    // Admin vaye ALL_REPORT hakkı varsa kendi sitesindeki herşeyi görebilir.
      $SITE_ID = $_SESSION['site_id'];
    }else{
            print_error("Bu sayfayı Görme Hakkınız Yok!!!");
      exit;
    } 
  //Hak kontrolü sonu  

     $start = $cUtility->myMicrotime();

     cc_page_meta();
  echo "<center>";
     page_header();
     echo "<br>";

?>
<form name="system_search" method="post" action="">
<table cellpadding="0" cellspacing="0" align="center" border="0" width="50%">
  <tr>
    <td width="15%">&nbsp;</td>
    <td width="70%">
    <?table_header("Sistem Bilgileri","100%");?>
    <center>
      <table cellpadding="0" cellspacing="0" align="center" border="0" width="100%">
      <tr>  
         <td width="35%" class="td1_koyu">Site Adı</td>
           <td width="5%"></td>
             <td>
                 <select name="SITE_ID" class="select1" style="width:200" <?if (!right_get("SITE_ADMIN")) {echo "disabled";}?>>
                     <?
                         $strSQL = "SELECT SITE_ID, SITE_NAME FROM SITES ORDER BY SITE_ID";
                         echo $cUtility->FillComboValuesWSQL($conn, $strSQL, false,  $SITE_ID);
                     ?>
                 </select>
             </td>
      </tr>
      <tr height="8"><td colspan="3"></td></tr>         
        <tr> 
        <td height="20" align="center" colspan="4" width="85%" class="font_beyaz"><a style="cursor:hand;" onclick ="javascript:sform('ext');">Dahili Listesi</a></td>
      </tr>
      <tr>
        <td height="20" align="center" colspan="4" width="85%" class="font_beyaz"><a style="cursor:hand;" onclick ="javascript:sform('user');">Kullanıcı Listesi</a></td>
             </tr>
      <tr> 
        <td height="20" align="center" colspan="4" width="85%" class="font_beyaz"><a style="cursor:hand;" onclick ="javascript:sform('dept');">Departman Listesi</a></td>
      </tr>
      <tr>
        <td height="20" align="center" colspan="4" width="85%" class="font_beyaz"><a style="cursor:hand;" onclick ="javascript:sform('addr');">Global Fihrist</a></td>
      </tr>    
      <tr> 
        <td height="20" align="center" colspan="4" width="85%" class="font_beyaz"><a style="cursor:hand;" onclick ="javascript:sform('trunk');">Hat Listesi</a></td>
             </tr>
      <tr>
        <td height="20" align="center" colspan="4" width="85%" class="font_beyaz"><a style="cursor:hand;" onclick ="javascript:sform('city');">İl Kodları</a></td>
      </tr>
      <tr> 
        <td height="20" align="center" colspan="4" width="85%" class="font_beyaz"><a style="cursor:hand;" onclick ="javascript:sform('country');">Ülkeler</a></td>
      </tr>
      <tr> 
        <td height="20" align="center" colspan="4" width="85%" class="font_beyaz"><a style="cursor:hand;" onclick ="javascript:sform('special');">Özel Numaralar</a></td>
      </tr>
      </table>
    <?table_footer();?>
  </td>
</table>    
   </form>
<?page_footer(0);?>
<script LANGUAGE="javascript" src="/scripts/popup.js"></script>
<script language="javascript" src="/scripts/form_validate.js"></script>
<script language="JavaScript" type="text/javascript">
    function sform(mytype){
        popup('','report_screen',800,600)
    document.all('SITE_ID').disabled=false;  
       document.all('system_search').action = 'report_system_prn.php?type=' + mytype;
        document.all('system_search').target= 'report_screen';
        document.all('system_search').submit();
  }
</script>