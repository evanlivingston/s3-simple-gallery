<?php
require_once 'functions.php';
require_once 'config.php';

// This script pulls a messege from the SQS job queue and ensures an update thumbnail exists
global $conf;
$sqs = new AmazonSQS();
$sdb = new AmazonSDB();
$s3 = new AmazonS3();

// Get the full url of the SQS queue
$response = $sqs->create_queue('photo-jobs');
$queue = $response->body->CreateQueueResult->QueueUrl;

function get_a_queue_message() {
	global $sqs, $queue;
	$response = $sqs->receive_message($queue);
	$message = $response->body->ReceiveMessageResult->Message;
	return ($message);
}

function replace_extension($filename, $new_extension) {
    return preg_replace('/\..+$/', '.' . $new_extension, $filename);
}

$size = $sqs->get_queue_size($queue);

if ($size > 0) {
	for ($i = 0; $i <= $size; $i++) {
		error_log("--- job $i of $size ---");	
		$job = (get_a_queue_message());
		$message = json_decode($job->Body, true);
	
		$file = $message['file'];
		$type = $message['type'];
		$thumb = replace_extension($file, 'jpg');
		if (stristr($file, 'jpg') ||stristr($file, 'mov') || stristr($file, 'png')) {

			$s3->set_object_acl($conf['buckets']['thumb'], $file, AmazonS3::ACL_PUBLIC);
			$s3->set_object_acl($conf['buckets'][$type], $file, AmazonS3::ACL_PUBLIC);
			$response = $sdb->put_attributes($conf['COLLECTION_DB'], $file, array(
						'thumb' => $conf['cdn']['thumb'] . $thumb,
						//TODO location should come from queue because we are dealing with different CDNs
						'location' => $conf['cdn'][$type] . $file 
			));
			//TODO only delete sqs message is response from sdb-put is ok
			// Remove the sqs message
		}
		$r = $sqs->delete_message ( $queue, $job->ReceiptHandle);
		if ($r->isOK()) {
			error_log("--- Removed job for $file from queue ---");
		}
	} 
	} else {
		error_log("--- There are no images to proccess ---");
}
?>

