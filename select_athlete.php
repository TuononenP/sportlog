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
include "./conn_sportlog.inc.php";
include("./php_functions/redirect.php");
include "./middle.php";
//execute if select button pressed
if (isset($_POST['submit']) && $_POST['submit'] == "Select") {
	$_SESSION['selected_athlete'] = $mysqli->real_escape_string($_POST['athlete']);
	$_SESSION['select_all'] = 0;
	redirect("./logged_user.php", 301);
} else {
	?>
<div class="margin_select_athlete">
	<h2>
		Select athlete
	</h2>
	<?php
	$result = $mysqli->query("CALL GetPersons()")
	or die(mysqli_error($mysqli));
	?>
	<!-- select user from the list-->
	<form name="select_athlete" id="select_athlete"
		action="<?php echo $_SERVER['$PHP_SELF']; ?>" method="post">
		<fieldset>
			<select name="athlete" id="athlete">
			<?php
			while ($row = $result->fetch_array()) {
				?>
				<option value="<?php echo $row['Person']; ?>">
				<?php echo $row['Person']; ?>
				</option>
				<?php
			}
			$result->close();
			?>
			</select>
			<div class="buttons">
				<input type="submit" name="submit" id="submit" value="Select" />
			</div>
		</fieldset>
	</form>
</div>
<?php
}
include "./footer.php";
$mysqli->close();
?>