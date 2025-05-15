<?php
//disable wsdl cache  
  ini_set("soap.wsdl_cache_enabled", "0");   
     
     
   $ws = 'ExampleClassWS.wsdl';  //wsdl file  
     
  //define the soap server location  
   $vLocation = "http://172.16.10.10/Tobe/services/RequirementManagement?wsdl";  
     
      
     
   //define the soap client object  
    $client = new SoapClient($ws, array(  
    "location" => $vLocation,  
    "trace"=>1, "exceptions"=>1  //optional parameters for debugging  
    ));  
       
     
  //call the method, boom() from the soap server class  
    $output = $client->boom(array('first'=>'PHP SOAP','last'=>'Tutorial'));  
       
  //access the value of the returned complex type, boomResponse  
  $value = $output->return;   
     
  echo "<p>
 boom response: ".$value;  
     
  //Optional - SOAP Client methods to see the last SOAP request and response  
   //echo("
REQUEST :
" . htmlspecialchars($client->__getLastRequest()) . "
");  
   //echo("
RESPONSE:
" .htmlspecialchars($client->__getLastResponse()) . "
");           
               
     
     
  //call the method, getDate()  
    $output = $client->getDate();  
     
  //access the value of the returned complex type, getDateResponse  
  $value = $output->return;    
     
  echo "<p>
 getDate response: ".$value;  
       
  //Optional - SOAP Client methods to see the last SOAP request and response  
  // echo("
REQUEST :
" . htmlspecialchars($client->__getLastRequest()) . "
");  
   //echo("
RESPONSE:
" .htmlspecialchars($client->__getLastResponse()) . "
");       

?>