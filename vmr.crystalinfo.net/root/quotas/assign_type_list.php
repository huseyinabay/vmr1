<?
   require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
   $cUtility = new Utility();
   $cdb = new db_layer();
   require_valid_login();
   $conn = $cdb->getConnection();
?>
<html>
<body bgcolor="#F0F8FF">
<?
   cc_page_meta();
   if ($type=="ext"){
         $sql_str = "SELECT EXT_ID AS FIELD1,EXT_NO AS FIELD2, DESCRIPTION AS FIELD3 FROM EXTENTIONS WHERE SITE_ID = $site_id";
       $header="Dahili Listesi";  
   }else if ($type=="dept" || $type=="dept_memb"){
         $sql_str = "SELECT DEPT_ID AS FIELD1,DEPT_NAME AS FIELD2, NULL AS FIELD3 FROM DEPTS WHERE SITE_ID = $site_id";
       $header="Departman Listesi";    
   }else if ($type=="auth"){
        $sql_str = "SELECT AUTH_CODE_ID AS FIELD1,AUTH_CODE AS FIELD2, AUTH_CODE_DESC AS FIELD3 FROM AUTH_CODES WHERE SITE_ID = $site_id";
       $header="Auth. Kod Listesi";    
   }else{
    table_in_table_header("Detayları","100%");
    table_in_table_footer(0);
    exit;
   }
     if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
        print_error($error_msg);
        exit;
     }
?>

<?

   echo "<center>";
   table_in_table_header($header,"100%");
   echo "</center>";?>
   <table align="center" cellspacing="2" cellpddding="1" border="0" width="100%">
      <?while ($row = mysqli_fetch_object($result)){?>   
      <tr>
         <td width="15%" class="bg_acik_f_koyu"><a href="#" onclick="send_value('<?=$row->FIELD1 ?>')"> <?=$row->FIELD1; ?></a></td>
         <td width="85%" class="bg_acik_f_acik"><?=$row->FIELD2?> - <?=$row->FIELD3?></td>
      </tr>
      <?}?>
   </table>
 <?table_in_table_footer();?>  
<script>
   function set_me(myval){
    window.parent.document.all('obj_id').value = myval;
  }
   
   function send_value(my_value){
      var my_type = '<?=$type?>';
      set_me(my_value)

    if (my_type=='ext')
      window.parent.document.all('EXT_ID').value = my_value;     
      else if (my_type=='dept' || my_type=='dept_memb') 
            window.parent.document.all('DEPT_ID').value = my_value;
      else if (my_type=='auth')       
            window.parent.document.all('AUTH_CODE_ID').value = my_value;
   } 
</script>

</body>
</html>   