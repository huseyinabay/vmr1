<?php
session_start(); // Use session variable on this page. This function must put on the top of page.
if (!isset($_SESSION['username']) || $_SESSION['usertype'] != 'admin') { // if session variable "username" does not exist.
    header("location: index.php?msg=Lütfen%20yetkili%20bir%20kullanıcı%20ile%20girin%20!"); // Re-direct to index.php
}
error_reporting(0);
include("lib/db.class.php");
include_once "config.php";
error_reporting(0);

?>