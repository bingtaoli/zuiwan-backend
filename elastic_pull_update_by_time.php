<?php

if (!isset($_GET['time_stamp'])){
	return;
}
$time_stamp = $_GET['time_stamp'];
echo shell_exec("./mysql_to_elastic_by_timestamp.sh $time_stamp");

