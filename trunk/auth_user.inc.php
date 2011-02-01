<?php
/************************************************************************
    Copyright (C) 2011 Petri Tuononen

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program. If not, see <http://www.gnu.org/licenses/>.
 ************************************************************************/
ob_start();
session_start();
include "conn_auth.inc.php";
//compares username and password to database ones
$query = "Call CheckUserLogin('" .$_SESSION['user_logged']. "',
'" .$_SESSION['user_password']. "')";
if($mysqli->multi_query($query)) {
	do {
		$result = $mysqli->store_result();
		if($result) {
			$rows = $result->num_rows;
			$result->close();
		}
	} while($mysqli->next_result());
}
//if username and password don't match
if ($rows != 1) {
	$redirect = $_SERVER['PHP_SELF'];
	header("Refresh: 2; URL=user_login.php?redirect=$redirect");
	echo "You are currently not logged in so you are being redirected.<br>";
	echo "(If your browser doesn't support this, " .
"<a href=\"user_login.php?redirect=$redirect\">click here</a>)";
	die();
}
?>