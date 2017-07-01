<?php 
  ini_set('max_execution_time', 0); //300 seconds = 5 minutes
	/* Code xử lý tạo pll từ search keywords */
	require_once 'v3/v3-config.php';
  $api_key = 'AIzaSyA28hITwrV2_TjiCTtCzA5nNKaroO-p0rY';
  $orders = array(
    'viewcount' => 'viewcount',
    'date' => 'date',
    'rating' => 'rating',
    'relevance' => 'relevance',
    'title' => 'title',
    'videocount' => 'videocount'
  );
  $onlyme = 3;

  if (isset($_SESSION['token'])) {
    $client->setAccessToken($_SESSION['token']);
  }

	// echo "Check token: " . $client->getAccessToken();
	if ($client->getAccessToken()) {
    $htmlBody = '';
    try {
      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $pllIdArr = array();
        $pllTitleArr = array();
        $pllDesArr = array();
        extract($_REQUEST);
        /* 
          $inputTotalpll: Total playlist want to create
          $inputVideos: Video id, cách nhau boi dau phay
          $title: Tieu de playlist
          $description: Mo ta playlist
          $keywords: Keywords, cach nhau boi dau phay
        */
        $keywArr = @explode(',', @$keywords);
        $countKw = @count(@$keywArr);
        $maxResults = 50; //floor($inputTotalpll/$countKw);
        if (@$onlyme == 1) {
          $maxResults = $inputTotalpll;
        } else if(!empty(@$inputTotalpll) && $inputTotalpll <= 50) {
          $maxResults = $inputTotalpll;
        }
        
        switch (@$onlyme) {
          case 1:
            $htmlBody = '';
            for ($i=0; $i < $maxResults; $i++) { 

              // Check token expired
              if($client->isAccessTokenExpired()) {
                  echo 'Access Token Expired - Get Refresh token';
                  $refreshToken = $_SESSION['refresh_token']; 
                  $client->refreshToken($refreshToken);
                  $_SESSION['token'] = $client->getAccessToken();
              }

              // Create pll
              $playlistSnippet = new Google_Service_YouTube_PlaylistSnippet();
              $playlistSnippet->setTitle($title. ' #'.$i);
              $playlistSnippet->setDescription($description);

              $playlistStatus = new Google_Service_YouTube_PlaylistStatus();
              $playlistStatus->setPrivacyStatus('public');

              $youTubePlaylist = new Google_Service_YouTube_Playlist();
              $youTubePlaylist->setSnippet($playlistSnippet);
              $youTubePlaylist->setStatus($playlistStatus);

              $playlistResponse = $youtube->playlists->insert('snippet,status', $youTubePlaylist, array());
              $htmlBody .= $playlistResponse['id'] ."\n";

              $existIds = @explode(",", @$inputVideos);

              // Coppy video to my pll
              for ($j=0; $j<count($existIds); $j++) {
                  $resourceId = new Google_Service_YouTube_ResourceId();
                  $resourceId->setVideoId($existIds[$j]);
                  $resourceId->setKind('youtube#video');

                  $playlistItemSnippet = new Google_Service_YouTube_PlaylistItemSnippet();
                  $playlistItemSnippet->setPlaylistId($playlistResponse['id']);
                  $playlistItemSnippet->setResourceId($resourceId);

                  $playlistItem = new Google_Service_YouTube_PlaylistItem();
                  $playlistItem->setSnippet($playlistItemSnippet);

                  try {
                    $playlistItemResponse = $youtube->playlistItems->insert('snippet,contentDetails', $playlistItem, array());
                  } catch(Google_Service_Exception $e) {
                    // By pass
                  }
              }
            }
            break;

          // Get Video Top Title
          case 2:
            $htmlBody = '';
            $totalPll = 0;
            // Search pll by keywords
            for ($i=0; $i < $countKw; $i++) {
              $searchUrl = 'https://www.googleapis.com/youtube/v3/search?part=snippet&type=video&fields=items&order='.@$order.'&maxResults='.$maxResults.'&key='.$api_key; 
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
                    $tmpTitle = array( @$result_json->items[$ipll]->snippet->title);
                    $pllTitleArr[] = $tmpTitle;
                    $totalPll += 1;
                  }
              }
            }

            // Tinh tong so pll can tao theo tu khoa: (inputPll * keywords)

            for ($i=0; $i < $totalPll; $i++) { 
              // Check token expired
              if($client->isAccessTokenExpired()) {
                  echo 'Access Token Expired - Get Refresh token';
                  $refreshToken = $_SESSION['refresh_token']; 
                  $client->refreshToken($refreshToken);
                  $_SESSION['token'] = $client->getAccessToken();
              }

              // Create pll
              $playlistSnippet = new Google_Service_YouTube_PlaylistSnippet();
              $playlistSnippet->setTitle(@$pllTitleArr[$i][0]);
              $playlistSnippet->setDescription(@$description);

              $playlistStatus = new Google_Service_YouTube_PlaylistStatus();
              $playlistStatus->setPrivacyStatus('public');

              $youTubePlaylist = new Google_Service_YouTube_Playlist();
              $youTubePlaylist->setSnippet($playlistSnippet);
              $youTubePlaylist->setStatus($playlistStatus);

              $playlistResponse = $youtube->playlists->insert('snippet,status', $youTubePlaylist, array());
              $htmlBody .= $playlistResponse['id'] ."\n";

              $existIds = @explode(",", @$inputVideos);

              // Coppy video to my pll
              for ($j=0; $j<count($existIds); $j++) {
                  $resourceId = new Google_Service_YouTube_ResourceId();
                  $resourceId->setVideoId($existIds[$j]);
                  $resourceId->setKind('youtube#video');

                  $playlistItemSnippet = new Google_Service_YouTube_PlaylistItemSnippet();
                  $playlistItemSnippet->setPlaylistId($playlistResponse['id']);
                  $playlistItemSnippet->setResourceId($resourceId);

                  $playlistItem = new Google_Service_YouTube_PlaylistItem();
                  $playlistItem->setSnippet($playlistItemSnippet);

                  try {
                    $playlistItemResponse = $youtube->playlistItems->insert('snippet,contentDetails', $playlistItem, array());
                  } catch(Google_Service_Exception $e) {
                    // By pass
                  }
              }
            }
            break;

          default:
            $htmlBody = '';
            // Search pll by keywords
            for ($i=0; $i < $countKw; $i++) {
              $searchUrl = 'https://www.googleapis.com/youtube/v3/search?part=snippet&type=playlist&fields=items&order='.@$order.'&maxResults='.$maxResults.'&key='.$api_key; 
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
                    $tmpId = array( @$result_json->items[$ipll]->id->playlistId);
                    $tmpTitle = array( @$result_json->items[$ipll]->snippet->title);
                    $tmpDes = array( @$result_json->items[$ipll]->snippet->description);
                    $pllIdArr[] = $tmpId;
                    $pllTitleArr[] = $tmpTitle;
                    $pllDesArr[] = $tmpDes;
                  }
              }
            }

            // Clone pll from source playlist
            for ($iCount=0; $iCount < count($pllIdArr); $iCount++) { 

              // Check token expired
              if($client->isAccessTokenExpired()) {
                  echo 'Access Token Expired - Get Refresh token';
                  $refreshToken = $_SESSION['refresh_token']; 
                  $client->refreshToken($refreshToken);
                  $_SESSION['token'] = $client->getAccessToken();
              }
              $pllID = $pllIdArr[$iCount][0];
              $pllTitile = $pllTitleArr[$iCount][0];
              $pllDes = $pllDesArr[$iCount][0];
              if (!empty(@$title)) {
                  $pllTitile = $title;
              }
              if (!empty(@$description)) {
                  $pllDes = $description;
              }

              // Get all pll items
              $options = array ("playlistId" => $pllID, "maxResults" => 50);
              $videos = "";
              $isCont = true;
              $iMaxVids = 0;

              do {
                  $playlist = $youtube->playlistItems->listPlaylistItems("snippet", $options);
                  $nextPageToken = $playlist["nextPageToken"];
                  $options["pageToken"] = $nextPageToken;

                  foreach ($playlist["items"] as $playlistItem) {
                      $videos .=  $playlistItem["snippet"]["resourceId"]["videoId"] . "#";
                      $iMaxVids++;
                      if ($iMaxVids >= $maxVideos) {
                        $isCont = false;
                        break;
                      }
                  }

              } while ($nextPageToken && $isCont);

              // Create pll
              $playlistSnippet = new Google_Service_YouTube_PlaylistSnippet();
              $playlistSnippet->setTitle($pllTitile);
              $playlistSnippet->setDescription($pllDes);

              $playlistStatus = new Google_Service_YouTube_PlaylistStatus();
              $playlistStatus->setPrivacyStatus('public');

              $youTubePlaylist = new Google_Service_YouTube_Playlist();
              $youTubePlaylist->setSnippet($playlistSnippet);
              $youTubePlaylist->setStatus($playlistStatus);

              $playlistResponse = $youtube->playlists->insert('snippet,status', $youTubePlaylist, array());

              $htmlBody .= $playlistResponse['id'] ."\n";

              $ids = @explode ( "#", $videos ) ;
              $existIds = @explode(",", @$inputVideos);
              $videoIds = $existIds + $ids;

              // Coppy video to my pll
              for ($j=0; $j<count($videoIds); $j++) {
                  $resourceId = new Google_Service_YouTube_ResourceId();
                  $resourceId->setVideoId($videoIds[$j]);
                  $resourceId->setKind('youtube#video');

                  $playlistItemSnippet = new Google_Service_YouTube_PlaylistItemSnippet();
                  $playlistItemSnippet->setPlaylistId($playlistResponse['id']);
                  $playlistItemSnippet->setResourceId($resourceId);

                  $playlistItem = new Google_Service_YouTube_PlaylistItem();
                  $playlistItem->setSnippet($playlistItemSnippet);

                  try {
                    $playlistItemResponse = $youtube->playlistItems->insert('snippet,contentDetails', $playlistItem, array());
                  } catch(Google_Service_Exception $e) {
                    // By pass
                  }
              }
            }
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
	<title>New Playlist</title>
	<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
  <script type="text/javascript" src="assets/jquery.min.js"></script>
</head>
<body>
  <div class="container"><div class="row"><div class="col-sm-8 col-sm-offset-2">
  <h2 class="text-center">Tool tạo playlist từ Search</h1>
  <h5 class="text-center alert alert-info">Note: Tổng Playlist/1 keywords <= 50</h4>
  <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" class="form-horizontal" role="form">

    <div class="form-group alert alert-success">
      <div class="text-center col-sm-4">
        <input type="radio" name="onlyme" id="onlyme1" value="1" <?php if(@$onlyme==1):?> checked="checked" <?php endif; ?>>
        Tạo Pll Từ Video Input, Input Title bằng tay
      </div>

      <div class="text-center  col-sm-4">
        <input type="radio" name="onlyme" id="onlyme2" value="2" <?php if(@$onlyme==2):?> checked="checked" <?php endif; ?>>
        Tạo Pll Từ Video Input,Lấy Title của Video Top
      </div>

      <div class="text-center  col-sm-4">
        <input type="radio" name="onlyme" id="onlyme3" value="3" <?php if(@$onlyme==3):?> checked="checked" <?php endif; ?>>
        Tạo playlist từ search keywords
      </div>
    </div>
  	<div class="form-group">
  		<label for="inputTotalpll" class="col-sm-4 control-label">Số lượng Pll trên 1 từ khóa:</label>
  		<div class="col-sm-8">
  			<input type="number" name="inputTotalpll" id="inputTotalpll" class="form-control" value="<?php echo @$inputTotalpll; ?>" required="required" placeholder="Số lượng Pll or Số lượng Pll/1 keyword">
  		</div>
  	</div>

    <div class="form-group">
      <label for="title" class="col-sm-4 control-label">Tiêu Đề Playlist: </label>
      <div class="col-sm-8">
        <input type="text" name="title" id="title" class="form-control" value="<?php echo @$title; ?>" placeholder="Nhập title chung cho toàn bộ playlist">
      </div>
    </div>

    <div class="form-group">
      <label for="description" class="col-sm-4 control-label">Mô Tả Playlist: </label>
      <div class="col-sm-8">
        <textarea rows="3" name="description" id="description" class="form-control" placeholder="Mô tả chung cho toàn bộ playlist, Nếu không nhập sẽ lấy thông tin từ playlist đã tìm được"><?php echo @$description; ?></textarea>
      </div>
    </div>

  	<div class="form-group">
  		<label for="inputVideos" class="col-sm-4 control-label">ID video của bạn:</label>
  		<div class="col-sm-8">
  			<input type="text" name="inputVideos" id="inputVideos" class="form-control" value="<?php echo @$inputVideos; ?>" placeholder="ID video, cách nhau bằng dấu phẩy">
  		</div>
  	</div>

    <div class="form-group">
      <label for="maxVideos" class="col-sm-4 control-label">Max Videos:</label>
      <div class="col-sm-8">
        <input type="number" name="maxVideos" id="maxVideos" class="form-control" value="<?php echo @$maxVideos; ?>" placeholder="Số lượng videos trong 1 Playlistpl">
      </div>
    </div>

  	<div class="form-group">
  		<label for="keywords" class="col-sm-4 control-label">Danh sách từ khóa:</label>
  		<div class="col-sm-8">
  			<textarea rows="4" name="keywords" id="keywords" class="form-control" placeholder="Nhập từ khóa vào đây, cách nhau bằng dấu phẩy"><?php echo @$keywords; ?></textarea>
  		</div>
  	</div>
    <div class="form-group">
      <label for="keywords" class="col-sm-4 control-label">Search Order:</label>
      <div class="col-sm-8">
          <select name="order" id="order" class="form-control">
            <?php 
              foreach ($orders as $key => $value) {
                  if(@$order == $key) {
                    echo "<option value=".$key." selected>".$value."</option>";
                  } else {
                    echo "<option value=".$key.">".$value."</option>";
                  }
              }
            ?>
          </select>
      </div>
    </div>

  	<div class="form-group">
  		<div class="col-sm-8 col-sm-offset-4">
  			<button type="submit" id="create_now" class="btn btn-primary">Quất Ngay!!!</button>
  		</div>
  	</div>

    <div class="form-group">
    <div class="col-xs-4 col-md-offset-4">
      <a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>/ytb-tool/index.php">Home Page</a>
    </div>
    </div>
  </form>
  </div>
  </div>
  <div class="row"><div class="col-sm-6 col-sm-offset-3" id="result_text"></div></div>
  <div class="row">
    <div class="col-sm-8 col-sm-offset-2" id="result_area">
      <textarea id="result" rows="4" class="form-control" placeholder="Kết quả added playlist">
        <?php echo @$htmlBody; ?>
      </textarea>
    </div>
  </div>
  </div>

  <script type="text/javascript">
      /*$('#onlyme1').click(function(event) {
        if($(this).is(':checked'))
        {
          $("#maxVideos").attr("disabled", "disabled");
          $("#keywords").attr("disabled", "disabled");
        } else {
          $("#maxVideos").removeAttr("disabled");
          $("#keywords").removeAttr("disabled");
        }
      });*/

      $('input[type=radio][name=onlyme]').change(function() {
        if (this.value == '1' && $(this).is(':checked')) {
            $("#maxVideos").attr("disabled", "disabled");
            $("#keywords").attr("disabled", "disabled");
            $("#maxVideos").removeAttr("required");
            $("#keywords").removeAttr("required");
            $("#title").attr("required", "required");
            $("#inputVideos").attr("required", "required");
        }
        else if (this.value == '2' && $(this).is(':checked')) {
            $("#maxVideos").attr("disabled", "disabled");
            $("#maxVideos").removeAttr("required");
            $("#title").removeAttr("required");
            $("#keywords").removeAttr("disabled");
            $("#keywords").attr("required", "required");
            $("#inputVideos").attr("required", "required");
        } else {
          $("#maxVideos").removeAttr("disabled");
          $("#keywords").removeAttr("disabled");
          $("#inputVideos").removeAttr("required");
          $("#title").removeAttr("required");
          $("#maxVideos").attr("required", "required");
          $("#keywords").attr("required", "required");
        }
    });

  </script>
</body>
</html>