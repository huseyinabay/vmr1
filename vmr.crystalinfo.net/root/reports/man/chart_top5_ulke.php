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

$data_points1 = array();

$query1 = "SELECT COUNT(CDR_MAIN_DATA.CDR_ID) AS y,
			  COUNT(CDR_MAIN_DATA.CDR_ID) AS adet,	
              CDR_MAIN_DATA.LocalCode AS C_CODE, 
			  TLocation.LocationName AS label
              FROM CDR_MAIN_DATA AS CDR_MAIN_DATA
              INNER JOIN TLocation ON CDR_MAIN_DATA.Locationid = TLocation.Locationid
              WHERE ERR_CODE = 0 AND CALL_TYPE = 1 AND MY_DATE BETWEEN '$t0' AND '$t1' AND CDR_MAIN_DATA.LocationTypeid ='3' AND CDR_MAIN_DATA.SITE_ID='$SITE_ID' ".$usr_crt."
              GROUP BY CDR_MAIN_DATA.CountryCode
              ORDER BY adet DESC LIMIT 5;";
			
$result1 = mysqli_query($conn,$query1);


while($row1 = mysqli_fetch_array($result1))
        {        
      /* Push the results in our array */
            $point1 = array("y" =>  $row1['y'], "label" =>  $row1['label'] );
            array_push($data_points1, $point1);
			
        }

    /* Encode this array in JSON form */
        echo json_encode($data_points1, JSON_NUMERIC_CHECK);
        
mysqli_close($conn);

?>;

	var price = <?php

date_default_timezone_set('Europe/Istanbul');
$conn = mysqli_connect("localhost","crinfo","SiGmA*19","MCRYSTALINFONE");

$data_points2 = array();

$query2 = "SELECT ROUND(SUM(PRICE),2) AS y,
			  COUNT(CDR_MAIN_DATA.CDR_ID) AS adet,	
			  CDR_MAIN_DATA.LocalCode AS C_CODE, 
			  TLocation.LocationName AS label
              FROM CDR_MAIN_DATA AS CDR_MAIN_DATA
              INNER JOIN TLocation ON CDR_MAIN_DATA.Locationid = TLocation.Locationid
              WHERE ERR_CODE = 0 AND CALL_TYPE = 1 AND MY_DATE BETWEEN '$t0' AND '$t1' AND CDR_MAIN_DATA.LocationTypeid='3' AND CDR_MAIN_DATA.SITE_ID='$SITE_ID' ".$usr_crt."
              GROUP BY CDR_MAIN_DATA.CountryCode
              ORDER BY adet DESC LIMIT 5;";

$result2 = mysqli_query($conn,$query2);


while($row2 = mysqli_fetch_array($result2))
        {        
      /* Push the results in our array */
            $point2 = array("y" =>  $row2['y'], "label" =>  $row2['label'] );
            array_push($data_points2, $point2);
			
        }

    /* Encode this array in JSON form */
        echo json_encode($data_points2, JSON_NUMERIC_CHECK);
        
mysqli_close($conn);
?>;

	var sure = <?php

date_default_timezone_set('Europe/Istanbul');
$conn = mysqli_connect("localhost","crinfo","SiGmA*19","MCRYSTALINFONE");

$data_points3 = array();

$query3 = "SELECT ROUND(SUM(CDR_MAIN_DATA.DURATION/60),2) AS y,
			  COUNT(CDR_MAIN_DATA.CDR_ID) AS adet,	
			  CDR_MAIN_DATA.LocalCode AS C_CODE, 
			  TLocation.LocationName AS label
              FROM CDR_MAIN_DATA AS CDR_MAIN_DATA
              INNER JOIN TLocation ON CDR_MAIN_DATA.Locationid = TLocation.Locationid
              WHERE ERR_CODE = 0 AND CALL_TYPE = 1 AND MY_DATE BETWEEN '$t0' AND '$t1' AND CDR_MAIN_DATA.LocationTypeid ='3' AND CDR_MAIN_DATA.SITE_ID='$SITE_ID' ".$usr_crt."
              GROUP BY CDR_MAIN_DATA.CountryCode
              ORDER BY adet DESC LIMIT 5;";
	

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
        text: "Top 5 Ülke"
      },

      data: [
      {
		type: "bar",
		toolTipContent: "<b>{label}</b><br>Arama: {y} Adet",
	  dataPoints: adet
	  },
      {
        type: "bar",
		toolTipContent: "<b>{label}</b><br>Süre: {y} Dakika",
        dataPoints: sure
		
      },
      {
        type: "bar",
		toolTipContent: "<b>{label}</b><br>Tutar: {y} TL",
        dataPoints: price

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