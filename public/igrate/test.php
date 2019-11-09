<?php 
$client = new SoapClient("http://ec2-23-20-207-240.compute-1.amazonaws.com:90/DataMigration.svc?wsdl");

//var_dump($client);
$testcenter["Name"]="Test Center 1";
$testcenter["Code"]="TC1";
$testcenter["MACID"]="A4:BA:DB:E2:3C:81";;
$data[0] = (object)$testcenter;

$testcenter["Name"]="Test Center 2";
$testcenter["Code"]="TC2";
$testcenter["MACID"]="C4:XA:DB:E2:3C:86";;
$data[1] = (object)$testcenter;

echo "Parameter sent to SARAS WS<hr>";
echo "<pre>";
 print_r($data);
echo "</pre>";

$response=$client->UpsertTestCenter($data); 
echo "Response from SARAS WS<hr>";
echo "<pre>";
print_r ($response);
echo "</pre>";
 
?>