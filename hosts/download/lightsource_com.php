<?php
set_time_limit(0);
if (!defined('RAPIDLEECH')) {
    require_once("index.html");
    exit;
}

class lightsource_com extends DownloadClass {
    
    public function Download($link) {
    	$FileName = explode('ministry/', $link);
        $FileName = $FileName[count($FileName)-1];
        $FileName = 'lights_'.str_replace('/', '', $FileName);
        $page = @file_get_contents($link);
        $viewLink = $this->get3Str('class="playVideo', 'href="', '"', $page);
        $page = @file_get_contents($viewLink);
        $dlink = $this->get3Str('configjw("playerContainer"', '"', '"', $page);
        
    	//$page = @file_get_contents($dlink);
        $this->RedirectDownload($dlink, $FileName, 0, 0, $FileName, $FileName);
    }
    public function get2Str($str1, $str2, $str) {
        $s = explode($str1, $str);
        if(count($s)<2) return $s[0];
        $s = explode($str2, $s[1]);
        return $s[0];
    }
    public function get3Str($str1, $str2, $str3, $str) {
		$s = explode($str1, $str);
		if(count($s)<2) return $s[0];
		$s = explode($str2, $s[1]);
		if(count($s)<2) return $s[0];
		$s = explode($str3, $s[1]);
		return $s[0];
	}
}

/*
 * zshare.net free download plugin by Ruud v.Tony 22-09-2011
 */
?>