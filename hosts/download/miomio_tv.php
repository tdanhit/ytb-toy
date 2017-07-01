<?php
set_time_limit(0);
if (!defined('RAPIDLEECH')) {
    require_once("index.html");
    exit;
}

class miomio_tv extends DownloadClass {
    
    public function Download($link) {
       $page = $this->dat_curl($link, '', array());
       $vId = $this->get3Str('flashvars="', 'vid=', '&', $page);
       $url = 'http://www.miomio.tv/mioplayer/mioplayerconfigfiles/sina.php?vid='.$vId;
       $head = array(
            'Referer: http://www.miomio.tv/mioplayer/mioplayer-v3.0.swf',
            'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:30.0) Gecko/20100101 Firefox/30.0'
        );
       $page = $this->dat_curl($url, '', $head);
       $xmlparser = xml_parser_create();
       xml_parse_into_struct($xmlparser,$page,$values);
       $dlink = '';
       $FileName = explode('watch/', $link);
       $FileName = explode('/', $FileName[1]);
       $FileName = "miomio_" . $FileName[0];   
       if(count($values))
       {
            foreach ($values as $val) {
                if(@$val['tag'] == 'URL')
                {
                    $dlink = $val['value'];
                    $tmp = explode('.', $dlink);
                    $FileName .= '.'.$tmp[count($tmp)-1];
                    break;
                }
            }
       }

        $this->RedirectDownload($dlink, $FileName, $cookie, 0, $FileName, $FileName);
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
    public function dat_curl($url,$post="", $header) {  
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
        if($header) { 
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header); 
        }
        //curl_setopt($ch, CURLOPT_HEADER,1); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); 
        $result=curl_exec ($ch); 
        curl_close ($ch); 
        return $result; 
    }
}

/*
 * zshare.net free download plugin by Ruud v.Tony 22-09-2011
 */
?>