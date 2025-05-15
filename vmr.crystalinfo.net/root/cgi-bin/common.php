<?php
 // COMMON.PHP : Genel tanımları vb içerir
 //require_once ("site.cnf");
 //$_SERVER['DOCUMENT_ROOT']     = "/";
 require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/site.cnf";
 //require_once ('/usr/local/wwwroot/multi.crystalinfo.net/root/cgi-bin/site.cnf');
 
    //ini_set('display_errors', 'On');
    //error_reporting(E_ALL);
	//error_reporting(E_ALL ^ E_NOTICE);

//$SITE_ID = $_SESSION['site_id'];


 
 $ERR = $_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF']."?". $_SERVER['QUERY_STRING'];
 if(!$_SERVER['SERVER_NAME']) $_SERVER['SERVER_NAME'] = "multi.crystalinfo.net";
 $_SERVER['HTTP_HOST']   = "https://".$_SERVER['SERVER_NAME'];
 $ADMIN_MAIL  = "crystalinfo@netser.com.tr";

 define('SESSION_TIME', 30);

 $LOGIN_FORM  = "loginfrm.php";
 $LOGIN_LOGS_TABLE = "LOGIN_LOGS";
 $MAX_PASS_LEN = 5;
 $page_size = 10;

 // The part of the IP to check for validity
 $IP_CHK_PART = "10.";

 # COLOR PATTERNS FOR CHARTS
 $COLOR_LIST = Array( 0xF9688E, 0x6EB6F5, 0xF9C62D, 0xCAE7BB, 0xB2B2B2, 0x7FC55C, 0xCADFF1, 0xFEEF95, 0xF7A9BD, 0xCFCFCF, 0x5AB12F, 0x4D85E1, 0xFF592E, 0xE61C50, 0x818181, 0x9081EB );
 $COLOR_BACKGROUND = Array(0xedf5fa, 0xf8fbfd);
 $MONTH_LIST = Array("Ocak", "Şubat", "Mart", "Nisan", "Mayıs", "Haziran", "Temmuz", "Ağustos", "Eylül", "Ekim", "Kasım", "Aralık");
 $CHART_ROOT = "/usr/local/wwwroot/multi.crystalinfo.net/root/cgi_bin/";

 define('FOTO_ROOT',dirname($_SERVER['DOCUMENT_ROOT'])."/files/");
 define('HELP','/help/');
 define('CSS_ROOT','/');
 

 define ("NL","<BR>\n");                //// New line .Constant olması heryerde gorulebilmesini saglar
 define ("CR","\n");
 define ("TRUE",1);
 define ("FALSE",0);
 define ("FUNC_ROOT",dirname($_SERVER['DOCUMENT_ROOT'])."/cgi-bin/");

 // YPD Sabitleri
 define ("YPD_BOS", 1);     // Dosya Bosta
 define ("YPD_ISLEM", 2);   // Dosya Isleniyor
 define ("YPD_TAMAM", 3);   // Dosya Tamamlandi
 define ("YPD_IPTAL", 4);   // Dosya Iptal Edildi

 // USER LEVEL Sabitleri
 define ("USR_ADMIN", 0);    // ADMIN
 define ("USR_MERKEZ", 2);   // MERKEZ
 define ("USR_MUSTERI", 3);  // MUSTERI
 define ("DBDB", "localhost");  // MUSTERI

 ///////////////////////////////// class Session /////////////////////////////////////
class Session {
var $sql_server   =  DB_IP;         //// MySQL server to connect to
var $sql_username = DB_USER;              //// Username to connect to database with
var $sql_password = DB_PWD;           //// Password to connect to database with
var $sql_database = DB_NAME;              //// Password to connect to database with
var $account_tbl  = "USERS";          //// The table where accounts reside

//-------------------------- session bilgilerinde tutulacak olankolonların adları ------------------
var $user_id_col    = "USER_ID";       //// User id column's name
var $site_id_col    = "SITE_ID";       //// Site id column's name
var $username_col   = "USERNAME";     //// usename column's name
var $on_ek_col     = "ON_EK";
var $passwd_col     = "PASSWORD";      //// Password column name
var $adi_col        = "NAME";                //// Kullanıcının adı
var $soyadi_col     = "SURNAME";             //// Kullanıcının soyadı
var $disabled_col   = "DISABLED";            //// Departman numarası

var $logged_in  = 0;              //// Determines if the session has a logged in user
var $last_login_id=0;

//----------- The variables to be held in SESSION array ----------------------
var $user_id   = 0;              //// The user id of the logged in user
var $site_id   = 0;              //// The site id of the logged in user
var $username  = "";             //// The logged in usename
var $adi           = "";         //// Kullanıcının adı
var $soyadi        = "";         //// Kullanıcının soyadı
var $level         = "";         //// Kullanıcının seviyesi 0-General Manager, 1-Managers, 3-Normal Users
var $musteri_id = "";
var $musteri_sirket_id = "";
var $company_name ="";

var $_SESSION_started = 0;         //// Determines if the session is started by start()
var $error_no = 0;                //// Error code for any error occuring  in the class
var $connected_to_db = 0;         //// Determines if a connection to the database is made

var $session_started = 0;

var $form_user = "";              //// The username entered by the user into the login form
var $form_passwd = "";            //// The password entered by the user into the login form
var $form_company_code = "";
var $form_ip = "";
var $link = "";

var $err_messages = array (0=>"Normal",
                           1=>"Kullanıcı adı sistemde bulunamadı...",
                           2=>"Hatalı şifre girişi",
                           3=>"Departman adı sistemde bulunamadı.",
                           4=>"Kullanıcı adı deaktive edilmiş durumda.",
                           5=>"Bağlı olduğununz IP adresinden sisteme giriş yapamazsınız.");

/*********************** MY_SESSION_START ***************************
 * Really starts the session                                        *
 ********************************************************************/
function log_login_request($user_id,$loggged_in=TRUE, $conn)
{
  global $LOGIN_LOGS_TABLE;
  //global $_SERVER['REMOTE_ADDR'];
  $_SERVER['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'];
  //global $_SESSION;
  //$link = $this->pconnect();
  $conn = mysqli_connect("localhost","crinfo","SiGmA*19","MCRYSTALINFONE");

    $success = ($loggged_in==TRUE ? "Y":"N");
    $curr_date = date("Y-m-d");
    $curr_time = date("H:i:s",time());
    $touch_time= time();
    $query = "INSERT INTO $LOGIN_LOGS_TABLE(USER_ID,LOGIN_DATE, LOGIN_TIME, SUCCESS, IP, LOGIN_TIMESTAMP, ACTIVE_TIME) VALUES ".
            "($user_id,\"$curr_date\",\"$curr_time\",\"$success\",\"$_SERVER[REMOTE_ADDR]\",\"$touch_time\",\"0\")";
     mysqli_query($conn, $query);

     //last_id is used to check, if one user logs in more than one
    $query = "SELECT ID FROM LOGIN_LOGS WHERE LOGIN_TIMESTAMP = $touch_time";
    $result = mysqli_query($conn,$query);
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    $this->last_login_id = $row["ID"];
	
	//  $this->log_login_request($row[$this->user_id_col],FALSE, $link);
	//print_r($row["ID"]);
	//echo $_SESSION["last_id"];
	}

/*********************** MY_SESSION_START ***************************
 * Really starts the session                                        *
 ********************************************************************/
function my_session_start(){
  session_start();
}

/*********************** TEST_LOGIN *********************************
 * Tests if there is a legel login in the sessions database         *
 ********************************************************************/
function test_login(){

  $this->my_session_start();

  $this->start();
}

/*********************** START **************************************
 * The method that starts the session                               *
 ********************************************************************/
function start($new_old="old"){
 //// The array that keep everything about the session
 //global $_SESSION;

 if ($this->session_started){
  $this->log_msg("Sessin:start-Start() called for already started session...");
 }
 else{ //// Session is not started yet...
   if($new_old=="old"){ //// Trying to revive an existing session.

     $this->session_started = 1;
   /*  if (!$this->checkIE())
     {
        $this->logged_in  = FALSE;
        session_destroy();
        $this->session_started = 0;
        header("Location:".$_SERVER['DOCUMENT_ROOT']."loginfrm.php");
     }
    */ 
     if (isset($_SESSION)){
       $this->logged_in           = TRUE;
       $this->user_id             = $_SESSION["user_id"];
       $this->username            = $_SESSION["username"];
       $this->adi                 = $_SESSION["adi"];
       $this->soyadi              = $_SESSION["soyadi"];
       $this->site_id             = $_SESSION["site_id"];
     }
     else{
       $this->logged_in  = FALSE;
       session_destroy();
       $this->session_started = 0;
     }
   }
   elseif ($new_old=="new"){
      //// The authentication tests are all passed. ıt seems so the
      //// session variables are set, except session id. Register them

      $this->session_started = TRUE;

      //session_register("SESSION");

      //// Fill-in all the real session vars with the authenticatioın data
      $_SESSION["user_id"]        = $this->user_id;
      $_SESSION["username"]       = $this->username;
      $_SESSION["adi"]            = $this->adi;
      $_SESSION["soyadi"]         = $this->soyadi;
      $_SESSION["site_id"]        = $this->site_id;
      $_SESSION["last_id"]        = $this->last_login_id;
	  
   }
   else{ //// neither "new"  nor "old"
     $this->log_msg("Sessin:start-start called with an unknown parameter...");
   }

 } //// session is was not started...

}

/*********************** LOGIN **************************************
 * The method that really authenticates the user and sets variables *
 ********************************************************************/
function login(){
	
	
       $_SERVER['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR']; //???? IP checks for security will be applied
       $link = $this->pconnect();
       //// Produce the query
      $lookup = " SELECT ".$this->account_tbl.".* FROM ".$this->account_tbl.
                  " WHERE ".$this->account_tbl.".".$this->username_col." = '".$this->form_user."' 
                  AND ".$this->account_tbl.".".$this->passwd_col." = PASSWORD('".$this->form_passwd."')";
	 
	 $result = mysqli_query($link,$lookup);

     if (mysqli_num_rows($result) != 1){
       $this->error_no = 1;
       $this->logged_in  = FALSE;
	   $row = null;
     }else{
		 
       $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
	   //echo "login fnc :".$lookup;die;
       if ($row[$this->disabled_col] == "N") {    // 1 //
         $this->logged_in  = TRUE;
         $this->user_id    = $row[$this->user_id_col];
         $this->username   = $row[$this->username_col];
         $this->adi        = $row[$this->adi_col];
         $this->soyadi     = $row[$this->soyadi_col];
         $this->site_id    = $row[$this->site_id_col];
   
       }else //// The account is disabled
         $this->error_no = 4;
     }
	 
	     if ($this->error_no)
           $this->log_login_request($row == null? 0 : $row[$this->user_id_col],FALSE, $link);
         else
           $this->log_login_request($row == null? 0 :$row[$this->user_id_col],TRUE, $link);
	   
	 
	 //echo "login fnc :".$lookup;die;
     return (!$this->error_no);
}




/*********************** NEW_LOGIN **********************************
 * A new user for login came in with her/his username and password  *
 * The parameters ara all comming from the login form               *
 ********************************************************************/
function login_request($frm_user, $frm_passwd, $frm_company_code, $frm_browser_ip,$frm_mobil="H"){

  //// Put the user provided info into class variables

  $this->form_user        = $frm_user;
  $this->form_passwd      = $frm_passwd;
  $this->form_company_code = $frm_company_code;
  $this->form_ip          = $frm_browser_ip;
  $this->mobil            = $frm_mobil;  

	//if($this->logged_in==FALSE) {
		  if ($this->login()) {

			$this->my_session_start();
			$this->start("new");

		  }
		  else
			$this->log_msg("Sessions:login_request()-".$this->err_message()."...");
	//}

}


/*********************** DESTROY ************************************
 * Destroys the started session and sets some related attributed    *
 ********************************************************************/
function destroy(){

  if ($this->session_started){
    session_destroy();
    $this->session_started = 0;
  }
  else{
      $this->log_msg("Session:destroy - destroy() called for a not started session...");
  }
}

/*********************** LOG_MSG ***********************************
 * Puts the log message into the log table for trace purposes       *
 ********************************************************************/
function log_msg($log_msg_text){

  if (!$this->connected_to_db)
     $this->pconnect();

  $log_date = date("Y-m-d");
  $log_time = date("H:i:s",time());
  mysqli_query($this->pconnect(),"INSERT INTO SESS_LOGS (LOG_DATE,LOG_TIME,MESSAGE) VALUES (\"$log_date\",\"$log_time\",\"$log_msg_text\")");

}

/*********************** CHECK IE5 **********************************
 * Browser'in IE5 ve ustu olup olmadigini kontrol et                *
 ********************************************************************/
function checkIE(){
    //global $HTTP_USER_AGENT;
	$HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];
    if (preg_match("/MSIE (\d)/i", $HTTP_USER_AGENT, $m) && $m[1] >= 5) { return TRUE; }
    else { return FALSE; }
}

/*********************** PCONNECT ***********************************
 * Make a persistent connection to the the authentication database  *
 * and select the database to connect to.                           *
 ********************************************************************/
function pconnect(){
  if (($this->connected_to_db)==0){
	 $this->link = mysqli_connect($this->sql_server,$this->sql_username,$this->sql_password,DB_NAME); 
    //$link = mysqli_connect($this->sql_server,$this->sql_username,$this->sql_password,DB_NAME);
    //mysqli_select_db($this->sql_database);

    $this->connected_to_db = 1;
	return $this->link;
  }
  else
    //$this->log_msg("Session:pconnect-connect() called for an already connected session...");
	return $this->link;
}

/*********************** ERR_MESSAGE ********************************
 * Returns the text of the error that has occured in the class      *
 ********************************************************************/
function err_message(){
  return $this->err_messages[$this->error_no];

}

} //// end of class Session




/*
//Set the session timeout for 2 seconds
$timeout = 10;

//Set the maxlifetime of the session
ini_set( "session.gc_maxlifetime", $timeout );

//Set the cookie lifetime of the session
ini_set( "session.cookie_lifetime", $timeout );

//Start a new session
session_start();

//Set the default session name
$s_name = session_name();

//Check the session exists or not
if(isset( $_COOKIE[ $s_name ] )) {
    setcookie( $s_name, $_COOKIE[ $s_name ], time() + $timeout, '/' );

    echo "Session is created for $s_name.<br/>";
} else {
    echo "Session is expired.<br/>";
}




*/





?>