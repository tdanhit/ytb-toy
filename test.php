<?php 
//require_once "functions.php";
require_once('rl_init.php');
ignore_user_abort(true);

login_check();

include(CLASS_DIR.'http.php');
$api_key = 'AIzaSyB0X-0jZwmNtZMdJzPVUGHLsZSPk1GVGJw';
$YT_Developer_Key = "AI39si7SnB9SXTjsmmmbzQwMFmKqE4gkIU6EpBQsM9zKcgBl9ql7JsBbDebEZ51b_uDMQDSQ_egkBUkJf3J2qa35erTQhXTLnA";
if(@$_REQUEST['act'] == 'ajax')
{
	extract($_REQUEST);

	die();
}
$formatType = array(
	'mp4'	=>	'mp4',
	'webm'	=>	'webm',
	'flv'	=>	'flv',
	'3gp'	=>	'3gp',
	'3gpp'	=>	'3gpp',
	'mov'	=>	'mov',
	'avi'	=>	'avi',
	'wmv'	=>	'wmv',
	);
$allowExt = array('mp4', 'webm', 'flv', '3gp', 'mov', 'avo', 'wmv');
$uemail = 'yoshioka@laptrinhvn.net';
$upass = '0979086442';
$processedVideos = array();
if(count($processedVideos) == 0 || !@$renderVideo)
							{
								$dirList = __DIR__ . '/files/';
								$scanned_directory = array_diff(scandir($dirList), array('..', '.', '.htaccess'));
								foreach ($scanned_directory as $file) {
									$ext = strtolower(pathinfo($dirList.$file, PATHINFO_EXTENSION));
									if(in_array($ext, $allowExt))
									{
										if(stristr($file, '_render.'))
										{
											$processedVideos[] = $dirList . $file;
										}
									}
								} 
							}

							foreach ($processedVideos as $key => $value) {
								//value là full DIR
								//get video info
								$html_name = htmlentities(basename($value));
								$dVideoId = explode('.', $html_name);
								$dVideoId = str_replace('_render', '', $dVideoId[0]);
								$dUrl = "https://www.googleapis.com/youtube/v3/videos?id=".$dVideoId."&key=".$api_key."&part=player,snippet,contentDetails,statistics";
								$dPage = @file_get_contents($dUrl);
								$video_json = @json_decode($dPage);
								$up_access = 'public';
								$up_embed = 'no';
								if(@$video_json->items)
								{
									//if video exist
									$vtitle = $video_json->items[0]->snippet->title;
									$vtags = @implode(',', @$video_json->items[0]->snippet->tags);
									$vdescription = urlencode($video_json->items[0]->snippet->description);
									$vcategory = 'Animals';
								} else {
									$vtitle = $html_name;
									$vdescription = '';
									$vtags = '';
								}

								$YT_Developer_Key = trim($YT_Developer_Key);

								$post = array();
								$post["Email"] = urlencode($uemail);
								$post["Passwd"] = urlencode($upass);
								$post["service"] = 'youtube';

								/*if (!@$usecurl) {
									$page = geturl ("www.google.com", 80, '/accounts/ClientLogin', "https://www.google.com/accounts/ClientLogin", 0, $post, 0, 0, 0, 0, 'https');
									is_page($page);

								} else {*/
									$page = YT_cURL ("https://www.google.com/accounts/ClientLogin", $post);
								//}
								
								//echo($page);die();
								show_error($page, "Error=BadAuthentication", "Login Failed: The login/password entered are incorrect.");
								show_error($page, "Error=NotVerified", "Login Failed: The account has not been verified.");
								show_error($page, "Error=TermsNotAgreed", "Login Failed: The account has not agreed to terms.");
								show_error($page, "Error=CaptchaRequired", "Login Failed: Need CAPTCHA. (Not supported yet)... Or check you login and try again.");
								show_error($page, "Error=Unknown", "Login Failed.");
								show_error($page, "Error=AccountDeleted", "Login Failed: The user account has been deleted.");
								show_error($page, "Error=AccountDisabled", "Login Failed: The user account has been disabled.");
								show_error($page, "Error=ServiceDisabled", "Login Failed: The user's access to the specified service has been disabled.");
								show_error($page, "Error=ServiceUnavailable", "Login Failed: Service is not available; try again later.");

								if (!preg_match('@Auth=([^\r|\n]+)@i', $page, $auth)) echo("Login Failed: Auth token not found.");

								$xml2 = "\r\n";
								$xml = "<?xml version='1.0'?>\r\n<entry xmlns='http://www.w3.org/2005/Atom' xmlns:media='http://search.yahoo.com/mrss/' xmlns:yt='http://gdata.youtube.com/schemas/2007'>\r\n";
								$xml .= "  <media:group>\r\n";
								$xml .= "    <media:title type='plain'>$vtitle</media:title>\r\n";
								$xml .= "    <media:description type='plain'>$vdescription</media:description>\r\n";
								$xml .= "    <media:keywords>$vtags</media:keywords>\r\n";
								// @<atom:category term='([^']+)' label='([^']+)'[^>]+><yt:assignable/>@i
								$xml .= "    <media:category scheme='http://gdata.youtube.com/schemas/2007/categories.cat'>$vcategory</media:category>\r\n";
								if ($up_access != 'public') {
									if ($up_access == 'unlisted') $xml2 .= "  <yt:accessControl action='list' permission='denied'/>\r\n";
									if ($up_access == 'private') $xml .= "    <yt:private/>\r\n";
								}
								if ($up_embed == 'no') $xml2 .= "  <yt:accessControl action='embed' permission='denied'/>\r\n";
								$xml .= "  </media:group>$xml2";
								$xml .= "</entry>";

								$upfiles = UploadToYoutube("uploads.gdata.youtube.com", 80, "/feeds/api/users/default/uploads", $YT_Developer_Key, $auth[1], $xml, 'files/'.$html_name, $value);
								die();
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

//[15-7-2011]  Written by Th3-822.
//[14-9-2011]  Added error msg for Invalid Developer Key && Added text before Upload button. - Th3-822.
//[19-9-2011]  Added more values for video upload, added auul support && Added function for use https with cURL if OpenSSL isn't loaded && Added a default Developer Key. - Th3-822.

function show_error()
{
	echo 'a';
}