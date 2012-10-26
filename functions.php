<?php 
require_once 'AWSSDKforPHP/sdk.class.php';
require_once 'config.php';

$s3 = new AmazonS3();

function get_num_chunks($bucket, $elements_at_a_time) {
	$chunks = array_chunk(list_objects_in_folder($bucket), $elements_at_a_time);
	return sizeof($chunks);
}

// A general function to get the contents of a bucket or folder.
function list_objects_in_folder($bucket, $folder='') {
	global $s3;

	$r = $s3->get_object_list($bucket, array(
		'prefix' => $folder
	));
	return $r;
}

function get_collection_part($bucket, $elements_at_a_time, $chunk_num) {
	$chunks = array_chunk(list_objects_in_folder($bucket), $elements_at_a_time);
	error_log(" --- size of chunked array = " . sizeof($chunks) . " --- ");
	return $chunks[$chunk_num];
}

// We loop though and get the links for all the images in a folder
function construct_links($bucket, $folder='') {

	$output = array();
	$objects = list_objects_in_folder($bucket, $folder);
	foreach ($objects as $object) {
		$output['images'][] =  urlencode($object); 
	}
	
	return $output;	
}
function cleanup_collection($bucket, $folder='') {
	global $s3;
	$objects = list_objects_in_folder($bucket, $folder);
	foreach ($objects as $object) {
		if (stristr($object, 'jpg') || stristr($object, 'png')) {
		} else  {
			$r = $s3->delete_object($bucket, $object);
			if ($r->isOK())
				error_log(" --- $object was removed --- ");
			else
				error_log(" --- there was an error removing $object --- ");
		}
	}
}

?>
