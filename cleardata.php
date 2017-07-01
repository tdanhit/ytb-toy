<?php
$iCountVideos = 0;
$iCountThumb = 0;
if(@$_REQUEST['act'] == 'ajax') {
	$dirListVideos = __DIR__ . '/files/';
	$dirListThumb = __DIR__ . '/thumbnails/';
	$videoCount = count( glob($dirListVideos."/*.*"));
	$thumbCount = count( glob($dirListThumb."/*.*"));

	// Delete videos
	foreach (glob($dirListVideos."/*.*") as $filename) {
	    if (is_file($filename)) {
	    	chmod($filename, 0777);
		   	if (unlink($filename)) {
		      	$iCountVideos++;
		   	}
	    }
	}

	foreach (glob($dirListThumb."/*.*") as $filename) {
	    if (is_file($filename)) {
	    	chmod($filename, 0777);
		   	if (unlink($filename)) {
		      	$iCountThumb++;
		   	}
	    }
	}
	// Delet thumbnails

	if ($iCountVideos == $videoCount && $iCountThumb == $thumbCount) {
		echo 'OK';
	} else {
		echo 'Can not remove files(Videos (' .$videoCount .'), Thumbnails ('.$thumbCount.'))';
	}
}

//header("Location:index.php");
?>