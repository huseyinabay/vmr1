<?

require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";	

$cdb = new db_layer();
require_valid_login();

header("Content-type:text/xml");
//header("Man: POST ".$PHP_SELF." HTTP/1.1");

//sql ler açıktan gönderilmemeli onun için bir select case ile halledilmeli
 $index =  substr($sql_query,0,2);
 $sql_query = substr($sql_query,2);
 $sql_query = urldecode($sql_query);
switch ($index)
{
   case 1:
      $sql_query = "SELECT DEPT_ID, DEPT_NAME FROM DEPTS WHERE " . $sql_query ." ORDER BY DEPT_NAME";
   break;
   case 2:
      $sql_query = "SELECT EXT_ID ,  EXT_NO FROM EXTENTIONS WHERE " . $sql_query ." ORDER BY EXT_NO";
   break;
   case 3:
      $sql_query = "SELECT AUTH_CODE_ID, AUTH_CODE FROM AUTH_CODES WHERE " . $sql_query ." ORDER BY AUTH_CODE";
   break;
   case 8:
      $sql_query = "SELECT USER_ID, CONCAT(NAME,' ',SURNAME) AS ADI FROM USERS WHERE " . $sql_query ."  ORDER BY ADI";
   break;
   case 5:
      $sql_query = "SELECT USER_ID, CONCAT(NAME,' ',SURNAME) AS ADI FROM USERS WHERE DISABLED = 'N' AND " . $sql_query ." ORDER BY ADI";
   break;
   case 6:
      $sql_query = "SELECT ID, AD FROM YP_ALT_GRUP WHERE " . $sql_query ."  ORDER BY ID";
   case 10:
      $sql_query = "SELECT TRUNK_ID, CONCAT(MEMBER_NO,' ',TRUNK_NAME) AS TRUNK_NAME FROM TRUNKS WHERE " . $sql_query ."  ORDER BY TRUNK_ID";
   case 11:
      $sql_query = "SELECT SUB_DEP_ID, FIH_SUB_DEPT FROM FIH_SUB_DEPT WHERE " . $sql_query ."  ORDER BY FIH_SUB_DEPT";
   break;
}
  $strSQL = $sql_query;
   $cdb->execute_sql($strSQL,$result,$mess);

   echo"<?xml version=\"1.0\" ?> ";//' Start our XML document.

   echo "<DATABASE> ";     //' Output start of data.

   //' Loop through the data records.
   while($row=mysql_fetch_row($result))
   {
        //Output start of record.
        echo "<SUB> ";
        $i = 0;
        // Loop through the fields in each record.
        foreach ($row as $value) 
        {
            $strName = mysqli_fetch_field_direct($result,$i);
            $strValue = $value;
            if (strlen($strName) > 0 ) $strName = turkish2utf($strName);
            If (strlen($strValue) > 0 ) $strValue = turkish2utf($strValue);
            echo "<" . trim($strName) . ">";
            echo trim($strValue); 
            echo "</" . trim($strName) . ">";
            $i++;
        }
        echo "</SUB> ";  //    ' Move to next city in database.
   }
   echo "</DATABASE> "; //' Output end of data.
?> 