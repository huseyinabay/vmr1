<?php
	require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/site.cnf";
	//require_once '/usr/local/wwwroot/multi.crystalinfo.net/root/cgi-bin/site.cnf';
	
   //ini_set('display_errors', 'On');
   //error_reporting(E_ALL);

 define("cFldNotNULL",      100);
 define("cFldWQuote",       1);
 define("cFldWoQuote",      3);
 define("cFldDate",         5);
 define("cFldDateTime",     7);
 define("cFldBit",          9);    
 define("cFldIdentity",     11);
 define("cFldReqSeperator", 50);
 define("cReqWQuote",       51);
 define("cReqWoQuote",      53);
 define("cReqDate",         55);
 define("cReqDatetime",     57);
 define("cReqBit",          59);       

 define ("cglbCLIENT_ID",9); // USER_COMPANIES TABLE_ID FOR AIG 

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
define("cUPDATE", 2  ); //     'Update  Update Operation on a table
define("cDELETE", 3  ); //     'Delete  delete operation on a table
define("cLOGIN" , 4  ); //     'Login to the system
define("cLOGOUT", 5  ); //     'Logout from the system

/*

'''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''
''''''''''''''''''''''  ORIGINS      ''''''''''''''''''''''''''''''
'''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''

*/
define("cCLIENT" , 1 ) ;      //      'CLIENT    The message is coming from a client application
define("cTRIGGER" ,2 )  ;     //      'TRIGGER    Message is coming from a trigger
define("cAUDIT_SYSTEM" ,3  );  //      'AUDIT-SYSTEM  Message is coming from an Audit or a System 

 
 
 
  // todo
  //_________________________________
  //1- file operation class
  //2- date time operation class
  //3- paging class
  //4- user controls (header, footer, menu function)
  //________________________________


 class db_layer{

        function getConnection()
       {

$conn = mysqli_connect(DB_IP ,DB_USER ,DB_PWD ,DB_NAME) or die("Could not connect database");
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }
mysqli_select_db($conn, DB_NAME);
//mysqli_query($conn, "SET NAMES 'Latin5';");
mysqli_query($conn, "SET NAMES UTF8");
//echo "Connection opened";
   
	return($conn);	
//mysqli_close($conn);		

       }
      

function execute_sql($arg_sql,&$arg_result,&$arg_error_msg){
   $cappl = new application ; 
   $conn = $this->getConnection();
 //  echo $arg_sql;
   if (!($arg_result = mysqli_query($conn, $arg_sql))){
     $arg_error_msg = "Veritabanı erişiminde bir problem yaşandı".NL."Hata : ".mysqli_error($conn).NL.NL.
                      "SQL = [".$arg_sql."]";
     $arg_sql1= $arg_sql."##".mysqli_error($conn);
	 echo $arg_error_msg;
     //$cAppl->lna_put_log($prm_DB_ID, $prm_CLIENT_ID, $prm_OPERATION, $prm_TYPE, $prm_ORIGIN, $prm_USERNAME, $prm_TABLE_NAME, $prm_IP, $prm_MAIL_IT, $prm_E_MAIL, $prm_MSG, $prm_SPARE1, $prm_SPARE2, $prm_SPARE3 );
     $cappl->lna_put_log('1', cCLIENT, cINSERT, cSYS_ERROR, cCLIENT, 'SYSTEM', "DB_ERR", $_SERVER['REMOTE_ADDR'], 0, "", addslashes(nl2br($arg_sql1)), "", "", "" );

	mysqli_close($conn);		
     return FALSE;
   }
   else {
     return TRUE;
     // For DELETE and UPDATE statements the result set is empty
   } 
}
 

function execute_insert($arg_sql,&$arg_result,&$arg_error_msg){
   $cappl = new application ; 
   $conn = $this->getConnection();
   //echo $arg_sql;
   if (!($arg_result = mysqli_query($conn, $arg_sql))){
     $arg_error_msg = "Veritabanı erişiminde bir problem yaşandı".NL."Hata : ".mysqli_error($conn).NL.NL.
                      "SQL = [".$arg_sql."]";
     $arg_sql1= $arg_sql."##".mysqli_error($conn);
	 echo $arg_error_msg;
     //$cAppl->lna_put_log($prm_DB_ID, $prm_CLIENT_ID, $prm_OPERATION, $prm_TYPE, $prm_ORIGIN, $prm_USERNAME, $prm_TABLE_NAME, $prm_IP, $prm_MAIL_IT, $prm_E_MAIL, $prm_MSG, $prm_SPARE1, $prm_SPARE2, $prm_SPARE3 );
     $cappl->lna_put_log('1', cCLIENT, cINSERT, cSYS_ERROR, cCLIENT, 'SYSTEM', "DB_ERR", $_SERVER['REMOTE_ADDR'], 0, "", addslashes(nl2br($arg_sql1)), "", "", "" );

	mysqli_close($conn);		
     return FALSE;
   }
   else {
     return mysqli_insert_id($conn);
     // For DELETE and UPDATE statements the result set is empty
   } 
}
    //----------------------------------------------------------------------
     //Function : get_Records
     //Purpose : takes an sql and return the portion of record
     // @$strSQL : sql string 
     // @$p : current page 
     // @$page_size : how many record will be shown on a page 
     // @&$pageCount :(output parameter) : howmany page will be get 
     //----------------------------------------------------------------------
      function get_Records($strSQL, $p, $page_size, &$pageCount, &$recCount)
       {
           $cnn = $this->getConnection();
           if ($p=="" || $p < 1)
              $p = 1;
              
           $tempsql = $strSQL. " LIMIT 0,200";
           $rs = mysqli_query($cnn, $tempsql);
           $num_rows = mysqli_num_rows($rs);


           $startFrom = (((int)$p-1) * $page_size) ;
           
           if ($startFrom > $num_rows) { $p = 1; }
           
           $tmpPageSize = $page_size;
           
           if (($startFrom + $page_size)>200) 
              $tmpPageSize = (200 - $startFrom);
               
           // if page and page size bigger than zero           
           if ($p>0 && $page_size>0)
           { 
              $strSQL .= " LIMIT " . (((int)$p-1) * $page_size) . " ,". $tmpPageSize; 
              $rs = mysqli_query($cnn, $strSQL);
           }
           if ($page_size>0)
               $pageCount =  ceil($num_rows / $page_size);
           else
               $pageCount = 0;   
           $recCount = $num_rows;
           return($rs);
		   mysqli_close($conn);	
       }
       
       function get_paging($page_count, $p, $form_name,$ORDERBY="")
       {
           if ($p > $page_count)
               $p = 1;
                  
           $first = 1; // first page 

           if ($p > 1) // previous page
               $prev = $p - 1;

           if ($p < $page_count) //next page   
               $next = $p + 1;

           $last = $page_count; // last page
           $return_str = "<table cellpadding=0 cellspacing=0><tr>";
           if ($p >1 ) {
              $return_str .=  "<td><a href=\"javascript:submit_form('$form_name','$first','$ORDERBY')\" onMouseOut=\"MM_swapImgRestore()\" onMouseOver=\"MM_swapImage('Image44','','".IMAGE_ROOT."geri1_over.gif',1)\">
              <img name=\"Image44\" border=\"0\" src=\"".IMAGE_ROOT."geri1.gif\" width=\"15\" height=\"15\" hspace=\"3\"></a></td>";
              $return_str .=  "<td><a href=\"javascript:submit_form('$form_name','$prev','$ORDERBY')\" onMouseOut=\"MM_swapImgRestore()\" onMouseOver=\"MM_swapImage('Image45','','".IMAGE_ROOT."geri_over.gif',1)\">
              <img name=\"Image45\" border=\"0\" src=\"".IMAGE_ROOT."geri.gif\" width=\"15\" height=\"15\" hspace=\"3\"></a></td>";
           }
//           else {
//               $return_str .=  "";
//           }
           $return_str .=  "<td class=\"text2\">";
           for ($u=1;$u<=$page_count;$u++){
             if ($u==$p){
               $return_str .= "<font color=\"red\"><b>[" .$p . "]</b></font>";  
             }else{
               $return_str .= "&nbsp;<a href=\"javascript:submit_form('$form_name','$u','$ORDERBY')\"><b>[" .$u . "]</b></a>&nbsp;";  
             }
           
           }
           $return_str .=  "</font></td>";  
           
           if ($p < $page_count ) {
              $return_str .=  "<td><a href=\"javascript:submit_form('$form_name','$next','$ORDERBY')\" onMouseOut=\"MM_swapImgRestore()\" onMouseOver=\"MM_swapImage('Image46','','".IMAGE_ROOT."ileri_over.gif',1)\">
              <img name=\"Image46\" border=\"0\" src=\"".IMAGE_ROOT."ileri.gif\" width=\"15\" height=\"15\" hspace=\"3\"></a></td>";
              $return_str .=  "<td><a href=\"javascript:submit_form('$form_name','$last','$ORDERBY')\" onMouseOut=\"MM_swapImgRestore()\" onMouseOver=\"MM_swapImage('Image47','','".IMAGE_ROOT."ileri1_over.gif',1)\">
              <img name=\"Image47\" border=\"0\" src=\"".IMAGE_ROOT."ileri1.gif\" width=\"15\" height=\"15\" hspace=\"3\"></a></td>";
           }
//           else {
//               $return_str .=  " > ";
//           }
           $return_str .=  "</tr></table>";
           return($return_str);
       }
       
       ######################
       # Show search time
       ######################
       function show_time($time) {
        return printf("Arama  %.4f saniye sürdü",($time));
       }
       ////////////////////////////////////////////////////////////////////////////
       // calc_current_page(CurPage, recordCount, PageSize) : This function used in search forms to show current record range
       //    CURPAGE     : current page being displayed
       //    RECORDCOUNT : Total record which is found at the end of search
       //    PAGESIZE    : Howmany record will be seen on a page
       //  SEYKAY (04.10.2001)
       ////////////////////////////////////////////////////////////////////////////
       function calc_current_page($curPage, $recordCount, $pageSize)
       {
           $page_temp = ((int)$curPage -1);

           if ($recordCount ==0)
              return("0 kayıt bulundu..!");
    
           if ($recordCount =="")
              return(false);
    
           if ($recordCount < $pageSize)
              $pageSize = $recordCount;  
    
           if ($recordCount < ($curPage * $pageSize))
              $lastP = $recordCount;
           else
              $lastP = ($curPage * $pageSize);
    $firstP = $recordCount - $pageSize;
           if ($recordCount < ($page_temp * $pageSize))
              $firstP = $recordCount - $pageSize;
           if ($firstP < 0) 
              $firstP = "0" ; 
           else
              $firstP = ($page_temp * $pageSize); 
     
           $str = "<b>" . $recordCount . " </b> Sonuç içinde  <b>" .  $firstP . " - " . $lastP  . "</b>  arası sonuçlar";
           return($str);
       }    

       function  UpdateString($strTableName, $arOfValues)
       {
         // $strChanges, $strReq, $strTemp, $strSQL, $i, $notNULL, $chkValue;
         // $fieldName, $dataType, $nullability, $val;
      
             for($i=0; $i<sizeof($arOfValues);$i++)
             {    
                  $fieldName = $arOfValues[$i][0];
                  $val = $arOfValues[$i][1];
                  $dataType = $arOfValues[$i][2];
                  
                  $notNULL = false;                  
                      //Set Nullability and return to base datatype
                  if ($dataType > cFldNotNULL)
                  { 
                      $notNULL = true;
                      $dataType = $dataType - cFldNotNULL;
                  }
                          //'Check NULL value
                  If ($val == "" && $dataType != cFldBit)
                  {    
                        //'and Not cFldNotNULL
                        //The value is empty and it is not a yes/no field.
                        //Set the value to NULL.
                        $strTemp = $fieldName . " = NULL ";
                  }else{
                        switch ($dataType)
                        { 
                             case cFldWQuote:
                             case cReqWQuote:
//                                 $strTemp = $fieldName . " = " . chr(39) . str_replace(chr(39),chr(39).chr(39), $val) . chr(39);
                                 $strTemp = $fieldName . " = '$val'";
                                 break;                                       
                             case cFldWoQuote:
                             case cReqWoQuote:
                                 $strTemp = $fieldName . " = " . $val;
                                 break;
                             case cFldDate:
                             case cReqDate:
                             case cFldDateTime:
                             case cReqDateTime:
                                 if ($val != "")
                                     $strTemp = $fieldName . " = '" . $val . "'" ;
                                 else 
                                     $strTemp = $fieldName . " = NULL " ;
                                 break;    
                             case cFldBit:
                             case cReqBit:
                                 If ($val == "" || $val == "0" || $val == "false" )
                                     $chkValue = 0;
                                 else
                                     $chkValue = 1;
                                 break;
                                 $strTemp = $fieldName . " = " . $chkValue;
                           } //end of switch
                      
                         } // end of if

                        if ($dataType < cFldReqSeperator) //Means this is a data field
                            $strChanges = $strChanges . ", " . $strTemp;
                        else
                            $strReq = $strReq . ", " . $strTemp;
                        $strTemp = "";


                   } // end of for

                    $strChanges = $this->StripFirstCharacter($strChanges);
                    $strReq = $this->StripFirstCharacter($strReq);

                    if (strlen($strChanges)>0) //         'Handle errors like empty string variable
                        $strSQL = "UPDATE " . $strTableName . " SET " . $strChanges;
                    if (strlen($strReq)>0)
                        $strSQL = $strSQL . " WHERE " . $strReq;
                    return($strSQL);
                   
             } //end of updatestring function 


             function StripFirstCharacter($inStr)
             {
                   if (strlen($inStr)> 0) 
                         return(substr($inStr ,1));
             }

             function InsertString($strTableName, $arOfValues)
             {
                for($i=0; $i<sizeof($arOfValues);$i++)
                {
                      $fieldName = $arOfValues[$i][0];
                      $val = $arOfValues[$i][1];
                      $dataType = $arOfValues[$i][2];
                  
                  $notNULL = false;                  
                      //Set Nullability and return to base datatype
                  if ($dataType > cFldNotNULL)
                  { 
                      $notNULL = true;
                            $dataType = $dataType - cFldNotNULL;
                  }
                  //Checking datatype for validity
                      //Only data variables allowed
                  if ($dataType > cFldReqSeperator)
                  {    
                            //Invalid data for an insert statement
                            //Handle this in an appropriate way
                  }else{
                       //Append column names for generating insert statement        
                       $strFieldList .= ", " . "`" . $fieldName . "`" ;    // yeni ver mysql için tek tırnak eklendi halil..
                       //'Check NULL value
                       If (($dataType != cFldBit) && (is_null($val) || $val == ''))  //  && ! cFldNotNULL    && $val!=0
                       {
                            //'and Not cFldNotNULL
                            //The value is empty and it is not a yes/no field.
                            // Set the value to NULL.
                           $strValueList .= ", NULL ";
                        }else{
                                 switch ($dataType)
                                 { 
                                     case cFldWQuote:
                                          $strValueList .=  ", " . chr(39) . str_replace(chr(39),chr(39).chr(39), $val) . chr(39);
                                         //$strValueList .=  ", " .$val;
                                         break;
                                     case cFldWoQuote:
                                         $strValueList .= ", " . $val;
                                         break;
                                    case cFldDate:
                                    case cFldDateTime:
                                        $strValueList .= ", '" . $val ."'";
                                        break;
                                    case cFldBit:
                                        //This is a yes/no value.
                                        If ($val == "" || $val == "0" || $val == "false" )
                                            $chkValue = 0;
                                        else
                                            $chkValue = 1;
                                        $strValueList .=  ", " . $chkValue;
                                        break;
                                  } //end of switch
                                  
                              }
                          }
                      }

                 $strValueList = $this->StripFirstCharacter($strValueList);
                 $strFieldList = $this->StripFirstCharacter($strFieldList);
				 
				// echo $strFieldList; die; 
				 			 
                 //        Check validty of strings
                 if ((strlen($strFieldList) > 0) && (strlen($strValueList) > 0 ))
                 {
                    $strSQL = "INSERT INTO " . $strTableName . " ( " . $strFieldList . " ) VALUES (" .$strValueList. ") " ;
                 }
  
               return($strSQL);                   
             
             }//end of insert string
             
            // Search
            function field_query($kriter, $alan, $operator, $deger)
            {
               $str = "";
               if ($alan !="" &&  $deger != "" && $deger != "''" && $deger != "'%%'" && $deger != "-1"&& $deger != "'%'")
               {
                  if ($kriter !="")
                  {
                      $str = " AND ". $alan . " " . $operator . " " . $deger;
                  }else{
                      $str =   $alan . " ". $operator . " " . $deger;
                 }
               }
              return($str);
           }
           // End of Search
           
       } //end of class
 class utility
 {
     //----------------------------------------------------------------------
     //Function : FillComboValuesWSQL
     //Purpose : Fill an SELECT object with a given SQL string
     //SQL String must contain two fields first value and then displayed data
     //@conn       : active connection
     //@strSQL     : sql string to fill combo(there must be only two field selected)
     //@firstRow   : first row fill be put if TRUE
     //@preselectValue : which value will be selected 
     //----------------------------------------------------------------------
     function FillComboValuesWSQL($conn, $strSQL, $firstRow, $preselectValue)
     {
     
         global  $cdb;
         $conn = $cdb->getConnection();     
         $rs = mysqli_query($conn, $strSQL);
         if ($firstRow == true)
            $tmpStr = "<option value='-1'>--Seçiniz--</option>\n";
             
         while($row = mysqli_fetch_row($rs))
         {
//             $tmpStr .= "<option>22</option>\n"; 
             $tmpStr .= "<option value='". $row[0]."'>".$row[1]. "</option>\n"; 
         }
         # SINGLE SELECT
         if (!is_array($preselectValue)) {
           if (strlen($preselectValue) > 0) { 
              $tmpStr = str_replace("value='" . $preselectValue . "'", " selected value='" . $preselectValue  . "'", $tmpStr);
           }
         }
         # MULTI SELECT
         else {
           foreach ($preselectValue as $tmp_val) {
             if (strlen($tmp_val) > 0) { 
                $tmpStr = str_replace("value='" . $tmp_val . "'", " selected value='" . $tmp_val  . "'", $tmpStr);
             }
           }
         
         }
         return($tmpStr);
     }
     // if return value is equal to radio value mark it as checked
     function is_radio_checked($returnVal, $elemVal )
     {
         if ($returnVal == $elemVal)
             return(" checked ");
     }
     // if select item is selected return selected
     function is_dropdown_selected($returnVal, $elemVal )
     {
         if ($returnVal == $elemVal)
             return(" selected ");
     }
     
     //  **************************************************************
     //  Calculates current microtime
     //  I throw this into all my classes for benchmarking purposes
     //  if you don't need it.


     function myMicrotime ()
     {
        $time = explode( " ", microtime());
          $usec = (double)$time[0];
            $sec = (double)$time[1];
        return $sec + $usec;
     }

        
 }
 class datetime_operations
 {
     function dayDropDown($preSelected)
     {  $str ;
        for($i=1; $i<=31; $i++)
        {
           if ($preSelected==$i)
               $str .= "<option value='" . $i . "' selected>".$i."</option>\n" ;
           else
               $str .= "<option value=" . $i . ">".$i."</option>\n" ;
        }
        return($str);
        
     } 

     function monthDropDown($showMonthNames, $lang, $preSelected)
     {
         //Language Options
         //0 : Turkish
         //1 : English
  
         //Month names in all supported languages
         $MonthArray[0] = Array("Ocak", "Şubat", "Mart", "Nisan", "Mayıs", "Haziran", "Temmuz", "Ağustos", "Eylül", "Ekim", "Kasım", "Aralık");
         $MonthArray[1] = Array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");

           for($i=1; $i<=12; $i++)
            {
              if (is_array($preSelected)){
                  $sec="";
                  foreach($preSelected as $v){
                    if ($i == $v){ $sec = " selected"; }
                  }
              if ($showMonthNames) 
                  $str .=  "<option value='" . $i ."'". $sec .">" . $MonthArray[$lang][$i-1] . "</option>\n" ;// nl2br('\');
              else
                  $str .=  "<option value='" . $i ."'". $sec .">" . $i . "</option>\n" ; //nl2br('\');
              }else{    
                  $tmpSel = " ";
                  if ($preSelected == $i)
                      $tmpSel = " selected";
                  if ($showMonthNames) 
                      $str .=  "<option value='" . $i ."'". $tmpSel .">" . $MonthArray[$lang][$i-1] . "</option>\n" ;// nl2br('\');
                  else
                      $str .=  "<option value='" . $i ."'". $tmpSel .">" . $i . "</option>\n" ; //nl2br('\');
         }
            }   
           return($str);

     } 
     
     function yearDropDown($beginDate, $endDate,$preSelected)
     {
        for ($i = $endDate; $i>=$beginDate ;$i--)
        {
           if (is_array($preSelected)){
             $sec="";
             foreach($preSelected as $v){
                    if ($i == $v){ $sec = " selected"; }
             }
            $str .= "<option value='" . $i . "'" . $sec .">" . $i . "</option>\n" ; 
           }else{
            $tmpSel = "";
            if ($preSelected == $i)
                 $tmpSel = " selected";
        $str .= "<option value='" . $i . "'" . $tmpSel .">" . $i . "</option>\n" ; 
           } 
        }
       return($str);
     } 
 
 }
 // this class will be used for user operation.
 // such as customization
 class user //????
 {
 
 }
 // this 
 class application
 {

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

$cdb = new db_layer();
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

 
  $lna_params = Array(); // ' Input parameters Array.    

//    'command.CreateParameter (Name, Type, Direction, Size, Value)
  $lna_params[0]  = Array("DB_ID",     $prm_DB_ID,       cFldWQuote) ;
  $lna_params[1]  = Array("CLIENT_ID",     $prm_CLIENT_ID,   cFldWQuote) ; 
  $lna_params[2]  = Array("OPERATION",     $prm_OPERATION,   cFldWQuote);
  $lna_params[3]  = Array("TYPE",         $prm_TYPE,        cFldWQuote);
  $lna_params[4]  = Array("ORIGIN",     $prm_ORIGIN,      cFldWQuote);
  $lna_params[5]  = Array("TABLE_NAME",    $prm_TABLE_NAME,  cFldWQuote);
  $lna_params[6]  = Array("IP",            $prm_IP,          cFldWQuote);
  $lna_params[7]  = Array("MAIL_IT",       $prm_MAIL_IT,     cFldWQuote); 
  $lna_params[8]  = Array("E_MAIL",        $prm_E_MAIL,      cFldWQuote);
  $lna_params[9]  = Array("MSG",         $prm_MSG,         cFldWQuote);
  $lna_params[10] = Array("SPARE1",     $prm_SPARE1,      cFldWQuote);
  $lna_params[11] = Array("SPARE2",     $prm_SPARE2,      cFldWQuote);
  $lna_params[12] = Array("SPARE3",       $prm_SPARE3,      cFldWQuote);
  $lna_params[13] = Array("DATE_TIME",    "NOW()",          cFldWoQuote);  
  $lna_params[14] = Array("USERNAME",      $prm_USERNAME,    cFldWQuote);  
        
    $sql_str =  $cdb->InsertString("LNA_LOGS", $lna_params);
    if (!($cdb->execute_sql($sql_str,$result,$error_msg)))
      {
           print_error($error_msg);
           exit;
      }
 }

 }
 
/* ////////////////////////////////////////////////////////////////////////////
/*  function : right_set ; sets a user rigths to an array and put this array into
/*   session after user logged in
/*   $use_id : the id of the user logged in
/*  date : 02.04.2002
/*  owner : seykay 
/* ////////////////////////////////////////////////////////////////////////////*/
 
  function right_set($user_id)
    {  //global $_SESSION ;
	
       $cdb = new db_layer(); 
       $sql_str = "SELECT MCRYSTALINFONE.RIGHTS.* FROM MCRYSTALINFONE.USERS_RIGHTS ".
                  " INNER JOIN MCRYSTALINFONE.RIGHTS ON MCRYSTALINFONE.RIGHTS.ID = MCRYSTALINFONE.USERS_RIGHTS.RIGHT_ID ".
                  " WHERE USER_ID = $user_id " ;
//echo $sql_str; die;
       if (!($cdb->execute_sql($sql_str,$result,$error_msg)))
       {
          print_error($error_msg);
          exit;
       }
       $test = array();
	   

       while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
       {
          array_push($test, $row['RIGHT_NAME']) ;
       }
	  
	  
//echo $test[0]; die;
       $_SESSION["rights"] = $test;
	      
//	print_r($_SESSION["rights"]); die;

     }
	
	
/* ////////////////////////////////////////////////////////////////////////////
/*  function : right_get ; returns true if user has right else return false 
/*   if there is no right array returns -1
/*   $right_name : name of the right which is questioned
/*  date : 02.04.2002
/*  owner : seykay 
/* ////////////////////////////////////////////////////////////////////////////*/
 
   function right_get($right_name)
    {  //global $_SESSION;
	   //echo $right_name; die;
      $result = Array(); 
      $result = $_SESSION["rights"];
//echo $right_name;print_r($result); die;
      if (is_array($result))
		return(in_array("$right_name", $result));
      else
         return(false);      
   }
   

/* ////////////////////////////////////////////////////////////////////////////
/*  function : right_toclient ; writes all rights of a user to the browser,  
/*   in this way client side control also will be available
#
#
#   right_check : this is a client side function and has a parameter name right_name;
#
#
/*  date : 02.04.2002
/*  owner : seykay 
/* ////////////////////////////////////////////////////////////////////////////*/
 
// echo $_SESSION["rights"]; die;

   function right_toclient()
   {  //global $_SESSION;
      echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
      echo " <!-- \n";

		//print_r($_SESSION["rights"]); die;

      echo  "var rights = new Array;\n";
	   //$_SESSION["rights"]=$test;
	  $test = $_SESSION["rights"];   //halil
	  
	  //print_r($test); die;
      if (is_array($test))
     { 
	 // print_r($test); die;
	 /* //halil php 8icin
          while(list($key,$value) = each($test))
          {
              echo  "rights['$value']= $key;\n";

          } 
		  */
	foreach ($test as $key => $value) {
		echo  "rights['$value']= $key;\n";
		}
		  
     }
	 
	  //echo  "ADMIN\n";  //FOR TEST halil
      echo "function right_check(right_name)\n" ;
      echo " { \n" ;
      echo "   if (isNaN(rights[right_name])) \n" ;
      echo "         return(false); // right is not found \n" ;
      echo "      else \n"; 
      echo "         return(true); // right is found \n" ;
      echo " } \n" ;
      echo "//-->\n";
      echo "</script>\n";
   }
   
# get_waiting_time : calculates the time between now and ALMA_TARIHI  
#
#  $timestamp : ALMA_TARIHI
#
#
#
function get_waiting_time($timestamp)
{
 $second = 0;
 $minute = 0;
 $hour   = 0;
 $day    = 0;
 $month  = 0;
 


$tmptime =$timestamp;
if (floor($tmptime/(3600*24*30))>0)
{
   $month = floor($tmptime/(3600*24*30));
   $tmptime = $tmptime % (3600*24*30);
   $str = "$month ay, ";  
}
if (floor($tmptime/(3600*24))>0)
{
   $day = floor($tmptime/(3600*24));
   $tmptime = $tmptime % (3600*24);
   $str .= "$day gün, ";     
}

if (floor($tmptime/(3600))>0)
{
   $hour = floor($tmptime/(3600));
   $tmptime = $tmptime % ((3600));
   $str .= "$hour saat, ";     
}

if (floor($tmptime/(60))>0)
{
   $minute = floor($tmptime/(60));
   $tmptime = $tmptime % (60);
   $str .= "$minute dakika, ";     
}
/*
if (($tmptime)>0)
{
   $str .= "$tmptime saniye, ";     
}
*/
// strip the last comma...
$str = substr($str,0,strlen($str)-2);
return($str);
}

?>
