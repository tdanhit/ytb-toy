<?php
set_time_limit(0);
function get3Str($str1, $str2, $str3, $str) {
	$s = explode($str1, $str);
	if(count($s)<2) return $s[0];
	$s = explode($str2, $s[1]);
	if(count($s)<2) return $s[0];
	$s = explode($str3, $s[1]);
	return $s[0];

}
function get2Str($str1, $str2, $str) {
	$s = explode($str1, $str);
	if(count($s)<2) return $s[0];
	$s = explode($str2, $s[1]);
	return $s[0];

}
function curl($url,$post="",$usecookie = false,$header=false, $ref=false) {  
	$ch = curl_init();
	if($post) {
		curl_setopt($ch, CURLOPT_POST ,1);
		curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
	}
	curl_setopt($ch, CURLOPT_URL, $url); 
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Linux i686) AppleWebKit/534.35 (KHTML, like Gecko) Ubuntu/10.10 Chromium/13.0.764.0 Chrome/13.0.764.0 Safari/534.35"); 
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	if ($usecookie) { 
		curl_setopt($ch, CURLOPT_COOKIEJAR, str_replace('\\','/',dirname(__FILE__)).'/'.$usecookie);
	curl_setopt($ch, CURLOPT_COOKIEFILE, str_replace('\\','/',dirname(__FILE__)).'/'.$usecookie);    
	} 
    if($header) { 
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded')); 
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
extract($_REQUEST); 
if(isset($query))
{
	$html = curl($query);
	$url = array();
	if(preg_match_all('/href=\"(\/watch.*v=[a-zA-Z0-9]+)\"/', $html, $matches))
	{
		$urls = array_unique($matches[1]);
		foreach ($urls as $u) {
			$page = curl("https://www.youtube.com".$u);
			if(stristr($page, 'name=attribution content='))
			{
				$line = get2Str('name=attribution content=', '/>', $page);
				if(strlen($line) == 22)
				{

					$url[] = $u;
				}
			}
		}
	}
	

}
?><!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Get</title>

		<!-- Bootstrap CSS -->
		<link href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" rel="stylesheet">

		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
			<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>
	<body>
		<h1 class="text-center">Hello World</h1>

		<div class="container">
			<div class="row">
				<div class="col-xs-12">
					<form action="" method="POST" class="form-horizontal" role="form">
						<div class="form-group">
							<label for="inputQuery" class="col-sm-2 control-label">URL:</label>
							<div class="col-sm-10">
								<input type="text" name="query" id="inputQuery" class="form-control" value="<?php echo @$query; ?>" required="required">
							</div>
						</div>
						
							<div class="form-group">
								<div class="col-sm-10 col-sm-offset-2">
									<button type="submit" class="btn btn-primary">Submit</button>
								</div>
							</div>
					</form>
					<?php 
					if(isset($url))
					{
						echo "<textarea rows='10' class='form-control'>";
						foreach ($url as $key => $value) {
							echo "https://www.youtube.com".$value."\r\n";
						}
						echo "</textarea>";
					}
					?>
				</div>
			</div>
		</div>

	</body>
</html>
