<?php 
require_once 'AWSSDKforPHP/sdk.class.php';
require_once 'config.php';
require_once 'functions.php';

$sdb = new AmazonSDB();

$collection = $conf['COLLECTION_DB'];
$response = $sdb->select("select * from $collection where itemname() is not null order by itemname() desc");
$items = $response->body->SelectResult;
$output = array();
foreach ($items->Item as $item) {
	$output[] = array('location' => (string)$item->Attribute[0]->Value, 'thumb' => (string)$item->Attribute[1]->Value);
}

	
$next_token = $response->body->SelectResult->NextToken;

echo json_encode($output);

?>
