<?php

	$conf['app_version'] = '1';
	$conf['debug'] = 0;

	$conf['JOB_QUEUE'] = 'photo-jobs';
	$conf['COLLECTION_DB'] = 'PHOTO_COLLECTION_DB';

	$conf['buckets']['photo'] = 'evanlivingston.photos'; 
	$conf['buckets']['video'] = 'evanlivingston.videos'; 
	$conf['buckets']['thumb'] = 'evanlivingston.thumbnails'; 

	$conf['cdn']['thumb'] = 'http://d3az4lu37d2qfs.cloudfront.net/'; 
	$conf['cdn']['video'] = 'http://s2100wzgn1rxy5.cloudfront.net/'; 
	$conf['cdn']['photo'] = 'https://s3.amazonaws.com/evanlivingston.photos/'; 

	$conf['THUMB_CDN'] = 'http://d2732vs6arm0zd.cloudfront.net/';
	$conf['VIDEO_CDN'] = 'http://s2100wzgn1rxy5.cloudfront.net/';


?>
