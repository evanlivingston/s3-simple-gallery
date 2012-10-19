<?php 
require_once 'AWSSDKforPHP/sdk.class.php';
require_once 'config.php';

$s3 = new AmazonS3();

//Here we set our bucket
$PHOTOS = 'evanlivingston.photos';


function createthumb($name,$filename,$new_w,$new_h){
        $system=explode('.',$name);
		if (stristr(end($system), 'jpg') || stristr(end($system), 'jpeg')) {
             $src_img=imagecreatefromjpeg($name);
        }
		if (stristr(end($system), 'png')) {
             $src_img=imagecreatefrompng($name);
        } 
		
        $old_x=imageSX($src_img);
        $old_y=imageSY($src_img);
        if ($old_x > $old_y) {
	     $y = 0;
	     $x = ($old_x - $old_y) / 2;
             $thumb_w=$new_w;
	     $smallestSide = $old_y;
             //$thumb_h=$old_y*($new_h/$old_x);
        }
        else { 
   	     $x = 0;
  	     $y = ($old_y - $old_x) / 2;
  	     $smallestSide = $old_x;
             //$thumb_w=$old_x*($new_w/$old_y);
             //$thumb_h=$new_h;
        }
	$thumbSize = 100;
	$dst_img = imagecreatetruecolor($thumbSize, $thumbSize);
	imagecopyresampled($dst_img, $src_img, 0, 0, $x, $y, $thumbSize, $thumbSize, $smallestSide, $smallestSide);

        //$dst_img=ImageCreateTrueColor($thumb_w,$thumb_h);
        //imagecopyresampled($dst_img,$src_img,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y); 
        if (preg_match("/png/",$system[1])){
             imagepng($dst_img,$filename); 
        } else {
             imagejpeg($dst_img,$filename); 
        }
        imagedestroy($dst_img); 
        imagedestroy($src_img); 
}


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


function create_thumb($object) {
	global $conf, $s3;

	//$s3->delete_object ( $conf['thumbs'], $object );
	// TODO checkt object acl before setting it
	$response = $s3->set_object_acl($conf['collection'], $object, AmazonS3::ACL_PUBLIC);
	$url = $s3->get_object_url($conf['collection'], $object);
	$dir = dirname($object);

	// Check for existance of object
	if ($s3->if_object_exists($conf['thumbs'], $object)) {
			error_log(" [ Thumb for $object already exists ] ");
	} else {
			$tempfile = '/tmp/thumb_'.urlencode(basename($object));
			copy($url, '/tmp/'.urlencode(basename($object)));
			createthumb('/tmp/'.urlencode(basename($object)), '/tmp/thumb_'.urlencode(basename($object)), 100,100);
			$sblob = file_get_contents($tempfile);
			// create image hash here
			//error_log(md5_file($tempfile ));
			error_log(" --- creating thumb for $object --- ");
			$response = $s3->create_object($conf['thumbs'], $object, array(
									'body' => $sblob , 
									'acl' => AmazonS3::ACL_PUBLIC, 
									'contentType' => 'image/png',));
			unlink($tempfile);
			unlink('/tmp/' .urlencode(basename($object)));
	}
}

//echo json_encode(construct_links($conf['collection']));
?>
