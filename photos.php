<?php 
require_once 'AWSSDKforPHP/sdk.class.php';
require_once 'config.php';
require_once 'functions.php';


$sdb = new AmazonSDB();
$next_token = (isset($_GET['next'])) ? $_GET['next'] : null;

$collection = $conf['COLLECTION_DB'];
$select_expression = "select * from $collection where itemname() is not null order by itemname() desc";

if ($next_token)
{
	$response = $sdb->select($select_expression, array(
		'NextToken' => $next_token,
	));
}
else {
	$response = $sdb->select($select_expression);
}

$items = $response->body->SelectResult;
$output = array();
foreach ($items->Item as $item) {
	$output['images'][] = array('location' => (string)$item->Attribute[0]->Value, 'thumb' => (string)$item->Attribute[1]->Value);
}

$output['next'] = (string)$response->body->SelectResult->NextToken;

echo json_encode($output);

?>
