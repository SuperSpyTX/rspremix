<?php
if(isset($_GET['step1'])) {
	?>
	The fsockopen() test. This test will check if fsockopen is working properly.. Is it a epic? or a fail?<br>
	<?php
	$fp = fsockopen("www.rapidshare.com", 80, $errno, $errstr, 30);
if (!$fp) {
    echo "<font size='10'>FAIL!</font><br>fsockopen() is not working! it returned $errstr while it loaded. Please check your installation of the fsockopen() function. Also make sure that allow_fopen_url is enabled on php.ini or this script will fail.<br>Debug:";
    print($errstr);
    exit();
} else {
    $out = "GET / HTTP/1.1\r\n";
    $out .= "Host: www.example.com\r\n";
    $out .= "Connection: Close\r\n\r\n";
    fwrite($fp, $out);
    while (!feof($fp)) {
        fgets($fp, 128);
    }
    fclose($fp);
   }
?>
<br><font size=5>Epic Success!</font><br><br>fsockopen() data Successfully retrieved information from rapidshare.com Click <a href="test.php?step2">Here</a> to continue testing cURL!
<?php
exit();}

if(isset($_GET['step2'])) {
	?>
	The cURL Test. This test will check if cURL is working properly.. Is it a epic? or a fail?
	<?php
	$curl = curl_init();
//curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($curl, CURLOPT_HEADER, 0);
curl_setopt($curl, CURLOPT_NOBODY, 1);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, false);
curl_setopt($curl, CURLOPT_POSTFIELDS, "login=".$User."&password=".$Pass);
//curl_setopt($curl, CURLOPT_COOKIEJAR, '/tmp/omfg342.txt');
//curl_setopt($curl, CURLOPT_COOKIEFILE, '/tmp/omfg342.txt');
curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($curl, CURLOPT_URL, "http://rapidshare.com");
$page = curl_exec($curl);
if(curl_error($curl)) {
	print "<font size='10'>FAILURE!</font><br>cURL is not working! it returned a error while it loaded. Please check your installation of cURL is correct. Also make sure you recompiled PHP while you were at it.<br><br>Debug: ";
	print(curl_error($curl));
	exit();
}
//echo($page);
?>
<br><font size=5>Epic Success!</font><br><br>cURL data Successfully retrieved information from rapidshare.com Click <a href="test.php?step3">Here</a> to continue testing cURL! but with SSL! This is the final test! Is it a epic success? or a epic failure?
<?php
exit(); }
if(isset($_GET['step3'])) {
		$curl = curl_init();
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($curl, CURLOPT_HEADER, 0);
curl_setopt($curl, CURLOPT_NOBODY, 1);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, false);
curl_setopt($curl, CURLOPT_POSTFIELDS, "login=".$User."&password=".$Pass);
//curl_setopt($curl, CURLOPT_COOKIEJAR, '/tmp/omfg342.txt');
//curl_setopt($curl, CURLOPT_COOKIEFILE, '/tmp/omfg342.txt');
curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($curl, CURLOPT_URL, "https://ssl.rapidshare.com/cgi-bin/premiumzone.cgi");
$page = curl_exec($curl);
if(curl_error($curl)) {
	print "<font size='10'>EPIC FAIL!</font><br>cURL with SSL is not working! it returned a error while it loaded. Please check your installation of cURL SSL is correct. Also make sure you recompiled PHP while you were at it. Sadly now you won't be able to use this generator without it.<br><br>Debug: ";
	print(curl_error($curl));
	exit();
}
?>
<font size=10>EPIC SUCCESS!</font><br><br>DONE! YAY! cURL data Successfully retrieved information from rapidshare.com! Congratulations! You are finished! You can now run RsPremiX with no problem. Don't forget to keep this if you make changes to your PHP install.
<?php exit(); } ?>
Welcome to the test. This test will prove that RsPremiX will work fine. if you fail at any of these tests. You will NEVER be able to use RsPremiX. and this goes to you. pankajabcd! You better not fail this test! This has also been checked for validity so there wont be any runtime PHP errors. Not unless you are blind. Oh please..<br><br>Click <a href="test.php?step1">Here</a> to begin testing. Hope you don't fail.

	