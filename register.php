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
include "./conn_reg.inc.php";
include('./php_functions/EmailAddressValidator.php');
function checkLogin() {
	if (!empty($_SESSION['user_logged']) &&
	(!empty($_SESSION['user_password']))) {
		return true;
	} else {
		return false;
	}
}
print "<body>";
include "./middle_register.php";
$validator = new EmailAddressValidator;
//execute if register button is pressed
if (isset($_POST['submit']) && $_POST['submit'] == "Register") {
	//check that mandatory fields are not empty
	if (!empty($_POST['username']) &&
	!empty($_POST['password']) &&
	!empty($_POST['first_name']) &&
	!empty($_POST['last_name']) &&
	$validator->check_email_address($_POST['email']) ) {
		$username = $mysqli->real_escape_string($_POST['username']);
		$query = "CALL IsAccount('$username')";
		if($mysqli->multi_query($query)) {
			do {
				$result = $mysqli->store_result();
				if($result) {
					$rows = $result->num_rows;
					$result->close();
				}
			} while($mysqli->next_result());
		}
		//check if the chosen username is already taken
		if ($rows != 0) {
			?>
<h2>
	Register as a new user
</h2>
<p>
	<b><i>The Username, <?php echo $_POST['username']; ?>, is already in
			use, please choose another!</i> </b>
</p>
<form name="register" id="register"
	action="<?php echo $_SERVER['$PHP_SELF']; ?>"
	onreset="formReset(this); return false;" method="post">
	<fieldset>
		<label for="username"> Username: <input type="text" name="username"
			id="username" /> * </label> <label for="password"> Password: <input
			type="password" name="password" id="password"
			value="<?php echo $_POST['password']; ?>" /> * </label> <label
			for="first_name"> First name: <input type="text" name="first_name"
			id="first_name" value="<?php echo $_POST['first_name']; ?>" /> * </label>
		<label for="last_name"> Last name: <input type="text" name="last_name"
			id="last_name" value="<?php echo $_POST['last_name']; ?>" /> * </label>
		<label for="email"> Email: <input type="text" name="email" id="email"
			value="<?php echo $_POST['email']; ?>" /> * </label>
		<div class="buttons">
			<input type="submit" name="submit" id="submit" value="Register" /> <input
				type="reset" value="Clear" />
		</div>
		<div class="req_fields_text">
			* Required fields
		</div>
	</fieldset>
</form>
			<?php
			if (!checkLogin()) {
				print '<a href="./index.php">Back to login</a> ';
			}
		} else {
			$first_name = $mysqli->real_escape_string($_POST['first_name']);
			$last_name = $mysqli->real_escape_string($_POST['last_name']);
			$email = $mysqli->real_escape_string($_POST['email']);
			$query = "CALL CreateAccount('$username',
'" .MD5($mysqli->real_escape_string($_POST['password'])). "',
'$first_name', '$last_name', '$email')";
			if($mysqli->multi_query($query)) {
				do {
					$result = $mysqli->store_result();
					if($result) {
						$result->close();
					}
				} while($mysqli->next_result());
			}
			$_SESSION['user_logged'] = $_POST['username'];
			$_SESSION['user_password'] = $_POST['password'];
			?>
<p>
	Thank you,
	<?php echo $_POST['first_name'] . " " .
	$_POST['last_name']; ?>
	for registering!
</p>
	<?php
	header("Refresh: 2; URL=index.php");
	echo "Your registration is complete! " .
"You are being redirected to the page you requested!<br>";
	echo "(If your browser doesn't support this, " .
"<a href=\"index.php\">click here</a>)";
	die();
		}
	} else {
		?>
<h2>
	Register as a new user
</h2>
<p>
	<b><i>The Username, Password, First name,<br />Last name and Email
			fields are required!</i> </b>
</p>
		<?php
		if (!($validator->check_email_address($_POST['email']))) {
			print "<p><b><i>Email address incorrect!</i></b></p>";
		}
		?>
<form name="register" id="register"
	action="<?php echo $_SERVER['$PHP_SELF']; ?>"
	onreset="formReset(this); return false;" method="post">
	<fieldset>
		<label for="username"> Username: <input type="text" name="username"
			id="username" value="<?php echo $_POST['username']; ?>" /> * </label>
		<label for="password"> Password: <input type="password"
			name="password" id="password"
			value="<?php echo $_POST['password']; ?>" /> * </label> <label
			for="first_name"> First name: <input type="text" name="first_name"
			id="first_name" value="<?php echo $_POST['first_name']; ?>" /> * </label>
		<label for="last_name"> Last name: <input type="text" name="last_name"
			id="last_name" value="<?php echo $_POST['last_name']; ?>" /> * </label>
		<label for="email"> Email: <input type="text" name="email" id="email"
			value="<?php echo $_POST['email']; ?>" /> * </label>
		<div class="buttons">
			<input type="submit" name="submit" id="submit" value="Register" /> <input
				type="reset" value="Clear" />
		</div>
		<div class="req_fields_text">
			* Required fields
		</div>
	</fieldset>
</form>
		<?php
		if (!checkLogin()) {
			print '<a href="./index.php">Back to login</a> ';
		}
	}
} else {
	?>
<h2>
	Register as a new user
</h2>
<p>
	<b> The Username, Password, First name,<br />Last name and Email fields
		are required. </b>
</p>
<form name="register" id="register"
	action="<?php echo $_SERVER['$PHP_SELF']; ?>"
	onreset="formReset(this); return false;" method="post">
	<fieldset>
		<label for="username"> Username: <input type="text" name="username"
			id="username" /> * </label> <label for="password"> Password: <input
			type="password" name="password" id="password" /> * </label> <label
			for="first_name"> First name: <input type="text" name="first_name"
			id="first_name" /> * </label> <label for="last_name"> Last name: <input
			type="text" name="last_name" id="last_name" /> * </label> <label
			for="email"> Email: <input type="text" name="email" id="email" /> * </label>
		<div class="buttons">
			<input type="submit" name="submit" id="submit" value="Register" /> <input
				type="reset" value="Clear" />
		</div>
		<div class="req_fields_text">
			* Required fields
		</div>
	</fieldset>
</form>
<?php
if (!checkLogin()) {
print '<a href="./index.php">Back to login</a> ';
}
}
include "./footer.php";
$mysqli->close();
?>