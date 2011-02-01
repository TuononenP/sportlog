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
print ('<body onload="document.edit_team.team.focus()">');
include "./middle.php";
if (isset($_POST['submit_edit'])) {
	$_SESSION['team_edit'] = $mysqli->real_escape_string($_POST['team']);
	$_SESSION['url'] = $mysqli->real_escape_string($_POST['url']);
}
//execute if submit button is pressed
if (isset($_POST['submit']) && $_POST['submit'] == "Submit") {
	//check that mandatory fields are not empty
	if (!empty($_POST['team']) && !empty($_POST['city']) && !empty($_POST['country']))
	{
		$team = $mysqli->real_escape_string($_POST['team']);
		$query = "CALL IsTeam('$team')";
		if($mysqli->multi_query($query)) {
			do {
				$result = $mysqli->store_result();
				if($result) {
					$rows = $result->num_rows;
					$result->close();
				}
			} while($mysqli->next_result());
		}
		function IsSameTeam() {
			global $team;
			$comp1 = strcmp($team, $_SESSION['team_edit']);
			if ($comp1 == 0) {
				return true;
			} else {
				return false;
			}
		}
		//check if the chosen team is already in the database
		if ($rows != 0 && IsSameTeam() == false) {
			?>
<h2>
	Edit team
</h2>
<p>
	<b><i>Same team found from the database.<br /> Please choose another. </i>
	</b>
</p>
<form name="edit_team" id="edit_team"
	action="<?php echo $_SERVER['$PHP_SELF']; ?>" method="post"
	onreset="formReset(this); return false;">
	<fieldset>
		<label for="team"> Team: <input type="text" name="team" id="team"
			value="<?php echo $_POST['team'] ?>" style="width: 200px" /> * </label>
		<label for="city"> City: <input type="text" name="city" id="city"
			value="<?php echo $_POST['city'] ?>" /> * </label> <label
			for="country"> Country: <input type="text" name="country"
			id="country" value="<?php echo $_POST['country'] ?>" /> * </label>
		<div class="buttons">
			<input type="submit" name="submit" id="submit" value="Submit" /> <input
				type="reset" value="Clear" />
		</div>
		<div class="req_fields_text">
			* Required fields
		</div>
	</fieldset>
</form>
			<?php
		} else {
			$city = $mysqli->real_escape_string($_POST['city']);
			$country = $mysqli->real_escape_string($_POST['country']);
			$query = "CALL EditTeam('" .$_SESSION['team_edit']. "',
'$team', '$city', '$country')";
			if($mysqli->multi_query($query)) {
				do {
					$result = $mysqli->store_result();
					if($result) {
						$result->close();
					}
				} while($mysqli->next_result());
			}
			redirect($_SESSION['url'], 301);
		}
	} else {
		?>
<h2>
	Edit team
</h2>
<p>
	<b><i>Team, City and Country fields are required.</i> </b>
</p>
<form name="edit_team" id="edit_team"
	action="<?php echo $_SERVER['$PHP_SELF']; ?>" method="post"
	onreset="formReset(this); return false;">
	<fieldset>
		<label for="team"> Team: <input type="text" name="team" id="team"
			value="<?php echo $_POST['team'] ?>" style="width: 200px" /> * </label>
		<label for="city"> City: <input type="text" name="city" id="city"
			value="<?php echo $_POST['city'] ?>" /> * </label> <label
			for="country"> Country: <input type="text" name="country"
			id="country" value="<?php echo $_POST['country'] ?>" /> * </label>
		<div class="buttons">
			<input type="submit" name="submit" id="submit" value="Submit" /> <input
				type="reset" value="Clear" />
		</div>
		<div class="req_fields_text">
			* Required fields
		</div>
	</fieldset>
</form>
		<?php
	}
} else {
	$query = "CALL GetTeamInfo('" .$_SESSION['team_edit']. "')";
	//get old values and save to variables
	if($mysqli->multi_query($query)) {
		do {
			$result = $mysqli->store_result();
			if($result) {
				$row = $result->fetch_array();
				$team_old = $row['Team'];
				$city_old = $row['City'];
				$country_old = $row['Country'];
				$result->close();
			}
		} while($mysqli->next_result());
	}
	?>
<h2>
	Edit team
</h2>
<form name="edit_team" id="edit_team"
	action="<?php echo $_SERVER['$PHP_SELF']; ?>" method="post"
	onreset="formReset(this); return false;">
	<fieldset>
		<label for="team"> Team: <input type="text" name="team" id="team"
			value="<?php echo $team_old; ?>" style="width: 200px" /> * </label> <label
			for="city"> City: <input type="text" name="city" id="city"
			value="<?php echo $city_old; ?>" /> * </label> <label for="country">
			Country: <input type="text" name="country" id="country"
			value="<?php echo $country_old; ?>" /> * </label>
		<div class="buttons">
			<input type="submit" name="submit" id="submit" value="Submit" /> <input
				type="reset" value="Clear" />
		</div>
		<div class="req_fields_text">
			* Required fields
		</div>
	</fieldset>
</form>
<?php
}
include "./footer.php";
$mysqli->close();
?>