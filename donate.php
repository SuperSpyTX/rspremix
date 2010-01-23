<?php
include  "config.php";
include "curl.php";
$toke = $INFO['tokens'];
mysql_connect($INFO['sql_host'],$INFO['sql_user'],$INFO['sql_pass']) or die(mysql_error());
mysql_select_db($INFO['sql_database'] ) or die(mysql_error());
$User = $_POST['username'];
$Pass = $_POST['password'];

if(!$User == "" && !$Pass == "") {
$result = check_login($User, $Pass);
if ($result == "true") {
$query = "INSERT INTO `accounts_rs` (`rapidshare`, `bw`) VALUES ('$User:$Pass', '0');";
mysql_query($query);
$query2 = "INSERT INTO `members` (`id`, `username`, `password`, `tokens`, `rapidshare`) VALUES(1, '$User', '$Pass', '$toke', '$User:$Pass');";
mysql_query($query2);
echo("Congratulations, you have succeeded in donation! Your login username and password is the rapidshare account you entered. Have fun!");
}
echo("Incorrect login");
}
echo("No password or username filled");
?>