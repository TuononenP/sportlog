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
<?php
if (isset($_POST['submit']) && $_POST['submit'] == "Yes") {
	$query = "Call DeleteAccountById('" . $_POST['id'] . "')";
	$result = $mysqli->query($query)
	or die(mysqli_error($mysqli));
	$mysqli->close();
	$_SESSION['user_logged'] = "";
	$_SESSION['user_password'] = "";
	header("Refresh: 2; URL=admin_area.php");
	echo "Account has been deleted! " .
"You are being sent to the admin area.<br>";
	echo "(If your browser doesn't support this, " .
"<a href=\"admin_area.php\">click here</a>)";
	die();
} else {
	?>
<h1>
	Admin Area
</h1>
<p>
	Are you sure you want to delete this user's account?<br /> There is no
	way to retrieve account once you confirm!<br />
</p>
<form name="delete_user" id="delete_user"
	action="<?php echo $_SERVER['$PHP_SELF']; ?>" method="post">
	<fieldset>
		<input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" />
		<div class="buttons">
			<input type="submit" name="submit" value="Yes" /> <input
				type="button" value=" No " onclick="history.go(-1);" />
		</div>
	</fieldset>
</form>
	<?php
}
include "./footer_admin.php";
?>