<?php
require_once 'functions.php';
require_once 'config.php';


// This script pulls a messege from the SQS job queue and ensures an update thumbnail exists
global $conf;
$sqs = new AmazonSQS();
$sdb = new AmazonSDB();
$s3 = new AmazonS3();

$response = $sqs->create_queue('photo-jobs');
$queue = $response->body->CreateQueueResult->QueueUrl;

function get_message() {
	global $sqs, $queue;
	$response = $sqs->receive_message($queue);
	$message = $response->body->ReceiveMessageResult->Message;
	return $message;
}


$size = $sqs->get_queue_size($queue);

if ($size > 0) {
	for ($i = 0; $i <= $size; $i++) {
		error_log("--- job $i of $size ---");	
		$job = (get_message());
		$image = $job->Body;
		if (stristr($image, 'jpg') || stristr($image, 'png')) {
			create_thumb($image);
			$response = $sdb->put_attributes($conf['COLLECTION_DB'], $image, array(
						'location' => $image,
						));
		} else {
			error_log("file - $image is not an image, removing...");
			$s3->delete_object ( $conf['collection'], $image );
		}
		$r = $sqs->delete_message ( $queue, $job->ReceiptHandle);
		if ($r->isOK()) {
			error_log("--- Removed job for $image from queue ---");
		}
	} 
	} else {
		error_log("--- There are no images to proccess ---");
}
?>

