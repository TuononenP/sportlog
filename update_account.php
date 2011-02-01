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
include('./php_functions/EmailAddressValidator.php');
print ('<body onload="document.update_account.email.focus()">');
include "./middle.php";
print('<h2>Update Account Information</h2>');
$validator = new EmailAddressValidator;
//execute if update button pressed
if (isset($_POST['submit']) && $_POST['submit'] == "Update") {
	if ($validator->check_email_address($_POST['email'])) {
		$email = $mysqli->real_escape_string($_POST['email']);
		$query = "CALL UpdateAccount('$email', '" .$_SESSION['user_logged']. "',
'" .$_SESSION['user_password']. "')";
		if($mysqli->multi_query($query)) {
			do {
				$result = $mysqli->store_result();
				if($result) {
					$result->close();
				}
			} while($mysqli->next_result());
		}
		$query = "CALL GetAllUSerInfo('" . $_SESSION['user_logged'] . "',
'" .$_SESSION['user_password']. "')";
		if($mysqli->multi_query($query)) {
			do {
				$result = $mysqli->store_result();
				if($result) {
					$row = $result->fetch_array();
					$email = $row['email'];
					$result->close();
				}
			} while($mysqli->next_result());
		}
		?>
<p>
	<b><i>Your account information has been updated.</i> </b>
</p>
<form name="update_account" id="update_account"
	action="<?php echo $_SERVER['$PHP_SELF']; ?>" method="post">
	<fieldset>
		<label for="email"> Email: <input type="text" name="email" id="email"
			value="<?php echo $email; ?>" /> </label>
		<div class="buttons">
			<input type="submit" name="submit" id="submit" value="Update" />
		</div>
	</fieldset>
</form>
<p>
	<a href="user_personal.php">Click here</a> to return to your account.
</p>
		<?php
	} else {
		?>
<p>
	<b><i>Incorrect email address!</i> </b>
</p>
<form name="update_account" id="update_account"
	action="<?php echo $_SERVER['$PHP_SELF']; ?>" method="post">
	<fieldset>
		<label for="email"> Email: <input type="text" name="email" id="email"
			value="<?php echo $email; ?>" /> </label>
		<div class="buttons">
			<input type="submit" name="submit" id="submit" value="Update" />
		</div>
	</fieldset>
</form>
<p>
	<a href="user_personal.php">Click here</a> to return to your account.
</p>
		<?php
	}
} else {
	$query = "CALL GetAllUSerInfo('" .$_SESSION['user_logged']. "',
'" .$_SESSION['user_password']. "')";
	if($mysqli->multi_query($query)) {
		do {
			$result = $mysqli->store_result();
			if($result) {
				$row = $result->fetch_array();
				$email = $row['email'];
				$result->close();
			}
		} while($mysqli->next_result());
	}
	?>
<form name="update_account" id="update_account"
	action="<?php echo $_SERVER['$PHP_SELF']; ?>" method="post">
	<fieldset>
		<label for="email"> Email: <input type="text" name="email" id="email"
			value="<?php echo $email; ?>" /> </label>
		<div class="buttons">
			<input type="submit" name="submit" id="submit" value="Update" />
		</div>
	</fieldset>
</form>
<p>
	<a href="user_personal.php">Click here</a> to return to your account.
</p>
<?php
}
include "./footer.php";
$mysqli->close();
?>