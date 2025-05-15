<?
//require_once("incs.php");
//page_header("SQL Yorumlayıcısı","sql_executer.php");
require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
require_valid_login();
$cdb = new db_layer();
$conn = $cdb->getConnection();
//$conn = mysqli_connect("localhost","crinfo","SiGmA*19","MCRYSTALINFONE");

check_right("ADMIN");
cc_page_meta();
echo "<center>";
page_header();
$hata =False;
//$sql_str = addslashes($sql_str);
if($act=="exec"){
    if(!$cdb->execute_sql($sql_str,$result,$err_msg)){
        $hata = true;
    }
//    $sql_str = stripslashes($sql_str);

	 //  ini_set('display_errors', 'On');
     //  error_reporting(E_ALL);
	 //  error_reporting(E_ALL ^ E_NOTICE);
   
 

}
?>

<form method=post action="sql_executer.php?act=exec" name="sql">
    <table width="75%" cellspacing="0" cellpadding="0" border="0" align="center">
        <tr align="center">
            <td class=tbl_head height="25">
            <a>SQL sorgunuzu yazın</a>
            </td>
        </tr>
		<tr align="center">
			<td class=tbl_body>
            <textarea cols=70 rows=10 class="textarea1" name="sql_str"><?=$sql_str?></textarea>
            </td>
        </tr>
        <tr>
            <td class=tbl_body colspan=2 height="25">
            <center><a href="javascript:document.sql.submit()">
                Çalıştır</a></center>
            </td>
        </tr>
    </table>
</form>
<?
if(!$hata && $act=="exec"){
    if(!preg_match("SELECT", $sql_str)){
       $num_row=mysqli_affected_rows($result);
        echo "<p class=body_line>Query ok. ".$num_row." adet satır etkilendi.</p>";
    }else{
        $num_row = mysqli_num_rows($result);
        $num_fields =mysqli_num_fields($result);
        ?><table BORDER = 1><tr><?
        for($j=0;$j<=$num_fields;$j++){
            $col=mysqli_fetch_field($result);
            echo "<td class=tbl_head>".$col->name."</td>";
            $col_name[$j] = $col->name;
        }
        echo "</t>";
        for($a=0;$a<=$num_row;$a++){
            echo "<tr>";
            $row=mysqli_fetch_array($result);
            for($i=0;$i<=$num_fields;$i++){
                echo "<td class=tbl_body>".$row[$col_name[$i]]."</td>";
            }
            echo "</tr>";
        }?>        
        </table>
<?
    }
}else{
    echo "<p class=body_line>".$err_msg."</p>";
}
page_footer(0);
?>