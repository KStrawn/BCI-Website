<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-type" content="text/html;charset=UTF-8" /> 
		<title>Blessing the Children Cropping Tool</title>
	</head>
	<body>
		<h1>UPLOAD!!</h1>
	</body>
</html>

<?
$childname = $_GET["urll"];
print $childname;


	set_time_limit(300);//for uploading big files
		
	$paths='children';

	$ftp_server='ftp.blessingthechildren.org';

	$ftp_user_name='KStrawn';

	$ftp_user_pass='Free22@@';

$fp = fopen($childname, 'r');


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


	// close the FTP connection
	ftp_close($conn_id);	
