<!DOCTYPE HTML>
<html>
<head>

<?
  require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
  $cUtility = new Utility();
  $cdb = new db_layer();
  require_valid_login();
 
  //Hak Kontrolü
$SITE_ID = $_SESSION['site_id'];
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
      $usr_crt = get_users_crt($_SESSION["user_id"], 3, $SITE_ID);
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
	date_default_timezone_set('Europe/Istanbul');
$month = date("m");
$year = date("Y");
$SITE_ID = $_SESSION['site_id'];
$cdr_table="CDR_MAIN_".$month."_".$year."";
$MY_YEAR_MONTH ="LIKE '".$year."-".$month."%'";

$conn = mysqli_connect("localhost","crinfo","SiGmA*19","MCRYSTALINFONE");

$data_points3 = array();

$val = mysqli_query($conn,"select 1 from ".$cdr_table."  LIMIT 1");

if($val == FALSE)
{
   $cdr_table="CDR_MAIN_DATA";
}
else
{
    $cdr_table="CDR_MAIN_".$month."_".$year."";
}



$query3 = "SELECT TLocationType.LocationType AS LOC,
            ROUND(SUM(".$cdr_table.".DURATION/60),2) AS y,
		    TLocationType.LocationType AS label
            FROM ".$cdr_table." 
            LEFT JOIN TLocationType ON ".$cdr_table.".LocationTypeid = TLocationType.LocationTypeid
            WHERE ERR_CODE = 0 AND CALL_TYPE = 1 AND ".$cdr_table.".LocationTypeid<5 AND ".$cdr_table.".SITE_ID='$SITE_ID' AND MY_DATE ".$MY_YEAR_MONTH."  ".$usr_crt."
		GROUP BY LOC
		ORDER BY LOC DESC;";
		
//echo $query3;

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
		backgroundColor: "#BED3E9",
				

		
      data: [
      {
        type: "doughnut",
		indexLabelFontSize: 9,

		toolTipContent: "<b>{label}</b><br>Süre: {y} Dakika",
		//indexLabel: "{label} {y}",
        dataPoints: sure
		
      }
      ]
	
	  
    });

chart.render();
	
}
</script>
<script type="text/javascript" src="/scripts/canvas/canvasjs.min.js"></script>
</head>
<body>
  <div id="chartContainer" style="height: 92px; width: 100%;">
  </div>
</body>
</html>