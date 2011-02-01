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
//execute if yes button pressed
if (isset($_POST['submit']) && $_POST['submit'] == "Yes") {
	$query = "CALL DeleteAccount('" .$_SESSION['user_logged']. "',
'" .$_SESSION['user_password']. "')";
	if($mysqli->multi_query($query)) {
		do {
			$result = $mysqli->store_result();
			if($result) {
				$result->close();
			}
		} while($mysqli->next_result());
	}
	$mysqli->close();
	//set login variables to null
	$_SESSION['user_logged'] = "";
	$_SESSION['user_password'] = "";
	header("Refresh: 2; URL=index.php");
	echo "Your account has been deleted! You are being sent to the " .
"home page!<br>";
	echo "(If you're browser doesn't support this, " .
"<a href=\"index.php\">click here</a>)";
	die();
} else {
	?>
<p>
	Are you sure you want to delete your account?<br /> There is no way to
	retrieve your account once you confirm!<br />
</p>
<form action="<?php echo $_SERVER['$PHP_SELF']; ?>" method="post">
	<fieldset>
		<div class="buttons">
			<input type="submit" name="submit" id="submit" value="Yes" /> <input
				type="button" value=" No " onclick="history.go(-1);" />
		</div>
	</fieldset>
</form>
	<?php
}
include "./footer.php";
?>