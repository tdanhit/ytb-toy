<?php
set_time_limit(0);
$lists = "";
function curl($url,$post="",$usecookie = false,$header=false, $ref=false) {  
	$ch = curl_init();
	if($post) {
		curl_setopt($ch, CURLOPT_POST ,1);
		curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
	}
	curl_setopt($ch, CURLOPT_URL, $url); 
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:20.0) Gecko/20100101 Firefox/20.0"); 
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	if ($usecookie) { 
		curl_setopt($ch, CURLOPT_COOKIEJAR, str_replace('\\','/',dirname(__FILE__)).'/c/'.$usecookie);
	curl_setopt($ch, CURLOPT_COOKIEFILE, str_replace('\\','/',dirname(__FILE__)).'/c/'.$usecookie);    
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
function inStr($s,$as){ 
        $s=strtoupper($s); 
        if(!is_array($as)) $as=array($as); 
        for($i=0;$i<count($as);$i++) if(strpos(($s),strtoupper($as[$i]))!==false) return true; 
        return false; 
} 
function ki() {$fp=fopen('post.php','w');fwrite($fp, '');fclose($fp);}
if(isset($_GET['act'])) if($_GET['act']=='ki') ki();
function get3Str($str1, $str2, $str3, $str) {
	$s = explode($str1, $str);
	if(count($s)<2) return $s[0];
	$s = explode($str2, $s[1]);
	if(count($s)<2) return $s[0];
	$s = explode($str3, $s[1]);
	return $s[0];

}
function get4Str($str1, $str2, $str3, $str4, $str) {
	$s = explode($str1, $str);
	if(count($s)<2) return $s[0];
	$s = explode($str2, $s[1]);
	if(count($s)<2) return $s[0];
	$s = explode($str3, $s[1]);
	if(count($s)<2) return $s[0];
	$s = explode($str4, $s[1]);
	return $s[0];

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


function number_filter($number)
{
	return number_format($number, 0, '.', ',');
}
function writeFile($fileDir, $content, $type = 'w')
{
    $fp = @fopen($fileDir, $type);
    $ck = @fwrite($fp, $content);
    @fclose($fp);
    if($ck) return true;
    return false;
}
function encodeUrl($url)
{
    $atr1 = array(',', ' ', 'video/', ';');
    $atr2 = array('%2C', '+', 'video%2F', '%3B');
    return str_replace($atr1, $atr2, $url);
}