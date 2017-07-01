
<?php 
	$mode = 2;
	ini_set('max_execution_time', 0);
	require_once 'v3/v3-config.php';
	$api_key = 'AIzaSyA28hITwrV2_TjiCTtCzA5nNKaroO-p0rY';
	if (isset($_SESSION['token'])) {
	    $client->setAccessToken($_SESSION['token']);
	}

	if ($client->getAccessToken()) {
		$htmlBody = '';
		try {
    		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    			extract($_REQUEST);
    			/*
					$mode: Che do update
					$inputPlaylistId: Danh sach playlist
					$inputVideos: Danh videos Id
					$maxVideos: Số video can them vào pll( Max = 20)
					$keywords: Keywords search
					$startPosition: Vi tri băt dau cap nhat video
    			*/
				$videoIdArr =array();
				$keywArr = @explode(',', @$keywords);
        		$countKw = @count(@$keywArr);
        		$iPosition = empty(@$startPosition) ? 0 : $startPosition;
        		$maxResult = empty(@$maxVideos) ? 10 : $maxVideos;
        		switch (@$mode) {
        			// Cap nhat pll by keywords search
        			case 1:
        				$htmlBody = '';
			            $totalVideos = 0;
			            $pllIds = @explode(',', @$inputPlaylistId);
		        		$countPll = @count(@$pllIds);
		        		if ($countPll == 0) {
		        			$htmlBody .= "<p> Không có playlist nào! </p>";
		        		}
			            // Search pll by keywords
			            for ($i=0; $i < $countKw; $i++) {
			              $searchUrl = 'https://www.googleapis.com/youtube/v3/search?part=snippet&type=video&fields=items&maxResults='.$maxResult.'&key='.$api_key; 
			              $searchUrl .= '&q='.urlencode($keywArr[$i]);
			              // make request
			              $context = stream_context_create(array(
			                  'http' => array('ignore_errors' => true),
			              ));

			              $dataRes = file_get_contents($searchUrl, false, $context);
			              $result_json = @json_decode($dataRes);

			              // Get pll -> id
			              if (@$result_json->items) {
			                  for ($ipll=0; $ipll < count($result_json->items); $ipll++) {
			                  	$tmpVidId = array($result_json->items[$ipll]->id->videoId);
			                    $videoIdArr[] = $tmpVidId;
			                    $totalVideos += 1;
			                  }
			              }
			            }
			            for ($i=0; $i < $countPll; $i++) {
			            	$position = $iPosition; 
		        			for ($j=0; $j < $totalVideos; $j++) { 
		        				$resourceId = new Google_Service_YouTube_ResourceId();
				                $resourceId->setVideoId($videoIdArr[$j][0]);
				                $resourceId->setKind('youtube#video');

				                $playlistItemSnippet = new Google_Service_YouTube_PlaylistItemSnippet();
				                $playlistItemSnippet->setPlaylistId(ltrim(rtrim($pllIds[$i])));
				                $playlistItemSnippet->setResourceId($resourceId);
				                $playlistItemSnippet->setPosition($position);

				                $playlistItem = new Google_Service_YouTube_PlaylistItem();
				                $playlistItem->setSnippet($playlistItemSnippet);

				                try {
				                  $playlistItemResponse = $youtube->playlistItems->insert('snippet,contentDetails', $playlistItem, array());
				                  } catch(Google_Exception $e) {
				                    // By pass
				                  }
				                $position++;
		        			}
		        			$htmlBody .= "Updated: https://www.youtube.com/playlist?list=".ltrim(rtrim($pllIds[$i])). "\n";
		        		}
        				break;
        			
        			// Cap nhat pll thu cong
        			case 2:
        				$pllIds = @explode(',', @$inputPlaylistId);
        				$vidIds = @explode(',', @$inputVideos);
		        		$countPll = @count(@$pllIds);
		        		$countVideos = @count(@$vidIds);
		        		if ($countPll == 0) {
		        			$htmlBody .= "<p> Không có playlist nào! </p>";
		        		}
		        		for ($i=0; $i < $countPll; $i++) { 
		        			$position = $iPosition;
		        			for ($j=0; $j < $countVideos; $j++) { 
		        				$resourceId = new Google_Service_YouTube_ResourceId();
				                $resourceId->setVideoId($vidIds[$j]);
				                $resourceId->setKind('youtube#video');

				                $playlistItemSnippet = new Google_Service_YouTube_PlaylistItemSnippet();
				                $playlistItemSnippet->setPlaylistId($pllIds[$i]);
				                $playlistItemSnippet->setResourceId($resourceId);
				                $playlistItemSnippet->setPosition($position);

				                $playlistItem = new Google_Service_YouTube_PlaylistItem();
				                $playlistItem->setSnippet($playlistItemSnippet);

				                try {
				                  $playlistItemResponse = $youtube->playlistItems->insert('snippet,contentDetails', $playlistItem, array());
				                  } catch(Google_Exception $e) {
				                    // By pass
				                  }
				                $position++;
		        			}
		        			$htmlBody .= "Updated: https://www.youtube.com/playlist?list=".ltrim(rtrim($pllIds[$i])). "\n";
		        		}
        				break;

        			// delete all video in playlist
        			case 3:
        				$pllIds = @explode(',', @$inputPlaylistId);
		        		$countPll = @count(@$pllIds);
		        		if ($countPll == 0) {
		        			$htmlBody .= "<p> Không có playlist nào! </p>";
		        		}
		        		for ($i=0; $i < $countPll; $i++) {
		        			$pageToken = false;
			        		do {
			        			if (!$pageToken) {
			        				$getListItemUrl = 'https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&maxResults=50&playlistId='.$pllIds[$i].'&fields=items%2Fid,nextPageToken&key='.$api_key;
			        			} else {
			        				$getListItemUrl = 'https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&maxResults=50&playlistId='.$pllIds[$i].'&fields=items%2Fid,nextPageToken&pageToken='.$pageToken.'&key='.$api_key;
			        			}
			        		 	
			        			$context = stream_context_create(array(
					                'http' => array('ignore_errors' => true),
					            ));
				                $dataRes = file_get_contents($getListItemUrl, false, $context);
				                $result_json = @json_decode($dataRes);

				                if (isset($result_json->nextPageToken)) {
				                	$pageToken = $result_json->nextPageToken;
				                } else {
				                	$pageToken = false;
				                }

				                // Get pll -> id
				                if (@$result_json->items) {
				                   for ($iId=0; $iId < count($result_json->items); $iId++) {
				                  	 $tmpVidId = $result_json->items[$iId]->id;
				                     try {
						                    $playlistItemResponse = $youtube->playlistItems->delete($tmpVidId);
						                } catch(Google_Exception $e) {
						                   $htmlBody .= sprintf('<p>An client error occurred: <code>%s</code></p>',htmlspecialchars($e->getMessage()));
						                }
				                   }
				                }
			        		 } while ($pageToken);
			        		 $htmlBody .= "Done: ".$pllIds[$i]. "\n";
		        		}

        				break;
        			default:
        				# code...
        				break;
        		}

    		}
    	} catch (Google_ServiceException $e) {
	        $htmlBody .= sprintf('<p>A service error occurred: <code>%s</code></p>',
	        htmlspecialchars($e->getMessage()));
	    } catch (Google_Exception $e) {
	        $htmlBody .= sprintf('<p>An client error occurred: <code>%s</code></p>',
	        htmlspecialchars($e->getMessage()));
	    }
	} else {
	    $state = mt_rand();
	    $client->setState($state);
	    $_SESSION['state'] = $state;

	    $authUrl = $client->createAuthUrl();
	    echo '<h3 class="text-center"><a href="'.$authUrl.'">Step 1: Sign-in with YouTube</a></h3>';
	}

?>
<!doctype html>
<html>
<head>
	<title>Update Playlist</title>
	<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
  <script type="text/javascript" src="assets/jquery.min.js"></script>
</head>

<body>
	<div class="container">
		<div class="row">
			<div class="col-sm-10 col-sm-offset-1">
  			<h2 class="text-center">Cập Nhật Playlist</h2>

  			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" class="form-horizontal" role="form">

  				<div class="form-group alert alert-success">
			      <div class="text-center col-sm-4">
			        <input type="radio" name="mode" id="mode1" value="1" <?php if(@$mode==1):?> checked="checked" <?php endif; ?>>
			        Thêm Videos Bằng keywords
			      </div>

			      <div class="text-center  col-sm-4">
			        <input type="radio" name="mode" id="mode2" value="2" <?php if(@$mode==2):?> checked="checked" <?php endif; ?>>
			        Thêm videos thủ công.
			      </div>

			      <div class="text-center  col-sm-4">
			        <input type="radio" name="mode" id="mode3" value="3" <?php if(@$mode==3):?> checked="checked" <?php endif; ?>>
			        Xóa Hết Videos trong playlists
			      </div>
			    </div>

	  			<div class="form-group">
			      <label for="inputPlaylistId" class="col-sm-4 control-label">Ids Playlist: </label>
			      <div class="col-sm-6">
			        <textarea rows="3" name="inputPlaylistId" id="inputPlaylistId" class="form-control" placeholder="Nhập vào id playlist, cách nhau bởi dấu phẩy" required="required"><?php echo @$inputPlaylistId; ?></textarea>
			      </div>
			    </div>


			    <div class="form-group">
			  		<label for="startPosition" class="col-sm-4 control-label">Vị trí Videos</label>
			  		<div class="col-sm-6">
			  			<input type="number" name="startPosition" id="startPosition" class="form-control" value="<?php echo @$startPosition; ?>" placeholder="Vị trí videos, bắt đầu từ 0">
			  		</div>
			  	</div>

			    <div class="form-group">
			  		<label for="inputVideos" class="col-sm-4 control-label">ID video của bạn:</label>
			  		<div class="col-sm-6">
			  			<input type="text" name="inputVideos" id="inputVideos" class="form-control" value="<?php echo @$inputVideos; ?>" placeholder="Danh sách Video ID muốn thêm vào pll, phân cách bởi dấu phẩy ( Max = 20)">
			  		</div>
			  	</div>

			  	<div class="form-group">
			      <label for="maxVideos" class="col-sm-4 control-label">Max Videos:</label>
			      <div class="col-sm-6">
			        <input type="number" name="maxVideos" id="maxVideos" class="form-control" value="<?php echo @$maxVideos; ?>" placeholder="Số lượng videos muốn thêm vào playlist trên 1 keyword (MAx = 20)">
			      </div>
			    </div>

			  	<div class="form-group">
			  		<label for="keywords" class="col-sm-4 control-label">Danh sách từ khóa:</label>
			  		<div class="col-sm-6">
			  			<textarea rows="4" name="keywords" id="keywords" class="form-control" placeholder="Nhập từ khóa vào đây, cách nhau bằng dấu phẩy"><?php echo @$keywords; ?></textarea>
			  		</div>
			  	</div>

			  	<!-- Button -->
			    <div class="form-group">
			  		<div class="col-sm-6 col-sm-offset-4">
			  			<button type="submit" id="update_now" class="btn btn-primary">Update Tốc Độ!!!</button>
			  		</div>
			  	</div>

			  	<div class="form-group">
				    <div class="col-xs-4 col-md-offset-4">
				      <a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/ytb-tool/index.php">Home Page</a>
				    </div>
			    </div>
	  		</form>
	  		</div>
	  		<div class="row">
		    <div class="col-sm-10 col-sm-offset-1" id="result_area">
		      <textarea rows="4" class="form-control" placeholder="Kết quả delete playlist">
		        <?php echo @$htmlBody; ?>
		      </textarea>
		    </div>
			</div>
  		</div>
  		</div>
  	</div>

  	<script type="text/javascript">
  	$(document).ready(function() {
  		if ($('#mode1').is(':checked')) {
  			$("#maxVideos").attr("required", "required");
            $("#keywords").attr("required", "required");
            $("#maxVideos").removeAttr("disabled");
            $("#keywords").removeAttr("disabled");
            $("#inputVideos").attr("disabled", "disabled");
            $("#inputVideos").removeAttr("required");
            $("#startPosition").removeAttr("disabled");
  		}
  		if ($('#mode2').is(':checked')) {
  			$("#maxVideos").attr("disabled", "disabled");
            $("#keywords").attr("disabled", "disabled");
            $("#maxVideos").removeAttr("required");
            $("#keywords").removeAttr("required");
            $("#inputVideos").attr("required", "required");
            $("#inputVideos").removeAttr("disabled");
            $("#startPosition").removeAttr("disabled");
  		}
  		if ($('#mode3').is(':checked')) {
  			$("#maxVideos").attr("disabled", "disabled");
          	$("#keywords").attr("disabled", "disabled");
          	$("#inputVideos").attr("disabled", "disabled");
          	$("#startPosition").attr("disabled", "disabled");
  		}
  	});

      $('input[type=radio][name=mode]').change(function() {
        if (this.value == '1' && $(this).is(':checked')) {
            $("#maxVideos").attr("required", "required");
            $("#keywords").attr("required", "required");
            $("#maxVideos").removeAttr("disabled");
            $("#keywords").removeAttr("disabled");
            $("#inputVideos").attr("disabled", "disabled");
            $("#inputVideos").removeAttr("required");
            $("#startPosition").removeAttr("disabled");
        }
        else if (this.value == '2' && $(this).is(':checked')) {
            $("#maxVideos").attr("disabled", "disabled");
            $("#keywords").attr("disabled", "disabled");
            $("#maxVideos").removeAttr("required");
            $("#keywords").removeAttr("required");
            $("#inputVideos").attr("required", "required");
            $("#inputVideos").removeAttr("disabled");
            $("#startPosition").removeAttr("disabled");
        } else {
          $("#maxVideos").attr("disabled", "disabled");
          $("#keywords").attr("disabled", "disabled");
          $("#inputVideos").attr("disabled", "disabled");
          $("#startPosition").attr("disabled", "disabled");
        }
    });

  </script>
 </body>