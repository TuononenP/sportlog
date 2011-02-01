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
session_start();
ob_start();
include "./header.php";
include "./auth_user.inc.php";
include "./conn_reg.inc.php";
include "./conn_auth.inc.php";
include "./middle.php";
$query = "CALL CheckUserLogin('" . $_SESSION['user_logged'] ."',
'" .$_SESSION['user_password']. "')";
$result = $mysqli->query($query)
or die(mysqli_error($mysqli));
$row = $result->fetch_array();
?>
<div class="main_page">
	You are logged into Sportlog.<br /> Logged user: <b><?php echo $row['first_name']; echo " ";
	echo $row['last_name']; ?>
	</b><br /> <a href="logout.php">Logout</a>
</div>
	<?php
	$result->close();
	include "./footer.php";
	$mysqli->close();
?>