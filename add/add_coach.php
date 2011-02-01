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
include "./conn_sportlog.inc.php";
include("./php_functions/redirect.php");
include("./php_functions/stored_procedure_tools.php");
print('<body onload="document.add_coach.coach.focus()">');
include "./middle.php";
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
		//check if the chosen coach is already in the database
		if ($rows != 0) {
			?>
<h2>
	Add new coach
</h2>
<p>
	<b><i>Same coach found from the database.<br /> Please choose another.
	</i> </b>
</p>
<form name="add_coach" id="add_coach"
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
			$query = "CALL InsertCoach('$coach', '$tel', $team)";
			if($mysqli->multi_query($query)) {
				do {
					$result = $mysqli->store_result();
					if($result) {
						$result->close();
					}
				} while($mysqli->next_result());
			}
			redirect("./index.php", 301);
		}
	} else {
		?>
<h2>
	Add new coach
</h2>
<p>
	<b><i>Coach and Tel fields are required.</i> </b>
</p>
<form name="add_coach" id="add_coach"
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
	?>
<h2>
	Add new coach
</h2>
<form name="add_coach" id="add_coach"
	action="<?php echo $_SERVER['$PHP_SELF']; ?>" method="post"
	onreset="formReset(this); return false;">
	<fieldset>
		<label for="coach"> Coach: <input type="text" name="coach" id="coach"
			style="width: 250px" /> * </label> <label for="tel"> Tel: <input
			type="text" name="tel" id="tel" style="width: 200px" /> * </label> <label
			for="team"> Team: <select name="team" id="team">
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
}
