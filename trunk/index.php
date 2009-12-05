<?php
error_reporting(E_ERROR | E_PARSE);
define( 'IBIET', 1 );
@require "config.php";
@require "function.php";
//@require "accountcheck.php"; Doesn't need it urgently
@require "db.php";

class Rapids {

    //-----------------------------------------------------
    // initialize
    //-----------------------------------------------------
    function initialize()
    {
        global $ibiet, $std, $DB, $INFO;

        $this->vars = &$INFO;
        $act = $_GET['action'];
        if($_POST['url'] != "")
        {
            $act = "makeurl";
        }
        $action = array("makeurl", "download");
        if(in_array($act, $action))
        {
            $this->$act();
            return;
        }
        else
        {
            $this->welcome();
        }
    }

    //-----------------------------------------------------
    // Welcome Screen
    //----------------------------------------------------- 
    function welcome()
    {
        global $ibiet, $std, $skin, $DB;

        $data = array();
        $check_serv = $this->serverload();
        if($check_serv['overloaded'])
        {
            $stage = "disabled";
            $txtstring = $check_serv['msg'];
        }
        else
        {
            if(file_exists("accountcheck.php"))
            {
                $t = $DB->query("SELECT * FROM accounts_rs WHERE bw > 0 LIMIT 1");
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
        if($ibiet->vars['dl_limit_perday'] > 0)
        {
            $l_limit = time() - 60*60*24;
            $ip = $_SERVER['REMOTE_ADDR'];
            $l = $DB->query("SELECT * FROM logs WHERE fdate > '$l_limit' AND ip = '$ip'");
            $link_downloaded = $DB->get_num_rows($l);
            $link_downloaded = ($link_downloaded) ? $link_downloaded : 0;
            $stat = "<span style='color:#688000; background-color:#BBDB54; font-size : 10pt; text-decoration: none; font-family: Trebuchet MS;'>So far you have downloaded $link_downloaded/{$ibiet->vars['dl_limit_perday']} links limit.<br>Current Rapidshare Bandwidth Available: $bw</span>";
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
        $data['html'] .= $stat;

        $data['title'] = "MegzLeech ~ Rapidshare Premium Link Generator ~";
        $std->_print($data);
    }

    //-----------------------------------------------------
    // Generate Link Screen
    //-----------------------------------------------------
    function makeurl()
    {
        global $ibiet, $std, $DB;

        $data = array();
        $link = addslashes(trim($_POST['url'])) ;
        $url = @parse_url($link);
        $ip = $_SERVER['REMOTE_ADDR'];
        if($url['host'] != "rapidshare.com" || !preg_match("#^/files/#", $url['path']))
        {
            $data['html'] = "<span style='color:#688000; background-color:#BBDB54; font-size : 10pt; text-decoration: none; font-family: Trebuchet MS;'><b>Please enter a valid rapidshare.com link.</b></span>";
            $data['title'] = "Error";
            $std->_print($data);
            return;
        }
        $refererr = @parse_url($_SERVER['HTTP_REFERER']);
        if($refererr['host'] != $_SERVER['HTTP_HOST'])
        {
            $data['html'] = "<span style='color:#688000; background-color:#BBDB54; font-size : 10pt; text-decoration: none; font-family: Trebuchet MS;'><b>No leeching, visit http://{$_SERVER['HTTP_HOST']} for more info</b></span>";
            $data['title'] = "Error";
            $std->_print($data);
            return;
        }
        if(!$ibiet->vars['premium_act'])
        {
            $data['html'] = "Sorry, there is no premium accounts available. Please come visit again soon.";
            $data['title'] = "Error";
            $std->_print($data);
            return;
        }
        if($ibiet->vars['dl_limit_perday'] > 0)
        {
            $l_limit = time() - 60*60*24;
            $l = $DB->query("SELECT * FROM logs WHERE fdate > '$l_limit' AND ip = '$ip'");
            $link_downloaded = $DB->get_num_rows($l);
            $link_downloaded = ($link_downloaded) ? $link_downloaded : 0;
            if($link_downloaded >= intval($ibiet->vars['dl_limit_perday']))
            {
                $data['html'] .= "You have downloaded $link_downloaded/{$ibiet->vars['dl_limit_perday']} links already.";

                $data['title'] = "Error";
                $std->_print($data);
            }
        }
        $t = $DB->query("SELECT * FROM accounts_rs WHERE bw > 0 LIMIT 1");
        $r = $DB->fetch_array($t);

        $full_link = $std->getserver($link);
        $try = $std->getFileInfo($full_link, $r);
        $filename = $try['filename'];
        $fsize = $try['fsize'];

        $now = time();
        $fid = rand(1000,1000000);
        $DB->query("INSERT INTO logs SET fid='$fid',filename='{$filename}',ip='$ip',fdate='$now',furl='$full_link', filesize='$fsize'");
        $dlurl = "index.php?action=download&id=$fid";
        

        $data['html'] .= "<br><a href='$dlurl'><span style='color:#688000; background-color:#BBDB54; font-size : 10pt; text-decoration: none; font-family: Trebuchet MS;'>Click here to download this file <br><b>$filename</b></span></a>\n";

        $data['title'] = "Download";
        $std->_print($data);
    }

    //-----------------------------------------------------
    // Download Screen
    //-----------------------------------------------------
    function download()
    {
		global $ibiet, $std, $DB;

        $id = addslashes(trim($_GET['id'])) ;
            $t = $DB->query("SELECT * FROM accounts_rs WHERE bw > 0 LIMIT 1");
       $r = $DB->fetch_array($t);
	     $rapidshare = $r['rapidshare'];
       list($rlogin, $rpass) = split(":", $rapidshare); 
        $limit = time() - 60*60*1; //Only download link that was generated in the last 6 hours
        $ip = $_SERVER['REMOTE_ADDR'];
        $q = $DB->query("SELECT * FROM logs WHERE fid=$id");
        $row = $DB->fetch_array($q);
        $qcheck = $row['valid'];
        $url = @parse_url($row['furl']);
        if($row['furl'] == "")
        {
            @header("HTTP/1.0 404 Not Found");
            echo "<span style='color:#688000; background-color:#BBDB54; font-size : 10pt; text-decoration: none; font-family: Trebuchet MS;'><b>404! Invalid Link</b></span>";
            exit;
        }
                
        if($qcheck > 0) {
        		 echo "<span style='color:#688000; background-color:#BBDB54; font-size : 10pt; text-decoration: none; font-family: Trebuchet MS;'><b>404! Invalid Link</b></span>";
        		 exit;
        }
         $DB->query("UPDATE `logs` SET `valid` = '1' WHERE `fid` = $id;");
        /*
        @header("Cache-Control:");
        @header("Cache-Control: public");
        @header("Content-Type: application/octet-stream");
        @header("Content-Disposition: attachment; filename=".$row['filename']);
        @header("Accept-Ranges: bytes");
        */
        // Epic Failure. Below is Epic Fix!
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
        @stream_set_timeout($fp, 300);
        fputs($fp, "POST {$url['path']}  HTTP/1.1\r\n");
        fputs($fp, $head.$vars);
        fflush($fp);
        $buff = 256;
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
                    $buff = 1024;
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
        global $ibiet, $std, $DB;

        if ( @file_exists('/proc/loadavg') )
        {
            if ( $fh = @fopen( '/proc/loadavg', 'r' ) )
            {
                $data = @fread( $fh, 6 );
                @fclose( $fh );
                $load_avg = explode( " ", $data );
                $server_load = trim($load_avg[0]);
                if ($server_load > $ibiet->vars['serverload'])
                {
                    return array('overloaded' => 1, 'loaded' => $server_load, 'msg' => "<span style='color:#688000; background-color:#BBDB54; font-size : 10pt; text-decoration: none; font-family: Trebuchet MS;'><b>Sorry, the server is overloaded, please try again in a moment</b></span>");
                }
            }
        }
        else
        {
            if ( $serverstats = @exec("uptime") )
            {
                preg_match( "/(?:averages)?\: ([0-9\.]+),[\s]+([0-9\.]+),[\s]+([0-9\.]+)/", $serverstats, $load );
                $server_load = $load[1];
                if ($server_load > $ibiet->vars['serverload'])
                {
                    return array('overloaded' => 1, 'loaded' => $server_load, 'msg' => "<span style='color:#688000; background-color:#BBDB54; font-size : 10pt; text-decoration: none; font-family: Trebuchet MS;'><b>Sorry, the server is overloaded, please try again in a moment</b></span>");
                }
            }
        }
    }
} // End Class

$ibiet = new Rapids;
$ibiet->initialize();
?>