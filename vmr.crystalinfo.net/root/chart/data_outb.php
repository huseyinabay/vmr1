<?php
header('Content-Type: application/json');
date_default_timezone_set('Europe/Istanbul');
$month = date("m");
$year = date("Y");
	 
$conn = mysqli_connect("localhost","crinfo","SiGmA*19","MCRYSTALINFONE");

$sqlQuery = "SELECT TLocationType.LocationType AS LOC,
COUNT(CDR_MAIN_".$month."_".$year.".CDR_ID) AS ARAMA_SAYISI,
SUM(CDR_MAIN_".$month."_".$year.".DURATION) AS SURE_SN,
SUM(CDR_MAIN_".$month."_".$year.".PRICE) AS ÜCRET,
MY_DATE AS TARIH,
TIME_STAMP_MONTH AS AY,
TIME_STAMP_YEAR AS YIL
FROM CDR_MAIN_".$month."_".$year."
LEFT JOIN TLocationType ON CDR_MAIN_".$month."_".$year.".LocationTypeid = TLocationType.LocationTypeid
WHERE ORIG_DN<99999 AND ERR_CODE=0 AND CDR_MAIN_".$month."_".$year.".LocationTypeid<5
GROUP BY LOC
ORDER BY LOC DESC;";

$result = mysqli_query($conn,$sqlQuery);

$data = array();
foreach ($result as $row) {
	$data[] = $row;
}

mysqli_close($conn);

echo json_encode($data);
?>