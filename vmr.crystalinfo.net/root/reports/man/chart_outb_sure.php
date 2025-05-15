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
	
	var sure = <?php
$t0 = $_GET['t0'];
$t1 = $_GET['t1'];
$SITE_ID = $_GET['site'];

date_default_timezone_set('Europe/Istanbul');
$conn = mysqli_connect("localhost","crinfo","SiGmA*19","MCRYSTALINFONE");

$data_points3 = array();

$query3 = "SELECT TLocationType.LocationType AS LOC,
            ROUND(SUM(CDR_MAIN_DATA.DURATION/60),2) AS y,
		    TLocationType.LocationType AS label
            FROM CDR_MAIN_DATA AS CDR_MAIN_DATA
            LEFT JOIN TLocationType ON CDR_MAIN_DATA.LocationTypeid = TLocationType.LocationTypeid
            WHERE ERR_CODE = 0 AND CALL_TYPE = 1 AND MY_DATE BETWEEN '$t0' AND '$t1' AND CDR_MAIN_DATA.LocationTypeid<5 AND CDR_MAIN_DATA.SITE_ID='$SITE_ID' ".$usr_crt."
		GROUP BY LOC
		ORDER BY LOC DESC;";

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
        text: "Bölge Bazında Dış Aramalar (Süre)"
      },

      data: [
      {
        type: "pie",
		toolTipContent: "<b>{label}</b><br>Süre: {y} Dakika",
		indexLabel: "{label} {y}",
        dataPoints: sure
		
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