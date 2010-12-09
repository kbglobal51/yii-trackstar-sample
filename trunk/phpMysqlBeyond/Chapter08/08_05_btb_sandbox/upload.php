<?php

// In an application, this could be moved to a config file
$upload_errors = array(
	// http://www.php.net/manual/en/features.file-upload.errors.php
	UPLOAD_ERR_OK 				=> "No errors.",
	UPLOAD_ERR_INI_SIZE  	=> "Larger than upload_max_filesize.",
  UPLOAD_ERR_FORM_SIZE 	=> "Larger than form MAX_FILE_SIZE.",
  UPLOAD_ERR_PARTIAL 		=> "Partial upload.",
  UPLOAD_ERR_NO_FILE 		=> "No file.",
  UPLOAD_ERR_NO_TMP_DIR => "No temporary directory.",
  UPLOAD_ERR_CANT_WRITE => "Can't write to disk.",
  UPLOAD_ERR_EXTENSION 	=> "File upload stopped by extension."
);
$error = $_FILES['file_upload']['error'];
$message = $upload_errors[$error];

echo "<pre>";
print_r($_FILES['file_upload']);
echo "</pre>";
echo "<hr />";

?>
<html>
	<head>
		<title>Upload</title>
	</head>
	<body>
	
<?php
// The maximum file size (in bytes) must be declared before the file input field
// and can't be larger than the setting for upload_max_filesize in php.ini.
//
// This form value can be manipulated. You should still use it, but you rely 
// on upload_max_filesize as the absolute limit.
//
// Think of it as a polite declaration: "Hey PHP, here comes a file less than X..."
// PHP will stop and complain once X is exceeded.
// 
// 1 megabyte is actually 1,048,576 bytes.
// You can round it unless the precision matters.
?>

		<?php if(!empty($message)) { echo "<p>{$message}</p>"; } ?>
		<form action="upload.php" enctype="multipart/form-data" method="POST">

		  <input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
		  <input type="file" name="file_upload" />

		  <input type="submit" name="submit" value="Upload" />
		</form>
	
	</body>
</html>