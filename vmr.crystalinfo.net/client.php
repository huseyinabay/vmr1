 <?php
 /*
 $client = new SoapClient('http://172.16.10.10/Tobe/services/RequirementManagement?wsdl');
 $params = array('usercode'=> 'webservice','password'=>'Yigidim2006');
 $result = $client->createOrder( $parms );
 print_r($result);

 */
 
 $client = new SoapClient("http://172.16.10.10/Tobe/services/RequirementManagement?wsdl", array(
    "usercode"      => "webservice", 
    "password"   => "Yigidim2006",
    "trace"      => 1, 
    "exceptions" => 0)); 

$client->yourFunction();

print "<pre>\n"; 
print "Request: \n".htmlspecialchars($client->__getLastRequest()) ."\n"; 
print "Response: \n".htmlspecialchars($client->__getLastResponse())."\n"; 
print "</pre>"; 

 
 
 ?>

 <script>
 /*
    ServiceAutharization auth = new ServiceAutharization();
    auth.setUsercode(webservice);
    auth.setPassword(Yigidim2006);
    RemoteCampaignManagerProxy remoteCampaignManager = new RemoteCampaignManagerProxy("http://172.16.10.10/Tobe/services/RequirementManagement?wsdl");
    ServiceResult result = remoteCampaignManager.addContactsToCampaign(auth,CAMPAIGNID, TBLCONTACTS.toXmlString());
    if (!Compare.equal(result.getErrorCode(), "NOERROR")) {
          MessageUtil.logMessage(ScheduleOperations.class, Level.FATAL, "Has not been worked successfully " + result.getStatusMessage());
    }     
*/
	
	</script>
