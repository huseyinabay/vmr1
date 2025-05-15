<?
/******************************************************************************
'Master Project: CC
'PURPOSE: Contains the constants and the functions necessary for Logging And Auditing system 
'INCLUDED BY: All the ASP files that do database operaions and need logging
'FUNCTIONS: 

'  Enters a log into the LNA Systme.
'  1- lna_put_log(   
'      prm_DB_ID,      
'      prm_CLIENT_ID,    
'      prm_OPERATION,    
'      prm_TYPE, 
'      prm_ORIGIN, 
'      MyUserName, 
'      prm_TABLE_NAME, 
'      prm_IP, 
'      prm_MAIL_IT, 
'      prm_E_MAIL, 
'      prm_MSG
'   )

'      
'*****************************************************************************

*/

/*

'''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''
''''''''''''''''''''''  TYPES   '''''''''''''''''''''''''''''''''''
'''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''
*/
define("cSYS_INFO",     1); //        'SYS Info              System Bilgisi
define("cSYS_WARNING",  2); //        'SYS Warning           System Warning
define("cSYS_ERROR",    3); //        'SYS Error             System Error
define("cAPPL_INFO",    4); //        'Appl Info             Application Internal info
define("cAPPL_WARNING", 5); //        'Appl Warninng         Application Internal Warning
define("cAPPL_ERROR",   6); //        'Appl Error            Application Internal Error
define("cBL_INFO",      7); //        'BL Info               Business Logic Info
define("cBL_WARNING",   8); //        'BL Warning            Business Logic Warning
define("cBL_ERROR",     9); //        'BL Error              Business Logic Error
/*
'''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''
''''''''''''''''''''''  OPERATIONS   ''''''''''''''''''''''''''''''
'''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''
*/
define("cINSERT", 1  ); //     'Insert  Insert operation on a table
define("cUPDATE", 2   ); //     'Update  Update Operation on a table
define("cDELETE", 3   ); //     'Delete  delete operation on a table
define("cLOGIN" , 4   ); //     'Login to the system
define("cLOGOUT" , 5 ); //     'Logout from the system

/*

'''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''
''''''''''''''''''''''  ORIGINS      ''''''''''''''''''''''''''''''
'''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''

*/
define("cCLIENT" , 1 ) ;      //      'CLIENT    The message is coming from a client application
define("cTRIGGER" ,2 )  ;     //      'TRIGGER    Message is coming from a trigger
define("cAUDIT_SYSTEM" ,3  );  //      'AUDIT-SYSTEM  Message is coming from an Audit or a System 


$glbLnaTypes = Array();
//'Needed when displaying activities
$glbLnaTypes[1] = "Sistem Bilgisi" ;
$glbLnaTypes[2] = "Sistem Uyarısı";
$glbLnaTypes[3] = "Sistem Hatası";
$glbLnaTypes[4] = "Yazılım Bilgi";
$glbLnaTypes[5] = "Yazılım Uyarısı";
$glbLnaTypes[6] = "Yazılım Hatası";
$glbLnaTypes[7] = "İşleyiş Bilgisi";
$glbLnaTypes[8] = "İşleyiş Uyarısı";
$glbLnaTypes[9] = "İşleyiş HAtası";


$glbLnaOps = Array();
//'Needed when displaying activities
$glbLnaOps[1] = "Ekleme" ;
$glbLnaOps[2] = "Güncelleme";
$glbLnaOps[3] = "Silme";
$glbLnaOps[4] = "Login";
$glbLnaOps[5] = "Logout";


$glbLnaOpsImg = Array();
//' The images to display for the operations
$glbLnaOpsImg[1] = "yeni_kayit.gif" ;
$glbLnaOpsImg[2] = "guncelle.gif" ;
$glbLnaOpsImg[3] = "sil.gif" ;
$glbLnaOpsImg[4] = "syslogin.gif" ;
$glbLnaOpsImg[5] = "syslogout.gif" ;


$glbLnaOrigins = Array();
//'Needed when displaying activities
$glbLnaOrigins[1] = "Kullanıcı" ;
$glbLnaOrigins[2] = "Tetikleme" ;
$glbLnaOrigins[3] = "Audit/Sistem" ;
/*
''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''
'    Function Name  : lna_put_log
'    Description    : Puts logs into database
'    Creator      : ZEKARS - 05_10_2001
'    Parameters    :
'      prm_DB_ID,      :  The application ID of the currently used application(from session or globals)
'      prm_CLIENT_ID,    :  The client company id for which the appl is being user (from session or globals)
'      prm_OPERATION,    :  The Operation done; delete, insert, update etc.
'      prm_TYPE,      :  The type of the Log; Info, Warning, Error
'      prm_ORIGIN,      :  The origin(ator) of the log; Audit, Client Appl, Trigger
'      prm_USERNAME,    :  The caller's username (from session)
'      prm_TABLE_NAME,    :  The database table that the OPERATION is done on
'      prm_IP,        :  The IP of the client (from session)
'      prm_MAIL_IT,    :  1: Yes, mail is to be sent, 0: no mail sending
'      prm_E_MAIL,      :  e-mail address if MAIL_IT=1
'      prm_MSG        :  The body of the message
''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''

*/
function lna_put_log( 
        $prm_DB_ID, 
        $prm_CLIENT_ID,
        $prm_OPERATION, 
        $prm_TYPE, 
        $prm_ORIGIN, 
        $prm_USERNAME, 
        $prm_TABLE_NAME, 
        $prm_IP, 
        $prm_MAIL_IT, 
        $prm_E_MAIL, 
        $prm_MSG, 
        $prm_SPARE1, 
        $prm_SPARE2, 
        $prm_SPARE3 )  
 {
     $lna_params = Array(); // ' Input parameters Array.    

//    'command.CreateParameter (Name, Type, Direction, Size, Value)
  $lna_params[0]  = Array("DB_ID",       $prm_DB_ID,       cFldWQuote) ;
  $lna_params[1]  = Array("CLIENT_ID",   $prm_CLIENT_ID,   cFldWQuote) ; 
  $lna_params[2]  = Array("OPERATION",   $prm_OPERATION,   cFldWQuote);
  $lna_params[3]  = Array("TYPE",         $prm_TYPE,        cFldWQuote);
  $lna_params[4]  = Array("ORIGIN",       $prm_ORIGIN,      cFldWQuote);
  $lna_params[5]  = Array("TABLE_NAME",  $prm_TABLE_NAME,  cFldWQuote);
  $lna_params[6]  = Array("IP",          $prm_IP,          cFldWQuote);
  $lna_params[7]  = Array("MAIL_IT",     $prm_MAIL_IT,     cFldWQuote); 
  $lna_params[8]  = Array("E_MAIL",      $prm_E_MAIL,      cFldWQuote);
  $lna_params[9]  = Array("MSG",         $prm_MSG,         cFldWQuote);
  $lna_params[10] = Array("SPARE1",       $prm_SPARE1,      cFldWQuote);
  $lna_params[11] = Array("SPARE2",       $prm_SPARE2,      cFldWQuote);
  $lna_params[12] = Array("SPARE3",       $prm_SPARE3,      cFldWQuote);
  
    
      $sql_str =  $cDB->InsertString("LNA_LOGS", $lna_params);
      if (!($cDB->execute_sql($sql_str,$result,$error_msg)))
      {
           print_error($error_msg);
           exit;
      }
 }

?>


