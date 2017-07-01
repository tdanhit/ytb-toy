<?php 
@session_start();
$htmlResult = "";
//chmod('../thumbnails', 777);
function createThumbnail($new_width,$new_height, $new_name)
{
    $image_dir = realpath('../thumbnails/logo/');
    $image_file = $image_dir . "/new-hd-logo.png";

    $mime = getimagesize($image_file);

    if($mime['mime']=='image/png') { 
        $src_img = imagecreatefrompng($image_file);
    }
    if($mime['mime']=='image/jpg' || $mime['mime']=='image/jpeg' || $mime['mime']=='image/pjpeg') {
        $src_img = imagecreatefromjpeg($image_file);
    }   

    $old_x          =   imageSX($src_img);
    $old_y          =   imageSY($src_img);

    if($old_x > $old_y) 
    {
        $thumb_w    =   $new_width;
        $thumb_h    =   $old_y*($new_height/$old_x);
    }

    if($old_x < $old_y) 
    {
        $thumb_w    =   $old_x*($new_width/$old_y);
        $thumb_h    =   $new_height;
    }

    if($old_x == $old_y) 
    {
        $thumb_w    =   $new_width;
        $thumb_h    =   $new_height;
    }

    $new_thumb_loc = $image_dir . DIRECTORY_SEPARATOR. $new_name;
    @unlink($new_thumb_loc);
    /*$dst_img        =   ImageCreateTrueColor($thumb_w,$thumb_h);

    imagecopyresampled($dst_img,$src_img,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y); 

    // New save location
    $new_thumb_loc = $image_dir . $new_name;

    if($mime['mime']=='image/png') {
        $result = imagepng($dst_img,$new_thumb_loc,0);
    }
    if($mime['mime']=='image/jpg' || $mime['mime']=='image/jpeg' || $mime['mime']=='image/pjpeg') {
        $result = imagejpeg($dst_img,$new_thumb_loc,80);
    }*/
    // ffmpeg -i input.jpg -vf scale=320:240 output_320x240.png

    if(DIRECTORY_SEPARATOR == '/') {
		$shell_command_thumbnail = '/usr/local/bin/ffmpeg -i "' . $image_file .'" -vf scale="'.$new_width.'":"' .$new_height. '" ' .$new_thumb_loc;
	} else {
		$shell_command_thumbnail = 'C:\lib\ffmpeg -i "' . $image_file .'" -vf scale="'.$new_width.'":"' .$new_height. '" ' .$new_thumb_loc;
	}
	//echo $shell_command_thumbnail;
    //imagedestroy($dst_img);
    if(DIRECTORY_SEPARATOR == '/') {
		shell_exec($shell_command_thumbnail);
	} else {
		exec($shell_command_thumbnail);
	} 
    imagedestroy($src_img);

    return true;
}
// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
	$target_dir = "../thumbnails/logo/";
	$target_file = $target_dir . "new-hd-logo.png"; //basename($_FILES["fileToUpload"]["name"]);
	$uploadOk = 1;
	$imageFileType = pathinfo($_FILES["fileToUpload"]["name"],PATHINFO_EXTENSION);

	// Check file size
	if ($_FILES["fileToUpload"]["size"] > 200000) {
		//echo "Sorry, your file is too large.";
		$htmlResult .= "<p class ='alert alert-danger'> Sorry, your file is too large. </p>";
		$uploadOk = 0;
	}
	// Allow certain file formats
	if($imageFileType != "png" ) {
		//echo "Sorry, only JSON files are allowed.";
		$htmlResult .= "<p class ='alert alert-danger'> Sorry, only PNG files are allowed.</p>";
		$uploadOk = 0;
	}
	// Check if $uploadOk is set to 0 by an error
	if ($uploadOk == 0) {
		//echo "Sorry, your file was not uploaded.";
		$htmlResult .= "<p class ='alert alert-danger'> Sorry, your thumbnail was not uploaded.</p>";
// if everything is ok, try to upload file
	} else {
		//chmod($target_file, 777);
		// Check if file already exists
		if (file_exists($target_file)) {
			@unlink($target_file);
			//echo "Sorry, file already exists.";
			//$uploadOk = 0;
		}
		if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
			// Scale image maxres(1280 - 720)
			createThumbnail(640, 360, 'new-hd-logo-1280x720.png');
			// Scale image standard(640 - 480)
			createThumbnail(320, 240, 'new-hd-logo-640x480.png');
			// Scale image high(480, 360)
			createThumbnail(240, 180, 'new-hd-logo-480x360.png');

			//echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
			$htmlResult .= "<p class ='alert alert-success'> The thumbnail" .$target_file." has been uploaded. </p> <br>";
			$htmlResult .= "<img src=" .@$target_file." height='128' width='128'>";
		} else {
			//echo "Sorry, there was an error uploading your file.";
			$htmlResult .= "<p class ='alert alert-danger'> Sorry, there was an error uploading your thumbnail.</p>";
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
	<title>Youtube Tool - Upload Thumbnail</title>

	<!-- Bootstrap CSS -->
	<link href="//netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
	<h1 class="text-center">Youtube Tool - Upload Thumbnail</h1>
	<div class="container ">
		<div class="row">
			<div class="col-xs-5 col-md-offset-3">
				<form action="" method="post" enctype="multipart/form-data" class="form-horizontal " role="form">
					<div class="form-group">
						<label for="file">Select Thumbnail File: </label>
						<input class="form-control" placeholder="Select json file" type="file" name="fileToUpload" id="fileToUpload">
					</div>
					<div class="form-group">
						<?php echo $htmlResult; ?>
						<!-- <button type="button" class="btn btn-info" id="submit">Upload File</button> -->
					</div>
					<div class="form-group">
						<input type="submit" value="Upload Thumbnail" name="submit" class="btn btn-info" >
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
