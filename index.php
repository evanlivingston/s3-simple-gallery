<head>
    <title>Evan Livingston | Photos</title>
	<link rel="stylesheet" type="text/css" href="style.css" />
    <script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
    <script type="text/javascript">  

	var next_token, sort;
		


	// Lets do some stuff when the page is loaded
	$(document).ready(function() {
		get_collection_chunk();
	}); // end document ready

        $(window).data('ajaxready', true).scroll(function(e) {
    if ($(window).data('ajaxready') == false) return;

    if ($(window).scrollTop() >= ($(document).height() - $(window).height())) {
       
		console.log('ok');
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
			console.log(data);
			next_token = data.next;
			$.each(data.images, function(i, item) {
				//console.log(item);
				$(".photos").append('<a href="' + item.location + '"> <img src="' + item.thumb + '" alt="some_text"></a>');
			});
                $(window).data('ajaxready', true);
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
