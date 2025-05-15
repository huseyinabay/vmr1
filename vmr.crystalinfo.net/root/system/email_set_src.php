<?
     require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
     $cUtility = new Utility();
     $cdb = new db_layer();
     require_valid_login();

    check_right("SITE_ADMIN");
   
     if ($p=="" || $p < 1)
       $p = 1;

     $start = $cUtility->myMicrotime();

     cc_page_meta();
     echo "<center>";
     page_header();
     echo "<br><br>";
     table_header_mavi("Sunucu Arama","50%");
?>
<center>
       <form name="email_set_src" method="post" action="email_set_src.php?act=src">
         <input type="hidden" name="p" VALUE="<?echo (int)$p?>">
         <input type="hidden" name="ORDERBY" value="<?=$ORDERBY?>">
         <table cellpadding="0" cellspacing="0" border="0" width="60%">

		 
		 		<tr class="form">
                    <td title="Module" class="font_beyaz">İlgili Modül</td>
                    <td>
                        <select name="set_module" class="select1" Maxlength="15" style="width: 131px;" >
							<option VALUE="<?echo "";?>"><?echo "";?></option> 
							<option value="CRM">CRM</option> 
							<option value="CDR">CDR</option>
							<option value="ACD">ACD</option> 
							<option value="CCR">CCR</option>
							<option value="CSY">CSY</option>
                        </select>
                    </td>
                </tr>	
		 
		 
		      <tr> 
                    <td width="51%" class="font_beyaz">Kayıt İsmi</td>
                    <td width="50%"><input type="text" class="input1" name="set_name" Maxlength="30"></td>
              </tr>

              <tr> 
                    <td width="51%" class="font_beyaz">Sunucu Adresi</td>
                    <td width="50%"><input type="text" class="input1" name="set_server" Maxlength="30"></td>
              </tr>
              <tr>
                  <td colspan=2 align=center><br>
                    <a href="javascript:submit_form('email_set_src');"><img name="Image631" border="0" src="<?=IMAGE_ROOT?>ara.gif"></a>
                  </td>
              </tr>
              </tr>
        </table>
  <table width="100%"> 

  </table>        
       </form>
<?
   table_footer_mavi();
   if ($act == "src") {

         $sql_str  = "SELECT * FROM email_sets WHERE set_id=1";
                  
         $rs = $cdb->get_Records($sql_str, (int)$p, $page_size,  $pageCount, $recCount);    
         $stop = $cUtility->myMicrotime();

?>
<br><br>
<?
table_arama_header("75%");
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td class="sonuc" width="50%" height="20"><? echo $cdb->calc_current_page((int)$p, $recCount, $page_size);?></td>
    <td class="sonuc" align="right" width="50%" height="20"><? $cdb->show_time(($stop -$start)); ?></td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr class="header_beyaz">
        <td>Modül</td>
		<td>Kayıt İsmi</td>
		<td>Sunucu</td>
        <td>Kullanıcı Adı</td>
        <td>Protokol</td>
        <td>Port</td>
		<td>Güncelle</td>
    </tr>
<? 
   $i;
   while($row = mysqli_fetch_object($rs)){
      $i++;
       echo " <tr class=\"".bgc($i)."\">".CR;
	   echo " <td>$row->set_name</td>".CR;
	   echo " <td>$row->set_server</td>".CR;
	   echo " <td>".SUBSTR($row->set_user,0,25)."</td>".CR;	   
	   echo " <td>$row->set_protokol</td>".CR;
	   echo " <td>$row->set_port</td>".CR;
       echo " <td><a HREF=\"email_set.php?act=upd&id=1\">Güncelle</td>".CR;
      echo " </tr>".CR;
    list_line(18);
   }
?>
</table>
<?table_arama_footer();   }?>
<table width="80%" align="center">
    <tr>
        <td align="center">
        <?
        echo $cdb->get_paging($pageCount, (int)$p, "email_set_src", $ORDERBY);
        ?></td>
    </tr>
</table>
<?page_footer(0);?>
<script language="javascript" src="/scripts/form_validate.js"></script>
<script language="JavaScript" type="text/javascript">


    function submit_form(form_name, page, sortby){
          document.all("ORDERBY").value = sortby;
          if (!sortby)
                document.all("ORDERBY").value = '';

          document.all("p").value = page;
          document.all(form_name).submit();
    }

</script>
</html>

