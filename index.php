<?php
require_once 'functions.php';

$thumbs = 'evanlivingston.photos.thumbs';
$collection = 'evanlivingston.photos';
$images = construct_links($collection);




// He we loop through build the html to display all images
foreach ($images['images'] as $image) {
    echo "<a href='https://s3.amazonaws.com/$collection/$image'><img src='https://s3.amazonaws.com/$thumbs/$image' alt='some_text'></a>";
    //create_thumb($image);
}


?>
