<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-type" content="text/html;charset=UTF-8" /> 
		<title>Blessing the Children Cropping Tool</title>
		<script src="js/jquery.min.js" type="text/javascript"></script>
		<script src="js/jquery.Jcrop.js" type="text/javascript"></script>
		<script src="js/jquery.color.js" type="text/javascript"></script>
		<link rel="stylesheet" href="css/jquery.Jcrop.css" type="text/css" />

		<script type="text/javascript">

			var oldPath = "";
			var jcrop_api;
			var old_image;
			var child_name;
			var lastImageData = 0;

			jQuery(function($) {

				var $_POST = <?php echo json_encode($_POST); ?>;

				child_name = ($_POST["child_name"]);
				document.getElementById('childNameDiv').innerHTML ="<p>Child's Name: " + child_name + "</p>";
			});

			function setCropToImage() {
				$('#target2').Jcrop({
					bgFade:     true,
					bgOpacity: .3,
					setSelect: [ 0, 0, 90, 120 ],
					aspectRatio: 7/6,
					onSelect: setCoords,
					onChange: setCoords,
					onDblClick: useImage
				});				
			}

			function drawCroppedImage(img, crop_x, crop_y, crop_width, crop_height) {
				//create the temporary canvas to crop the image
				var canvas = document.createElement('canvas');
				var ctx = canvas.getContext('2d');

				canvas.width = 340;
				canvas.height = 296;

				// draw the image with offset
				ctx.drawImage(img, crop_x,crop_y,crop_width,crop_height, 0,0,canvas.width,canvas.height);

				// output the base64 of the cropped image
				//document.getElementById('output').innerHTML = "<img id='target2' src=" + canvas.toDataURL('image/jpeg') + ">";
				var canvasData = canvas.toDataURL("image/jpg");
				lastImageData = canvasData;
				document.getElementById('output').innerHTML = "<img id='target2' src=" + canvasData + ">";
			}

			var AjaxPostCall = function(url, dataJSON, callback, error) {
				$.ajax({
						type : 'POST',
						url : url,
						dataType : 'json',
						data: {
							data : dataJSON
						},
						success : function(data){
							callback(data);
						},
						error : function(err) {
							error(err.responseText);
						}
				});
			};

			function uploadImage() {
				if(lastImageData) {
					var replyText = {name: child_name, imageData: lastImageData, path: oldPath};

					AjaxPostCall("testSave.php", replyText, function(data){
						var imageName = data.substr(1);
						if (confirm('The picture has been saved with the name: \n' + imageName)) {
						    //location = "http://www.blessingthechildren.org/children/upload";
						    window.close();
						}
						//alert('The picture has been saved with the name: \n' + imageName);
						//location = "http://www.blessingthechildren.org/children/upload";
					}, function(error){
						console.log(error);
					});
				} else {
					alert("The cropped image is not available.")
				}

			}

			function AjaxSucceeded(result) {  
			    if (result.d != null && result.d != '') {
			        alert("Success? " + result.d);  //result must be followed by .d to display the results, this is a json requirement
			      }
			}

			function AjaxFailed(result) {
			    alert("Failure? " + result.status + ' ' + result.statusText);
			}

			function revertToOldImage() {
				document.getElementById('output').innerHTML = "<img id='target2' src=" + old_image.src + ">";
				setCropToImage();
			}
					
			function useImage() {
				var c = jcrop_api;
				var img = document.getElementById('target2');
				console.log(img.src);

				drawCroppedImage(img, c.x, c.y, c.w, c.h);
			}

			function setCoords(c) {
				jcrop_api = c;
			}

			function drawImage(src) {
				// output the image
				var img = new Image;
				img.src = src;
				old_image = img;
				document.getElementById('output').innerHTML = "<img id='target2' src=" + img.src + ">";
				setCropToImage();
			}

			function setPath(path) {
				drawImage(path);
				oldPath = path;
			}

		</script>

	</head>

	<body>
		<div class="main">
			<div class="article">

				<h1>Blessing the Children</h1>
				<h2>Image Cropping Tool</h2>
				<div id="childNameDiv" ></div>
				<div id="output" ></div>

			</div>

			<input type="button" id="button1" value="Crop Image" onclick="useImage();">
			<input type="button" id="button2" value="Revert to Original" onclick="revertToOldImage();">
			<input type="button" id="button3" value="Upload Image" onclick="uploadImage();">
		</div>

	</body>
</html>

<?php
$target_path = "uploads/";

$target_path = $target_path . basename( $_FILES['uploadedfile']['name']); 

if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
    echo "<script>setPath('" . $target_path . "');</script>";
} else{
    echo "<p><strong>There was an error uploading the file, please try again!</strong></p>";
}

?>