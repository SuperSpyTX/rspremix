<?php
$host="localhost"; // Host name 
$username=""; // Mysql username 
$password=""; // Mysql password 
$db_name=""; // Database name 
$tbl_name="members"; // Table name
if(!session_is_registered("dlpremium")) {
    header('WWW-Authenticate: Basic realm="Members Login (BETA)"');
}
// Connect to server and select databse.
mysql_connect("$host", "$username", "$password")or die("cannot connect"); 
mysql_select_db("$db_name")or die("cannot select DB");

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
// If result matched $myusername and $mypassword, table row must be 1 row

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