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
include("./php_functions/stored_procedure_tools.php");
print('<body onload="document.edit_coach.coach.focus()">');
include "./middle.php";
if (isset($_POST['submit_edit'])) {
	$_SESSION['coach_edit'] = $mysqli->real_escape_string($_POST['coach']);
	$_SESSION['url'] = $mysqli->real_escape_string($_POST['url']);
}
//execute if submit button is pressed
if (isset($_POST['submit']) && $_POST['submit'] == "Submit") {
	//check that mandatory fields are not empty
	if (!empty($_POST['coach']) && !empty($_POST['tel'])) {
		$coach = $mysqli->real_escape_string($_POST['coach']);
		$query = "CALL IsCoach('$coach') ";
		if($mysqli->multi_query($query)) {
			do {
				$result = $mysqli->store_result();
				if($result) {
					$rows = $result->num_rows;
					$result->close();
				}
			} while($mysqli->next_result());
		}
		function IsSameCoach() {
			global $coach;
			$comp1 = strcmp($coach, $_SESSION['coach_edit']);
			if ($comp1 == 0) {
				return true;
			} else {
				return false;
			}
		}
		/* check if the chosen coach is already in the database.
		 * primary key fields can be same while editing.
		 */
		if ($rows != 0 && IsSameCoach() == false) {
			?>
<h2>
	Edit coach
</h2>
<p>
	<b><i>Same coach found from the database.<br /> Please choose another.
	</i> </b>
</p>
<form name="edit_coach" id="edit_coach"
	action="<?php echo $_SERVER['$PHP_SELF']; ?>" method="post"
	onreset="formReset(this); return false;">
	<fieldset>
		<label for="coach"> Coach: <input type="text" name="coach" id="coach"
			value="<?php echo $_POST['coach'] ?>" style="width: 250px" /> * </label>
		<label for="tel"> Tel: <input type="text" name="tel" id="tel"
			value="<?php echo $_POST['tel'] ?>" style="width: 200px" /> * </label>
		<label for="team"> Team: <select name="team" id="tel">
				<option value="<?php echo $_POST['team'] ?>">
				<?php echo $_POST['team'] ?>
				</option>
				<option value="">
					Select
				</option>
				<?php
				if($mysqli->multi_query('CALL GetTeams()')) {
					do {
						$result = $mysqli->store_result();
						if($result) {
							while ($row = $result->fetch_array()) {
								?>
				<option value="<?php echo
$row['Team']; ?>">
<?php echo $row['Team']; ?>
				</option>
				<?php
							}
							$result->close();
						}
					} while($mysqli->next_result());
				}
				?>

		</select> </label>
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
			//set fields to null if empty.
			//otherwise place post_variable as string.
			$team = prepare_optional($mysqli->real_escape_string($_POST['team']));
			$tel = $mysqli->real_escape_string($_POST['tel']);
			$query = "CALL EditCoach('" .$_SESSION['coach_edit']. "',
'$coach', '$tel', $team)";
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
	Edit coach
</h2>
<p>
	<b><i>Coach and Tel fields are required.</i> </b>
</p>
<form name="edit_coach" id="edit_coach"
	action="<?php echo $_SERVER['$PHP_SELF']; ?>" method="post"
	onreset="formReset(this); return false;">
	<fieldset>
		<label for="coach"> Coach: <input type="text" name="coach" id="coach"
			value="<?php echo $_POST['coach'] ?>" style="width: 250px" /> * </label>
		<label for="tel"> Tel: <input type="text" name="tel" id="tel"
			value="<?php echo $_POST['tel'] ?>" style="width: 200px" /> * </label>
		<label for="team"> Team: <select name="team" id="team">
				<option value="<?php echo $_POST['team'] ?>">
				<?php echo $_POST['team'] ?>
				</option>
				<option value="">
					Select
				</option>
				<?php
				if($mysqli->multi_query('CALL GetTeams()')) {
					do {
						$result = $mysqli->store_result();
						if($result) {
							while ($row = $result->fetch_array()) {
								?>
				<option value="<?php echo $row['Team'];
?>">
<?php echo $row['Team']; ?>
				</option>
				<?php
							}
							$result->close();
						}
					} while($mysqli->next_result());
				}
				?>
		</select> </label>
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
	$query = "CALL GetCoach('" .$_SESSION['coach_edit']. "')";
	//get old values and save to variables
	if($mysqli->multi_query($query)) {
		do {
			$result = $mysqli->store_result();
			if($result) {
				$row = $result->fetch_array();
				$coach_old = $row['Coach'];
				$tel_old = $row['Tel'];
				$team_old = $row['Team'];
				$result->close();
			}
		} while($mysqli->next_result());
	}
	?>
<h2>
	Edit coach
</h2>
<form name="edit_coach" id="edit_coach"
	action="<?php echo $_SERVER['$PHP_SELF']; ?>" method="post"
	onreset="formReset(this); return false;">
	<fieldset>
		<label for="coach"> Coach: <input type="text" name="coach" id="coach"
			value="<?php echo $coach_old; ?>" style="width: 250px" /> * </label>
		<label for="tel"> Tel: <input type="text" name="tel" id="tel"
			value="<?php echo $tel_old; ?>" style="width: 200px" /> * </label> <label
			for="team"> Team: <select name="team" id="team">
				<option value="<?php echo $team_old; ?>">
				<?php echo $team_old; ?>
				</option>
				<option value="">
					Select
				</option>
				<?php
				if($mysqli->multi_query('CALL GetTeams()')) {
					do {
						$result = $mysqli->store_result();
						if($result) {
							while ($row = $result->fetch_array()) {
								?>
				<option value="<?php echo $row['Team']; ?>">
				<?php echo $row['Team']; ?>
				</option>
				<?php
							}
							$result->close();
						}
					} while($mysqli->next_result());
				}
				?>
		</select> </label>
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