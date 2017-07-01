<?php
set_time_limit(0);
if (!defined('RAPIDLEECH')) {
    require_once("index.html");
    exit;
}

class nicovideo_jp extends DownloadClass {
    
    public function Download($link) {
       $cookies = array(
            'area' =>    'US',
            'lang' =>    'en-us',
            'nicohistory' =>  'sm27874418%3A1451233488%3A1451233488%3A029f9850bd75d047%3A1%2Csm27883367%3A1451233222%3A1451233244%3A456ffad1989781bd%3A6',
            'nicosid' =>  '1452421127.812791006',
            'user_session' =>     'user_session_53902481_dfba02cf2b8f6f02a6b3da554f79d695f51302fd3e56f5765452d76c3e8850fc',
            'user_session_secure' => 'NTM5MDI0ODE6QS5pYzc0eFNFUjN4UU1yY0dlbHBuaC5EYUowTC1yYmxBWk44YnZzdWFNMw',
            'nicoWatchTagPin' =>  0,
        );
       $cookie = array();
       foreach( $cookies as $key => $value ) {
          $cookie[] = "{$key}={$value}";
        };

        $cookie = implode('; ', $cookie);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $link); 
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:20.0) Gecko/20100101 Firefox/20.0");  
        curl_setopt($ch, CURLOPT_COOKIE, $cookie);    
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); 
        $result=curl_exec ($ch); 
        curl_close ($ch); 

        $dlink = '';
        $FileName = '';
        if(stristr($result, '%26url%3Dhttp%253A%252F%252F'))
        {
            $dlink = $this->get2Str('%26url%3Dhttp%253A%252F%252F', 'http%253A', $result);
            $dlink = str_replace(array('%252F', '%253F', '%253D', '%26', '%3D'), array('/', '?', '=', '&', '='), $dlink);
            $dlink = 'http://'.$dlink;
            $FileName = explode('watch/', $link);
            $FileName = $FileName[1];

        } else {
            echo "File Not Found ";
            die();
        }

        $this->RedirectDownload($dlink, $FileName, $cookie, 0, $FileName, 'ss'.$FileName);
    }
    public function get2Str($str1, $str2, $str) {
        $s = explode($str1, $str);
        if(count($s)<2) return $s[0];
        $s = explode($str2, $s[1]);
        return $s[0];
    }
}

/*
 * zshare.net free download plugin by Ruud v.Tony 22-09-2011
 */
?>