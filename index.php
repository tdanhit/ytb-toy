<?php 
require_once 'v3/v3-config.php';
@session_start();
ini_set('memory_limit', '99999M');
$filelog = 'filelog.txt';
require_once "functions.php";
$api_key = 'AIzaSyB0X-0jZwmNtZMdJzPVUGHLsZSPk1GVGJw';
$YT_Developer_Key = "AI39si7SnB9SXTjsmmmbzQwMFmKqE4gkIU6EpBQsM9zKcgBl9ql7JsBbDebEZ51b_uDMQDSQ_egkBUkJf3J2qa35erTQhXTLnA";
if(@$_REQUEST['act'] == 'ajax')
{
	extract($_REQUEST);
	die();
}
//die();
$categories = array(
	'22' => 'People & Blogs', 
	'1' => 'Film & Animation', 
	'2' => 'Cars & Vehicles', 
	'10' => 'Music', 
	'15' => 'Pets & Animals', 
	'17' => 'Sports', 
	'19' => 'Travel & Events', 
	'20' => 'Gaming', 
	'23' => 'Comedy', 
	'25' => 'News & Politics', 
	'24' => 'Entertainment', 
	'27' => 'Education', 
	'26' => 'Howto & Style', 
	'29' => 'Nonprofits & Activism', 
	'28' => 'Science & Technology');
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
$hoursRange = array(
	'1' => '1 Giờ',
	'2' => '2 Giờ',
	'3' => '3 Giờ',
	'4' => '4 Giờ',
	'5' => '5 Giờ',
	'6' => '6 Giờ',
	'7' => '7 Giờ',
	'8' => '8 Giờ',
	'9' => '9 Giờ',
	'10' => '10 Giờ'
	);
$statusArray = array(
	'private' => 'Private',
	'unlisted' => 'Unlisted',
	'public' => 'Public',
	);

$cutTimeRange = array(
	'0' => 	'Giữ nguyên độ dài',
	'01'	=>	'Cắt 1s',
	'03'	=>	'Cắt 3s',
	'05'	=>	'Cắt 5s',
	'06'	=>	'Cắt 6s',
	'07'	=>	'Cắt 7s',
	'08'	=>	'Cắt 8s',
	'09'	=>	'Cắt 9s',
	'10'=>	'Cắt 10s',
	'15'=>	'Cắt 15s'
	);

$allowExt = array('mp4', 'webm', 'flv', '3gp', 'mov', 'avo', 'wmv');
$amthanh = 0;
$hinhanh = 0;
$cutVideo = 0;
//$uploadVideo = 1;
$setThumbnail=0;
$categoryId=22;
$publicAt = null;
$deleteAfter = 1;
$downVideo = 1;
$renderVideo = 1;
$uploadVideo = 1;
$puslishSerial = 0;
$videoStatus = 'private';
$lat = '';
$lng = '';
extract($_REQUEST);

if (isset($_GET['code'])) {

	$client->authenticate($_GET['code']);
	$NewAccessToken = json_decode($client->getAccessToken());

	// Save to session
	$_SESSION['refresh_token'] = $NewAccessToken->refresh_token;
	$_SESSION['token'] = $client->getAccessToken();

	header('Location: ' . REDIRECT_URI);
}

if (isset($_SESSION['token'])) {
	$client->setAccessToken($_SESSION['token']);
}

if(isset($links))
{
	unset($_SESSION['dat_step']);
	$_SESSION['links'] = @$links;
	$_SESSION['playlists'] = @$playlists;
	$_SESSION['publicAt'] = @$publicAt;
	$_SESSION['puslishSerial'] = @$puslishSerial;
	$_SESSION['videoStatus'] = @$videoStatus;
	$_SESSION['lat'] = @$lat;
	$_SESSION['lng'] = @$lng;

	$_SESSION['amthanh'] = @$amthanh;
	$_SESSION['hinhanh'] = @$hinhanh;
	$_SESSION['cutVideo'] = @$cutVideo;
	//$_SESSION['uemail'] = $uemail;
	//$_SESSION['upass'] = $upass;
	if(empty($_REQUEST['setThumbnail'])) $setThumbnail = 0;
	if(empty($_REQUEST['videoStatus'])) $videoStatus = 'private';
	if(empty($_REQUEST['puslishSerial'])) $puslishSerial = 0;
	if(empty($_REQUEST['deleteAfter'])) $deleteAfter = 0;
	if(empty($_REQUEST['downVideo'])) $downVideo = 0;
	if(empty($_REQUEST['renderVideo'])) $renderVideo = 0;
	if(empty($_REQUEST['uploadVideo'])) $uploadVideo = 0;
	if(empty($_REQUEST['lat'])) $lat = '';
	if(empty($_REQUEST['lng'])) $lng = '';
	$_SESSION['setThumbnail'] = @$setThumbnail;
	$_SESSION['puslishSerial'] = $puslishSerial;
	$_SESSION['videoStatus'] = $videoStatus;
	$_SESSION['publicAt'] = @$publicAt;
	$_SESSION['deleteAfter'] = @$deleteAfter;
	$_SESSION['downVideo'] = @$downVideo;
	$_SESSION['renderVideo'] = @$renderVideo;
	$_SESSION['uploadVideo'] = @$uploadVideo;
	$_SESSION['lat'] = @$lat;
	$_SESSION['lng'] = @$lng;

	$_SESSION['categoryId'] = $categoryId;
	$_SESSION['videoTitle'] = @$videoTitle;
	$_SESSION['titlePosition'] = @$titlePosition;
	$_SESSION['videoDes'] = @$videoDes;
	$_SESSION['desPosition'] = @$desPosition;
	$_SESSION['videoTags'] = @$videoTags;
	$_SESSION['tagsPosition'] = @$tagsPosition;
} else {
	if(isset($_SESSION['amthanh']))
	{
		$links = @$_SESSION['links'];
		$playlists = @$_SESSION['playlists'];
		$amthanh = @$_SESSION['amthanh'];
		$hinhanh = @$_SESSION['hinhanh'];
		$cutVideo = @$_SESSION['cutVideo'];
		//$uemail = @$_SESSION['uemail'];
		//$upass = @$_SESSION['upass'];
		$videoStatus = @$_SESSION['videoStatus'];
		$setThumbnail = @$_SESSION['setThumbnail'];
		$deleteAfter = @$_SESSION['deleteAfter'];
		$downVideo = @$_SESSION['downVideo'];
		$renderVideo = @$_SESSION['renderVideo'];
		$uploadVideo = @$_SESSION['uploadVideo'];
		$puslishSerial = @$_SESSION['puslishSerial'];

		$lat = @$_SESSION['lat'];
		$lng = @$_SESSION['lng'];

		$categoryId = @$_SESSION['categoryId'];
		$videoTitle = @$_SESSION['videoTitle'];
		$titlePosition = @$_SESSION['titlePosition'];
		$videoDes = @$_SESSION['videoDes'];
		$desPosition = @$_SESSION['desPosition'];
		$videoTags = @$_SESSION['videoTags'];
		$tagsPosition = @$_SESSION['tagsPosition'];
	} else {
		$amthanh = 0;
		$hinhanh = 0;
		$cutVideo = 0;
	}
	
}
?>
<!DOCTYPE html>
<html lang="">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Youtube Reup Tool</title>

		<!-- Bootstrap CSS -->
		<link href="//netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
		<link href="assets/css/bootstrap-datetimepicker.css" rel="stylesheet">
		<style type="text/css">
		#map {
		    width: 100%;
		    height: 300px;
		}
		.controls {
		    margin-top: 10px;
		    border: 1px solid transparent;
		    border-radius: 2px 0 0 2px;
		    box-sizing: border-box;
		    -moz-box-sizing: border-box;
		    height: 32px;
		    outline: none;
		    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
		}
		#searchInput {
		    background-color: #fff;
		    font-family: Roboto;
		    font-size: 15px;
		    font-weight: 300;
		    margin-left: 12px;
		    padding: 0 11px 0 13px;
		    text-overflow: ellipsis;
		    width: 50%;
		}
		#searchInput:focus {
		    border-color: #4d90fe;
		}
		.nsuccess {
			font-weight: bold;
			color: #5cb85c;
		}
		.nfail {
			font-weight: bold;
			color: #d9534f;
		}
		.icon-calendar {
		    background-position: -192px -120px;
		}
		[class^="icon-"], [class*=" icon-"] {
		    display: inline-block;
		    width: 14px;
		    height: 14px;
		    margin-top: 1px;
		    line-height: 14px;
		    vertical-align: text-top;
		    background-image: url('assets/img/glyphicons-halflings.png');
		    background-repeat: no-repeat;
		}
		.btn { padding: 14px 26px;};
		#checkrs {display: none;}
		</style>
		<script type="text/javascript">
		var isUp = true;
		</script>
	</head>
	<body>
		<h1 class="text-center">Tool Upload YTB</h1>
		<div class="container">
			<div class="row">
				<div class="form-group">
					<div class="col-xs-10">
						<div class="form-group btn-group">
							<input type="text" name="code" id="code" class="form-control hide " value="<?php echo @$_GET['code']; ?>" placeholder="Code">
							<button type="button" class="btn btn-primary" id="uploadfile">Up Json</button>
							<button type="button" class="btn btn-primary" id="uploadthumbnail">Up Thumbnail</button>
							<button type="button" class="btn btn-primary" id="cleardata">Xóa Data Lỗi</button>
							<button type="button" class="btn btn-primary" id="createPll">Tạo Playlist</button>
							<button type="button" class="btn btn-primary" id="deletePlaylist">Xóa Playlist</button>
							<button type="button" class="btn btn-primary" id="updatePlaylist">Update PLL</button>
							<button type="button" class="btn btn-info" id="testupload">Refresh Token</button>
						</div>
					</div>
					<div class="col-xs-2" id="checkrs"></div>
				</div>
				<div class="col-xs-12">
					<form action="<?php echo $_SERVER['PHP_SELF']; ?>?dat_step=downloaded" method="POST" class="form-horizontal" role="form" onsubmit="return checkForm();">
						<div class="form-group">
							<div class="col-sm-12">
								<textarea name="links" id="inputLinks" class="form-control" rows="7" <?php if(@$downVideo): ?>required="required"<?php endif; ?>><?php echo @$links; ?></textarea>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-3">
								<label>
									<input type="checkbox" name="setThumbnail" value="1" <?php if(@$setThumbnail==1):?> checked="checked" <?php endif; ?>>
									Custom Thumbnail
								</label>
								<textarea  name="playlists" id="inputPlaylists" class="form-control hide" rows="5" <?php if(@$playlists): ?>required="required"<?php endif; ?>><?php echo @$playlists; ?></textarea>
							</div>
							<div class="col-sm-3">
								<label>Lịch Public: </label>
								<div class="well form-group">
									<div id="datetimepicker1" class="input-append">
								    	<input name="publicAt" id="publicAt" class="form-control" value="<?php echo @$_SESSION['publicAt']; ?>" date-format="yyyy-MM-DD HH:mm:ss" type="text"></input>
								    	<span class="add-on">
								      	<i data-time-icon="icon-time" data-date-icon="icon-calendar"></i>
								    	</span>
								  	</div>
								  	<p id="utc-date"></p>
									<label>
										<!-- <input type="checkbox" name="puslishSerial" value="1" <?php if(@$puslishSerial==1):?> checked="checked" <?php endif; ?>> -->
										Publish Cách Nhau:
										<select name="puslishSerial" id="puslishSerial" class="form-control">
											<?php 
											  foreach ($hoursRange as $key => $value) {
											  		if(@$puslishSerial == $key) {
												     	echo "<option value=".$key." selected>".$value."</option>";
											  		} else {
											  			echo "<option value=".$key.">".$value."</option>";
											  		}
												}
											?>
										</select>
									</label>
									
								</div>
							</div>
							<div class="col-sm-3">
								<label>Select Category: </label>
								<div class="well">
									<select name="categoryId" class="form-control">
									<?php 
									  foreach ($categories as $key => $value) {
									  		if(@$categoryId == $key) {
										     	echo "<option value=".$key." selected>".$value."</option>";
									  		} else {
									  			echo "<option value=".$key.">".$value."</option>";
									  		}
										}
									?>
									</select>
								</div>
							</div>

							<!-- Status Privacy -->
							<div class="col-sm-3">
								<label>Select Video's Status: </label>
								<div class="well">
									<select name="videoStatus" id="videoStatus" class="form-control">
									<?php 
									  foreach ($statusArray as $key => $value) {
									  		if(@$videoStatus == $key) {
										     	echo "<option value=".$key." selected>".$value."</option>";
									  		} else {
									  			echo "<option value=".$key.">".$value."</option>";
									  		}
										}
									?>
									</select>
								</div>
							</div>
						</div>
						<!-- Select Location -->
						
						<div class="form-group" >
							<div class="col-sm-4" >
								<h4>Select Video's Location: </h4>
								<input type="text" name="lat" class="form-control" value="<?php echo @$_SESSION['lat']; ?>" id="lat"> <br>
								<input type="text" name="lng" class="form-control" value="<?php echo @$_SESSION['lng']; ?>" id="lng"> <br>

	            				<button type="button" id="clear-location" class="btn btn-danger"> Xóa Location</button>
							</div>
							<div class="col-sm-8">
								<input id="searchInput" class="controls" type="text" placeholder="Enter a location">
								<div id="map"></div>
							</div>
						</div>

						<div class="form-group">

							<div class="col-sm-2">
								<h4>Cắt đầu - Cắt Đuôi</h4>
								<select name="cutVideo" id="cutVideo" class="form-control">
									<?php 
									  foreach ($cutTimeRange as $key => $value) {
									  		if(@$cutVideo == $key) {
										     	echo "<option value=".$key." selected>".$value."</option>";
									  		} else {
									  			echo "<option value=".$key.">".$value."</option>";
									  		}
										}
									?>
								</select>
							</div>

							<div class="col-sm-2 hide">
								<h4>Render Âm Thanh</h4>
								<div class="radio">
									<label>
										<input type="radio" name="amthanh" id="inputAmthanh" value="0" <?php if(@$amthanh==0):?> checked="checked" <?php endif; ?>>
										Không chỉnh Âm
									</label>
								</div>
								<div class="radio">
									<label>
										<input type="radio" name="amthanh" id="inputAmthanh" value="1" <?php if(@$amthanh==1):?> checked="checked" <?php endif; ?>>
										Âm Thanh M.1
									</label>
								</div>
								<div class="radio">
									<label>
										<input type="radio" name="amthanh" id="inputAmthanh" value="2" <?php if(@$amthanh==2):?> checked="checked" <?php endif; ?>>
										Âm Thanh M.2 
									</label>
								</div>
							</div>
							<div class="col-sm-2 hide">
								<h4>Render Hình Ảnh</h4>
								<div class="radio">
									<label>
										<input type="radio" name="hinhanh" id="inputhinhanh" value="0" <?php if(@$hinhanh==0):?> checked="checked" <?php endif; ?>>
										Không chỉnh hình ảnh
									</label>
								</div>
								<div class="radio">
									<label>
										<input type="radio" name="hinhanh" id="inputhinhanh" value="1" <?php if(@$hinhanh==1):?> checked="checked" <?php endif; ?>>
										Hình Ảnh M.1
									</label>
								</div>
								<div class="radio">
									<label>
										<input type="radio" name="hinhanh" id="inputhinhanh" value="2" <?php if(@$hinhanh==2):?> checked="checked" <?php endif; ?>>
										Hình Ảnh M.2
									</label>
								</div>
								<div class="radio">
									<label>
										<input type="radio" name="hinhanh" id="inputhinhanh" value="3" <?php if(@$hinhanh==3):?> checked="checked" <?php endif; ?>>
										Hình Ảnh M.3
									</label>
								</div>
								<div class="radio">
									<label>
										<input type="radio" name="hinhanh" id="inputhinhanh" value="4" <?php if(@$hinhanh==4):?> checked="checked" <?php endif; ?>>
										Hình Ảnh M.4
									</label>
								</div>
								<div class="radio">
									<label>
										<input type="radio" name="hinhanh" id="inputhinhanh" value="5" <?php if(@$hinhanh==5):?> checked="checked" <?php endif; ?>>
										Hình Ảnh M.5
									</label>
								</div>
							</div>
							
							<div class="col-sm-8">
								<h4>Cài Đặt Data</h4>
								<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
								  <div class="panel panel-info">
									<div class="panel-heading" role="tab" id="headingOne">
									  <h4 class="panel-title">
										<a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
										  Cài Đặt Data
										</a>
									  </h4>
									</div>
									<div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
									  <div class="panel-body">
										<div class="row">
											<div class="col-sm-8">
												<input type="text" name="videoTitle" id="inputVideoTitle" class="form-control" value="<?php echo @$videoTitle ?>" placeholder="Tiêu đề Video">
											</div>
											<div class="col-sm-4">
												<select name="titlePosition" id="input" class="form-control">
													<option value="1" <?php echo (@$titlePosition==1)?'selected':''; ?>>Ở đầu</option>
													<option value="2" <?php echo (@$titlePosition==2)?'selected':''; ?>>Ở cuối</option>
												</select>
											</div>
										</div>
										<hr>
										<div class="row">
											<div class="col-sm-8">
											<textarea name="videoDes" id="inputVideoDes" class="form-control" rows="3" placeholder='Mô Tả'><?php echo (@$videoDes)?$videoDes:''; ?></textarea>
											</div>
											<div class="col-sm-4">
												<select name="desPosition" id="input" class="form-control">
													<option value="1" <?php echo (@$desPosition==1)?'selected':''; ?>>Đặt thành</option>
													<option value="2" <?php echo (@$desPosition==2)?'selected':''; ?>>Thêm vào cuối</option>
												</select>
											</div>
										</div>
										<hr>
										<div class="row">
											<div class="col-sm-8">
												<input type="text" name="videoTags" id="inputvideoTags" class="form-control" value="<?php echo @$videoTags ?>" placeholder="Tags">
											</div>
											<div class="col-sm-4">
												<select name="tagsPosition" id="input" class="form-control">
													<option value="1" <?php echo (@$tagsPosition==1)?'selected':''; ?>>Đặt thành</option>
													<option value="2" <?php echo (@$tagsPosition==2)?'selected':''; ?>>Thêm vào cuối</option>
												</select>
											</div>
										</div>
									  </div>
									</div>
								  </div>
								 </div>
							</div>
							<input type="hidden" id="ischeck" name="ischeck" value="<?php echo (isset($ischeck))?$ischeck:'0'; ?>">
							<div class="col-sm-2">
								<h4>Cài Đặt Tool</h4>
								<div class="checkbox">
									<label>
										<input type="checkbox" name="deleteAfter" value="1" <?php if(@$deleteAfter==1):?> checked="checked" <?php endif; ?>>
										Xóa file sau upload.
									</label>
									<label>
										<input type="checkbox" name="downVideo" id="downVideo" value="1" <?php if(@$downVideo==1):?> checked="checked" <?php endif; ?>>
										Download Videos
									</label>
									<label>
										<input type="checkbox" name="renderVideo" id="renderVideo" value="1" <?php if(@$renderVideo==1):?> checked="checked" <?php endif; ?>>
										Render Videos
									</label>
									<label>
										<input type="checkbox" id="uploadVideo" name="uploadVideo" value="1" <?php if(@$uploadVideo==1):?> checked="checked" <?php endif; ?>>
										Re-Upload Videos
									</label>
									<br>

									<!-- <a href="rpl.php" target="_blank">Trình quản lý File</a> -->
								</div>
								<button type="submit" class="btn btn-success btn-block btn-large" name="submit" style="font-size: 20px;">Re-Upload</button>
							</div>

							
						</div>
					</form>
					<script src="classes/js.js"></script>
				    <script async defer
					    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDgx4Kt7GGo7cr6_r-JdAqqLr3JEEF25RY&sensor=false&libraries=places&callback=initMap">
					</script>
					<script src="assets/js/map.js"></script>
					<script type="text/javascript">
						$('#uploadVideo').click(function(event) {
							if($(this).is(':checked'))
							{
								$('#uemail').prop('required', 'required');
								$('#upass').prop('required', 'required');
							} else {
								$('#uemail').removeProp('required');
								$('#upass').removeProp('required');
							}
						});
						$('#downVideo').click(function(event) {
							if($(this).is(':checked'))
							{
								$('#inputLinks').prop('required', 'required');
							} else {
								$('#inputLinks').removeProp('required');
							}
						});
						$('#clear-location').click(function(event) {
							$('#lat').val('');
							$('#lng').val('');
						});

						$("#uploadfile").click(function() {
							window.location.href = 'v3/upload-json.php';
						});

						$("#createPll").click(function() {
							window.location.href = 'create-pll.php';
						});

						$("#deletePlaylist").click(function() {
							window.location.href = 'delete-pll.php';
						});
						$("#updatePlaylist").click(function() {
							window.location.href = 'update-pll.php';
						});

						$("#uploadthumbnail").click(function() {
							window.location.href = 'v3/upload-thumbnail.php';
						});
						$('#cleardata').click(function(event) {
							$.ajax({
								url: 'cleardata.php',
								type: 'POST',
								data: {act: 'ajax'},
								success: function(res)
								{
									if(res == 'OK')
									{
										$('#checkrs').html('<p class="alert alert-success"> Đã Xóa Files Lỗi Thành Công! !</p>');
										$('#checkrs').animate({ width: 'show' });
										$('#ischeck').val(1);
										setTimeout(function(){ $('#checkrs').animate({ width: 'hide' }); }, 15000);
									} else {
										$('#checkrs').html('<div class="alert alert-danger">'+res+' !</div>');
										$('#checkrs').animate({ width: 'show' });
										setTimeout(function(){ $('#checkrs').animate({ width: 'hide' }); }, 15000);
									}
								},
							});
						});
					</script>
					<script type="text/javascript" src="assets/jquery.min.js"></script>
					<script src="assets/bootstrap.min.js"></script>
					<script type="text/javascript" src="assets/js/bootstrap-datetimepicker.min.js"></script>
					<script type="text/javascript" src="assets/js/moment-with-locales.js"></script>
					<script type="text/javascript" src="assets/js/moment-timezone-with-data-2012-2022.js"></script>
					<script type="text/javascript" src="assets/js/moment-timezone-with-data.js"></script>
					<script type="text/javascript" src="assets/js/moment-timezone.js"></script>
					
					<script type="text/javascript">
						  $(function() {
						    $('#datetimepicker1').datetimepicker({
						      	timeZone: 'GMT',
							    sideBySide: true,
							    format: 'yyyy-MM-dd hh:mm:ss'
						    });
						    var dateVal = $('#publicAt').val();
						    dateVal = moment(dateVal,'YYYY-MM-DDThh:mm:ssZ').tz('America/Los_Angeles').format('YYYY-MM-DD HH:mm');
						    if (dateVal != "Invalid date") {
						    	$('#utc-date').text('Youtube Time: ').append(dateVal);
						    }
					    	
						    $('#datetimepicker1').datetimepicker().on('changeDate', function (ev) {
							    var dateVal = $('#publicAt').val();

							    /*$userTimezone = new DateTimeZone('America/Los_Angeles');
								$gmtTimezone = new DateTimeZone('GMT');
								$myDateTime = new DateTime(dateVal, $gmtTimezone);
								$offset = $userTimezone->getOffset($myDateTime);
								$myInterval=DateInterval->createFromDateString((string)$offset . 'seconds');
								$myDateTime->add($myInterval);
								$result = $myDateTime->format('Y-m-d H:i:s a');*/
								//Echo $result;

								//dateVal = moment(dateVal).format('YYYY-MM-DDThh:mm:ssZ')
							    dateVal = moment(dateVal,'YYYY-MM-DDThh:mm:ssZ').tz('America/Los_Angeles').format('YYYY-MM-DD HH:mm');
							    //alert(dateVal);
						    	$('#utc-date').text('Youtube Time: ').append(dateVal);
							});

						    /*$('#utc-date').text('').append(moment().tz('UTC').format('MM-DD-YYYY HH:mm'));*/
						  });
					</script>
					<script type="text/javascript">
						$('#testupload').click(function(event) {
							if($('#uemail').val() == '')
							{
								alert('Email trống !'); return;
							}
							if($('#upass').val() == '')
							{
								alert('Pass trống !'); return;
							}
							$.ajax({
								url: 'v3-login.php',
								type: 'POST',
								data: {code: $('#code').val(), act: 'ajax'},
								success: function(res)
								{
									if(res == 'Login OK')
									{
										$('#checkrs').html('<p class="alert alert-success"> Đã Xác thực thành công! !</p>');
										$('#checkrs').animate({ width: 'show' });
										$('#ischeck').val(1);
										setTimeout(function(){ $('#checkrs').animate({ width: 'hide' }); }, 15000);
									} else {
										$('#checkrs').html('<div class="alert alert-danger">'+res+' !</div>');
										$('#checkrs').animate({ width: 'show' });
										setTimeout(function(){ $('#checkrs').animate({ width: 'hide' }); }, 15000);
									}
								},
							});
							
						});
						var checkForm = function()
						{
							//console.log($('#uploadVideo').is(':checked'));
							if($('#ischeck').val() == 0 && $('#uploadVideo').is(':checked'))
							{
								//alert('Cần test Upload trước khi up');
								//return false;
							}
							if(!$('#downVideo').is(':checked') && !$('#renderVideo').is(':checked') && !$('#uploadVideo').is(':checked'))
							{
								alert('Chưa chọn chức năng');
								return false;
							}
						}

						
					</script>
					<?php 
					if(@$_GET['dat_step'] == 'downloaded')
					{
						if(@$downVideo == 0)
						{
							echo "<script>window.location.href = '".$_SERVER['PHP_SELF']."?dat_step=render';</script>";
							die();
						}
					}
					if(@$_GET['dat_step'] == 'render')
					{
						if(@$renderVideo == 0)
						{
							echo "<script>window.location.href = '".$_SERVER['PHP_SELF']."?dat_step=upload';</script>";
							die();
						}
					}
					if(@$_GET['dat_step'] == 'upload')
					{
						if(@$uploadVideo == 0)
						{
							echo "<script>window.location.href = '".$_SERVER['PHP_SELF']."?dat_step=done';</script>";
							die();
						}
					}
					?>
					<script type="text/javascript">
					/* <![CDATA[ */
						var current_dlink = -1;
						var links = new Array();
						var start_link = 'dat_index.php?audl=doum&ytube_mp4=on&yt_fmt=highest';

						function startauto() {
							current_dlink = -1;
							//document.getElementById('auto').style.display = 'none';
							nextlink();
						}

						function nextlink() {
							if (document.getElementById('status'+current_dlink)) document.getElementById('status'+current_dlink).innerHTML = 'Finished';
							current_dlink++;

							if (current_dlink < links.length) {
								//document.getElementById('status'+current_dlink).innerHTML = 'Started';
								opennewwindow(current_dlink);
							} else {
								location.href = '<?php echo $_SERVER['PHP_SELF']; ?>?dat_step=render';
							}
						}

						function opennewwindow(id) {
							window.frames['idownload'].location = start_link+'&link='+links[id];
						}

						function addLinks() {
							var tbody = document.getElementById('links').getElementsByTagName('tbody')[0];
							var stringLinks = document.getElementById('addlinks').value;
							var regexRN = new RegExp('\r\n', 'g');
							var regexN = new RegExp('\n', 'g');
							var stringLinksN = stringLinks.replace(regexRN, "\n");
							var arrayLinks = stringLinksN.split(regexN);
							for (var i = 0; i < arrayLinks.length; i++) {
								var row = document.createElement('tr');
								var td1 = document.createElement('td');
								td1.appendChild(document.createTextNode(arrayLinks[i]));
								var td2 = document.createElement('td');
								td2.appendChild(document.createTextNode('Waiting'));
								td2.setAttribute('id', 'status'+links.length);
								row.appendChild(td1);
								row.appendChild(td2);
								tbody.appendChild(row);
								links[links.length] = arrayLinks[i];
							}
							document.getElementById('addlinks').value = '';
						}
						<?php 
						//echo "--".@$downVideo."--";die();
						if(@$links && @$_SESSION['dat_step'] != 'downloaded' && $downVideo == 1 && @$_GET['dat_step'] != 'upload') {
							echo '</script><iframe width="90%" height="300" src="" name="idownload" id="idownload" frameborder="0">Frames not supported, update your browser</iframe><script>';
							$getlinks = array_values(array_unique(array_filter(array_map('trim', explode("\r\n", $links)))));
							for ($i = 0; $i < count($getlinks); $i++) echo "\tlinks[$i] = '" . urlencode($getlinks[$i]) . "';\n";
							$_SESSION['dat_step'] = 'downloaded';
							echo 'startauto();';
						}

						?>
						
					/* ]]> */
					</script>
					<?php 
					if(@$_GET['dat_step'] != 'render' && isset($renderVideo) && isset($submit) && @$_GET['dat_step'] != 'upload')
						{
							//echo "<script>window.location.href='".$_SERVER['PHP_SELF']."?dat_step=render';</script>";die();
						}
						?>
					<script type="text/javascript">
								$(function(){
									//$('#downloadif').attr('src', 'dat_down.php');  
									$('#idownload').on('load', function(){
										//$(this).show();
									   nextlink();
									});

								});
								</script>
					<?php 

					if(isset($links))
					{
						$link = explode("\r\n", $links);
						
						$downloadedVideos = $renderedVideos = $processedVideos = array();
						/*if(@$downVideo && @$_SESSION['dat_step'] != 'downloaded') {
							echo "<strong><font color='blue'>DOWNLOAD:</font></strong> <br>";
							$_SESSION['links'] = $links;
							$_SESSION['dat_links'] = $links;
								?>	
								<iframe border='0' src='' id='downloadif' width='90%' height='100'></iframe>
								
								<?php
							//include_once "templates/plugmod/dat_footer.php";
						}
						xflush();
						//session_destroy();
						die();*/
						
						if(@$_GET['dat_step'] == 'render') {
							$isSchedule = false;
							$iCount = 0;
							$_SESSION['publicArray'] = array();
							$arrayTmp = array();

							echo "<strong><font color='blue'>RENDER:</font></strong> <br>";
							xflush();
							if(count($downloadedVideos) == 0 || !@$downvideo)
							{
								$dirList = __DIR__ . '/files/';
								$scanned_directory = array_diff(scandir($dirList), array('..', '.', '.htaccess'));

								foreach ($scanned_directory as $file) {
									$ext = strtolower(pathinfo($dirList.$file, PATHINFO_EXTENSION));
									if(in_array($ext, $allowExt))
									{
										$downloadedVideos[] = $file;
									}
								}
							}

							if(@$_SESSION['publicAt'] != null && @$_SESSION['videoStatus'] != 'public') {
						    	$isSchedule = true;
						    }
							foreach ($downloadedVideos as $video) {
								$fileName = __DIR__ . '/files/' . $video;
								$ext = pathinfo($fileName, PATHINFO_EXTENSION);
								$videoId = basename($fileName, '.'.$ext);

								// set session
								if ($isSchedule) {
									if (@$_SESSION['lastDateTimePub'] !== null && $iCount != 0) {
							    		$datetime = new DateTime(@$_SESSION['lastDateTimePub'], new DateTimeZone('America/Los_Angeles'));
							    	} else {
							    		$datetime = new DateTime(@$_SESSION['publicAt'], new DateTimeZone('America/Los_Angeles'));
							    	}
							    	echo $datetime->format('Y-m-d H:i:s');
							    	if(@$_SESSION['puslishSerial'] != 0) {
							    		$_SESSION['lastDateTimePub'] = $datetime->format('Y-m-d H:i:s');
							    		$tmpArr = array($videoId=>$_SESSION['lastDateTimePub']);
							    		$datetime->add(new DateInterval('PT'. $_SESSION['puslishSerial'] .'H'));
							    		$_SESSION['lastDateTimePub'] = $datetime->format('Y-m-d H:i:s');
							    		$_SESSION['publicArray'] += $tmpArr;
							    	}
								}
								$iCount++;
								if($ext != '') $output = str_replace('.'.$ext, '_render.'.$ext, $fileName);
								else $output = $fileName . '_render.mp4';
								if(file_exists($output)) @unlink($output);
								//echo shell_exec('cat /etc/passwd 2>&1; echo $?');
								if(DIRECTORY_SEPARATOR == '/') {
									$first_param = '/usr/local/bin/ffmpeg -y -i "'.$fileName.'"  -map_metadata 0 -preset superfast ';
									$last_param = '"'.$output.'" 2>&1; echo $?';
								} else {
									$first_param = 'C:\lib\ffmpeg -y -i "'.$fileName.'"  -map_metadata 0 -preset superfast ';
									$last_param = '"'.$output.'" 2>&1';
								}
								
								$param = ' ';
								if($amthanh == 1)
								{
									$param .= '-af "pan=stereo|c0<c0+0*c1,aeval=val(0)|-val(1),volume=1.6" ';
								} 
								elseif($amthanh == 2)
								{
									$param .= '-af "pan=stereo|c0<c0+0*c1|c1<c0+0*c1,aeval=val(0)|-val(1)" ';
								} else {
									$param .= ' -c:a copy ';
								}
								//hinh anh
								if($hinhanh == 1)
								{
									//cat di 1s dau tien
									$param .= ' -ss 00:00:01 -c:v copy ';
								} elseif($hinhanh == 2)
								{
									//crop 1.1%, scale, box blur
									$param .= '-vf "crop=iw/1.1:ih/1.1,scale=854:480,boxblur=1:0" ';
								} 
								elseif($hinhanh == 3)
								{
									$param .= '-af "atempo=1.1" -vf "setpts=PTS/1.1,crop=iw/1.1:ih/1.1,scale=854:480,boxblur=1:0 [mv]; movie='.__DIR__.'/40.png [f1]; [mv][f1]overlay=0:0" ';
								}
								elseif($hinhanh == 4)
								{
									$param .= '-af "atempo=1.2" -vf "setpts=PTS/1.2,crop=iw/1.15:ih/1.15,scale=854:480,boxblur=1:1 [mv]; movie='.__DIR__.'/50.png [f1]; [mv][f1]overlay=0:0" ';
								}
								elseif($hinhanh == 5)
								{
									$param .= '-af "atempo=1.25" -vf "setpts=PTS/1.25,crop=iw/1.2:ih/1.2,scale=854:480,boxblur=1:1 [mv]; movie='.__DIR__.'/60.png [f1]; [mv][f1]overlay=0:0" ';
								}

								// Cat video
								if ($cutVideo != '0') {
									// Get video durations
									if(DIRECTORY_SEPARATOR == '/') {
										$ffprobe_shell = '/usr/local/bin/ffprobe -v error -select_streams v:0 -show_entries stream=duration -of default=noprint_wrappers=1:nokey=1 '.$fileName.' 2>&1 ';
										writeFile('log.txt', @$ffprobe_shell ."\r\n", 'a');
										$ffprobe_out = shell_exec($ffprobe_shell);
										$endDuration = floor($ffprobe_out) - $cutVideo;
									} else {
										$ffprobe_shell = 'C:\lib\ffprobe -v error -select_streams v:0 -show_entries stream=duration -of default=noprint_wrappers=1:nokey=1 '.$fileName.' 2>&1 ';
										writeFile('log.txt', @$ffprobe_shell ."\r\n", 'a');
										exec($ffprobe_shell, $ffprobe_out);
										$endDuration = floor($ffprobe_out[0]) - $cutVideo;
									}
									// Get time
									writeFile('log.txt', @$ffprobe_out ."\r\n", 'a');
									
									$endHH = floor($endDuration / 3600);
									$endMM = floor(($endDuration % 3600) / 60);
									$endSS = floor(($endDuration % 3600) % 60);
									// check format
									if ($endHH < 10) {
										$endHH = '0'.$endHH;
									}
									if ($endMM < 10) {
										$endMM = '0'.$endMM;
									}
									if ($endSS < 10) {
										$endSS = '0'.$endSS;
									}
									$param .= ' -ss 00:00:'.$cutVideo.' -to '.$endHH.':'.$endMM.':'.$endSS.' -c:v copy ';
									$ffprobe_out = array();
								}

								$shell_command = $first_param . ' ' . $param . ' ' . $last_param;
								writeFile('log.txt', @$shell_command ."\r\n", 'a');
								//$shell_command = '/usr/local/bin/ffmpeg -i "'.$fileName.'" -af "pan=stereo|c0<c0+0*c1|c1<c0+0*c1,aeval=val(0)|-val(1)" "'.$output.'" 2>&1; echo $?';
								//$shell_command = 'C:\lib\ffmpeg -i "'.$fileName.'" -af "pan=stereo|c0<c0+0*c1|c1<c0+0*c1,aeval=val(0)|-val(1)" "'.$output.'" 2>&1; echo $?';
								// /root/bin/ffmpeg
								$matches = null;
								if(DIRECTORY_SEPARATOR == '/') {
									 $out = shell_exec($shell_command);
								} else {
									if(preg_match("'\b\movie.*png\b'", $shell_command, $matches)) {
										$strTmp = str_replace('\\', '/', $matches[0]);
										$strTmp = preg_replace("/(:)/", "\\\\\\\\\\:", $strTmp);
										$shell_command = preg_replace("'\b\movie.*png\b'", $strTmp, $shell_command);
									}
									exec($shell_command, $out);
								}
								writeFile('log.txt', @$out ."\r\n", 'a');
								//echo $output;
								if(@stristr($out, 'speed=') || @stristr($out, 'muxing overhead:'))
								{
									$processedVideos[] = $output;
									printf('<span class=\'nsuccess\'>Đã render xong file:</span> <b>%1$s</b><br>', $video);
								} else if(stristr(implode(" ", $out), 'speed=') || stristr(implode(" ", $out), 'muxing overhead:')) {
									$processedVideos[] = $output;
									printf('<span class=\'nsuccess\'>Đã render xong file:</span> <b>%1$s</b><br>', $video);
								} else {
									printf('<span class=\'nfail\'>Lỗi render file:</span> <b>%1$s</b><br>', $video);
									writeFile('log.txt', $shell_command."\r\n", 'a');
								}
								xflush();
								clearstatcache();
								//@unlink($fileName);
								if(stristr($out, 'speed=') || stristr($out, 'muxing overhead:')
									|| stristr(implode(" ", $out), 'speed=') || stristr(implode(" ", $out), 'muxing overhead:') )
								{
									@unlink($fileName);
								}
							}
							$arrayTmp = array();
							echo "<script>window.location.href = '".$_SERVER['PHP_SELF']."?dat_step=upload';</script>";
							die();
						}

						if(@$_GET['dat_step'] == 'upload')
						{
							echo "<iframe src='dat_auul.php' id='dat_auul' frameborder='0' width='100%' height='500' onload='AdjustIframeHeightOnLoad();'></iframe>";
							
						}
					}
					?>
				</div>
			</div>
		</div>
		<div style="margin-bottom: 100px;"></div>
		<script type="text/javascript">
		function AdjustIframeHeightOnLoad() { document.getElementById("dat_auul").style.height = document.getElementById("dat_auul").contentWindow.document.body.scrollHeight + "px"; }
		function AdjustIframeHeight(i) { document.getElementById("dat_auul").style.height = parseInt(i) + "px"; }
		</script>


	</body>
</html>
