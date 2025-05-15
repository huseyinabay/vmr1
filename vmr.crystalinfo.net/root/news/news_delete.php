<?PHP
   require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
   $cUtility = new Utility();
   $cdb = new db_layer();
   require_valid_login();
   $conn = $cdb->getConnection();

 $error_msg="Normal";
 
 if (isset($frm_sira_no)){ //1//
    // Bu haberi kaydeden kişi silme isteyen kişi midir
    
    if (!($cdb->execute_sql("SELECT USER_ID FROM NEWS WHERE SIRA_NO=".$frm_sira_no.";",$result,$error_msg))){
       print_error($error_msg);
       exit;
    }
    else {  //2//
      $row=mysqli_fetch_object($result);
 
      //// Başkasına ait bir mesaj

      if ($row->USER_ID <> $_SESSION["user_id"]){
         print_error("Bu mesajı giren kişi siz değilsiniz.<BR> <font color=\"#ff0000\"><b>Başkalarına</font></b> ait haberleri silemezsiniz.");
         exit;
      }
    } //2//

    if (!($cdb->execute_sql("DELETE FROM NEWS WHERE SIRA_NO=".$frm_sira_no.";",$result,$error_msg))){
       print_error($error_msg);
       exit;
    }
 } //1//
 else { 
   $error_msg="Silinecek olan haberin numarası belirtilmedi.";
   print_error($error_msg);
   exit;
 }  

 // Herşey normal bir şekilde ilerledi ise mesaj ver ve haberler 
 header("Location:"."news_list.php");


?>

<?php  ?>