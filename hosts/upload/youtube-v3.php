<?php
//@session_start();
require_once 'v3/v3-config.php'; 

// Youtube Developer Key. NEEDED TO WORK...
$YT_Developer_Key = "AI39si7SnB9SXTjsmmmbzQwMFmKqE4gkIU6EpBQsM9zKcgBl9ql7JsBbDebEZ51b_uDMQDSQ_egkBUkJf3J2qa35erTQhXTLnA";
// Get your Developer Key @ http://code.google.com/apis/youtube/dashboard/gwt/index.html
$api_key = 'AIzaSyDgx4Kt7GGo7cr6_r-JdAqqLr3JEEF25RY';
####### Account Info. ###########
/*if(@$_SESSION['uemail']) $uemail = $_SESSION['uemail'];
if(@$_SESSION['upass']) $upass = $_SESSION['upass'];
if(!$uemail)
{
	die("BAN CHUA NHAP EMAIL");
}*/
//$upload_acc['youtube_com']['user'] = @$uemail; //Set your username/email
//$upload_acc['youtube_com']['pass'] = @$upass; //Set your password
##############################

$YT_Developer_Key = trim($YT_Developer_Key);
if (empty($YT_Developer_Key)) html_error("Developer Key is empty, please set yours @ {$page_upload["youtube.com"]}.", 0);
if (!preg_match("@\.(mp4|flv|mpe?g|mkv|wmv|mov|3gp|avi)$@i", $lname, $fext)) echo "<p style='color:red;text-align:center;font-weight:bold;'>This file format doesn't looks like a video file allowed by youtube.</p>\n";
// Check for https support.
$usecurl = $cantuse = false;
if (!extension_loaded('openssl')) {
	if (extension_loaded('curl')) {
		$cV = curl_version();
		if (in_array('https', $cV['protocols'], true)) $usecurl = true;
	} else $cantuse = true;
	if ($cantuse) html_error("Need OpenSSL enabled in php or cURL (with SSL) to use this plugin.");
}
$not_done = true;
$continue_up = $login = false;
$categories = array('People' => 'People & Blogs', 'Film' => 'Film & Animation', 'Autos' => 'Autos & Vehicles', 'Music' => 'Music', 'Animals' => 'Pets & Animals', 'Sports' => 'Sports', 'Travel' => 'Travel & Events', 'Games' => 'Gaming', 'Comedy' => 'Comedy', 'News' => 'News & Politics', 'Entertainment' => 'Entertainment', 'Education' => 'Education', 'Howto' => 'Howto & Style', 'Nonprofit' => 'Nonprofits & Activism', 'Tech' => 'Science & Technology');

// Anhtd Add new 
if (isset($_SESSION['token'])) {
	$client->setAccessToken($_SESSION['token']);
} else {
	die("Invalid Token, Please refresh token");
}
// Anhtd add end
// if ($upload_acc['youtube_com']['user'] && $upload_acc['youtube_com']['pass'])
echo "Check token: " . $client->getAccessToken();
if ($client->getAccessToken()) {
	// Check token expired
	if($client->isAccessTokenExpired()) {
	    echo 'Access Token Expired - Get Refresh token';
	    $refreshToken = $_SESSION['refresh_token']; 
  		$client->refreshToken($refreshToken);
	    $_SESSION['token'] = $client->getAccessToken();
	}

	// End check token expired
	$auul = array();
	$auul['enable'] = true; // Change it to true and set the values below for use with auul.

	$auul['title'] = ""; // If Empty: <Filename>.
	$auul['description'] = ' '; // If Empty: 'Uploaded with rapidleech.'
	$auul['tags'] = ' '; // Add tags like 'tag1, tag 2, tag3' - If Invalid, 'Example tag, upload, rapidleech'.
	$auul['category'] = ' '; // 'People', 'Film', 'Autos', 'Music', 'Animals', 'Sports', 'Travel', 'Games', 'Comedy', 'News', 'Entertainment', 'Education', 'Howto', 'Nonprofit' or 'Tech' (Case sensitive) - If Invalid: 'People'.
	$auul['access'] = 'public'; // 'public', 'unlisted' or 'private' (Case sensitive) - If Invalid: 'public'.
	$auul['embed'] = true; // Change to false for disable embedding.

	if ($auul['enable']) {
		$_REQUEST['up_title'] = $auul['title'];
		$_REQUEST['up_description'] = $auul['description'];
		$_REQUEST['up_tags'] = $auul['tags'];
		$_REQUEST['up_category'] = $auul['category'];
		$_REQUEST['up_access'] = $auul['access'];
		$_REQUEST['up_embed'] = ($auul['embed'] ? 'yes' : 'no');
		$_REQUEST['action'] = "FORM";
	}
	$login = true;
	echo "<p style='text-align:center;font-weight:bold;'>Using Default Login and Pass.</p>\n";
}

if ($_REQUEST['action'] == "FORM") 
	$continue_up=true;
else {
	echo "<table border='0' style='width:270px;' cellspacing='0' align='center'>
	<form method='POST'>
	<input type='hidden' name='action' value='FORM' />";
	if (!$login) echo "<tr><td style='white-space:nowrap;'>&nbsp;YouTube or Google Login*</td><td>&nbsp;<input type='text' name='up_login' value='' style='width:160px;' /></td></tr>
	<tr><td style='white-space:nowrap;'>&nbsp;Password*</td><td>&nbsp;<input type='password' name='up_pass' value='' style='width:160px;' /></td></tr>\n";
	echo "<tr><td colspan='2' align='center'><br />Video options *<br /><br /></td></tr>
	<tr><td style='white-space:nowrap;'>Title:</td><td>&nbsp;<input type='text' name='up_title' value='$lname' style='width:160px;' /></td></tr>
	<tr><td style='white-space:nowrap;'>Description:</td><br /><td><textarea rows='5' name='up_description' style='width:160px;'>Uploaded with rapidleech.</textarea></td></tr>
	<tr><td style='white-space:nowrap;'>Tags: </td><td>&nbsp;<input type='text' name='up_tags' value='Example tag, upload, rapidleech' style='width:160px;' /></td></tr>
	<tr><td style='white-space:nowrap;'>Category:</td><td>&nbsp;<select name='up_category' style='width:160px;height:20px;'>\n";
	foreach($categories as $n => $v) echo "\t<option value='$n'>$v</option>\n";
	echo "</select></td></tr>\n";
	echo "<tr><td style='white-space:nowrap;'>Privacy: <br /><select name='up_access' style='width:8em;height:20px;'><option value='public'>Public</option><option value='unlisted'>Unlisted</option><option value='private'>Private</option></select></td><td style='white-space:nowrap;'><input type='checkbox' name='up_embed' value='no' />&nbsp; Make video not embeddable</td></tr>";
	echo "<tr><td colspan='2' align='center'><br /><small>By clicking 'Upload', you certify that you own all rights to the content or that you are authorized by the owner to make the content publicly available on YouTube, and that it otherwise complies with the YouTube Terms of Service located at <a href='http://www.youtube.com/t/terms' target='_blank'>http://www.youtube.com/t/terms</a></small><br /><br /><input type='submit' value='Upload' /></td></tr>\n";
	echo "<tr><td colspan='2' align='center'><small>*You can set it as default in <b>{$page_upload["youtube.com"]}</b></small></td></tr>\n";
	echo "</table>\n</form>\n";
	echo "<script type='text/javascript'>self.resizeTo(700,580);</script>\n"; //Resize upload window
}

if ($continue_up) {
	$not_done = false;
	// Login
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='login' width='100%' align='center'>Validating login</div>\n";

	//get video info
	$dVideoId = explode('.', $lname);
	$dVideoId = str_replace('_render', '', $dVideoId[0]);
	$dUrl = "https://www.googleapis.com/youtube/v3/videos?id=".$dVideoId."&key=".$api_key."&part=snippet";
	$dPage = @file_get_contents($dUrl);
	$video_json = @json_decode($dPage);
	$vthumb = "thumbnails/" .$dVideoId. ".png";
	$vHd = "thumbnails/logo/hd-logo.png";
	if(@$video_json->items)
	{
		//if video exist
		$ttitle = str_replace('&', '-', $video_json->items[0]->snippet->title);
		$vtags = @$video_json->items[0]->snippet->tags;
		$vdescription = str_replace('&', '&amp;', $video_json->items[0]->snippet->description);
		// echo $video_json->items[0]->snippet->thumbnails->maxres->url;
		if(!empty($video_json->items[0]->snippet->thumbnails->maxres->url)) {
			file_put_contents($vthumb, @file_get_contents($video_json->items[0]->snippet->thumbnails->maxres->url));
			$vHd = "thumbnails/logo/new-hd-logo-1280x720.png";
		} else if(!empty($video_json->items[0]->snippet->thumbnails->standard->url)) {
			file_put_contents($vthumb, @file_get_contents($video_json->items[0]->snippet->thumbnails->standard->url));
			$vHd = "thumbnails/logo/new-hd-logo-640x480.png";
		} else {
			file_put_contents($vthumb, @file_get_contents($video_json->items[0]->snippet->thumbnails->high->url));
			$vHd = "thumbnails/logo/new-hd-logo-480x360.png";
		}
		//file_put_contents($vthumb, @file_get_contents($video_json->items[0]->snippet->thumbnails->maxres->url));
		if(DIRECTORY_SEPARATOR == '/') {
			$shell_command_img = '/usr/local/bin/ffmpeg -y -i "' . $vthumb .'" -i "' .$vHd. '" -filter_complex overlay=10:30  "'. $vthumb .'"';
		} else {
			$shell_command_img = 'C:\lib\ffmpeg -y -i "' . $vthumb .'" -i "' .$vHd. '" -filter_complex overlay=10:30  "'. $vthumb .'"';
		}
		echo $shell_command_img;
		if (@$_SESSION['setThumbnail'] == 1) {
			if(DIRECTORY_SEPARATOR == '/') {
				shell_exec($shell_command_img);
			} else {
				exec($shell_command_img);
			}
		}
		//ffmpeg -y -i thumbnail.png -i hd.png -filter_complex overlay=x=10  out.png
	}
	//end get video info
	if(@$_SESSION['videoTitle'] != '') {
		if($_SESSION['titlePosition'] == 1) $ttitle = $_SESSION['videoTitle'] . $ttitle;
		else $ttitle = $ttitle . $_SESSION['videoTitle'];
	}
	if(strlen($ttitle) > 100)
	{
		$vtitle = $dVideoId;
	} else {
		$vtitle = $ttitle;
	}
	if(@$_SESSION['videoDes'] != '') {
		if($_SESSION['desPosition'] == 1) $vdescription = $_SESSION['videoDes'];
		else $vdescription = $vdescription . $_SESSION['videoDes'];
	}
	if(@$_SESSION['videoTags'] != '') {
		if($_SESSION['tagsPosition'] == 1) {
			$vtags = @explode(",", $_SESSION['videoTags']);
		}
		else {
			$vtags = @implode(',', $vtags) . ',' . $_SESSION['videoTags'];
			$vtags = @explode(',', $vtags);
		}
	}
	if(@$_SESSION['categoryId'] != 0) {
		$vcategory = $_SESSION['categoryId'];
	}

	$vtitle = str_replace('&', ' ', $vtitle);
	$vdescription = str_replace('&', '&amp;', $vdescription);
	// $vtags = str_replace('&', '&amp;', $vtags);
	// $vtags = explode(",", $vtags);
	// if(strlen($vtags) >= 500) $vtags = substr($vtags, 0, 499);

	// Uploading
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";
	
	// Anhtd upload start

	try{
	    // REPLACE this value with the path to the file you are uploading.
	    $videoPath = 'files/'.$lname;
	    $video = new Google_Service_YouTube_Video();
	    $isSetLocation = false;
	    // Create a snippet with title, description, tags and category ID
	    // Create an asset resource and set its snippet metadata and type.
	    // This example sets the video's title, description, keyword tags, and
	    // video category.
	    $snippet = new Google_Service_YouTube_VideoSnippet();
	    $snippet->setTitle($vtitle);
	    $snippet->setDescription($vdescription);
	    $snippet->setTags($vtags);
	    
	    //snippet.publishedAt

	    // Numeric video category. See
	    // https://developers.google.com/youtube/v3/docs/videoCategories/list 
	    $snippet->setCategoryId($vcategory);

	    // Set the video's status to "public". Valid statuses are "public",
	    // "private" and "unlisted".
	    $status = new Google_Service_YouTube_VideoStatus();
	    echo @$_SESSION['videoStatus'];
	    $status->privacyStatus = @$_SESSION['videoStatus'];

	    if(@$_SESSION['publicAt'] != null && @$_SESSION['videoStatus'] != 'public') {
	    	/*if (@$_SESSION['lastDateTimePub'] !== null) {
	    		$datetime = new DateTime(@$_SESSION['lastDateTimePub']);
	    	} else {
	    		$datetime = new DateTime(@$_SESSION['publicAt']);
	    	}*/
	    	
	    	$datetime = new DateTime($_SESSION['publicArray'][$dVideoId]);
	    	//$datetime->setTimeZone(new DateTimeZone('Asia/Novosibirsk'));
			echo "<p> publishedAt: ".$datetime->format('Y-m-d\TH:i:s.\0\0\0\Z'). "</p>"; // 'Y-m-d\TH-i-s.\0\0\0\Z'
			$status->setPublishAt($datetime->format('Y-m-d\TH:i:s.\0\0\0\Z'));

			// Set new time publish
			/*if(@$_SESSION['puslishSerial'] != 0) {
	    		$datetime->add(new DateInterval('PT'. $_SESSION['puslishSerial'] .'H'));
	    		$_SESSION['lastDateTimePub'] = $datetime->format('Y-m-d H:i:s');
	    	}*/
	    }

	    // Associate the snippet and status objects with a new video resource.
	    $video->setSnippet($snippet);
	    $video->setStatus($status);

	     // check location
	    if (!empty(@$_SESSION['lat']) && !empty(@$_SESSION['lng'])) {
	    	$recordingDetails = new Google_Service_YouTube_VideoRecordingDetails();
	    	$locationdetails = new Google_Service_YouTube_GeoPoint();
	    	$locationdetails->setLatitude($_SESSION['lat']);
	        $locationdetails->setLongitude($_SESSION['lng']);
	        $recordingDetails->setLocation($locationdetails);
	        $video->setRecordingDetails($recordingDetails);
	        $isSetLocation = true;
	    }

	    // Specify the size of each chunk of data, in bytes. Set a higher value for
	    // reliable connection as fewer chunks lead to faster uploads. Set a lower
	    // value for better recovery on less reliable connections.
	    $chunkSizeBytes = 1 * 1024 * 1024;

	    // Setting the defer flag to true tells the client to return a request which can be called
	    // with ->execute(); instead of making the API call immediately.
	    $client->setDefer(true);

	    // Create a request for the API's videos.insert method to create and upload the video.
	    if ($isSetLocation) {
	    	$insertRequest = $youtube->videos->insert("status,snippet,recordingDetails", $video);
	    } else {
	    	$insertRequest = $youtube->videos->insert("status,snippet", $video);
	    }
	    

	    // Create a MediaFileUpload object for resumable uploads.
	    $media = new Google_Http_MediaFileUpload(
	        $client,
	        $insertRequest,
	        'video/*',
	        null,
	        true,
	        $chunkSizeBytes
	    );
	    $media->setFileSize(filesize($videoPath));

	    // Read the media file and upload it.
	    $status = false;
	    $handle = fopen($videoPath, "rb");
	    while (!$status && !feof($handle)) {
	      $chunk = fread($handle, $chunkSizeBytes);
	      $status = $media->nextChunk($chunk);
	    }
	    fclose($handle);

	    if ($status->status['uploadStatus'] == 'uploaded') {
            echo "Uploaded successfully!";
            $myVideoId = $status['id'];
            // Upload thumbnails
            $imageSize = 2 * 1024 * 1024;
            if(!empty(file_get_contents($vthumb)) && filesize($vthumb) < $imageSize) {
	            $setThumbnailRequest = $youtube->thumbnails->set($myVideoId);
	            $thumbnailmedia = new Google_Http_MediaFileUpload(
			        $client,
			        $setThumbnailRequest,
			        'image/png',
			        null,
			        true,
			        $chunkSizeBytes
			    );
			    $thumbnailmedia->setFileSize(filesize($vthumb));
			    $status = false;
			    $thumbnailhandle = fopen($vthumb, "rb");
			    while (!$status && !feof($thumbnailhandle)) {
			      $chunk = fread($thumbnailhandle, $chunkSizeBytes);
			      $status = $thumbnailmedia->nextChunk($chunk);
			    }

			    fclose($thumbnailhandle);
			    @unlink($vthumb);
            } else {
            	echo "Can not set thumbnails for the video!";
            }

            // Add videos to playlist
            if(@$_SESSION['addPlaylist'] == 1 && @$_SESSION['playlists'] != '') {
            	$playlists = $_SESSION['playlists'];
            	$playlistsArray = explode("\r\n", $playlists);
            	for ($pCount = 0; $pCount < count($playlistsArray); $pCount++) {
				    echo "<p> The playlist Id is: $playlistsArray[$pCount] </p>";
				    $playlistId = $playlistsArray[$pCount];
				    
				    // Set Video resource - prepare to upload.
				    $resourceId = new Google_Service_YouTube_ResourceId();
    				$resourceId->setVideoId($myVideoId);
    				$resourceId->setKind('youtube#video');

    				// Set Snipet for playlist
    				echo $myVideoId . " ---- " . $playlistId;
    				$playlistItemSnippet = new Google_Service_YouTube_PlaylistItemSnippet();
				    $playlistItemSnippet->setPlaylistId("ADDDd");
				    $playlistItemSnippet->setResourceId($resourceId);
				    $playlistItemSnippet->setPosition(0);

				    // Execute API call insert to playlist
				    $playlistItem = new Google_Service_YouTube_PlaylistItem();
				    $playlistItem->setSnippet($playlistItemSnippet);
				    $playlistItemResponse = $youtube->playlistItems->insert(
				        'snippet,contentDetails', $playlistItem, array());

				    // Get respone
				    // echo $playlistItemResponse;
				    // echo $playlistItemResponse->getResponseBody();
					
					$htmlBody .= "<h3>New PlaylistItem</h3><ul>";
				    $htmlBody .= sprintf('<li>%s (%s)</li>',
				        $playlistItemResponse['snippet']['title'],
				        $playlistItemResponse['id']);
				    $htmlBody .= '</ul>';
				    echo $htmlBody;
				} 
            }
        }
	    // If you want to make other calls after the file upload, set setDefer back to false
	    // s$client->setDefer(true);
	
	// Update youtube video ID to database
	//$db->update($result['video_id'],$status['id']);
	} catch (Google_Service_Exception $e) {
	    $htmlBody .= sprintf('<p>A service error occurred: <code>%s</code></p>',
	        htmlspecialchars($e->getMessage()));
	  } catch (Google_Exception $e) {
	    $htmlBody .= sprintf('<p>An client error occurred: <code>%s</code></p>', htmlspecialchars($e->getMessage()));
		$htmlBody .= 'Please reset session <a href="logout.php">Logout</a>';
		die($htmlBody);
	  }
	  
	 $_SESSION['token'] = $client->getAccessToken();
	// Anhtd upload end

	if(@$_SESSION['deleteAfter']) {
		@unlink('files/'.$lname);
	}
	//sleep(3);

	flush();
	sleep(10);
	//echo "<p style='text-align:center;font-weight:bold;'>Please check your video in your <a href='http://www.youtube.com/my_videos'>youtube page</a> for details/errors.<br />The '".lang(71)."' link is for go to 'edit video' page.</p>\n";
	$download_link = "http://www.youtube.com/watch?v=".$vid[1];
	$adm_link = "http://www.youtube.com/my_videos_edit?ns=1&video_id={$vid[1]}&next=%2Fmy_videos";
	//set_monetize($uemail, $upass, $vid[1], @$ttitle);
	//set monetization
}

function set_monetize($email, $pass, $videoId, $ttitle = '')
{
	$cookie = 'c.txt';

	//$url = 'https://www.google.com/accounts/ClientLogin';
	//$var = 'Email='.urldecode($email).'&Passwd='.urlencode($pass).'&service=youtube';
	//$page = curl($url, $var, $cookie);

	//$url = 'https://accounts.google.com/ServiceLogin?passive=true&hl=en&continue=https%3A%2F%2Fwww.youtube.com%2Fsignin%3Ffeature%3Dredirect_login%26hl%3Den%26next%3D%252Fmy_videos%253Fo%253DU%26action_handle_signin%3Dtrue%26app%3Ddesktop&uilel=3&service=youtube';
	//$page = dat_curl($url, '', $cookie);
	//$url = 'https://accounts.google.com/ServiceLoginAuth';
	//$GALX = get3Str('name="GALX"', 'value="', '"', $page);
	//$gxf = get3Str('name="gxf"', 'value="', '"', $page);
	//$var = "Page=PasswordSeparationSignIn&GALX=$GALX&gxf=".urlencode($gxf)."&continue=https%3A%2F%2Fwww.youtube.com%2Fsignin%3Ffeature%3Dredirect_login%26next%3D%252Fmy_videos%253Fo%253DU%26hl%3Den%26action_handle_signin%3Dtrue%26app%3Ddesktop&service=youtube&hl=en&ProfileInformation=&_utf8=%E2%98%83&bgresponse=&pstMsg=1&dnConn=&checkConnection=youtube%3A218%3A1&checkedDomains=youtube&identifiertoken=&identifiertoken_audio=&identifier-captcha-input=&Email=".urlencode($email)."&Passwd=".urlencode($pass)."&PersistentCookie=yes&rmShown=1";
	//$header = array();
	//$header[] = 'Referer: https://accounts.google.com/ServiceLogin?sacu=1&continue=https%3A%2F%2Fwww.youtube.com%2Fsignin%3Faction_handle_signin%3Dtrue%26app%3Ddesktop%26feature%3Dsign_in_button%26next%3D%252F%26hl%3Den&hl=en&service=youtube';
	//$header[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
	//$header[] = 'Content-Type: application/x-www-form-urlencoded';
	

	//$page = dat_curl($url, $var, $cookie, $header);
	//echo $page;die();
	$page = dat_curl('https://www.youtube.com/my_videos?o=U', '', $cookie);
	if(stristr($page, 'name="session_token"'))
	{
		$session_token = get3Str('name="session_token"', 'value="', '"', $page);
		$channel_request_id = get3Str('vm-bulk-actions-form', 'channel=', '"', $page);
		$url = 'https://www.youtube.com/bulk_actions_ajax?o=U&channel='.$channel_request_id.'&action_enqueue=1';
		if(strlen($videoId) >= 11 && strlen($videoId) <= 13) {
			$var = "session_token=".urlencode($session_token)."&bulk_action_search_query&bulk_action_video_ids=$videoId&monetization=ads&ad_formats=overlay_ads&ad_formats=trueview_instream_ads&ad_formats=product_listing_ads";
			if(strlen($ttitle) > 10)
			{
				//https://www.youtube.com/edit?o=U&video_id=bw-Bom38FdM
				$var = "session_token=".urlencode($session_token)."&bulk_action_search_query&bulk_action_video_ids=$videoId&monetization=ads&title_operation=set&title=".urlencode($ttitle);
			}
			//echo $var;
			$page = dat_curl($url, $var, $cookie);
		}
	}
	//@unlink('c.txt');
}

function dat_curl($url,$post="",$usecookie = false,$header=false, $ref=false) {  
	$ch = curl_init();
	if($post) {
		curl_setopt($ch, CURLOPT_POST ,1);
		curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
	}
	curl_setopt($ch, CURLOPT_URL, $url); 
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:30.0) Gecko/20100101 Firefox/30.0"); 
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	if ($usecookie) { 
		curl_setopt($ch, CURLOPT_COOKIEJAR, str_replace('\\','/',dirname(__FILE__)).'/c/'.$usecookie);
		curl_setopt($ch, CURLOPT_COOKIEFILE, str_replace('\\','/',dirname(__FILE__)).'/c/'.$usecookie);
		
	} 
    if($header) { 
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header); 
    }
	if($ref) { 
        curl_setopt($ch, CURLOPT_REFERER, $ref);
    }
	//curl_setopt($ch, CURLOPT_HEADER,1); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); 
	$result=curl_exec ($ch); 
	curl_close ($ch); 
	return $result; 
}

function get3Str($str1, $str2, $str3, $str) {
	$s = @explode($str1, $str);
	if(count($s)<2) return $s[0];
	$s = @explode($str2, $s[1]);
	if(count($s)<2) return $s[0];
	$s = explode($str3, $s[1]);
	return $s[0];

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
function get2Str($str1, $str2, $str) {
        $s = explode($str1, $str);
        if(count($s)<2) return $s[0];
        $s = explode($str2, $s[1]);
        return $s[0];
}

//[15-7-2011]  Written by Th3-822.
//[14-9-2011]  Added error msg for Invalid Developer Key && Added text before Upload button. - Th3-822.
//[19-9-2011]  Added more values for video upload, added auul support && Added function for use https with cURL if OpenSSL isn't loaded && Added a default Developer Key. - Th3-822.

?>