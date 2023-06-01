<?php

	$data = $_POST['data'];

	$name = $data['name'];
	$imageData = $data['imageData'];
	$oldPath = $data['path'];




	// Remove the headers (data:,) part.  
	// A real application should use them according to needs such as to check image type
	$filteredData=substr($imageData, strpos($imageData, ",")+1);

	// Need to decode before saving since the data we received is already base64 encoded
	$unencodedData=base64_decode($filteredData);

	//echo "unencodedData".$unencodedData;

	$ftp_server='ftp.blessingthechildren.org';

	$ftp_user_name='bciphoto';

	$ftp_user_pass='Face1010';




	// set up a connection to ftp server
	$conn_id = ftp_connect($ftp_server);

	// login with username and password
	$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);

	// Save file.  This example uses a hard coded filename for testing, 
	// but a real application can specify filename in POST variable
	$fp = fopen( '../'.$name.'.jpg', 'w' );
	fwrite( $fp, $unencodedData);
	if($fp) {
		echo json_encode(':'.$name.'.jpg');
		unlink($oldPath);
	}
	fclose( $fp );
?>

