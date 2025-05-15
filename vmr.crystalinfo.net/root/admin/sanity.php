<?php
   require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
   include("/usr/local/httpd/chartdir/phpchartdir.php");
   $cUtility = new Utility();
   $cdb = new db_layer();
   $capp = new  application();
   require_valid_login();

   /*
   	   ini_set('display_errors', 'On');
error_reporting(E_ALL);
   */
  //Site Admin veya Admin Hakkı yokda bu tanımı yapamamalı
   check_right("SITE_ADMIN");

   cc_page_meta(0);
   page_header();
   echo "<br>";
   table_header("Sanity Raporu", "500");
?>
<table width="100%" cellspacing="0" cellpadding="0" border="0">
  <tr>
    <td width="100%" colspan="2" align="center" height="277">
      <?//////////////////////DISK STATUS//////////////////////////////
            exec((" df /| awk '{ print $5 }'"), $aa) ;
            $dolu = str_replace("%", "", $aa[1]);
            $bos = 100 -$dolu;
            ?>
        <iframe src="../chart/disk_stat.php?dolu=<?=$dolu?>&bos=<?=$bos?>" width="440" height="260"></iframe>
      <?//////////////////////END OF DISK STATUS//////////////////////////////?>      
      <hr>
      </td>
  </tr>  
  <tr>  
    <td width="30%" class="td1_koyu">Database Boyutu</td>
    <td width="70%" class="td1">    
      <?
	  //////////////////////DB SIZE//////////////////////////////  

	/*		
		$sql_str = "SELECT SUM(DATA_LENGTH + INDEX_LENGTH) /1024 /1024 AS MB FROM information_schema.TABLES WHERE table_schema='MCRYSTALINFONE'" ; 
            if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
                  print_error($error_msg);
                  exit;
            }
            $row = mysqli_fetch_object($result);
            echo "$row->MB"." "."MB"."<BR>"; 
	 */

	  
        unset($aa);
          exec(("du -h /var/lib/mysql/MCRYSTALINFONE/ | awk '{ print $1 }'"), $aa) ;
          $k =0;
          echo $aa[0];
  
   //////////////////////END OF DB SIZE//////////////////////////////           
          //echo "<HR>";
  	  
		  ?>
    </td>  
  </tr> 


  
  <tr>
    <td class="td1_koyu">Son İşlenen Kayıt</td>
    <td class="td1">    
      <? 
      //////////////////////LAST RECORD////////////////////////////// 
            $sql_str = "SELECT * FROM SEMI_ARCHIEVE ORDER BY ID DESC LIMIT 1" ; 
            if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
                  print_error($error_msg);
                  exit;
            }
            $row = mysqli_fetch_object($result);
            echo "$row->LINE1"."<BR>"; 
      ?>
     </td> 


  <tr>
    <td class="td1_koyu"></td>
    <td class="td1">    
          <?echo "$row->LINE2"."<BR>";?>
    </td>    
  </tr>  
  <tr>
    <td class="td1_koyu"></td>
    <td class="td1">    
          <?echo "$row->LINE3"."<BR>";?>
    </td>    
  </tr>  
      <?//////////////////////END OF LAST RECORD////////////////////////////// ?>

      <?
      //////////////////////LAST RECORD//////////////////////////////                 
          $sql_str = "
                    SELECT COUNT(*) AS CNT, ERR_CODE, ERR_CODES.HEADER FROM CDR_MAIN_DATA
                    INNER JOIN ERR_CODES ON ERR_CODES.ID = CDR_MAIN_DATA.ERR_CODE
                    WHERE TIME_STAMP_MONTH = MONTH(NOW())
            GROUP BY ERR_CODE
                   " ; 
          if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
              print_error($error_msg);
              exit;
          }
      while($row = mysqli_fetch_object($result)){
      echo "<tr><td class=\"td1\" align=\"center\">".$row->CNT."</td>";       
      echo "<td class=\"td1_koyu\">".$row->HEADER."</td></tr>";  
            }
      //////////////////////END OF LAST RECORD//////////////////////////////           
      //echo "<HR>";?>
  </tr> 



	 	  <tr>
 <td class="td1_koyu">Crystalinfo Ver</td>

    <td class="td1">    
	2023.10.02
	<br>
	<br>
     </td>  
	 </tr>



 <tr>
 <td class="td1_koyu">OS</td>

    <td class="td1">    
      <? 
      //////////////////////os ver////////////////////////////// 
	  	  //echo php_uname();
		  //echo PHP_OS;
	  
		    unset($aa);
          //exec(("cat /etc/SuSE-release"), $aa) ;
		  exec(("cat /etc/redhat-release"), $aa) ;
          $k =0;
          echo $aa[0]; echo " "; echo $aa[1];  
      ?>
	  <br>
	  <br>
     </td>  
	 </tr>
	 
	 
	  <tr>
 <td class="td1_koyu">Kernel</td>

    <td class="td1">    
      <? 

		  unset($aa);
		  exec(("cat /proc/version"), $aa) ;
          $k =0;
          echo $aa[0]; echo " "; echo $aa[1];  
      ?>
	  <br>
	  <br>
     </td>  
	 </tr>
	 
	  

	  <tr>
 <td class="td1_koyu">PHP</td>

    <td class="td1">    
      <? 
	    unset($ap);
		  exec(("php -v"), $ap) ;
          $k =0;
          echo $ap[0];
		  ?>
      <br>
	  <br>
     </td>  
	 </tr>
	 
	 
	 	  <tr> 
 <td class="td1_koyu">MySQL</td>

    <td class="td1">    
      <? 
	    unset($ap);
		  exec(("mysql -V"), $ap) ;
          $k =0;
          echo $ap[0];
		  ?>

	  <br>
     </td>  
	 </tr>



<?
// Linux CPU
$load = sys_getloadavg();
$cpuload = $load[0];
// Linux MEM
$free = shell_exec('free');
$free = (string)trim($free);
$free_arr = explode("\n", $free);
$mem = explode(" ", $free_arr[1]);
$mem = array_filter($mem, function($value) { return ($value !== null && $value !== false && $value !== ''); }); // removes nulls from array
$mem = array_merge($mem); // puts arrays back to [0],[1],[2] after 
$memtotal = round($mem[1] / 1000000,2);
$memused = round($mem[2] / 1000000,2);
$memfree = round($mem[3] / 1000000,2);
$memshared = round($mem[4] / 1000000,2);
$memcached = round($mem[5] / 1000000,2);
$memavailable = round($mem[6] / 1000000,2);

?>

  
 <tr> <td class="td1_koyu">CPU Kullanımı :</td> <td class="td1"> <? echo $cpuload; ?> <br> </td></tr>
 <tr> <td class="td1_koyu">RAM Kullanımı :</td> <td class="td1"> <? echo $memused; ?> <br> </td> </tr>
 <tr> <td class="td1_koyu">RAM Bellek Boş:</td> <td class="td1"> <? echo $memfree ?> <br> </td> </tr>
 <tr> <td class="td1_koyu">RAM Bellek Boş:</td> <td class="td1"> <? echo $memcached ?> <br> </td> </tr>
 <tr> <td class="td1_koyu">RAM Bellek Tot:</td> <td class="td1"> <? echo $memtotal ?> <br> </td> </tr>


	 
	 	  <tr>
 <td class="td1_koyu"></td>

    <td class="td1">    

     </td>  
	 </tr>
	 
	 
	 
	  			<tr>
            <td height="20" colspan="2" >
        <a href="/doc/CRYSTALINFO_USER_GUIDE.pdf" onmouseover="javascript:show_hide(20)" onmouseout="javascript:show_hide(20)">
         -  Crystalinfo Kullanım Kılavuzu İndir  -  <img border="0" align="center" src="<?=IMAGE_ROOT?>detay_gor.gif" alt=""></a> 
		</td>
				</tr>
	 
	 
	   <tr class="bgc2">
    <td colspan="2" class="td1_koyu" align="center">Sistem Detayları</td>
  </tr>
  
</table>      
<?table_footer();
  echo "<br>";
  page_footer("");
?>
