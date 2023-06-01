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

			var jcrop_api;
			var old_image;
			var child_name;
			var lastImageData = 0;

			jQuery(function($) {

				var $_GET = {};

				document.location.search.replace(/\??(?:([^=]+)=([^&]*)&?)/g, function () {
					function decode(s) {
						return decodeURIComponent(s.split("+").join(" "));
					}

					$_GET[decode(arguments[1])] = decode(arguments[2]);
				});

				child_name = ($_GET["child_name"]);
				document.getElementById('childNameDiv').innerHTML ="<p>Child's Name: " + child_name + "</p>";

				document.getElementById('fileinput').addEventListener('change', cropLoadedImage, false);
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
					var replyText = {name: child_name, imageData: lastImageData};

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

			function drawImage(img) {
				// output the image
				old_image = img;
				document.getElementById('output').innerHTML = "<img id='target2' src=" + img.src + ">";
				setCropToImage();
			}

			function cropLoadedImage(evt) {
				//Retrieve the first (and only!) File from the FileList object
				var f = evt.target.files[0]; 

				if (f) {
					var reader = new FileReader();
					reader.readAsDataURL(f);
					reader.onload = function(e) { 
						var img = new Image;
						img.onload = function() {
							drawImage(img);
						}
						img.src = event.target.result;
					}
				} else { 
					alert("Failed to load file");
				}
			}



			function closeee() {
				console.log("trying to close the window");
				self.close();
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


			<p>
			Please specify a file:<br>
			<input type="file" id="fileinput" /><br/>
			</p>

			<input type="button" id="button1" value="Crop Image" onclick="useImage();">
			<input type="button" id="button2" value="Revert to Original" onclick="revertToOldImage();">
			<input type="button" id="button3" value="Upload Image" onclick="uploadImage();">
		</div>

	</body>
</html>

<?
$childname = $_GET["child_name"];
print $childname;

if(isset($_POST["submit"])) {

	set_time_limit(300);//for uploading big files
		
	$paths='children';

	$ftp_server='ftp.blessingthechildren.org';

	$ftp_user_name='KStrawn';

	$ftp_user_pass='Free22@@';

$fp = fopen('http://blessingthechildren.org/children/dolphins.jpg', 'r');


	$filep=$_FILES['userfile']['tmp_name'];


	// set up a connection to ftp server
	$conn_id = ftp_connect($ftp_server);

	// login with username and password
	$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);

	// check connection and login result
	if ((!$conn_id) || (!$login_result)) {
	       echo "FTP connection has encountered an error!";
	       echo "Attempted to connect to $ftp_server for user $ftp_user_name....";
	       exit;
	   } else {
	       echo "Connected to $ftp_server, for user $ftp_user_name".".....";
	   }

	// upload the file to the path specified
	//$upload = ftp_put($conn_id, $paths.'/'.$childname.'.jpg', $filep, FTP_BINARY);

if (ftp_fput($conn_id, $paths.'/werx.jpg', $fp, FTP_BINARY)) {
    echo "Successfully uploaded $file\n";
} else {
    echo "There was a problem while uploading $file\n";
}

	// check the upload status
	if (!$upload) {
	       echo "FTP upload has encountered an error!";
	   } else {
	       echo "Uploaded file with name $childname.jpg to $ftp_server ";
	   }

	// close the FTP connection
	ftp_close($conn_id);	
}