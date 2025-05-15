<?  //INCLUDES
   require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
  
   require_valid_login();

   $cdb = new db_layer();
   $conn = $cdb->getConnection();

  if (isset($pass1) or isset($pass2)){
    if ( ($pass1 == $pass2) and ($pass2 <> "") and (strlen($pass2) >= $MAX_PASS_LEN) ) {
        $sql_str = "UPDATE USERS SET PASSWORD = PASSWORD('$pass1') WHERE USER_ID ='$id' ;";

        if (!($cdb->execute_sql($sql_str,$haber_result,$error_msg))){ 
          print_error($error_msg);
          exit;
        }
      
       // logout the user for re-login
      //header("Location:/logout.php");
      echo "Şifreniz Başarıyla Değiştirildi, Tekrar Login Olabilirsiniz";
    
    } // There was a problem with the passwords
    else {
      print_error("Şifre giriş kurallarına uygun giriş yapmadınız.".NL.
                "Lütfen şifre giriş ekranında belirtilen kurallara uyunuz...");
      exit;
    }  
        
  
  }
  else{

   echo "<BR><BR><BR>";
   cc_page_meta(0);  

    if (empty($id) || $id==""){
      print_error("Hatalı Durum Oluştu!");
      exit;
    }  

  table_header("Şifre Değişikliği","100%");
?>

<form name="login" action="chgpsw.php" method="post">
<div align="center">  
<table class="menu" border="0" width="21%" height="123" cellspacing="0" cellpadding="0">
  <tr>
    <td class="subheader" width="13%" nowrap align="left" height="23">Yeni Şifre: </td>
    <td width="87%" nowrap height="23">
<input class ="input1" type="password" name="pass1" maxlength="15" size="20" value="">
    </td>
  </tr>
  <tr>
    <td class="subheader" width="13%" nowrap align="left" height="23">Yeni Şifre Tekrar: </font></td>
    <td width="87%" nowrap height="23">
<input class ="input1" type="password" name="pass2" maxlength="32" size="20">
<input type="hidden" name="id" value="<?=$id?>">
    </td>
  </tr>
  <tr>
    <td width="100%" nowrap colspan="2" height="59">
      <p align="center">
         <br>
         <input class="button1" type="submit" value="Değiştir" name="B1">

          
    </td>
  </tr>
</table>
         <HR>
<table CLASS=MENU>
  <tr>
    <td>
    <p align="center">
         <h2>Kurallar</h2>
        * Her iki şifreniz de <font color="#FF0000">aynı</font> olmalıdır<br>
        * Uzunluğu en az <font color="#FF0000"><? echo $MAX_PASS_LEN; ?>&nbsp;</font>karakter&nbsp; olmalıdır<br>
        * <font color="#FF0000">Boşluk</font> içermemelidir<br>
        * <font color="#FF0000">*()/&amp;%+^'!</font> gibi karakterler <font color="#FF0000">olmamalıdır</font></p>      </p>
    </td>
  </tr>
</table>

</div>
</form>

<?
  }
  
  table_footer();

?>