<?php
header('Content-Type: application/json');
date_default_timezone_set('Europe/Istanbul');
$month = date("m");
$year = date("Y");
$month1 = date("m")-1;

$conn = mysqli_connect("localhost","crinfo","SiGmA*19","MCRYSTALINFONE");

$sqlQuery = "SELECT CONCAT (TIME_STAMP_DAY,'-',TIME_STAMP_MONTH ) AS GUN,
COUNT(CDR_MAIN_DATA.CDR_ID) AS ARAMA_SAYISI,
SUM(CDR_MAIN_DATA.DURATION) AS SURE_SN,
SUM(CDR_MAIN_DATA.PRICE) AS ÜCRET,
MY_DATE AS TARIH,
TIME_STAMP_MONTH AS AY,
TIME_STAMP_YEAR AS YIL
FROM CDR_MAIN_DATA
WHERE ORIG_DN<99999 AND ERR_CODE=0 AND TIME_STAMP_YEAR=$year AND TIME_STAMP_MONTH BETWEEN $month1 AND $month
GROUP BY GUN
ORDER BY TARIH DESC
LIMIT 0, 10;";

$result = mysqli_query($conn,$sqlQuery);

$data = array();
foreach ($result as $row) {
	$data[] = $row;
}

mysqli_close($conn);

echo json_encode($data);
?>