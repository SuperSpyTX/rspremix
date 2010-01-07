<?php
$tbl_name="members"; // Table name
if(!session_is_registered("dlpremium")) {
    header('WWW-Authenticate: Basic realm="Members Login (BETA)"');
}
// Connect to server and select databse.
include  "config.php";
mysql_connect($INFO['sql_host'],$INFO['sql_user'],$INFO['sql_pass']) or die(mysql_error());
mysql_select_db($tbl_name) or die(mysql_error());

// username and password sent from form 
$myusername=$_SERVER['PHP_AUTH_USER'];
$mypassword=$_SERVER['PHP_AUTH_PW'];

// To protect MySQL injection (more detail about MySQL injection)
$myusername = stripslashes($myusername);
$mypassword = stripslashes($mypassword);
$myusername = mysql_real_escape_string($myusername);
$mypassword = mysql_real_escape_string($mypassword);

$sql="SELECT * FROM $tbl_name WHERE username='$myusername' and password='$mypassword'";
$result=mysql_query($sql);

// Mysql_num_row is counting table row
$count=mysql_num_rows($result);
$row=mysql_fetch_array($result);
// If result matched $myusername and $mypassword, table row must be 1 row
if(!$row['rapidshare'] == "admin") {
$rapidshare = $row['rapidshare'];
list($rlogin, $rpass) = split(":", $rapidshare); 
require_once("curl.php");
$re = check_login($rlogin, $rpass);
if($re == "false") {
echo("You are not allowed to login since your Rapidshare account is invalid");
exit();
}
}
if($count==1){
// Register $myusername, $mypassword and redirect to file "login_success.php"
session_register("dlpremium");
$_SESSION['user'] = $myusername;
$_SESSION['pass'] = $mypassword;
header("Location: index.html");
}
else {
echo "Wrong Username or Password";
}
?>