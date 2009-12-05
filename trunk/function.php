<?php

if ( ! defined( 'IBIET' ) )
{
	print "You cannot access this file directly.";
	exit();
}

class FUNC {

	function getserver($link)
	{
		global $ibiet;

        $url = @parse_url($link);
        $curl = curl_init();
curl_setopt($curl, CURLOPT_HEADER, 1);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($curl, CURLOPT_URL, $link);
$fp = curl_exec($curl);
$start = "<form action=";
$end = '" method="post">';
$startposition = strpos($fp,$start);
if($startposition > 0) {
$endposition = strpos($fp,$end, $startposition);
}
$length = $endposition-$startposition;
$result = substr($fp,$startposition,$length);
$result = substr($result, 14);
return $result;
	}

	function getFileInfo($link)
	{
		global $ibiet, $DB;
		  $t = $DB->query("SELECT * FROM accounts_rs WHERE bw > 0 LIMIT 1");
                $r = $DB->fetch_array($t);
				$rapidshare = $r['rapidshare'];
                list($rlogin, $rpass) = split(":", $rapidshare); 

        $url = parse_url($link);
        require_once("curl.php");
        $cook43 = get_cookie($User, $Pass);
        $vars = "dl.start=PREMIUM&uri={$url['path']}&directstart=1";
        $head = "Host: {$url['host']}\r\n";
        $head .= "User-Agent: Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)\r\n";
        $head .= "Cookie: $cook43\r\n";
        $head .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $head .= "Content-Length: ".strlen($vars)."\r\n";
        $head .= "Connection: close\r\n\r\n";
        $fp = @fsockopen($url['host'], 80, $errno, $errstr);
        if (!$fp)
        {
            echo "The script says <b>$errstr</b>, please try again later.";
            exit;
        }
        fputs($fp, "POST {$url['path']}  HTTP/1.1\r\n");
        fputs($fp, $head.$vars);
       
        $buff = 64;
        while (!feof($fp))
        {
            $tmp .= fgets($fp, $buff);
            $d = explode("\r\n\r\n", $tmp);
            if($d[1])
            {
                preg_match("#filename=(.+?)\n#", $tmp, $fname);
                preg_match("#Content-Length: (.+?)\n#", $tmp, $fsize);
                $h['filename'] = $fname[1] != "" ? $fname[1] : basename($url['path']);
                $h['fsize'] = $fsize[1];
                break;
            }
        }
        @fclose($fp);
        return $h;
	}

    //-----------------------------------------------------
    // Print output
    //-----------------------------------------------------
    function _print($data)
    {
        global $ibiet, $DB;

        $t = "<html>\n";
        $t .= "<head>\n";
        $t .= "<title>{$data['title']}</title>\n";
        $t .= "<meta http-equiv=\"content-type\" content=\"text/html; charset=iso-8859-1\" />\n";
        $t .= "</head>\n\n";
        $t .= "<body>\n";
        $t .= "<table width='100%' height='100%'><tr><td align='center' valign='top'>\n";
        $t .= $data['html'];
        $t .= "\n</td></tr></table>\n";
        $t .= "</body>\n";
        $t .= "</html>";
        $DB->close();
		@header("Cache-Control: no-cache, must-revalidate, max-age=0");
		@header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		@header("Pragma: no-cache");
        print $t;
        exit;
    }

} //End function Class
$std = new FUNC;
?>