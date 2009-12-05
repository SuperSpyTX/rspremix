<?php
function get_value_from_code( $before, $after, $code)
{
$match = '#'.$before.'(.+?)'.$after.'#';
if( preg_match( $match, $code, $match))
{
return $match[1];
}
return false;
}
include  "config.php";
mysql_connect($INFO['sql_host'],$INFO['sql_user'],$INFO['sql_pass']) or die(mysql_error());
mysql_select_db($INFO['sql_database'] ) or die(mysql_error());
function get_bw($rapidshare) {
list($User, $Pass) = split(":", $rapidshare); 
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
$bwacc = explode('Traffic left:</td><td align=right><b><script>document.write(setzeTT(""+Math.ceil(', $page);
if (isset($bwacc[1])) {
$bwacc = explode ("</b></td>",$bwacc[1]);
$tbw = round(($bwacc[0] / 1000),1);
//preg_match('/Set-Cookie: (.*)/i', $page, $cook); Already Called by Curl.php if it needs to dl.
//$cookie = $cook[1];
//$log3 = fopen("omfg432.txt", "w");
fwrite($log3, $cookie);
if ($tbw>=50) {
return $tbw;
}
}
}
$source = mysql_query("SELECT * FROM accounts_rs") or $noax=1; 
while($row = mysql_fetch_array( $source )) {
$rapidshare = $row['rapidshare'];
$result=get_bw($rapidshare);
$query="Update accounts_rs set bw = '$result' WHERE rapidshare='$rapidshare'";
if($result <=0.5)
{
mysql_query("Update accounts_rs set bw = '0' WHERE rapidshare='$rapidshare'");
}
mysql_query($query) or die (mysql_error());
}
?>