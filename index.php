<head>
    <title>Evan Livingston | Photos</title>
	<link rel="stylesheet" type="text/css" href="style.css" />
	<link rel="stylesheet" type="text/css" href="./colorbox/example2/colorbox.css" />
	<script src="flowplayer.min.js"></script>
    <script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
	<script src="./colorbox/colorbox/jquery.colorbox.js"></script>
    <script type="text/javascript">  

	var next_token, sort;
		


	// Lets do some stuff when the page is loaded
	$(document).ready(function() {
		get_collection_chunk();
	}); // end document ready

        $(window).data('ajaxready', true).scroll(function(e) {
    if ($(window).data('ajaxready') == false) return;

    if ($(window).scrollTop() >= ($(document).height() - $(window).height())) {
       
		get_collection_chunk();
        $(window).data('ajaxready', false);
    }
});

	// function to 
	function get_collection_chunk()
	{
		$.ajax({
		url : "photos.php",
		data : { next : next_token, sort : sort },
		dataType: 'json',
		success : function (data) {
			//console.log(data);
			next_token = data.next;
			$.each(data.images, function(i, item) {
				//console.log(item);
				$(".photos").append('<a class="gallery" href="' + item.location + '"> <img src="' + item.thumb + '" alt="some_text"></a>');
			});
                $(window).data('ajaxready', true);
				jQuery('a.gallery').colorbox({ opacity:0.5 , rel:'group1', height: '90%'});
		}
		});
	
	} //end get_collection_chunk 



   
   </script>  
</head>
<body>
	<div class=header>
		<div class=toolbox>
			<a href="#" id="thelink">Settings</a>
		</div> 
	</div>
	<div class=photos>
	</div>
</body>
</html>
