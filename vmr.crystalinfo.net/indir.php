<?php
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="zimmetrapor.xls"'); // <-- düzenleyin
header ('Content-Transfer-Encoding: binary');
header('Cache-Control: max-age=0');
$dosyayolu = "upload/excel.xls"; // <-- düzenleyin
readfile($dosyayolu);

?>