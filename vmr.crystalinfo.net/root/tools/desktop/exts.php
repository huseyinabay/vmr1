<?
    require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
    $cDB = new db_layer();
    $sql_str = "SELECT SITES.SITE_NAME AS SITE_NAME, DEPTS.DEPT_NAME AS DEPT_NAME, 
                       EXTENTIONS.DESCRIPTION AS DESCRIPTION, EXTENTIONS.EXT_NO AS EXT_NO
                       FROM SITES
                         LEFT JOIN EXTENTIONS ON SITES.SITE_ID = EXTENTIONS.SITE_ID
                         LEFT JOIN DEPTS ON EXTENTIONS.DEPT_ID = DEPTS.DEPT_ID
                       WHERE RESIDE_IN_EXTEN = 1
						   ORDER BY SITES.SITE_NAME, DEPTS.DEPT_NAME;";
    if (!($cDB->execute_sql($sql_str,$result,$error_msg))){
          print_error($error_msg);
          exit;
    }
    while($row = mysqli_fetch_object($result)){
      echo $row->SITE_NAME."#".$row->DEPT_NAME."#".$row->DESCRIPTION."#".$row->EXT_NO."<BR>\n";
    }
?>