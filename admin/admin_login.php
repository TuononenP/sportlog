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
include "./conn.inc.php";
include("../php_functions/redirect.php");
include "./header_admin.php";
include "./middle_admin.php";
?>
<?php
if (isset($_POST['submit'])) {
	$query = "CALL CheckAdminLogin(
'" .$mysqli->real_escape_string($_POST['username']). "',
'" .MD5($mysqli->real_escape_string($_POST['password'])). "')";
	$result = $mysqli->query($query)
	or die(mysqli_error($mysqli));
	$row = $result->fetch_array();
	if ($result->num_rows == 1) {
		$_SESSION['admin_logged'] = $_POST['username'];
		$_SESSION['admin_password'] = $_POST['password'];
		$result->close();
		$mysqli->close();
		redirect("./index.php",301);
	} else {
		?>
<h2>
	Admin Login
</h2>
<p>
	<b><i>Invalid Username and/or Password</i> </b>
</p>
<form name="admin_login" id="admin_login"
	action="<?php echo $_SERVER['$PHP_SELF']; ?>" method="post">
	<fieldset>
		<label for="username"> Username: <input type="text" name="username"
			id="username" /> </label> <label for="password"> Password: <input
			type="password" name="password" id="username" /> </label>
		<div class="buttons">
			<input type="submit" name="submit" id="submit" value="Login" />
		</div>
	</fieldset>
</form>
		<?php
	}
} else {
	?>
<h2>
	Admin Login
</h2>
<p>
	Login below by supplying your username and password
</p>
<form name="admin_login" id="admin_login"
	action="<?php echo $_SERVER['$PHP_SELF']; ?>" method="post">
	<fieldset>
		<label for="username"> Username: <input type="text" name="username"
			id="username" /> </label> <label for="password"> Password: <input
			type="password" name="password" id="username" /> </label>
		<div class="buttons">
			<input type="submit" name="submit" id="submit" value="Login" />
		</div>
	</fieldset>
</form>
<?php
}
include "./footer_admin.php";
?>