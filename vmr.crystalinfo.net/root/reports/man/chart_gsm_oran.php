<!DOCTYPE HTML>
<html>
<head>

<?
  require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
  $cUtility = new Utility();
  $cdb = new db_layer();
  require_valid_login();
 
  //Hak Kontrolü

   $usr_crt = "";
    if (right_get("SITE_ADMIN")){
      //Site admin hakkı varsa herşeyi görebilir.  
      //Site id gelmemişse kişinin bulunduğu site raporu alınır.
      if(!$SITE_ID){$SITE_ID = $_SESSION['site_id'];}
    }elseif(right_get("ADMIN") || right_get("ALL_REPORT")){
      // Admin vaye ALL_REPORT hakkı varsa kendi sitesindeki herşeyi görebilir.
      $SITE_ID = $_SESSION['site_id'];
    }elseif(got_dept_right($_SESSION["user_id"])==1){
      //Bir departmanın raporunu görebiliyorsa kendi sitesindekileri girebilir.
      $SITE_ID = $_SESSION['site_id'];
      //echo $dept_crt = get_depts_crt($_SESSION["user_id"]);
      $usr_crt = get_users_crt($_SESSION["user_id"], 1, $SITE_ID);
      $alert = "Bu rapor sadece sizin yetkinizde olan departmanlara ait dahililerin bilgilerini içerir.";
    }else{
      print_error("Bu sayfayı Görme Hakkınız Yok!!!");
      exit;
    }

    //echo  $usr_crt;
  //Hak kontrolü sonu  

 ?>


  <script type="text/javascript">
 /*
 var t0 = '<?php echo $_GET['t0'];?>';
 var t1 = '<?php echo $_GET['t1'];?>';
 
 document.write(t0);
 document.write(t1); 
 */
	
	var adet = <?php
$t0 = $_GET['t0'];
$t1 = $_GET['t1'];
$SITE_ID = $_GET['site'];

date_default_timezone_set('Europe/Istanbul');
$conn = mysqli_connect("localhost","crinfo","SiGmA*19","MCRYSTALINFONE");

$data_points3 = array();

$query3 = "SELECT COUNT(CDR_MAIN_DATA.CDR_ID)  AS yes,
	(SELECT COUNT(CDR_MAIN_DATA.CDR_ID)  
          FROM CDR_MAIN_DATA
          INNER JOIN TTelProvider ON TTelProvider.TelProviderid = CDR_MAIN_DATA.TO_PROVIDER_ID
          WHERE ERR_CODE = 0 AND CALL_TYPE = 1 AND MY_DATE BETWEEN '$t0' AND '$t1' AND CDR_MAIN_DATA.LocationTypeid=2 AND CDR_MAIN_DATA.SITE_ID='$SITE_ID' ".$usr_crt.") AS total,
	 ROUND(COUNT(CDR_MAIN_DATA.CDR_ID)/(SELECT COUNT(CDR_MAIN_DATA.CDR_ID)  
          FROM CDR_MAIN_DATA
          INNER JOIN TTelProvider ON TTelProvider.TelProviderid = CDR_MAIN_DATA.TO_PROVIDER_ID
          WHERE ERR_CODE = 0 AND CALL_TYPE = 1 AND MY_DATE BETWEEN '$t0' AND '$t1' AND CDR_MAIN_DATA.LocationTypeid=2 AND CDR_MAIN_DATA.SITE_ID='$SITE_ID' ".$usr_crt."),2) 
          * 100 AS y,
	  TTelProvider.TelProvider AS label
          FROM CDR_MAIN_DATA AS CDR_MAIN_DATA
          INNER JOIN TTelProvider ON TTelProvider.TelProviderid = CDR_MAIN_DATA.TO_PROVIDER_ID
          WHERE ERR_CODE = 0 AND CALL_TYPE = 1 AND MY_DATE BETWEEN '$t0' AND '$t1' AND CDR_MAIN_DATA.LocationTypeid=2 AND CDR_MAIN_DATA.SITE_ID='$SITE_ID' ".$usr_crt."
          GROUP BY label;";

$result3 = mysqli_query($conn,$query3);


while($row3 = mysqli_fetch_array($result3))
        {        
      /* Push the results in our array */
            $point3 = array("y" =>  $row3['y'], "label" =>  $row3['label'] );
            array_push($data_points3, $point3);
			
        }

    /* Encode this array in JSON form */
        echo json_encode($data_points3, JSON_NUMERIC_CHECK);
        
mysqli_close($conn);

?>;



  window.onload = function () {

    var chart = new CanvasJS.Chart("chartContainer",
    {
		animationEnabled: true,
      title:{
        text: "GSM Operatör Kullanımı"
      },

      data: [
      {
        type: "pie",
		startAngle: 240,
		yValueFormatString: "##0.00\"%\"",
		indexLabel: "{label} {y}",
		toolTipContent: "<b>{label}</b><br>Kullanım: {y} ",
        dataPoints: adet
		
      }
      ]
	
	  
    });

chart.render();
	
}
</script>
<script type="text/javascript" src="/scripts/canvas/canvasjs.min.js"></script></head>
<body>
  <div id="chartContainer" style="height: 282px; width: 100%;">
  </div>
</body>
</html>