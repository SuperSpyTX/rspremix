<?php

if ( ! defined( 'IBIET' ) )
{
	print "You cannot access this file directly.";
	exit();
}
class db {

    var $query_count    = 0;
    var $obj  = array (
        'sql_host'      => '',
        'sql_user'      => '',
        'sql_pass'      => '',
        'sql_database'  => ''
    );

    function connect()
    {
        @mysql_connect($this->obj['sql_host'], $this->obj['sql_user'], $this->obj['sql_pass']);
        @mysql_select_db($this->obj['sql_database']) or die(mysql_error());
    }

    function query($query)
    {
        $this->query_count++;
        $query = mysql_query($query) or die(mysql_error());
        return $query;
    }

    function fetch_array($query)
    {
        $query = mysql_fetch_array($query);
        return $query;
    }

    function fetch_field($query)
    {
        $query = mysql_fetch_field($query);
        return $query;
    }

    function get_num_rows($query) {
        return mysql_num_rows($query);
    }

    function get_query_cnt()
    {
        return $this->query_count;
    }

    function close()
    {
        mysql_close();
    }
}

$DB = new db;
$DB->obj['sql_database']     = $INFO['sql_database'];
$DB->obj['sql_user']         = $INFO['sql_user'];
$DB->obj['sql_pass']         = $INFO['sql_pass'];
$DB->obj['sql_host']         = $INFO['sql_host'];
$DB->connect();
?>