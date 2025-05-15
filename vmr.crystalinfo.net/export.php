<?php

include_once("init.php");


$result = $_GET['query'];

//$result = $db->query("SELECT serial_number AS SERI_NO, stock_name AS URUN_ADI, contact1 AS PERSONEL, status AS DURUMU FROM stock_serial");
/*
while ($row = mysqli_fetch_array($result)) {

                                    echo $row['username']; 
									echo $row['password'];
									echo $row['user_type']; 
                                    echo $row['answer'];

                                } 
	
    $data1 = array(
        array("First Name" => "Nitya", "Last Name" => "Maity", "Email" => "nityamaity87@gmail.com", "Message" => "Test message by Nitya"),
        array("First Name" => "Codex", "Last Name" => "World", "Email" => "info@codexworld.com", "Message" => "Test message by CodexWorld"),
        array("First Name" => "John", "Last Name" => "Thomas", "Email" => "john@gmail.com", "Message" => "Test message by John"),
        array("First Name" => "Michael", "Last Name" => "Vicktor", "Email" => "michael@gmail.com", "Message" => "Test message by Michael"),
        array("First Name" => "Sarah", "Last Name" => "David", "Email" => "sarah@gmail.com", "Message" => "Test message by Sarah")
    );
    */
    function filterData(&$str)
    {
        $str = preg_replace("/\t/", "\\t", $str);
        $str = preg_replace("/\r?\n/", "\\n", $str);
        if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
    }
    
    // file name for download
    $fileName = "codexworld_export_data" . date('Ymd') . ".xls";
    
    // headers for download
    header("Content-Disposition: attachment; filename=\"$fileName\"");
    header("Content-Type: application/vnd.ms-excel");
    
    $flag = false;
    foreach($result as $row) {
        if(!$flag) {
            // display column names as first row
            echo implode("\t", array_keys($row)) . "\n";
            $flag = true;
        }
        // filter data
        array_walk($row, 'filterData');
        echo implode("\t", array_values($row)) . "\n";

    }
    
    exit;
	
?>