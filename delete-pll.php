<?php 
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
    			$pllIds = @explode(',', @$inputPlaylistId);
        		$countPll = @count(@$pllIds);
        		if ($countPll == 0) {
        			$htmlBody .= "<p> Không có playlist nào! </p>";
        		}
        		for ($i=0; $i < $countPll; $i++) { 
        			try {
	                    $playlistItemResponse = $youtube->playlists->delete(ltrim(rtrim($pllIds[$i])));
	                    $htmlBody .= "Deleted: https://www.youtube.com/playlist?list=" .ltrim(rtrim($pllIds[$i])). "\n";
	                } catch(Google_Exception $e) {
	                    // by pass
	                }
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
	<title>Delete Playlist</title>
	<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
  <script type="text/javascript" src="assets/jquery.min.js"></script>
</head>

<body>
	<div class="container">
		<div class="row">
		<div class="col-sm-8 col-sm-offset-2">
  		<h2 class="text-center">Xóa Playlist Theo ID</h2>

  		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" class="form-horizontal" role="form">
  			<div class="form-group">
		      <label for="inputPlaylistId" class="col-sm-4 control-label">Ids Playlist: </label>
		      <div class="col-sm-8">
		        <textarea rows="3" name="inputPlaylistId" id="inputPlaylistId" class="form-control" placeholder="Nhập vào id playlist, cách nhau bởi dấu phẩy" required="required"><?php echo @$inputPlaylistId; ?></textarea>
		      </div>
		    </div>

		    <div class="form-group">
		  		<div class="col-sm-8 col-sm-offset-4">
		  			<button type="submit" id="create_now" class="btn btn-primary">Xóa Ngay!!!</button>
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
		    <div class="col-sm-6 col-sm-offset-3" id="result_area">
		      <textarea rows="4" class="form-control" placeholder="Kết quả delete playlist">
		        <?php echo @$htmlBody; ?>
		      </textarea>
		    </div>
			</div>
  		</div>
  	</div>
</body>