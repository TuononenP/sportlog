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
include "./auth_admin.inc.php";
include "./conn.inc.php";
include('../php_functions/EmailAddressValidator.php');
include "./header_admin.php";
include "./middle_admin.php";
?>
<h2>
	Update user information
</h2>
<?php
$validator = new EmailAddressValidator;
if (isset($_POST['submit']) && $_POST['submit'] == "Update") {
	if ($validator->check_email_address($_POST['email'])) {
		$username = $mysqli->real_escape_string($_POST['username']);
		$first_name = $mysqli->real_escape_string($_POST['first_name']);
		$last_name = $mysqli->real_escape_string($_POST['last_name']);
		$email = $mysqli->real_escape_string($_POST['email']);
		$id = $mysqli->real_escape_string($_POST['id']);
		if (!empty($_POST['password'])) {
			$password = MD5($mysqli->real_escape_string($_POST['password']));
			$query = "CALL UpdateWholeAccount('$username', '$password', '$first_name',
'$last_name', '$email', '$id')";
		} else {
			$query = "CALL UpdateWholeAccount_ExceptPass('$username', '$first_name',
'$last_name', '$email', '$id')";
		}
		if($mysqli->multi_query($query)) {
			do {
				$result = $mysqli->store_result();
				if($result) {
					$result->close();
				}
			} while($mysqli->next_result());
		}
		?>
<b>User information has been updated.</b>
<br />
		<?php
	} else {
		?>
<p>
	<b>Incorrect email address!</b>
</p>
<form name="update_user" id="update_user"
	action="<?php echo $_SERVER['$PHP_SELF']; ?>" method="post">
	<fieldset>
		<input type="hidden" name="id" id="id"
			value="<?php echo $_GET['id']; ?>" /> <label for="username">
			Username: <input type="text" name="username" id="username"
			value="<?php echo $_POST['username']; ?>" /> </label> <label
			for="password"> <!-- User password won't be displayed--> Password: <input
			type="password" name="password" id="password
value=" " /> Not displayed </label> <label for="first_name"> First Name:
			<input type="text" name="first_name" id="first_name"
			value="<?php echo $_POST['first_name']; ?>" /> </label> <label
			for="last_name"> Last Name: <input type="text" name="last_name"
			id="first_name" value="<?php echo $_POST['last_name']; ?>" /> </label>
		<label for="email"> Email: <input type="text" name="email" id="email"
			value="<?php echo $_POST['email']; ?>" /> </label>
		<div class="buttons">
			<input type="submit" name="submit" id="submit" value="Update" />
		</div>
	</fieldset>
</form>
		<?php
	}
} else {
	$id = $mysqli->real_escape_string($_GET['id']);
	$query = "CALL GetAccount('$id')";
	if($mysqli->multi_query($query)) {
		do {
			$result = $mysqli->store_result();
			if($result) {
				$row = $result->fetch_array();
				$result->close();
			}
		} while($mysqli->next_result());
	}
	?>
<form name="update_user" id="update_user"
	action="<?php echo $_SERVER['$PHP_SELF']; ?>" method="post">
	<fieldset>
		<input type="hidden" name="id" id="id"
			value="<?php echo $_GET['id']; ?>" /> <label for="username">
			Username: <input type="text" name="username" id="username"
			value="<?php echo $row['username']; ?>" /> </label> <label
			for="password"> <!-- User password won't be displayed--> Password: <input
			type="password" name="password" id="password
value=" " /> Not displayed </label> <label for="first_name"> First Name:
			<input type="text" name="first_name" id="first_name"
			value="<?php echo $row['first_name']; ?>" /> </label> <label
			for="last_name"> Last Name: <input type="text" name="last_name"
			id="first_name" value="<?php echo $row['last_name']; ?>" /> </label>
		<label for="email"> Email: <input type="text" name="email" id="email"
			value="<?php echo $row['email']; ?>" /> </label>
		<div class="buttons">
			<input type="submit" name="submit" id="submit" value="Update" />
		</div>
	</fieldset>
</form>
<?php
}
$mysqli->close();
?>
<a href="admin_area.php">Return</a>
to the admin area.
<?php
include "./footer_admin.php";
?>