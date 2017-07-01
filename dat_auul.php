<?php

require_once('rl_init.php');

if ($options['auto_upload_disable']) {
	require_once('deny.php');
	exit();
}
error_reporting(0);
ignore_user_abort(true);

login_check();

$id = 1;
require_once(HOST_DIR.'download/hosts.php');
require_once(CLASS_DIR.'http.php');
include(TEMPLATE_DIR.'header1.php');
?>
<br />
<center>
<?php
	// If the user submit to upload, go into upload page
	if ($_GET['action'] == 'upload') {
		// Define another constant
		if(!defined('CRLF')) define('CRLF',"\r\n");
		// The new line variable
		$nn = "\r\n";
		// Initialize some variables here
		$uploads = array();
		$total = 0;
		$hostss = array();
		// Get number of windows to be opened
		$openwin = (int) $_POST['windows'];
		if ($openwin <= 0) $openwin = 4;
		$openwin--;
		// Sort the upload hosts and files
		foreach ($_POST['files'] as $file) {
			foreach ($_POST['hosts'] as $host) {
				$hostss[] = $host;
				$uploads[] = array('host' => $host,
					'file' => DOWNLOAD_DIR.base64_decode($file));
				$total++;
			}
		}
		// Clear out duplicate hosts
		$hostss = array_unique($hostss);
		// If there aren't anything
		if (count($uploads) == 0) {
			//echo lang(46);
			exit;
		}
		$save_style = "";
		if ($_POST['save_style'] != 'Default') {
			$save_style = '&save_style='.urlencode(base64_encode($_POST['save_style']));
		}
		$start_link = "dat_upload.php";
		$i = 0;
		foreach ($uploads as $upload) {
			$getlinks[$i][] = "?uploaded=".$upload['host']."&filename=".urlencode(base64_encode($upload['file'])).$save_style;
			$i++;
			if ($i>$openwin) $i = 0;
		}
?>
<script type="text/javascript">
/* <![CDATA[ */
<?php
	for ($i=0;$i<=$openwin;$i++) {
?>
	var current_dlink<?php echo $i; ?>=-1;
	var links<?php echo $i; ?> = new Array();
<?php
	}
?>
	var start_link='<?php echo $start_link; ?>';
	var usingwin = 0;

	function startauto()
		{
			current_dlink0=-1;
			//document.getElementById('auto').style.display='none';
			nextlink0();
<?php
	for ($i=1;$i<=$openwin;$i++) {
?>
			if (links<?php echo $i; ?>.length > 0) {
				current_dlink<?php echo $i; ?>=-1;
				nextlink<?php echo $i; ?>();
			} else {
				document.getElementById('idownload<?php echo $i; ?>').style.display = 'none';
			}
<?php
	}
?>
		}

<?php
	for ($i=0;$i<=$openwin;$i++) {
?>
	function nextlink<?php echo $i; ?>() {
		current_dlink<?php echo $i; ?>++;
		if (current_dlink<?php echo $i; ?> < links<?php echo $i; ?>.length) {
			//setTimeout(function(){  }, 4000);
			opennewwindow<?php echo $i; ?>(current_dlink<?php echo $i; ?>);
		} else {
			document.getElementById('idownload<?php echo $i; ?>').style.display = 'none';
		}
	}

	function opennewwindow<?php echo $i; ?>(id) {
		window.frames["idownload<?php echo $i; ?>"].location = start_link+links<?php echo $i; ?>[id]+'&auul=<?php echo $i; ?>';
	}
<?php
	}
		for ($j=0;$j<=$openwin;$j++) {
			foreach ($getlinks[$j] as $i=>$link) {
				echo "\tlinks{$j}[".$i."]='".$link."';\n";
			}
		}
?>
/* ]]> */
</script>
<?php
	for ($i=0;$i<=$openwin;$i++) {
		if (( $i+1 )% 2) echo "<br />";
?>
<iframe width="49%" height="250" src="" name="idownload<?php echo $i; ?>" id="idownload<?php echo $i; ?>" style="float:left; border:1px solid;"><?php echo lang(30); ?></iframe>
<?php
	}
?>
<script type="text/javascript">startauto();</script><br />
<?php

	} else {
?>
<?php 
$options['show_all'] = true;
$_COOKIE["showAll"] = 1;
_create_list();
require_once("classes/options.php");
unset($Path);
?>
<form action="dat_auul.php?action=upload" method="POST" name="fff" role="form">
<input type="hidden" name="hosts[]" id="input" class="form-control" value="youtube.com">
<input type="hidden" name="windows" id="input" class="form-control" value="2">
<input type="hidden" name="save_style" id="input" class="form-control" value="Default">
<?php
	foreach($list as $key => $file) {
		if(file_exists($file["name"])) {
?>
	<td><input type="hidden" name="files[]" value="<?php echo base64_encode(basename($file["name"])); ?>" />
<?php
		}
	}
?>
</form>
<script type="text/javascript">
	document.fff.submit();

</script>
<?php

}

?>
</center>
<?php include(TEMPLATE_DIR.'footer.php'); ?>