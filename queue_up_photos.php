<?php
require_once 'functions.php';
require_once 'config.php';

// This script will create an SQS queue and fill it will all items from the photo library.

$sqs = new AmazonSQS();

// Here we take chunks of the collection, chunk them further and send them to an SQS queue
function create_queue_items($queue, $set, $type) {
	global $sqs, $conf;
	$sets = array_chunk($set, 10);
	foreach ($sets as $groups) {
		$messages = array();
		$i = 0;
		foreach ($groups as $e) {
			$messages[] = array( 'Id' => $i, 'MessageBody' => json_encode(array('file' => $e, 'type' => $type)));
			$i++;
		}
		$response = $sqs->send_message_batch( $queue, $messages );
		if (!$response->isOK()) {
			print_r($response);
			error_log("### Problem adding items to queue");
		}
	}
}

// This function gets a list of all items in a bucket and chunks them into managable pieces
// and hands off the pieces to create_queue_items
function proccess_collection($type) {
	global $sqs, $conf;

	$response = $sqs->create_queue($conf['JOB_QUEUE']);
	$queue = $response->body->CreateQueueResult->QueueUrl;
	$num_of_chunks = get_num_chunks($conf['buckets'][$type], 100);
	error_log(" +++ Number of Chunks = $num_of_chunks +++ " );
	for ($i = 0; $i < $num_of_chunks; $i++) {
		error_log(" +++ Processing Chunk $i ++++ ");
		$set = get_collection_part($conf['buckets'][$type], 100, $i);
		create_queue_items($queue, $set, $type);
	}
}

// Let's start the proccess
proccess_collection('video');
?>
