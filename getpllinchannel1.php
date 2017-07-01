<?php
set_time_limit(0);

$api_key = 'AIzaSyB0X-0jZwmNtZMdJzPVUGHLsZSPk1GVGJw';
function youtube_dl($videoId)
{
    $youtubeUrl = 'http://www.youtube.com/watch?v='.$videoId;

    if ($contentHtml = file_get_contents($youtubeUrl)) {

        if (preg_match('/;ytplayer\.config\s*=\s*({.*?});/', $contentHtml, $matches)) {

            $jsonData  = json_decode($matches[1], true);
            $streamMap = $jsonData['args']['url_encoded_fmt_stream_map'];
            $videoUrls = array();

            foreach (explode(',', $streamMap) as $url)
            {
                $url = str_replace('\u0026', '&', $url);
                $url = urldecode($url);

                parse_str($url, $data);
                $dataURL = $data['url'];
                unset($data['url']);

                $videoUrls[] = array(
                    'itag'    => $data['itag'],
                    'quality' => $data['quality'],
                    'type'	  => $data['type'],
                    'url'     => $dataURL.'&'.urldecode(http_build_query($data))
                );
            }

            return $videoUrls;
        }
    }

    return array();
}
function get2Str($str1, $str2, $str) {
	$s = explode($str1, $str);
	if(count($s)<2) return $s[0];
	$s = explode($str2, $s[1]);
	return $s[0];

}
function xflush(){
    echo(str_repeat(' ',1024));
    // check that buffer is actually set before flushing
    if (ob_get_length()){           
        @ob_flush();
        @flush();
        @ob_end_flush();
    }   
    @ob_start();
}
extract($_REQUEST);
if(isset($channelid))
{

}
?>
<!DOCTYPE html>
<html lang="">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Get Playlists in Channel</title>

		<!-- Bootstrap CSS -->
		<link href="//netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">

		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
			<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>
	<body>
		<h1 class="text-center">Get Playlists in Channel</h1>
		
		<div class="container">
			<div class="row">
				<div class="col-xs-12">
					<form action="" method="POST" class="form-horizontal" role="form">
						<div class="form-group">
							<div class="col-sm-12">
								<input type="text" name="channelid" id="channelid" class="form-control" value="<?php echo @$channelid ?>">
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-10 col-sm-offset-2">
								<button type="submit" class="btn btn-primary">Submit</button>
							</div>
						</div>
					</form>
					<?php 
					if(isset($channelid))
					{
						$plls = array();
						$pageToken = '';
						
						$is_break = false;
						do {
							xflush();
							$url = 'https://www.googleapis.com/youtube/v3/playlists?part=id&maxResults=50&channelId='.$channelid.'&key=' . $api_key .'&pageToken='.$pageToken;
							$json = json_decode(file_get_contents($url));
							foreach ($json->items as $key => $value) {
								$not = 0;
								$plid = @$value->id;
								if(!$plid) continue;
								if(!in_array($plid, $plls)) {
									$plls[] = $plid;
								} else {
									$not += 1;
								}
							}
							echo count($plls);
							if($not >= 2) break;
							if(@!$json->nextPageToken) break;
							$pageToken = $json->nextPageToken;
							xflush();
						} while(true);
						echo '
						<strong>Total: '.count($plls).' playlist</strong>
						<br>
							<textarea rows="10" class="form-control">'.implode("\r\n", $plls).'</textarea>
						';

						/*	
							$url = "https://www.googleapis.com/youtube/v3/search?key=".$api_key."&channelId=".$channelid."&part=snippet,id&order=date&maxResults=50&pageToken=".$pageToken;
							//echo $url;die();
							$json = json_decode(file_get_contents($url));
							foreach ($json->items as $key => $value) {
								$videoId = @$value->id->videoId;
								if(!$videoId) continue;
									echo "<div>https://www.youtube.com/watch?v=".$videoId; 
									echo "</div>";
							}
							if(@!$json->nextPageToken) break;
							$pageToken = $json->nextPageToken;
							echo $pageToken."<br>";
							*/
							
					}
					?>
				</div>
			</div>
		</div>
	</body>
</html>