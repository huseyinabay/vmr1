<?
   require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
   if (!defined("IMAGE_ROOT")){ // Note that it should be quoted
      define("IMAGE_ROOT", "/images/");
   }  
 ?>
<?cc_page_meta(0);
page_header();
echo "<br>";
table_header("Müşteri Özel Bölümü","40%");?>
<table awidth="100%" border="0" cellspacing="0" cellpadding="0" height="100%">
  <tr>
    <td align="center" valign="middle">Bu müşteri için özelleşmiş bir yapı yoktur.</td>
  </tr>
</table>
<?table_footer();
page_footer(0);?>
