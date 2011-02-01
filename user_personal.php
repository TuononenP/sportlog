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
include "./header.php";
include "./auth_user.inc.php";
include "./conn_reg.inc.php";
include "./middle.php";
?>
<h2>
	Personal information area
</h2>
<p>
	Here you can update your personal information, or delete your account.
</p>
<p>
	Your current information is shown below:
</p>
<?php
$query = "CALL GetAllUserInfo('" . $_SESSION['user_logged'] . "',
'" .$mysqli->real_escape_string($_SESSION['user_password']). "')";
$result = $mysqli->query($query)
or die(mysqli_error($mysqli));
$row = $result->fetch_array();
?>
First Name:
<?php echo $row['first_name']; ?>
<br />
Last Name:
<?php echo $row['last_name']; ?>
<br />
Email:
<?php echo $row['email']; ?>
<br />
<br />
<a href="update_account.php">Update Account</a>
|
<a href="delete_account.php">Delete Account</a>
<?php
$result->close();
include "./footer.php";
$mysqli->close();
?>