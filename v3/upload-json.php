<?php 
@session_start();
$htmlResult = "";
// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
	$target_dir = "json-upload/";
	$target_file = $target_dir . "client_secret.json"; //basename($_FILES["fileToUpload"]["name"]);
	$uploadOk = 1;
	$imageFileType = pathinfo($_FILES["fileToUpload"]["name"],PATHINFO_EXTENSION);

	// Check file size
	if ($_FILES["fileToUpload"]["size"] > 500000) {
		//echo "Sorry, your file is too large.";
		$htmlResult .= "<p class ='alert alert-danger'> Sorry, your file is too large. </p>";
		$uploadOk = 0;
	}
	// Allow certain file formats
	if($imageFileType != "json" ) {
		//echo "Sorry, only JSON files are allowed.";
		$htmlResult .= "<p class ='alert alert-danger'> Sorry, only JSON files are allowed.</p>";
		$uploadOk = 0;
	}
	// Check if $uploadOk is set to 0 by an error
	if ($uploadOk == 0) {
		//echo "Sorry, your file was not uploaded.";
		$htmlResult .= "<p class ='alert alert-danger'> Sorry, your file was not uploaded.</p>";
// if everything is ok, try to upload file
	} else {
		// Check if file already exists
		if (file_exists($target_file)) {
			@unlink($target_file);
			//echo "Sorry, file already exists.";
			//$uploadOk = 0;
		}
		if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
			//echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
			$htmlResult .= "<p class ='alert alert-success'> The file" .$target_file." has been uploaded. </p>";
		} else {
			//echo "Sorry, there was an error uploading your file.";
			$htmlResult .= "<p class ='alert alert-danger'> Sorry, there was an error uploading your file.</p>";
		}
	}
}
?>

<!DOCTYPE html>
<html lang="">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Youtube Reup Tool</title>

	<!-- Bootstrap CSS -->
	<link href="//netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
	<h1 class="text-center">Youtube Upload Client Json File</h1>
	<div class="container ">
		<div class="row">
			<div class="col-xs-4 col-md-offset-3">
				<form action="" method="post" enctype="multipart/form-data" class="form-horizontal " role="form">
					<div class="form-group">
						<label for="file">Select Json File: </label>
						<input class="form-control" placeholder="Select json file" type="file" name="fileToUpload" id="fileToUpload">
					</div>
					<div class="form-group">
						<?php echo $htmlResult; ?>
						<input type="submit" value="Upload Credential" name="submit" class="btn btn-info" >
						<!-- <button type="button" class="btn btn-info" id="submit">Upload File</button> -->
					</div>
				</form>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-4 col-md-offset-3">
				<a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/ytb-tool/index.php">Home Page</a>
			</div>
		</div>
	</div>

	<script type="text/javascript" src="../assets/jquery.min.js"></script>
	<script src="../assets/bootstrap.min.js"></script>
	<script type="text/javascript">
		$('#test').click(function(event) {
			if($('#fileToUpload').val() == ''){
				alert('Chưa chọn file !'); return;
			}
			var file_data = $('#fileToUpload').prop('files')[0];   
		    var form_data = new FormData(this);                  
		    form_data.append('file', file_data);
		    form_data.append('act', 'upload');
		    alert(form_data);                            
		    $.ajax({
	                url: 'upload.php', // point to server-side PHP script 
	                dataType: 'text',  // what to expect back from the PHP script, if anything
	                cache: false,
	                contentType: false,
	                processData: false,
	                data: form_data,                         
	                type: 'post',
	                success: function(php_script_response){
	                    alert(php_script_response); // display response from the PHP script, if any
	                }
		     });
			
		});
	</script>
</body>
</html>
