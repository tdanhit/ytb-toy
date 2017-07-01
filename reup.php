<?php 
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
$amthanh = 0;
$hinhanh = 1;
//$uploadVideo = 1;
$deleteAfter = 1;
$downVideo = 1;
$renderVideo = 1;
$uploadVideo = 1;
extract($_REQUEST);
if(isset($links))
{
	unset($_SESSION['dat_step']);
	$_SESSION['links'] = $links;
	$_SESSION['amthanh'] = $amthanh;
	$_SESSION['hinhanh'] = $hinhanh;
	$_SESSION['uemail'] = $uemail;
	$_SESSION['upass'] = $upass;
} else {
	if(isset($_SESSION['amthanh']))
	{
		$links = @$_SESSION['links'];
		$amthanh = @$_SESSION['amthanh'];
		$hinhanh = @$_SESSION['hinhanh'];
		$uemail = @$_SESSION['uemail'];
		$upass = @$_SESSION['upass'];
	} else {
		$amthanh = 0;
		$hinhanh = 1;
	}
	
}
?><!DOCTYPE html>
<html lang="">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Youtube Reup Tool</title>

		<!-- Bootstrap CSS -->
		<link href="//netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">

		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
			<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
		<style type="text/css">
		.nsuccess {
			font-weight: bold;
			color: #5cb85c;
		}
		.nfail {
			font-weight: bold;
			color: #d9534f;
		}
		#checkrs {display: none;}
		</style>
		<script type="text/javascript">
		var isUp = true;
		</script>
	</head>
	<body>
		<h1 class="text-center">Youtube Reup Tool</h1>
		
		<div class="container">
			<div class="row">
				<div class="col-xs-12">
					<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" class="form-horizontal" role="form" onsubmit="return checkForm();">
						<div class="form-group">
							<div class="col-sm-12">
								<textarea name="links" id="inputLinks" class="form-control" rows="10" <?php if(@$downVideo): ?>required="required"<?php endif; ?>><?php echo @$links; ?></textarea>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-2">
								<h4>ÂM THANH</h4>
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
							<div class="col-sm-2">
								<h4>HÌNH ẢNH</h4>
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
							<div class="col-sm-2">
								<h4>CÀI ĐẶT</h4>
								<div class="checkbox">
									<label>
										<input type="checkbox" name="deleteAfter" value="1" <?php if(@$deleteAfter==1):?> checked="checked" <?php endif; ?>>
										Xóa file sau Render
									</label>
									<label>
										<input type="checkbox" name="downVideo" id="downVideo" value="1" <?php if(@$downVideo==1):?> checked="checked" <?php endif; ?>>
										Download Video
									</label>
									<label>
										<input type="checkbox" name="renderVideo" id="renderVideo" value="1" <?php if(@$renderVideo==1):?> checked="checked" <?php endif; ?>>
										Render Video
									</label>
									<label>
										<input type="checkbox" id="uploadVideo" name="uploadVideo" value="1" <?php if(@$uploadVideo==1):?> checked="checked" <?php endif; ?>>
										Upload Video
									</label>
									<br>

									<a href="rpl.php" target="_blank">Trình quản lý File</a>
								</div>
							</div>
							<div class="col-sm-4">
								<h4>UPLOAD</h4>
								<input type="text" name="uemail" id="uemail" class="form-control" value="<?php echo @$uemail; ?>" <?php if(@$uploadVideo==1):?>required="required"<?php endif; ?> placeholder="Email Kênh">
								<input type="text" name="upass" id="upass" class="form-control" value="<?php echo @$upass; ?>" <?php if(@$uploadVideo==1):?>required="required"<?php endif; ?> placeholder="Pass Kênh">
								<button type="button" class="btn btn-info" id="testupload">Test Upload</button>
								<div id="checkrs"></div>
							</div>
							<input type="hidden" id="ischeck" name="ischeck" value="<?php echo (isset($ischeck))?$ischeck:'0'; ?>">
							<div class="col-sm-2">
								<h4></h4>
								<button type="submit" class="btn btn-success btn-block btn-large" style="font-size: 20px;">REUP</button>
							</div>
						</div>
						
					</form>
					<script src="classes/js.js"></script>
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
						
					</script>
					<script type="text/javascript" src="//code.jquery.com/jquery.min.js"></script>
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
								url: 'checklogin.php',
								type: 'POST',
								data: {email: $('#uemail').val(), pass: $('#upass').val(), act: 'ajax'},
								success: function(res)
								{
									if(res == 'Login OK')
									{
										$('#checkrs').html('<div class="alert alert-success">Login OK !</div>');
										$('#checkrs').slideDown();
										$('#ischeck').val(1);
										setTimeout(function(){ $('#checkrs').slideUp(); }, 1500);
									} else {
										$('#checkrs').html('<div class="alert alert-danger">'+res+' !</div>');
										$('#checkrs').slideDown();
										setTimeout(function(){ $('#checkrs').slideUp(); }, 1500);
									}
								},
							});
							
						});
						var checkForm = function()
						{
							//console.log($('#uploadVideo').is(':checked'));
							if($('#ischeck').val() == 0 && $('#uploadVideo').is(':checked'))
							{
								alert('Cần test Upload trước khi up');
								return false;
							}
							if(!$('#downVideo').is(':checked') && !$('#renderVideo').is(':checked') && !$('#uploadVideo').is(':checked'))
							{
								alert('Chưa chọn chức năng');
								return false;
							}
						}

						
					</script>
					
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
						if(@$links && @$_SESSION['dat_step'] != 'downloaded'&& isset($downVideo)) {
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
					if(@$_GET['dat_step'] != 'render' && isset($renderVideo))
						{
							echo "<script>window.location.href='".$_SERVER['PHP_SELF']."?dat_step=render';</script>";die();
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

							foreach ($downloadedVideos as $video) {
								$fileName = __DIR__ . '/files/' . $video;
								$ext = pathinfo($fileName, PATHINFO_EXTENSION);
								if($ext != '') $output = str_replace('.'.$ext, '_render.'.$ext, $fileName);
								else $output = $fileName . '_render.mp4';
								if(file_exists($output)) @unlink($output);
								//echo shell_exec('cat /etc/passwd 2>&1; echo $?');
								$first_param = '/usr/local/bin/ffmpeg -y -i "'.$fileName.'"  -map_metadata 0 -preset superfast -acodec copy ';
								$last_param = '"'.$output.'" 2>&1; echo $?';
								$param = ' ';
								if($amthanh == 1)
								{
									$param .= '-af "pan=stereo|c0<c0+0*c1|c1<c0+0*c1,aeval=val(0)|-val(1)" ';
								} 
								elseif($amthanh == 2)
								{
									$param .= '-af "pan=stereo|c0<c0+0*c1,aeval=val(0)|-val(1),volume=1.6" ';
								}
								//hinh anh
								if($hinhanh == 1)
								{
									//cat di 1s dau tien
									$param .= ' -ss 00:00:01 ';
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
								$shell_command = $first_param . ' ' . $param . ' ' . $last_param;
								//$shell_command = '/usr/local/bin/ffmpeg -i "'.$fileName.'" -af "pan=stereo|c0<c0+0*c1|c1<c0+0*c1,aeval=val(0)|-val(1)" "'.$output.'" 2>&1; echo $?';
								//$shell_command = 'C:\lib\ffmpeg -i "'.$fileName.'" -af "pan=stereo|c0<c0+0*c1|c1<c0+0*c1,aeval=val(0)|-val(1)" "'.$output.'" 2>&1; echo $?';
								// /root/bin/ffmpeg
								$out = shell_exec($shell_command);
								if(stristr($out, 'speed='))
								{
									$processedVideos[] = $output;
									printf('<span class=\'nsuccess\'>Đã render xong file:</span> <b>%1$s</b><br>', $video);
								} else {
									printf('<span class=\'nsuccess\'>Lỗi render file:</span> <b>%1$s</b><br>', $video);
									writeFile('log.txt', $shell_command."\r\n", 'a');
								}
								xflush();
								clearstatcache();
								if(@$deleteAfter)
								{
									@unlink($fileName);
								}

							}
							echo "<script>window.location.href = '".$_SERVER['PHP_SELF']."?dat_step=upload';</script>";
							die();
						}

						if(@$_GET['dat_step'] == 'upload')
						{
							/*if(count($processedVideos) == 0 || !@$renderVideo)
							{
								$dirList = __DIR__ . '/files/';
								$scanned_directory = array_diff(scandir($dirList), array('..', '.', '.htaccess'));
								foreach ($scanned_directory as $file) {
									$ext = strtolower(pathinfo($dirList.$file, PATHINFO_EXTENSION));
									if(in_array($ext, $allowExt))
									{
										if(inStr($file, '_render.'))
										{
											$processedVideos[] = $dirList . $file;
										}
									}
								} 
							}

							foreach ($processedVideos as $key => $value) {
								
							}*/

							echo "<iframe src='dat_auul.php' id='dat_auul' frameborder='0' width='100%' height='500' onload='AdjustIframeHeightOnLoad();'></iframe";
							?>
								$('#dat_auul').on('load', function(){
								     location.href='<?php echo $_SERVER['PHP_SELF']; ?>?dat_step=done';
								    });
							<?php
							if(@$_GET['dat_step'] = 'done')
							{
								//echo "<strong><font color='blue'>DONE ALL</font></strong> <br>";
							}
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
