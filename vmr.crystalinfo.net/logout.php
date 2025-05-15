<?php
session_start();
session_destroy();
unset($_SESSION["id"]);
unset($_SESSION["username"]);
unset($_SESSION["usertype"]);
header("location: index.php?msg=Sistemden%20başarı%20ile%20çıkış%20yaptınız!&type=information");
?>