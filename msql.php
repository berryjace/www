<?php

//production
$host = 'localhost';
$username = 'greekamc';
$password = 'am@kMg!RcA47';
$db = 'admin_amc';

$conn = mysql_connect($host, $username, $password) or die ('Cannot connect to server');
$db = mysql_select_db($db, $conn) or die('Cannot connect to database');

$sql = 'Select * from vendor_royalty_report_submissions group by uploaded_on';
$rs = mysql_query($sql);

while($row = mysql_fetch_object($rs)){
	$submission_hash = sha1(md5(microtime()));
	$sql = 'update vendor_royalty_report_submissions 
			set submission_hash="'.$submission_hash.'" 
			where uploaded_on="'.$row->uploaded_on.'"';
	$rs2 = mysql_query($sql);
}
