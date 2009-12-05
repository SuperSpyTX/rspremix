<?php
function get_cookie($User, $Pass) {
$curl = curl_init();
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($curl, CURLOPT_HEADER, 1);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, "login=".$User."&password=".$Pass);
curl_setopt($curl, CURLOPT_COOKIEJAR, '/tmp/omfg342.txt');
curl_setopt($curl, CURLOPT_COOKIEFILE, '/tmp/omfg342.txt');
curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($curl, CURLOPT_URL, "https://ssl.rapidshare.com/cgi-bin/premiumzone.cgi");
$page = curl_exec($curl);
preg_match('/Set-Cookie: (.*)/i', $page, $cook);
$cookie = $cook[1];
return $cookie;
}
?>