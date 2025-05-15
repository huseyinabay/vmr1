<?
   require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
   $cUtility = new Utility();
   $cdb = new db_layer();
   
   require_valid_login();

   if ($p=="" || $p < 1)
       $p = 1;

   $start = $cUtility->myMicrotime();

   	cc_page_meta();
   	echo "<center>";
   	page_header();
   	echo "<br><br>";
   	table_header_mavi("Hat Analizi","50%");
?>
<script LANGUAGE="javascript" src="/scripts/popup.js"></script>
<center>
   		<form name="trunk_analiz" method="post" action="trunk_analiz.php?act=src">
       	<input type="hidden" name="p" VALUE="<?echo $p?>">
       	<input type="hidden" name="ORDERBY" value="<?=$ORDERBY?>">
       	<table cellpadding="0" cellspacing="0" border="0" width="60%">
          	<tr> 
                    <td width="30%" class="font_beyaz">Tarih</td>
                    <td width="80%" NOWRAP>
                      	<input type="teXt" class="input1" name="DATE" VALUE="<?echo $row->DATE?>" Maxlength="10">  
                      	<img style="cursor:hand" SRC="<?=IMAGE_ROOT?>takvim_icon.gif" onclick="javascript:show_calendar(document.all('DATE').name,null,null,null,event.screenX,event.screenY,1)">
                       	<img style="cursor:hand" SRC="<?=IMAGE_ROOT?>sil_icon.gif" onclick="javascript:document.all('DATE').value=''">
                    </td>
          	</tr>
           	<tr> 
                    <td width="50%" class="font_beyaz" NOWRAP>Hat Adedi</td>
                    <td width="50%"><input type="text" class="input1" name="DEPT_NAME" VALUE="<?echo $DEPT_NAME?>" size="4" Maxlength="2"></td>
          	</tr>
           	<tr> 
                    <td width="50%" class="font_beyaz"></td>
                    <td width="50%"><input type="submit" class="input1" name="Göster" VALUE="Göster" size="15" Maxlength="20"></td>
          	</tr>
      	</table>
   		</form>
