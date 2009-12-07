<?php
//ini_set("display_errors", "1");
define( 'IBIET', 1 );
session_start();
@require "config.php";
@require "db.php";
    //-----------------------------------------------------
    // Initialize
    //-----------------------------------------------------
    function initialize()
    {
        global  $DB, $INFO;
if(session_is_registered("dlpremium")) {
	$dlpremium = "true";
}
        //$this->vars = &$INFO;
        $act = $_GET['action'];
        if($_POST['url'] != "")
        {
            makeurl($_POST['url']);
        }
        if($act == "download") {
        		download();
        }
        if($act == "" && !$act == "makeurl" && !$act == "download") {
        welcome();
        }
    }

    //-----------------------------------------------------
    // Welcome
    //----------------------------------------------------- 
    function welcome()
    {
        global  $skin, $DB;

        $data = array();
        //$check_serv = serverload();
        if($check_serv['overloaded'])
        {
            $stage = "disabled";
            $txtstring = $check_serv['msg'];
        }
        else
        {
            if(file_exists("accountcheck.php"))
            {
                $t = $DB->query("SELECT * FROM accounts_rs WHERE bw > 0");
                $r = $DB->fetch_array($t);
				$rapidshare = $r['rapidshare'];
				$bw = $r['bw'];
                list($rlogin, $rpass) = split(":", $rapidshare); 
                if($rapidshare)
                {
                    $txtstring = "Enter rapidshare.com URL here";
                }
                else
                {
                    $stage = "disabled";
                    $txtstring = "Sorry, there is no more premium accounts available.";
                }
            }
            else
            {
                $stage = "disabled";
                $txtstring = "Sorry, there is no more premium accounts available. Please come visit again soon.";
            }
        }
        if($dl_limit > 0)
        {
            $l_limit = time() - 60*60*24;
            $ip = $_SERVER['REMOTE_ADDR'];
            $l = $DB->query("SELECT * FROM logs WHERE fdate > '$l_limit' AND ip = '$ip'");
            $link_downloaded = $DB->get_num_rows($l);
            $link_downloaded = ($link_downloaded) ? $link_downloaded : 0;
            $stat = "<span style='color:#688000; background-color:#BBDB54; font-size : 10pt; text-decoration: none; font-family: Trebuchet MS;'>So far you have downloaded $link_downloaded/{$dl_limit} links limit.<br>Current Rapidshare Bandwidth Available: $bw</span>";
        }

        $data['html'] .= "<script>\n";
        $data['html'] .= "function CheckForm() {\n";
        $data['html'] .= "  url = document.getElementById('url');\n";
        $data['html'] .= "  if (url) {\n";
        $data['html'] .= "      if (url.value.substr(0,21) != \"http://rapidshare.com\") {\n";
        $data['html'] .= "          alert(\"Please enter valid rapidshare.com URL\");\n";
        $data['html'] .= "          return false;\n";
        $data['html'] .= "      }\n";
        $data['html'] .= "  }\n";
        $data['html'] .= "}\n";
        $data['html'] .= "</script>\n";
        $data['html'] .= "<form action=\"\" method=\"post\" onsubmit=\"return CheckForm()\">\n";
        $data['html'] .= "<input type=\"text\" id=\"url\" style=\"text-align:center\" name=\"url\" value=\"$txtstring\" size=\"60\" onfocus=\"if(this.value=='Enter rapidshare.com URL here'){this.value=''}\" $stage>\n";
        $data['html'] .= "<br><input type=\"submit\" value=\"Download\" $stage></form>";
        if($dlpremium == "true") {
        		print "<span style='color:orange; background-color:Yellow; font-size : 10pt; text-decoration: none; font-family: Trebuchet MS;'>Premium Downloads Enabled (BETA)</span>";
        }
        $data['html'] .= "<span style='color:#688000; background-color:#BBDB54; font-size : 10pt; text-decoration: none; font-family: Trebuchet MS;'>So far you have downloaded $link_downloaded/{$dl_limit} links limit.<br>Current Rapidshare Bandwidth Available: $bw</span>";

        $data['title'] = "RsPremiX ~ Rapidshare Premium Link Generator ~";
        _print($data);
        
    }
    // GetFileInfo and Download is now here.
function getFileInfo($link)
	{
		global  $DB;
		  $t = $DB->query("SELECT * FROM accounts_rs WHERE bw > 0 LIMIT 1");
                $r = $DB->fetch_array($t);
				$rapidshare = $r['rapidshare'];
                list($rlogin, $rpass) = split(":", $rapidshare); 

        $url = parse_url($link);
        require_once("curl.php");
        $cook43 = get_cookie($rlogin, $rpass);
        $vars = "dl.start=PREMIUM&uri={$url['path']}&directstart=1";
        $head = "Host: {$url['host']}\r\n";
        $head .= "User-Agent: Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)\r\n";
        $head .= "Cookie: $cook43\r\n";
        $head .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $head .= "Content-Length: ".strlen($vars)."\r\n";
        $head .= "Connection: close\r\n\r\n";
        $fp = @fsockopen($url['host'], 80, $errno, $errstr);
        if($errstr == "php_network_getaddresses: getaddrinfo failed: name or service not known") {
        		echo "There is a problem with the Rapidshare URL. Please go check it out.";
        		exit;
        }
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
function getserver($link)
	{

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

    //-----------------------------------------------------
    // Generate Link Screen
    //-----------------------------------------------------
    function makeurl($lin)
    {
        global  $DB;

        $data = array();
        $link = addslashes(trim($lin)) ;
        $url = @parse_url($link);
        $ip = $_SERVER['REMOTE_ADDR'];
        $user = $_SESSION['user'];
        $pass = $_SESSION['pass'];
        $retrieve = $DB->query("SELECT * FROM members WHERE username='$user' and password='$pass'");
        $aw = $DB->fetch_array($retrieve);
        $tokens = $aw['tokens'];
        $tokens2 = $tokens-1;
        if($tokens == "0") {
        	print "<span style='color:yellow; background-color:orange; font-size : 10pt; text-decoration: none; font-family: Trebuchet MS;'><b>Sorry, there is no more Premium Download Tokens. You won't be able to download any more files until you buy more tokens.</span>";
        	exit();
        }
        $DB->query("UPDATE `members` SET  `tokens` =  '$tokens2' WHERE username='$user' and password='$pass'");
        if($url['host'] != "rapidshare.com" || !preg_match("#^/files/#", $url['path']))
        {
            $data['html'] = "<span style='color:#688000; background-color:#BBDB54; font-size : 10pt; text-decoration: none; font-family: Trebuchet MS;'><b>Please enter a valid rapidshare.com link.</b></span>";
            $data['title'] = "Error";
            _print($data);
            return;
        }
        $refererr = @parse_url($_SERVER['HTTP_REFERER']);
        if($refererr['host'] != $_SERVER['HTTP_HOST'])
        {
            $data['html'] = "<span style='color:#688000; background-color:#BBDB54; font-size : 10pt; text-decoration: none; font-family: Trebuchet MS;'><b>No leeching, visit http://{$_SERVER['HTTP_HOST']} for more info</b></span>";
            $data['title'] = "Error";
            _print($data);
            return;
        }
        if($dl_limit > 0)
        {
            $l_limit = time() - 60*60*24;
            $l = $DB->query("SELECT * FROM logs WHERE fdate > '$l_limit' AND ip = '$ip'");
            $link_downloaded = $DB->get_num_rows($l);
            $link_downloaded = ($link_downloaded) ? $link_downloaded : 0;
            if($link_downloaded >= intval($dl_limit))
            {
                $data['html'] .= "You have downloaded $link_downloaded/{$dl_limit} links already. Come back in 24hrs for reset.";

                $data['title'] = "Error";
                _print($data);
            }
        }
        $t = $DB->query("SELECT * FROM accounts_rs WHERE bw > 0 LIMIT 1");
        $r = $DB->fetch_array($t);
        $rapidshare = $r['rapidshare'];
        if($rapidshare == "") {
        		$data['html'] = "Sorry, there is no premium accounts available. Please come visit again soon.";
            $data['title'] = "Error";
            _print($data);
            return;
        }
         if(session_is_registered("dlpremium")) {
        	$dlpremium = "true";
        }
        $full_link = getserver($link);
        $try = getFileInfo($full_link, $r);
        $filename = $try['filename'];
        $fsize = $try['fsize'];
        /*
$htm2 = strpos($filename, ".html");
if($htm2 === true) {
		$filename = str_replace(".html", "", $filename);
		$full_link = str_replace(".html", "", $full_link);
}
*/
        $now = time();
        $fid = rand(1000,1000000);
        $DB->query("INSERT INTO logs SET fid='$fid',filename='{$filename}',ip='$ip',fdate='$now',furl='$full_link', filesize='$fsize'");
        $dlurl = "index.php?action=download&id=$fid";
        if(!$dlpremium == "true") {
         print "Your download can begin <a href='$dlurl'>here</a>";
         exit();
        } else {
        	header("Location: $dlurl");
        	
        }
    }

    //-----------------------------------------------------
    // Download Screen
    //-----------------------------------------------------
    function download()
    {
		global  $DB;

        $id = addslashes(trim($_GET['id'])) ;
            $t = $DB->query("SELECT * FROM accounts_rs WHERE bw > 0 LIMIT 1");
       $r = $DB->fetch_array($t);
	     $rapidshare = $r['rapidshare'];
       list($rlogin, $rpass) = split(":", $rapidshare); 
        $limit = time() - 60*60*1; //Only download link that was generated in the last hour.
        $ip = $_SERVER['REMOTE_ADDR'];
        $q = $DB->query("SELECT * FROM logs WHERE fid=$id");
        $row = $DB->fetch_array($q);
        $qcheck = $row['valid'];
        $url = @parse_url($row['furl']);
        $fnfile = $row['filename'];
        $row['filename'] = str_replace(".html", "", $fnfile);
        if($row['furl'] == "")
        {
            @header("HTTP/1.0 404 Not Found");
            echo "<span style='color:#688000; background-color:#BBDB54; font-size : 10pt; text-decoration: none; font-family: Trebuchet MS;'><b>Invalid or Broken Link.</b></span>";
            exit;
        }
                 if(session_is_registered("dlpremium")) {
        	$dlpremium = "true";
        }
        if($qcheck > 0 && !$dlpremium == "true") {
        		 echo "<span style='color:#688000; background-color:#BBDB54; font-size : 10pt; text-decoration: none; font-family: Trebuchet MS;'><b>Invalid or Broken Link.</b></span>";
        		 exit;
        }
         $DB->query("UPDATE `logs` SET `valid` = '1' WHERE `fid` = $id;");
         
        header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename='.$row['filename']);
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header("Accept-Ranges: bytes");
header('Pragma: public');
        if(isset($_SERVER['HTTP_RANGE']))
        {
            list($a, $range)=explode("=",$_SERVER['HTTP_RANGE']);
            $range = str_replace("-", "", $range);
            $new_length = $row['filesize'] - $range;
            @header("HTTP/1.1 206 Partial Content");
            @header("Content-Length: $new_length");
        }
        else
        {
            @header("Content-Length: ".$row['filesize']);
        }
        require_once("curl.php");
        $cook43 = get_cookie($User, $Pass);
        $vars = "dl.start=PREMIUM&uri={$url['path']}&directstart=1";
        $head = "Host: {$url['host']}\r\n";
        $head .= "User-Agent: Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)\r\n";
        $head .= "Cookie: $cook43 \r\n";
        $head .= "Content-Type: application/x-www-form-urlencoded\r\n";
        if($range != "") $head .= "Range: bytes={$range}-\r\n";
        $head .= "Content-Length: ".strlen($vars)."\r\n";
        $head .= "Connection: close\r\n\r\n";
        $fp = @fsockopen($url['host'], 80, $errno, $errstr);
        if (!$fp)
        {
            echo "<span style='color:#688000; background-color:#BBDB54; font-size : 10pt; text-decoration: none; font-family: Trebuchet MS;'>The script says <b>$errstr</b>, please try again later.</span>";
            exit;
        }
        $max_speed = 4;
        $max_timeout = 4;
        if($dlpremium == "true") {
        		$max_speed = 2024;
        		$max_timeout = 300;
        }
        @stream_set_timeout($fp, $max_timeout);
        fputs($fp, "POST {$url['path']}  HTTP/1.1\r\n");
        fputs($fp, $head.$vars);
        fflush($fp);
        $buff = $max_speed;
        while (!feof($fp))
        {
            $data = fgets($fp, $buff);
            if($headerdone)
            {
                print $data;;
            }
            if(!$headerdone)
            {
                $tmp .= $data;
                $d = explode("\r\n\r\n", $tmp);
                if($d[1])
                {
                    print $d[1];
                    $headerdone = true;
                    $buff = $max_speed;
                }
            }
            flush();
            ob_flush();
        }
        @fclose($fp);
        exit;
    }

    //-----------------------------------------------------
    // Check Server Load
    //-----------------------------------------------------
    function serverload()
    {
        global  $std, $DB;

        if ( @file_exists('/proc/loadavg') )
        {
            if ( $fh = @fopen( '/proc/loadavg', 'r' ) )
            {
                $data = @fread( $fh, 6 );
                @fclose( $fh );
                $load_avg = explode( " ", $data );
                $server_load = trim($load_avg[0]);
                if ($server_load > $INFO['serverload'])
                {
                    return array('overloaded' => 1, 'loaded' => $server_load, 'msg' => "Sorry, the server is overloaded, please try again in a moment</b></span>");
                }
            }
        }
        else
        {
            if ( $serverstats = @exec("uptime") )
            {
                preg_match( "/(?:averages)?\: ([0-9\.]+),[\s]+([0-9\.]+),[\s]+([0-9\.]+)/", $serverstats, $load );
                $server_load = $load[1];
                if ($server_load > $INFO['serverload'])
                {
                    return array('overloaded' => 1, 'loaded' => $server_load, 'msg' => $server_load);
                }
            }
        }
    }
     function _print($data)
    {
        global  $DB;
           $user = $_SESSION['user'];
        $pass = $_SESSION['pass'];
        $retrieve = $DB->query("SELECT * FROM members WHERE username='$user' and password='$pass'");
        $aw = $DB->fetch_array($retrieve);
        $tokens = $aw['tokens'];

        $t = "<html>\n";
        $t .= "<head>\n";
        $t .= "<title>{$data['title']}</title>\n";
        $t .= "<meta http-equiv=\"content-type\" content=\"text/html; charset=iso-8859-1\" />\n";
        $t .= "</head>\n\n";
        $t .= "<body>\n";
        $t .= "<table width='100%' height='100%'><tr><td align='center' valign='top'>\n";
                    if(session_is_registered("dlpremium")) {
	$dlpremium = "true";
}
if($dlpremium == "true" && !$tokens == "0") {
        		$t .= "<span style='color:Yellow; background-color:orange; font-size : 10pt; text-decoration: none; font-family: Trebuchet MS;'>Premium Download Enabled (BETA)<br> Download Tokens: $tokens</span>";
        } 
        if($dlpremium == "true" && $tokens == "0") {
        		$t .= "<span style='color:Orange; background-color:red; font-size : 10pt; text-decoration: none; font-family: Trebuchet MS;'>Premium Download Disabled (BETA)<br> You have no more tokens.</span>";
        }

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

    initialize();
?>