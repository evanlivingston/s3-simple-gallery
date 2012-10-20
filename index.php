<head>
    <title>Evan Livingston | Photos</title>
	<link rel="stylesheet" type="text/css" href="style.css" />
    <script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
    <script type="text/javascript">  

	// Lets do some stuff when the page is loaded
	$(document).ready(function() {
		get_collection_chunk();
	}); // end document ready


	// function to 
	function get_collection_chunk()
	{
		$.ajax({
		url : "photos.php",
		dataType: 'json',
		success : function (data) {
			console.log(data);
			$.each(data, function(i, item) {
				console.log(item);
				$(".photos").append('<a href="' + item.location + '"> <img src="' + item.thumb + '" alt="some_text"></a>');
			});
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
