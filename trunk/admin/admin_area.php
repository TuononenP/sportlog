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
include "./auth_admin.inc.php";
include "./conn.inc.php";
include "./header_admin.php";
include "./middle_admin.php";
?>
<h2>
	Admin Area
</h2>
<p>
	Below is a list of users and your available administrator privileges.<br />
	<a href="index.php">Click here</a> to go to the main admin page.
</p>
<?php
$query = "CALL ListUsers()";
$result = $mysqli->query($query)
or die(mysqli_error($mysqli));
while ($row = $result->fetch_array()) {
	echo $row['first_name'] . " " . $row['last_name'];
	?>
&nbsp;&nbsp;
<a href="update_user.php?id=<?php
echo $row['id']; ?>">Update User</a>
|
<a href="delete_user.php?id=<?php echo $row['id'];?>">Delete User</a>
<?php
}
$result->close();
$mysqli->close();
include "./footer_admin.php";
?>