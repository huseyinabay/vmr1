<? require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
   $cdb = new db_layer();
   require_valid_login();

   header("Content-type:text/xml");
   //header("Man: POST ".$PHP_SELF." HTTP/1.1");
   //sql ler açıktan gönderilmemeli onun için bir select case ile halledilmeli
   $sql_query = "SELECT ID, REPLACE(DATA,'&',' ') AS DATA FROM RAW_ARCHIEVE WHERE SITE_ID = '1' ORDER BY ID DESC LIMIT 0, 3";
   if($ID){
        $sql_query = "SELECT ID, REPLACE(DATA,'&',' ') AS DATA 
                          FROM RAW_ARCHIEVE
						  WHERE SITE_ID = '1'
                          ORDER BY ID DESC
                          LIMIT 0, 3
                          ";
   }


   $cdb->execute_sql($sql_query,$result,$mess);
   if (mysqli_num_rows($result)< 9 ) return 0;
   echo"<?xml version=\"1.0\" ?> ";//' Start our XML document.

   echo "<DATABASE> ";     //' Output start of data.

   //' Loop through the data records.
   while($row=mysqli_fetch_row($result))
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
               if (strlen($strValue) > 0 ) $strValue = turkish2utf($strValue);
               echo "<" . trim($strName) . ">";
               echo trim($strValue); 
               echo "</" . trim($strName) . ">";
               $i++;
        }
        echo "</SUB> ";  //    ' Move to next city in database.
   }
   echo "</DATABASE> "; //' Output end of data.
   
?> 