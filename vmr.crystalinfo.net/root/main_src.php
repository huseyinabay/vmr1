<?
   //require_once(dirname($DOCUMENT_ROOT)."/cgi-bin/functions.php");
   require_once $_SERVER['DOCUMENT_ROOT'].'/cgi-bin/functions.php';
   $cUtility = new Utility();
   $cdb = new db_layer();
   require_valid_login();
   
   
      //ini_set('display_errors', 'On');
	  //error_reporting(E_ALL);
   
   
   
   if (!defined("IMAGE_ROOT")){
      define("IMAGE_ROOT", "/images/");
   }  
   if($act != "src"){
        print_error("Hatalı Parametre Girildi.");
        exit;
   }
   if($src_str==""){
        print_error("Arama Kriteri Girmediniz.");
        exit;
   }
     if ($p=="" || $p < 1)
       $p = 1;
   cc_page_meta(0);
   page_header("");
?>

<form name="main_search" action="main_src.php?act=src" method=post>
    <input type="hidden" name="p" VALUE="<?echo $p?>">
    <input type="hidden" name="ORDERBY" value="<?=$ORDERBY?>">
    <table width="170"  border="0" cellspacing="0" cellpadding="0" bgcolor="358BCF">
      <tr> 
        <td width="22%"><img src="<?=IMAGE_ROOT?>ara1.gif" width="29" height="28"></td>
        <td width="78%" >
        <input type="text" name="src_str" size="12" CLASS="input1" value="<? =$src_str ?>">
        </td><td width="58%" >
        <a href="javascript:submit_form('main_search')">
         <img border="0" src="<?=IMAGE_ROOT?>ara2.gif"></a>
        </td>
      </tr>
    </table>
</form>
<?
   if ($act == "src") {
         $kriter = "";

         $kriter .= "USERS.SITE_ID = '".$_SESSION['site_id']."'";
         $kriter .= " AND USERS.NAME LIKE '%".$src_str."%'";
         $kriter .= " OR USERS.SURNAME LIKE '%".$src_str."%'";
         $kriter .= " OR USERS.GSM LIKE '%".$src_str."%'";
         $kriter .= " OR USERS.EMAIL LIKE '%".$src_str."%'";
         $kriter .= " OR USERS.POSITION LIKE '%".$src_str."%'";
         $kriter .= " OR DEPTS.DEPT_NAME LIKE '%".$src_str."%'";
         $kriter .= " OR EXTENTIONS.EXT_NO LIKE '%".$src_str."%'";
      
         $sql_str  = "SELECT USERS.USER_ID,USERS.NAME,USERS.SURNAME,USERS.GSM,
                        USERS.EMAIL,USERS.POSITION,DEPTS.DEPT_NAME,EXTENTIONS.EXT_NO
                      FROM USERS
                      LEFT JOIN DEPTS ON DEPTS.DEPT_ID = USERS.DEPT_ID
                      LEFT JOIN EXTENTIONS ON USERS.EXT_ID1 = EXTENTIONS.EXT_ID 
                        OR USERS.EXT_ID2 = EXTENTIONS.EXT_ID
                        OR USERS.EXT_ID3 = EXTENTIONS.EXT_ID
                      ";
         if ($kriter != "")
               $sql_str .= " WHERE ". $kriter;  
         if ($ORDERBY) {
               $sql_str .= " ORDER BY ". $ORDERBY ;
         }
//echo $sql_str;
//die;
         $rs = $cdb->get_Records($sql_str, $p, $page_size,  $pageCount, $recCount);    
if($recCount>0){
?>
<BR><BR>
<?
table_arama_header("93%");
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" bordercolor="60ADD1">
  <tr > 
    <td class="sonuc" HEIGHT="20" width="40%">Bulunan Kullanıcılar</td>
    <td class="sonuc" align="right" width="50%"><? echo $cdb->calc_current_page($p, $recCount, $page_size);?></td>
  </tr>
</table>

<table width="100%" border="0" cellspacing="0" cellpadding="0" ALIGN="left">
    <tr class="header_beyaz" >
        <td HEIGHT="20">ID</td>
        <td>Adı</td>
        <td>Soyadı</td>
        <td>GSM</td>
        <td>Departman</td>
        <td>Dahili</td>
    </tr>
<? 
   $i;
   while($row = mysqli_fetch_object($rs)){
         $i++;
         echo "<tr class=\"".bgc($i)."\">".CR;
         echo "  <td HEIGHT=20>$row->USER_ID</td> ".CR;
         echo "  <td>$row->NAME</td>".CR;
         echo "  <td>$row->SURNAME </td>".CR;
     echo "  <td>$row->GSM </td>".CR;
         echo "  <td>$row->DEPT_NAME</td>".CR;
         echo "  <td>$row->EXT_NO</td>".CR;
         echo "</tr>".CR;
       list_line(18);
   }
?>
</table>
<?
table_arama_footer();  
 }
 }
?>
<table width="80%">
    <tr>
        <td align="center">
        <?echo $cdb->get_paging($pageCount, $p, "main_search", $ORDERBY);?></td>
    </tr>
</table>
<?
   if ($act == "src") {
      $kriter = "";   
    $kriter .= "CONTACTS.SITE_ID = '".$_SESSION['site_id']."' ";
       if (!right_get("FIHRIST")){
      $IS_GLOBAL ="0";
      $USER_ID = $_SESSION["user_id"];
      $kriter .= " AND CONTACTS.USER_ID = '$USER_ID' ";
    }else{
      $IS_GLOBAL = "''";
        }
         $kriter .= " AND CONTACTS.IS_GLOBAL = $IS_GLOBAL AND (";
         $kriter .= " CONTACTS.NAME LIKE '%".$src_str."%'";
         $kriter .= " OR CONTACTS.SURNAME LIKE '%".$src_str."%'";
         $kriter .= " OR CONTACTS.COMPANY LIKE '%".$src_str."%'";
         $kriter .= " OR CONTACTS.PERSONAL_EMAIL LIKE '%".$src_str."%'";
         $kriter .= " OR CONTACTS.COMPANY_EMAIL LIKE '%".$src_str."%'";
         $kriter .= " OR PHONES.CITY_CODE LIKE '%".$src_str."%'";
         $kriter .= " OR PHONES.PHONE_NUMBER LIKE '%".$src_str."%')";
         
         $sql_str  = "SELECT CONTACTS.NAME,CONTACTS.SURNAME,CONTACTS.COMPANY,
                        CONTACTS.PERSONAL_EMAIL,CONTACTS.COMPANY_EMAIL,
                        PHONES.CITY_CODE,PHONES.PHONE_NUMBER,CONTACTS.CONTACT_ID 
                      FROM CONTACTS
                      LEFT JOIN PHONES ON CONTACTS.CONTACT_ID=PHONES.CONTACT_ID
                      ";
         
         if ($kriter != "")
               $sql_str .= " WHERE ". $kriter;  

         if ($ORDERBY) {
               $sql_str .= " ORDER BY ". $ORDERBY ;      
         }
//ECHO $sql_str;die;
         $rs = $cdb->get_Records($sql_str, $p, $page_size,  $pageCount, $recCount);    
if($recCount>0){

?>
<br><br>
<?
table_arama_header("95%");
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td class="sonuc" width="50%" height="20">Fihristten Bulunanlar</td>
    <td class="sonuc" align="right" width="50%" height="20"><? echo $cdb->calc_current_page($p, $recCount, $page_size);?></td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr class="header_beyaz">
        <td width="5%">ID</a></td>
        <td width="15%">Adı</td>
        <td width="15%">Soyadı</td>
        <td width="20%">Firma</td>
        <td width="20%">Şirket E-Mail</td>
        <td width="15%">Telefon</td>
    </tr>
<? 
   $i;
   while($row = mysqli_fetch_object($rs)){
         $i++;
        echo " <tr class=\"".bgc($i)."\">".CR;
       echo " <td height=\"20\">$row->CONTACT_ID</td> ".CR;
       echo " <td>$row->NAME</td>".CR;
       echo " <td>$row->SURNAME</td>".CR;
       echo " <td>$row->COMPANY</td>".CR;
       echo " <td>$row->COMPANY_EMAIL</td>".CR;
      echo " <td>$row->CITY_CODE"." "."$row->PHONE_NUMBER</td>".CR;
        echo "</tr>".CR;
       list_line(18);
   }
?>
</table>
<table width="80%" align="center">
    <tr>
        <td align="center">
        <?
        echo $cdb->get_paging($pageCount, $p, "main_search", $ORDERBY);
        ?></td>
    </tr>
</table>
<?table_arama_footer(); }}?>
<?page_footer("");?>
<script language="javascript" src="/scripts/form_validate.js"></script>
<script language="JavaScript" type="text/javascript">
<!--

    function submit_form(form_name, page, sortby){
        if(document.main_search.src_str.value==''){
            alert("Arama Kriteri Girmediniz!..");
        }else{
          document.all("ORDERBY").value = sortby;
          if (!sortby)
                document.all("ORDERBY").value = '';

          document.all("p").value = page;
          document.all(form_name).submit();
         }
     }
//-->
</script>
