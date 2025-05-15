<!DOCTYPE HTML>
<html>
<head>

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
          WHERE ERR_CODE = 0 AND CALL_TYPE = 1 AND MY_DATE BETWEEN '2020-07-01' AND '2020-07-31' AND CDR_MAIN_DATA.LocationTypeid=2) AS total,
	 ROUND(COUNT(CDR_MAIN_DATA.CDR_ID)/(SELECT COUNT(CDR_MAIN_DATA.CDR_ID)  
          FROM CDR_MAIN_DATA
          INNER JOIN TTelProvider ON TTelProvider.TelProviderid = CDR_MAIN_DATA.TO_PROVIDER_ID
          WHERE ERR_CODE = 0 AND CALL_TYPE = 1 AND MY_DATE BETWEEN '2020-07-01' AND '2020-07-31' AND CDR_MAIN_DATA.LocationTypeid=2),2) 
          * 100 AS y,
	  TTelProvider.TelProvider AS label
          FROM CDR_MAIN_DATA AS CDR_MAIN_DATA
          INNER JOIN TTelProvider ON TTelProvider.TelProviderid = CDR_MAIN_DATA.TO_PROVIDER_ID
          WHERE ERR_CODE = 0 AND CALL_TYPE = 1 AND MY_DATE BETWEEN '2020-07-01' AND '2020-07-31' AND CDR_MAIN_DATA.LocationTypeid=2
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
        text: "Top 5 Departman"
      },

      data: [
      {
        type: "bar",
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
  <div id="chartContainer" style="height: 300px; width: 100%;">
  </div>
</body>
</html>