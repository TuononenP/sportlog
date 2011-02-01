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
include "./header.php";
include "./conn_auth.inc.php";
include("./php_functions/redirect.php");
print ('<body onload="document.user_login.username.focus()">');
include "./middle_login.php";
define("TBL_ATTEMPTS", "login_attempts");
define("ATTEMPTS_NUMBER", "3");
define("TIME_PERIOD", "1");
function ConfirmUser($user, $pass) {
	include "conn_auth.inc.php";
	$username = $mysqli->real_escape_string($user);
	//compares username and password to database ones
	$query = "Call CheckUserLogin('$username',
'" .$mysqli->real_escape_string($pass). "')";
	if($mysqli->multi_query($query)) {
		do {
			$result = $mysqli->store_result();
			if($result) {
				$rows = $result->num_rows;
				$result->close();
			}
		} while($mysqli->next_result());
	}
	if ($rows == 1) {
		return 1;
	} else {
		return 0;
	}
}
function ConfirmIpAddress($ip) {
	include "conn_login_attempt.inc.php";
	$query = "SELECT attempts, (CASE when last_login is not NULL and
DATE_ADD(last_login, INTERVAL ".TIME_PERIOD." MINUTE)>NOW() then 1 else 0 end)
as Denied FROM ".TBL_ATTEMPTS." WHERE ip = '$ip'";
	//$period = TIME_PERIOD;
	//$attempts = TBL_ATTEMPTS;
	//print $period;
	//print $attempts;
	//$query = "CALL ConfirmIp('$ip')";
	if($mysqli->multi_query($query)) {
		do {
			$result = $mysqli->store_result();
			if($result) {
				$data = $result->fetch_array();
				$result->close();
			}
		} while($mysqli->next_result());
	}
	//Verify that at least one login attempt is in database
	if (!$data) {
		return 0;
	}
	if ($data["attempts"] >= ATTEMPTS_NUMBER) {
		if($data["Denied"] == 1) {
			return 1;
		} else {
			ClearLoginAttempts($ip);
			return 0;
		}
	}
	return 0;
}
function ClearLoginAttempts($ip) {
	include "conn_login_attempt.inc.php";
	$query = "UPDATE ".TBL_ATTEMPTS." SET attempts = 0 WHERE ip = '$ip'";
	//$attempts = TBL_ATTEMPTS;
	//$query = "CALL ClearLoginAttempts('$ip', '$attempts')";
	if($mysqli->multi_query($query)) {
		do {
			$result = $mysqli->store_result();
			if($result) {
				$result->close();
			}
		} while($mysqli->next_result());
	}
}
/*
 * First, if IP address is forwarded, get forwarded address otherwise remote address.
 * If attempt to get client ip is successful, get client ip otherwise address from
 * the first phase (either forwarded or remote).
 */
function GetIpAddress() {
	return (empty($_SERVER['HTTP_CLIENT_IP'])?(empty($_SERVER['HTTP_X_FORWARDED_FOR'])?
	$_SERVER['REMOTE_ADDR']:$_SERVER['HTTP_X_FORWARDED_FOR']):$_SERVER['HTTP_CLIENT_IP']);
}
function AddLoginAttempt($ip) {
	include "conn_login_attempt.inc.php";
	//increase number of attempts
	//set last login attempt time if required
	$query = "SELECT * FROM ".TBL_ATTEMPTS." WHERE ip = '$ip'";
	if($mysqli->multi_query($query)) {
		do {
			$result = $mysqli->store_result();
			if($result) {
				$data = $result->fetch_array();
				$result->close();
			}
		} while($mysqli->next_result());
	}
	if($data) {
		$attempts = $data["attempts"]+1;
		if($attempts==3) {
			$query = "UPDATE ".TBL_ATTEMPTS." SET attempts=".$attempts.",
last_login=NOW() WHERE ip = '$ip'";
			if($mysqli->multi_query($query)) {
				do {
					$result = $mysqli->store_result();
					if($result) {
						$result->close();
					}
				} while($mysqli->next_result());
			}
		} else {
			$query = "UPDATE ".TBL_ATTEMPTS." SET attempts=".$attempts."
WHERE ip = '$ip'";
			if($mysqli->multi_query($query)) {
				do {
					$result = $mysqli->store_result();
					if($result) {
						$result->close();
					}
				} while($mysqli->next_result());
			}
		}
	} else {
		$query = "INSERT INTO ".TBL_ATTEMPTS." (attempts, ip, last_login)
values (1, '$ip', NOW())";
		if($mysqli->multi_query($query)) {
			do {
				$result = $mysqli->store_result();
				if($result) {
					$result->close();
				}
			} while($mysqli->next_result());
		}
	}
}
//execute if login button pressed
if (isset($_POST['submit'])) {
	//check if ip address is blocked
	$blocked = ConfirmIpAddress(GetIpAddress());
	if ($blocked != 1) {
		//if username and password match
		if (ConfirmUser($_POST['username'], MD5($_POST['password']))) {
			$_SESSION['user_logged'] = $_POST['username'];
			$_SESSION['user_password'] = MD5($_POST['password']);
			//select all athletes at start
			$_SESSION['select_all'] = 1;
			//ClearLoginAttempts(GetIpAddress());
			redirect("./logged_user.php", 301);
		} else {
			AddLoginAttempt(GetIpAddress());
			?>
<p>
	Invalid Username and/or Password<br /> Not registered?
</p>
<p>
	<a href="register.php">Click here</a> to register.
</p>
<form name="user_login" action="<?php echo $_SERVER['$PHP_SELF']; ?>"
	method="post">
	<fieldset>
		<label for="username"> Username: <input type="text" name="username"
			id="username" /> </label> <label for="password"> Password: <input
			type="password" name="password" id="password" /> </label>
		<div class="buttons">
			<input type="submit" name="submit" id="submit" value="Login" />
		</div>
	</fieldset>
</form>
			<?php
		}
	} else {
		?>
<p>
	You have exceeded the maximum amount of login attempts (
	<?php echo ATTEMPTS_NUMBER ?>
	).<br /> Access denied for
	<?php echo TIME_PERIOD ?>
	minutes.<br /> <br /> Site will automatically refresh back to login
	page when access is again permitted.
	<?php
	$redirect = "./user_login.php";
	header("Refresh: 60; URL=$redirect");
	die();
	?>
</p>
	<?php
	}
} else {
	?>
<p>
	Login below by supplying your username and password<br /> Or <a
		href="register.php">click here</a> to register.<br />
</p>
<form name="user_login" action="<?php echo $_SERVER['$PHP_SELF']; ?>"
	method="post">
	<fieldset>
		<label for="username"> Username: <input type="text" name="username"
			id="username" /> </label> <label for="password"> Password: <input
			type="password" name="password" id="password" /> </label>
		<div class="buttons">
			<input type="submit" name="submit" id="submit" value="Login" />
		</div>
	</fieldset>
</form>
<?php
}
include "./footer_login.php";
?>