<?php
// This code was created by phpMyBackupPro v.2.1
// http://www.phpMyBackupPro.net
$_POST['db']=array("mysql", "phpmyadmin", "registration", "sportlog", );
$_POST['tables']="on";
$_POST['data']="on";
$_POST['drop']="on";
$_POST['zip']="gzip";
$period=(3600*24)*2;
$security_key="0c32f84a8ffd99b4c8afdbefed78cc9e";
// This is the relative path to the phpMyBackupPro v.2.1 directory
@chdir("../../phpMyBackupPro/");
@include("backup.php");
?>