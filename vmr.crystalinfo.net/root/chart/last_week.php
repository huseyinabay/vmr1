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
//$t0 = $_GET['t0'];
//$t1 = $_GET['t1'];
//$SITE_ID = $_GET['site'];

date_default_timezone_set('Europe/Istanbul');

$month = date("m");
$year = date("Y");
$month1 = date("m")-1;

$conn = mysqli_connect("localhost","crinfo","SiGmA*19","MCRYSTALINFONE");

$data_points1 = array();

$query1 = "SELECT CONCAT (TIME_STAMP_DAY,'-',TIME_STAMP_MONTH ) AS GUN,
COUNT(CDR_MAIN_DATA.CDR_ID) AS y,
SUM(CDR_MAIN_DATA.DURATION) AS SURE_SN,
SUM(CDR_MAIN_DATA.PRICE) AS ÜCRET,
MY_DATE AS label,
TIME_STAMP_MONTH AS AY,
TIME_STAMP_YEAR AS YIL
FROM CDR_MAIN_DATA
WHERE ORIG_DN<99999 AND ERR_CODE=0 AND TIME_STAMP_YEAR=$year AND TIME_STAMP_MONTH BETWEEN $month1 AND $month ".$usr_crt."
GROUP BY GUN
ORDER BY label DESC
LIMIT 0, 10;";
$result1 = mysqli_query($conn,$query1);

//echo $query1;

while($row1 = mysqli_fetch_array($result1))
        {        
      /* Push the results in our array */
            $point1 = array("y" =>  $row1['y'], "label" =>  $row1['label'] );
            array_push($data_points1, $point1);
			
        }

    /* Encode this array in JSON form */
        echo json_encode($data_points1, JSON_NUMERIC_CHECK);
        
mysqli_close($conn);

/*


	  backgroundColor: '#49e2ff',
                                borderColor: '#46d5f1',
                                hoverBackgroundColor: '#CCCCCC',
                                hoverBorderColor: '#666666',
								backgroundColor: ["#00FFFF", "#8e5ea2","#3cba9f","#e8c3b9","#c45850","#FFD700","#A9A9A9","#000080","#FF69B4", "#3e95cd"]

*/

?>;

  window.onload = function () {

    var chart = new CanvasJS.Chart("chartContainer",
    {
		animationEnabled: true,
		backgroundColor: "#BED3E9",
		
		 axisX:{
		valueFormatString: "DD-MM-YY" ,
         labelAngle: 45,
		 //labelFontColor: "rgb(0,75,141)"
      },
	/*	
      title:{
        text: "Son 10 Günün Çağrı Grafiği"
      },
*/
      data: [
      {
		type: "column",
		//color: "rgba(0,135,147,.3)",
		toolTipContent: "<b>{label}</b><br>Arama: {y} Adet",
	  dataPoints: adet
	  
	  
	  }
      ]
	
	  
    });

chart.render();
	
}
</script>
<script type="text/javascript" src="/scripts/canvas/canvasjs.min.js"></script></head>
<body>
  <div id="chartContainer" style="height: 215px; width: 100%;">
  </div>
  
</body>
</html>