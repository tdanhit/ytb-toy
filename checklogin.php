<?php 
//require_once "functions.php";
require_once('rl_init.php');
include(CLASS_DIR.'http.php');
$usecurl = $cantuse = false;
if (!extension_loaded('openssl')) {
	if (extension_loaded('curl')) {
		$cV = curl_version();
		if (in_array('https', $cV['protocols'], true)) $usecurl = true;
	} else $cantuse = true;
	if ($cantuse) html_error("Need OpenSSL enabled in php or cURL (with SSL) to use this plugin.");
}

$api_key = 'AIzaSyB0X-0jZwmNtZMdJzPVUGHLsZSPk1GVGJw';
$YT_Developer_Key = "AI39si7SnB9SXTjsmmmbzQwMFmKqE4gkIU6EpBQsM9zKcgBl9ql7JsBbDebEZ51b_uDMQDSQ_egkBUkJf3J2qa35erTQhXTLnA";
if(@$_REQUEST['act'] == 'ajax')
{
	extract($_REQUEST);
	
	$YT_Developer_Key = trim($YT_Developer_Key);

	$post = array();
	$post["Email"] = urlencode($_REQUEST['email']);
	$post["Passwd"] = urlencode($_REQUEST['pass']);
	$post["service"] = 'youtube';

	if (!$usecurl) {

		$page = geturl ("www.google.com", 80, '/accounts/ClientLogin', "https://www.google.com/accounts/ClientLogin", 0, $post, 0, 0, 0, 0, 'https');
		///var_dump($page);
		//is_page($page);
		//echo($page);die();
	} else {
		$page = YT_cURL ("https://www.google.com/accounts/ClientLogin", $post);
	}
	
	show_error($page, "Error=BadAuthentication", "Login Failed: The login/password entered are incorrect.");

	show_error($page, "Error=NotVerified", "Login Failed: The account has not been verified.");
	show_error($page, "Error=TermsNotAgreed", "Login Failed: The account has not agreed to terms.");
	show_error($page, "Error=CaptchaRequired", "Login Failed: Need CAPTCHA. (Not supported yet)... Or check you login and try again.");
	show_error($page, "Error=Unknown", "Login Failed.");
	show_error($page, "Error=AccountDeleted", "Login Failed: The user account has been deleted.");
	show_error($page, "Error=AccountDisabled", "Login Failed: The user account has been disabled.");
	show_error($page, "Error=ServiceDisabled", "Login Failed: The user's access to the specified service has been disabled.");
	show_error($page, "Error=ServiceUnavailable", "Login Failed: Service is not available; try again later.");
	echo "Login OK";
}
function show_error($page, $string, $res)
{
	if(stristr($page, $string))
	{
		echo $res;
		die();
	}
}
function rtags($str, $rpl='_') {
	$str = trim($str);
	$str = str_replace(array('<', '>'), $rpl, $str);
	return $str;
}

// Small cURL function for login (If OpenSSL isn't loaded and cURL have SSL support)
function YT_cURL($link, $post) {
	$opt = array(CURLOPT_HEADER => 1, CURLOPT_REFERER => $link,
		CURLOPT_SSL_VERIFYPEER => 0, CURLOPT_SSL_VERIFYHOST => 0, CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_USERAGENT => "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.6) Gecko/20050317 Firefox/1.0.2");
	$opt[CURLOPT_POST] = 1;
	$opt[CURLOPT_POSTFIELDS] = formpostdata($post);
	$ch = curl_init($link);
	foreach ($opt as $O => $V) { // Using this instead of 'curl_setopt_array'
		curl_setopt($ch, $O, $V);
	}
	$page = curl_exec($ch);
	$errz = curl_errno($ch);
	$errz2 = curl_error($ch);
	curl_close($ch);

	if ($errz != 0) html_error("YT:[cURL:$errz] $errz2");
	return $page;
}

// upfile function edited for YT upload.
function UploadToYoutube($host, $port, $url, $dkey, $uauth, $XMLReq, $file, $filename) {
	global $nn, $lastError, $sleep_time, $sleep_count;

	if (!is_readable($file)) {
		$lastError = sprintf(lang(65),$file);
		return FALSE;
	}

	$fileSize = getSize($file);

	$bound = "--------" . md5(microtime());
	$saveToFile = 0;

	$postdata .= "--" . $bound . $nn;
	$postdata .= 'Content-Type: application/atom+xml; charset=UTF-8' . $nn . $nn;
	$postdata .= $XMLReq . $nn;
	$postdata .= "--" . $bound . $nn;
	$postdata .= "Content-Type: application/octet-stream" . $nn . $nn;

	$zapros = "POST " . str_replace ( " ", "%20", $url ) . " HTTP/1.1{$nn}Host: $host{$nn}Authorization: GoogleLogin auth=$uauth{$nn}GData-Version: 2.1{$nn}X-GData-Key: key=$dkey{$nn}Slug: $filename{$nn}Content-Type: multipart/related; boundary=$bound{$nn}Content-Length: " . (strlen($postdata) + strlen($nn . "--$bound--$nn") + $fileSize) . "{$nn}Connection: Close$nn$nn$postdata";
	$errno = 0; $errstr = "";
	$fp = @stream_socket_client("$host:$port", $errno, $errstr, 120, STREAM_CLIENT_CONNECT);

	if (!$fp) html_error(sprintf(lang(88),$host,$port));
	if ($errno || $errstr) {
		$lastError = $errstr;
		return false;
	}

	echo "<p>";
	printf(lang(90),$host,$port);
	echo "</p>";

	echo(lang(104).' <b>'.$filename.'</b>, '.lang(56).' <b>'.bytesToKbOrMb($fileSize).'</b>...<br />');
	global $id;
	$id = md5(time() * rand( 0, 10 ));
	require(TEMPLATE_DIR . '/uploadui.php');
	flush();

	$timeStart = getmicrotime();
	$chunkSize = GetChunkSize($fileSize);

	fputs($fp, $zapros);
	fflush($fp);

	$fs = fopen($file, 'r');

	$local_sleep = $sleep_count;
	while ( ! feof ( $fs ) ) {
		$data = fread ( $fs, $chunkSize );
		if ($data === false) {
			fclose($fs);
			fclose($fp);
			html_error (lang(112));
		}

		if (($sleep_count !== false) && ($sleep_time !== false) && is_numeric($sleep_time) && is_numeric($sleep_count) && ($sleep_count > 0) && ($sleep_time > 0)) {
			$local_sleep --;
			if ($local_sleep == 0) {
				usleep($sleep_time);
				$local_sleep = $sleep_count;
			}
		}

		$sendbyte = fputs($fp, $data);
		fflush($fp);

		if ($sendbyte === false) {
			fclose($fs);
			fclose($fp);
			html_error(lang(113));
		}

		$totalsend += $sendbyte;

		$time = getmicrotime() - $timeStart;
		$chunkTime = $time - $lastChunkTime;
		$chunkTime = $chunkTime ? $chunkTime : 1;
		$lastChunkTime = $time;
		$speed = round($sendbyte / 1024 / $chunkTime, 2);
		$percent = round($totalsend / $fileSize * 100, 2);
		echo '<script type="text/javascript">pr('."'"  . $percent . "', '" . bytesToKbOrMb ( $totalsend ) . "', '" . $speed . "');</script>\n";
		flush();

	}
	fclose ($fs);
	fputs ($fp, $nn . "--" . $bound . "--" . $nn);
	fflush ($fp);
	while (!feof($fp)) {
		$data = fgets($fp, 16384);
		if ($data === false) {
			break;
		}
		$page .= $data;
	}
	fclose ($fp);
	return $page;
}