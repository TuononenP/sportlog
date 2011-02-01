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
print ('<body onload="document.edit_athlete.person.focus()">');
include "./middle.php";
if (isset($_POST['submit_edit'])) {
	$_SESSION['person_edit'] = $mysqli->real_escape_string($_POST['person']);
	$_SESSION['url'] = $mysqli->real_escape_string($_POST['url']);
}
//execute if submit button is pressed
if (isset($_POST['submit']) && $_POST['submit'] == "Submit") {
	//check that mandatory fields are not empty
	if (!empty($_POST['person'])) {
		$person = $mysqli->real_escape_string($_POST['person']);
		$query = "CALL IsPerson('$person')";
		if($mysqli->multi_query($query)) {
			do {
				$result = $mysqli->store_result();
				if($result) {
					$rows = $result->num_rows;
					$result->close();
				}
			} while($mysqli->next_result());
		}
		function IsSameAthlete() {
			global $person;
			$comp1 = strcmp($person, $_SESSION['person_edit']);
			if ($comp1 == 0) {
				return true;
			} else {
				return false;
			}
		}
		//check if the chosen name is already taken. It can be the same old name.
		if ($rows != 0 && IsSameAthlete() == false) {
			?>
<h2>
	Edit athlete
</h2>
<p>
	<b><i>Same athlete found from the database.<br /> Please choose
			another. </i> </b>
</p>
<form name="edit_athlete" id="edit_athlete"
	action="<?php echo $_SERVER['$PHP_SELF']; ?>" method="post"
	onreset="formReset(this); return false;">
	<fieldset>
		<label for="person"> Person: <input type="text" name="person"
			id="person" style="width: 250px" /> * </label> <label for="weight">
			Weight: <input type="text" name="weight" id="weight"
			value="<?php echo $_POST['weight']; ?>" /> </label> <label
			for="height"> Height: <input type="text" name="height" id="height"
			value="<?php echo $_POST['height']; ?>" /> </label> <label
			for="birthdate"> Birthdate: <select name="month" id="month">
				<option value="<?php echo $_POST['month']; ?>">
				<?php echo $_POST['month']; ?>
				</option>
				<option value="">
					month
				</option>
				<?php
				for ($i=1; $i<13; $i++) {
					?>
				<option value="<?php echo $i; ?>">
				<?php echo $i; ?>
				</option>
				<?php
				}
				?>
		</select> <select name="day" id="day">
				<option value="<?php echo $_POST['day']; ?>">
				<?php echo $_POST['day']; ?>
				</option>
				<option value="">
					day
				</option>
				<?php
				for ($i=1; $i<32; $i++) {
					?>
				<option value="<?php echo $i; ?>">
				<?php echo $i; ?>
				</option>
				<?php
				}
				?>
		</select> <select name="year" id="year">
				<option value="<?php echo $_POST['year']; ?>">
				<?php echo $_POST['year']; ?>
				</option>
				<option value="">
					year
				</option>
				<?php
				for ($i=1900; $i<=date('Y'); $i++) {
					?>
				<option value="<?php echo $i; ?>">
				<?php echo $i; ?>
				</option>
				<?php
				}
				?>
		</select> </label> <label for="rest_hr"> Resting heartrate: <input
			type="text" name="rest_hr" id="rest_hr"
			value="<?php echo $_POST['rest_hr']; ?>" /> </label> <label
			for="max_hr"> Max heartrate: <input type="text" name="max_hr"
			id="max_hr" value="<?php echo $_POST['max_hr']; ?>" /> </label> <label
			for="blood_pres_sys"> Blood pressure systolic: <input type="text"
			name="blood_pres_sys" id="blood_pres_sys"
			value="<?php echo $_POST['blood_pres_sys']; ?>" /> </label> <label
			for="blood_pres_dias"> Blood pressure diastolic: <input type="text"
			name="blood_pres_dias" id="blood_pres_dias"
			value="<?php echo $_POST['blood_pres_dias']; ?>" /> </label> <label
			for="team"> Team: <select name="team" id="team">
				<option value="<?php echo $_POST['team']; ?>">
				<?php echo $_POST['team']; ?>
				</option>
				<option value="">
					-------
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
			//create birthdate as date format if day, month and year is chosen.
			if (!empty($_POST['month']) && !empty($_POST['day'])
			&& !empty($_POST['year'])) {
				$birthdate = $mysqli->real_escape_string($_POST['year']) . "-" .
				$mysqli->real_escape_string($_POST['month']) .
"-" . $mysqli->real_escape_string($_POST['day']);
				$birthdate = "'$birthdate'";
			} else {
				$birthdate = "NULL";
			}
			//set fields to null if empty.
			//otherwise place post_variable as string.
			$weight = prepare_optional($mysqli->real_escape_string($_POST['weight']));
			$height = prepare_optional($mysqli->real_escape_string($_POST['height']));
			$rest_hr = prepare_optional($mysqli->real_escape_string($_POST['rest_hr']));
			$max_hr = prepare_optional($mysqli->real_escape_string($_POST['max_hr']));
			$blood_pres_sys = prepare_optional($mysqli->real_escape_string(
			$_POST['blood_pres_sys']));
			$blood_pres_dias = prepare_optional($mysqli->real_escape_string(
			$_POST['blood_pres_dias']));
			$team = prepare_optional($mysqli->real_escape_string($_POST['team']));
			$query = "CALL EditPerson('" .$_SESSION['person_edit']. "',
'$person', $weight, $height, $birthdate, $rest_hr, $max_hr,
$blood_pres_sys, $blood_pres_dias, $team)";
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
	Edit athlete
</h2>
<p>
	<b>The person name is required!</b>
</p>
<form name="edit_athlete" id="edit_athlete"
	action="<?php echo $_SERVER['$PHP_SELF']; ?>" method="post"
	onreset="formReset(this); return false;">
	<fieldset>
		<label for="person"> Person: <input type="text" name="person"
			id="person" style="width: 250px" /> * </label> <label for="weight">
			Weight: <input type="text" name="weight" id="weight"
			value="<?php echo $_POST['weight']; ?>" /> </label> <label
			for="height"> Height: <input type="text" name="height" id="height"
			value="<?php echo $_POST['height']; ?>" /> </label> <label
			for="birthdate"> Birthdate: <select name="month" id="month">
				<option value="<?php echo $_POST['month']; ?>">
				<?php echo $_POST['month']; ?>
				</option>
				<option value="">
					month
				</option>
				<?php
				for ($i=1; $i<13; $i++) {
					?>
				<option value="<?php echo $i; ?>">
				<?php echo $i; ?>
				</option>
				<?php
				}
				?>
		</select> <select name="day" id="day">
				<option value="<?php echo $_POST['day']; ?>">
				<?php echo $_POST['day']; ?>
				</option>
				<option value="">
					day
				</option>
				<?php
				for ($i=1; $i<32; $i++) {
					?>
				<option value="<?php echo $i; ?>">
				<?php echo $i; ?>
				</option>
				<?php
				}
				?>
		</select> <select name="year" id="year">
				<option value="<?php echo $_POST['year']; ?>">
				<?php echo $_POST['year']; ?>
				</option>
				<option value="">
					year
				</option>
				<?php
				for ($i=1900; $i<=date('Y'); $i++) {
					?>
				<option value="<?php echo $i; ?>">
				<?php echo $i; ?>
				</option>
				<?php
				}
				?>
		</select> </label> <label for="rest_hr"> Resting heartrate: <input
			type="text" name="rest_hr" id="rest_hr"
			value="<?php echo $_POST['rest_hr']; ?>" /> </label> <label
			for="max_hr"> Max heartrate: <input type="text" name="max_hr"
			id="max_hr" value="<?php echo $_POST['max_hr']; ?>" /> </label> <label
			for="blood_pres_sys"> Blood pressure systolic: <input type="text"
			name="blood_pres_sys" id="blood_pres_sys"
			value="<?php echo $_POST['blood_pres_sys']; ?>" /> </label> <label
			for="blood_pres_dias"> Blood pressure diastolic: <input type="text"
			name="blood_pres_dias" id="blood_pres_dias"
			value="<?php echo $_POST['blood_pres_dias']; ?>" /> </label> <label
			for="team"> Team: <select name="team" id="team">
				<option value="<?php echo $_POST['team']; ?>">
				<?php echo $_POST['team']; ?>
				</option>
				<option value="">
					-------
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
	$query = "CALL GetOnePersonInfo('" .$_SESSION['person_edit']. "')";
	//get old values and save to variables
	if($mysqli->multi_query($query)) {
		do {
			$result = $mysqli->store_result();
			if($result) {
				$row = $result->fetch_array();
				$person_old = $row['Person'];
				$weight_old = $row['Weight'];
				$height_old = $row['Height'];
				$birthdate_old = $row['Birthdate'];
				$month_old = substr($birthdate_old, 5, 2);
				$day_old = substr($birthdate_old, 8, 2);
				$year_old = substr($birthdate_old, 0, 4);
				$resthr_old = $row['RestHR'];
				$maxhr_old = $row['MaxHR'];
				$blood_pres_sys_old = $row['Blood Pressure Systolic'];
				$blood_pres_dias_old = $row['Blood Pressure Diastolic'];
				$team_old = $row['Team'];
				$result->close();
			}
		} while($mysqli->next_result());
	}
	?>
<h2>
	Edit athlete
</h2>
<form name="edit_athlete" id="edit_athlete"
	action="<?php echo $_SERVER['$PHP_SELF']; ?>" method="post"
	onreset="formReset(this); return false;">
	<fieldset>
		<label for="person"> Person: <input type="text" name="person"
			id="person" value="<?php echo $person_old ?>" style="width: 250px" />
			* </label> <label for="weight"> Weight: <input type="text"
			name="weight" id="weight" value="<?php echo $weight_old ?>" /> </label>
		<label for="height"> Height: <input type="text" name="height"
			id="height" value="<?php echo $height_old ?>" /> </label> <label
			for="birthdate"> Birthdate: <select name="month" id="month">
				<option value="<?php echo $month_old ?>">
				<?php echo $month_old ?>
				</option>
				<option value="">
					month
				</option>
				<?php
				for ($i=1; $i<13; $i++) {
					?>
				<option value="<?php echo $i; ?>">
				<?php echo $i; ?>
				</option>
				<?php
				}
				?>
		</select> <select name="day" id="day">
				<option value="<?php echo $day_old ?>">
				<?php echo $day_old ?>
				</option>
				<option value="">
					day
				</option>
				<?php
				for ($i=1; $i<32; $i++) {
					?>
				<option value="<?php echo $i; ?>">
				<?php echo $i; ?>
				</option>
				<?php
				}
				?>
		</select> <select name="year" id="year">
				<option value="<?php echo $year_old ?>">
				<?php echo $year_old ?>
				</option>
				<option value="">
					year
				</option>
				<?php
				for ($i=1900; $i<=date('Y'); $i++) {
					?>
				<option value="<?php echo $i; ?>">
				<?php echo $i; ?>
				</option>
				<?php
				}
				?>
		</select> </label> <label for="rest_hr"> Resting heartrate: <input
			type="text" name="rest_hr" id="rest_hr"
			value="<?php echo $resthr_old ?>" /> </label> <label for="max_hr">
			Max heartrate: <input type="text" name="max_hr" id="max_hr"
			value="<?php echo $maxhr_old ?>" /> </label> <label
			for="blood_pres_sys"> Blood pressure systolic: <input type="text"
			name="blood_pres_sys" id="blood_pres_sys"
			value="<?php echo $blood_pres_sys_old ?>" /> </label> <label
			for="blood_pres_dias"> Blood pressure diastolic: <input type="text"
			name="blood_pres_dias" id="blood_pres_dias"
			value="<?php echo $blood_pres_dias_old ?>" /> </label> <label
			for="team"> Team: <select name="team" id="team">
				<option value="<?php echo $team_old ?>">
				<?php echo $team_old ?>
				</option>
				<option value="">
					-------
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