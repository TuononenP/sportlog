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
print ('<body onload="document.edit_exercise.person.focus()">');
include "./middle.php";
if (isset($_POST['submit_edit'])) {
	$_SESSION['person_edit'] = $mysqli->real_escape_string($_POST['person']);
	$_SESSION['date_edit'] = $mysqli->real_escape_string($_POST['date']);
	$_SESSION['time_edit'] = $mysqli->real_escape_string($_POST['time']);
	$_SESSION['url'] = $mysqli->real_escape_string($_POST['url']);
}
//execute if submit button is pressed
if (isset($_POST['submit']) && $_POST['submit'] == "Submit") {
	//check that mandatory fields are not empty
	//it is enough that one of hours, minutes or seconds fields is selected
	if (!empty($_POST['person']) && !empty($_POST['year']) &&
	!empty($_POST['month']) && !empty($_POST['day']) &&
	(!empty($_POST['hours']) || !empty($_POST['minutes']) ||
	!empty($_POST['seconds'])) && !empty($_POST['sport'])) {
		//form date
		//escape strings
		$year = $mysqli->real_escape_string($_POST['year']);
		$month = $mysqli->real_escape_string($_POST['month']);
		$day = $mysqli->real_escape_string($_POST['day']);
		//form date format
		$date = "{$year}-{$month}-{$day}";
		/* If ExerciseTime's hours, minutes or seconds field is not selected
		 * the variable in question gets value zero. Otherwise ExerciseTime saved to
		 * the MySQL database would be incorrect. For instance one hour would become
		 * one second. That's because MySQL time format ignores white spaces.
		 */
		$hours = prepare_timedate($mysqli->real_escape_string($_POST['hours']));
		$minutes = prepare_timedate($mysqli->real_escape_string($_POST['minutes']));
		$seconds = prepare_timedate($mysqli->real_escape_string($_POST['seconds']));
		//form time
		$time = "{$hours}:{$minutes}:{$seconds}";
		$person = $mysqli->real_escape_string($_POST['person']);
		$query = "CALL IsExercise('$person', '$date', '$time') ";
		if($mysqli->multi_query($query)) {
			do {
				$result = $mysqli->store_result();
				if($result) {
					$rows = $result->num_rows;
					$result->close();
				}
			} while($mysqli->next_result());
		}
		//Compare former and new strings for person, date and time fields.
		//It ensures that we are just editing exercise, not creating new.
		function IsSameExercise() {
			global $person, $date, $time;
			$comp1 = strcmp($person, $_SESSION['person_edit']);
			$comp2 = strcmp($date, $_SESSION['date_edit']);
			$comp2 = strcmp($time, $_SESSION['time_edit']);
			if ($comp1 == 0 && $comp2 == 0 && $comp3 == 0) {
				return true;
			} else {
				return false;
			}
		}
		/* check if an exercise with same person, date, time is
		 * already in the database. These values combine T_Exercises
		 * table's primary key. Primary key fields can be same while
		 * editing.
		 */
		if ($rows != 0 && IsSameExercise() == false) {
			?>
<h2>
	Edit exercise
</h2>
<p>
	<b><i>Same exercise found from the database.<br /> Please choose
			another. </i> </b>
</p>
<form name="edit_exercise" id="edit_exercise"
	action="<?php echo $_SERVER['$PHP_SELF']; ?>" method="post"
	onreset="formReset(this); return false;">
	<fieldset>
		<label for="person"> Person: <select name="person" id="person">
				<option value="<?php echo $_POST['person'] ?>">
				<?php echo $_POST['person'] ?>
				</option>
				<option value="">
					Select
				</option>
				<?php
				if($mysqli->multi_query('CALL GetPersons()')) {
					do {
						$result = $mysqli->store_result();
						if($result) {
							while ($row = $result->fetch_array()) {
								?>
				<option value="<?php echo
$row['Person']; ?>">
<?php echo $row['Person']; ?>
				</option>
				<?php
							}
							$result->close();
						}
					} while($mysqli->next_result());
				}
				?>

		</select> * </label> <label for="date"> Date: <select name="month"
			id="month">
				<option value="<?php echo $_POST['month'] ?>">
				<?php echo $_POST['month'] ?>
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
				<option value="<?php echo $_POST['day'] ?>">
				<?php echo $_POST['day'] ?>
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
				<option value="<?php echo $_POST['year'] ?>">
				<?php echo $_POST['year'] ?>
				</option>
				<option value="">
					year
				</option>
				<?php
				for ($i=date('Y'); $i>=1990; $i--) {
					?>
				<option value="<?php echo $i; ?>">
				<?php echo $i; ?>
				</option>
				<?php
				}
				?>
		</select> * </label> <label for="time"> Time: <select name="hours"
			id="hours">
				<option value="<?php echo $_POST['hours'] ?>">
				<?php echo $_POST['hours'] ?>
				</option>
				<option value="">
					hours
				</option>
				<?php
				for ($i=0; $i<24; $i++) {
					?>
				<option value="<?php echo $i; ?>">
				<?php echo $i; ?>
				</option>
				<?php
				}
				?>
		</select> <select name="minutes" id="minutes">
				<option value="<?php echo $_POST['minutes'] ?>">
				<?php echo $_POST['month'] ?>
				</option>
				<option value="">
					min
				</option>
				<?php
				for ($i=0; $i<60; $i++) {
					?>
				<option value="<?php echo $i; ?>">
				<?php echo $i; ?>
				</option>
				<?php
				}
				?>
		</select> <select name="seconds" id="seconds">
				<option value="<?php echo $_POST['seconds'] ?>">
				<?php echo $_POST['seconds'] ?>
				</option>
				<option value="">
					sec
				</option>
				<?php
				for ($i=0; $i<60; $i++) {
					?>
				<option value="<?php echo $i; ?>">
				<?php echo $i; ?>
				</option>
				<?php
				}
				?>
		</select> * </label> <label for="sport"> Sport: <select name="sport"
			id="sport">
				<option value="<?php echo $_POST['sport'] ?>">
				<?php echo $_POST['sport'] ?>
				</option>
				<option value="">
					Select
				</option>
				<?php
				if($mysqli->multi_query('CALL GetSports()')) {
					do {
						$result = $mysqli->store_result();
						if($result) {
							while ($row = $result->fetch_array()) {
								?>
				<option value="<?php echo
$row['Sport']; ?>">
<?php echo $row['Sport']; ?>
				</option>
				<?php
							}
							$result->close();
						}
					} while($mysqli->next_result());
				}
				?>

		</select> * </label> <label for="equipment_brand"> Equipment brand: <select
			name="equipment_brand" id="equipment_brand"
			onchange="show_models(this.value)">
				<option value="<?php echo $_POST['equipment_brand'] ?>">
				<?php echo $_POST['equipment_brand'] ?>
				</option>
				<option value="">
					-----
				</option>
				<?php
				if($mysqli->multi_query('CALL GetEquipmentBrands()')) {
					do {
						$result = $mysqli->store_result();
						if($result) {
							while ($row = $result->fetch_array()) {
								?>
				<option value="<?php echo
$row['Brand']; ?>">
<?php echo $row['Brand']; ?>
				</option>
				<?php
							}
							$result->close();
						}
					} while($mysqli->next_result());
				}
				?>

		</select> </label>
		<div id="models">
			<label for="equipment_model"> Equipment model: <select
				name="equipment_model" id="equipment_model" style="width: 85px">
					<option value="<?php echo $_POST['equipment_model'] ?>">
					<?php echo $_POST['equipment_model'] ?>
					</option>
			</select> </label>
		</div>
		<label for="location"> Location: <input type="text" name="location"
			id="location" value="<?php echo $_POST['location'] ?>"
			style="width: 200px" /> </label> <label for="distance"> Distance
			(km): <input type="text" name="distance" id="distance"
			value="<?php echo $_POST['distance'] ?>" style="width: 100px" /> </label>
		<label for="duration"> Duration: <select name="duration_hours"
			id="duration_hours">
				<option value="<?php echo $_POST['duration_hours'] ?>">
				<?php echo $_POST['duration_hours'] ?>
				</option>
				<option value="">
					hours
				</option>
				<?php
				for ($i=0; $i<25; $i++) {
					?>
				<option value="<?php echo $i; ?>">
				<?php echo $i; ?>
				</option>
				<?php
				}
				?>
		</select> <select name="duration_minutes" id="duration_minutes">
				<option value="<?php echo $_POST['duration_minutes'] ?>">
				<?php echo $_POST['duration_minutes'] ?>
				</option>
				<option value="">
					min
				</option>
				<?php
				for ($i=0; $i<60; $i++) {
					?>
				<option value="<?php echo $i; ?>">
				<?php echo $i; ?>
				</option>
				<?php
				}
				?>
		</select> <select name="duration_seconds" id="duration_seconds">
				<option value="<?php echo $_POST['duration_seconds'] ?>">
				<?php echo $_POST['duration_seconds'] ?>
				</option>
				<option value="">
					sec
				</option>
				<?php
				for ($i=0; $i<60; $i++) {
					?>
				<option value="<?php echo $i; ?>">
				<?php echo $i; ?>
				</option>
				<?php
				}
				?>
		</select> </label> <label for="calories"> Calories (kcal): <input
			type="text" name="calories" id="calories"
			value="<?php echo $_POST['calories'] ?>" style="width: 100px" /> </label>
		<label for="avg_hr"> Average heartrate: <input type="text"
			name="avg_hr" id="avg_hr" value="<?php echo $_POST['heartrate'] ?>"
			style="width: 50px" /> </label> <label for="temperature"> Temperature
			(&#8451;): <input type="text" name="temperature" id="temperature"
			value="<?php echo $_POST['temperature'] ?>" style="width: 50px" /> </label>
		<label for="notes"> Notes: <br /> <textarea name="notes" id="notes"
				rows="5" cols="40">
				<?php echo trim($_POST['notes']) ?>
			</textarea> </label>
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
		} else { //insert new exercise to the database
			//form duration
			//if duration's hours, minutes or seconds field is not empty
			if(!empty($_POST['duration_hours']) || !empty($_POST['duration_minutes'])
			|| !empty($_POST['duration_seconds'])) {
				//place '00' to empty fields.
				$duration_hours = prepare_timedate($mysqli->real_escape_string(
				$_POST['duration_hours']));
				$duration_minutes = prepare_timedate($mysqli->real_escape_string(
				$_POST['duration_minutes']));
				$duration_seconds = prepare_timedate($mysqli->real_escape_string(
				$_POST['duration_seconds']));
				//escape strings
				//form time format
				$duration = "{$duration_hours}:{$duration_minutes}:{$duration_seconds}";
				$duration = "'$duration'";
			} else {
				//set NULL if all fields are empty
				$duration = "NULL";
			}
			//set optional fields to null if empty.
			//otherwise place post_variable as string.
			$location = prepare_optional($mysqli->real_escape_string(
			$_POST['location']));
			$distance = prepare_optional($mysqli->real_escape_string(
			$_POST['distance']));
			$calories = prepare_optional($mysqli->real_escape_string(
			$_POST['calories']));
			$avg_hr = prepare_optional($mysqli->real_escape_string(
			$_POST['avg_hr']));
			$temperature = prepare_optional($mysqli->real_escape_string(
			$_POST['temperature']));
			$equipment_brand = prepare_optional($mysqli->real_escape_string(
			$_POST['equipment_brand']));
			$equipment_model = prepare_optional($mysqli->real_escape_string(
			$_POST['equipment_model']));
			$notes = prepare_optional($mysqli->real_escape_string($_POST['notes']));
			$sport = $mysqli->real_escape_string($_POST['sport']);
			$query = "CALL EditExercise('" .$_SESSION['person_edit']. "',
'" .$_SESSION['date_edit']. "', '" .$_SESSION['time_edit']. "',
'$person', '$date', '$time', '$sport', $location, $distance, $duration,
$calories, $avg_hr, $temperature, $equipment_brand, $equipment_model,
$notes)";
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
	Edit exercise
</h2>
<p>
	<b><i>Person, Date, Time and Sport fields are required.</i> </b>
</p>
<form name="edit_exercise" id="edit_exercise"
	action="<?php echo $_SERVER['$PHP_SELF']; ?>" method="post"
	onreset="formReset(this); return false;">
	<fieldset>
		<label for="person"> Person: <select name="person" id="person">
				<option value="<?php echo $_POST['person'] ?>">
				<?php echo $_POST['person'] ?>
				</option>
				<option value="">
					Select
				</option>
				<?php
				if($mysqli->multi_query('CALL GetPersons()')) {
					do {
						$result = $mysqli->store_result();
						if($result) {
							while ($row = $result->fetch_array()) {
								?>
				<option value="<?php echo
$row['Person']; ?>">
<?php echo $row['Person']; ?>
				</option>
				<?php
							}
							$result->close();
						}
					} while($mysqli->next_result());
				}
				?>
		</select> * </label> <label for="date"> Date: <select name="month"
			id="month">
				<option value="<?php echo $_POST['month'] ?>">
				<?php echo $_POST['month'] ?>
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
				<option value="<?php echo $_POST['day'] ?>">
				<?php echo $_POST['day'] ?>
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
				<option value="<?php echo $_POST['year'] ?>">
				<?php echo $_POST['year'] ?>
				</option>
				<option value="">
					year
				</option>
				<?php
				for ($i=date('Y'); $i>=1990; $i--) {
					?>
				<option value="<?php echo $i; ?>">
				<?php echo $i; ?>
				</option>
				<?php
				}
				?>
		</select> * </label> <label for="time"> Time: <select name="hours"
			id="hours">
				<option value="<?php echo $_POST['hours'] ?>">
				<?php echo $_POST['hours'] ?>
				</option>
				<option value="">
					hours
				</option>
				<?php
				for ($i=0; $i<24; $i++) {
					?>
				<option value="<?php echo $i; ?>">
				<?php echo $i; ?>
				</option>
				<?php
				}
				?>
		</select> <select name="minutes" id="minutes">
				<option value="<?php echo $_POST['minutes'] ?>">
				<?php echo $_POST['month'] ?>
				</option>
				<option value="">
					min
				</option>
				<?php
				for ($i=0; $i<60; $i++) {
					?>
				<option value="<?php echo $i; ?>">
				<?php echo $i; ?>
				</option>
				<?php
				}
				?>
		</select> <select name="seconds" id="seconds">
				<option value="<?php echo $_POST['seconds'] ?>">
				<?php echo $_POST['seconds'] ?>
				</option>
				<option value="">
					sec
				</option>
				<?php
				for ($i=0; $i<60; $i++) {
					?>
				<option value="<?php echo $i; ?>">
				<?php echo $i; ?>
				</option>
				<?php
				}
				?>
		</select> * </label> <label for="sport"> Sport: <select name="sport"
			id="sport">
				<option value="<?php echo $_POST['sport'] ?>">
				<?php echo $_POST['sport'] ?>
				</option>
				<option value="">
					Select
				</option>
				<?php
				if($mysqli->multi_query('CALL GetSports()')) {
					do {
						$result = $mysqli->store_result();
						if($result) {
							while ($row = $result->fetch_array()) {
								?>
				<option value="<?php echo $row['Sport'];
?>">
<?php echo $row['Sport']; ?>
				</option>
				<?php
							}
							$result->close();
						}
					} while($mysqli->next_result());
				}
				?>
		</select> * </label> <label for="equipment_brand"> Equipment brand: <select
			name="equipment_brand" id="equipment_brand"
			onchange="show_models(this.value)">
				<option value="<?php echo $_POST['equipment_brand'] ?>">
				<?php echo $_POST['equipment_brand'] ?>
				</option>
				<option value="">
					-----
				</option>
				<?php
				if($mysqli->multi_query('CALL GetEquipmentBrands()')) {
					do {
						$result = $mysqli->store_result();
						if($result) {
							while ($row = $result->fetch_array()) {
								?>
				<option value="<?php echo $row['Brand'];
?>">
<?php echo $row['Brand']; ?>
				</option>
				<?php
							}
							$result->close();
						}
					} while($mysqli->next_result());
				}
				?>
		</select> </label>
		<div id="models">
			<label for="equipment_model"> Equipment model: <select
				name="equipment_model" id="equipment_model" style="width: 85px">
					<option value="<?php echo $_POST['equipment_model'] ?>">
					<?php echo $_POST['equipment_model'] ?>
					</option>
			</select> </label>
		</div>
		<label for="location"> Location: <input type="text" name="location"
			id="location" value="<?php echo $_POST['location'] ?>"
			style="width: 200px" /> </label> <label for="distance"> Distance
			(km): <input type="text" name="distance" id="distance"
			value="<?php echo $_POST['distance'] ?>" style="width: 100px" /> </label>
		<label for="duration"> Duration: <select name="duration_hours"
			id="duration_hours">
				<option value="<?php echo $_POST['duration_hours'] ?>">
				<?php echo $_POST['duration_hours'] ?>
				</option>
				<option value="">
					hours
				</option>
				<?php
				for ($i=0; $i<25; $i++) {
					?>
				<option value="<?php echo $i; ?>">
				<?php echo $i; ?>
				</option>
				<?php
				}
				?>
		</select> <select name="duration_minutes" id="duration_minutes">
				<option value="<?php echo $_POST['duration_minutes'] ?>">
				<?php echo $_POST['duration_minutes'] ?>
				</option>
				<option value="">
					min
				</option>
				<?php
				for ($i=0; $i<60; $i++) {
					?>
				<option value="<?php echo $i; ?>">
				<?php echo $i; ?>
				</option>
				<?php
				}
				?>
		</select> <select name="duration_seconds" id="duration_seconds">
				<option value="<?php echo $_POST['duration_seconds'] ?>">
				<?php echo $_POST['duration_seconds'] ?>
				</option>
				<option value="">
					sec
				</option>
				<?php
				for ($i=0; $i<60; $i++) {
					?>
				<option value="<?php echo $i; ?>">
				<?php echo $i; ?>
				</option>
				<?php
				}
				?>
		</select> </label> <label for="calories"> Calories (kcal): <input
			type="text" name="calories" id="calories"
			value="<?php echo $_POST['calories'] ?>" style="width: 100px" /> </label>
		<label for="avg_hr"> Average heartrate: <input type="text"
			name="avg_hr" id="avg_hr" value="<?php echo $_POST['heartrate'] ?>"
			style="width: 50px" /> </label> <label for="temperature"> Temperature
			(&#8451;): <input type="text" name="temperature" id="temperature"
			value="<?php echo $_POST['temperature'] ?>" style="width: 50px" /> </label>
		<label for="notes"> Notes: <br /> <textarea name="notes" id="notes"
				rows="5" cols="40">
				<?php echo trim($_POST['notes']) ?>
			</textarea> </label>
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
	//get exercise data
	$query = "CALL GetExercise('" .$_SESSION['person_edit']. "',
'" .$_SESSION['date_edit']. "', '" .$_SESSION['time_edit']. "')";
	//get old values and save to variables
	if($mysqli->multi_query($query)) {
		do {
			$result = $mysqli->store_result();
			if($result) {
				$row = $result->fetch_array();
				$person_old = $row['Person'];
				$date_old = $row['Exercise Date'];
				$month_old = substr($date_old, 5, 2);
				$day_old = substr($date_old, 8, 2);
				$year_old = substr($date_old, 0, 4);
				$time_old = $row['Exercise Time'];
				$hours_old = substr($time_old, 0, 2);
				$minutes_old = substr($time_old, 3, 2);
				$seconds_old = substr($time_old, 6, 2);
				$sport_old = $row['Sport'];
				$location_old = $row['Location'];
				$distance_old = $row['Distance'];
				$duration_old = $row['Duration'];
				$calories_old = $row['Calories'];
				$avghr_old = $row['AvgHR'];
				$temp_old = $row['Temp'];
				$brand_old = $row['Equipment Brand'];
				$model_old = $row['Equipment Model'];
				$notes_old = $row['Notes'];
				$result->close();
			}
		} while($mysqli->next_result());
	}
	?>
<h2>
	Edit exercise
</h2>
<form name="edit_exercise" id="edit_exercise"
	action="<?php echo $_SERVER['$PHP_SELF']; ?>" method="post"
	onreset="formReset(this); return false;">
	<fieldset>
		<label for="person"> Person: <select name="person" id="person">
				<option value="<?php echo $person_old ?>">
				<?php echo $person_old ?>
				</option>
				<option value="">
					Select
				</option>
				<?php
				if($mysqli->multi_query('CALL GetPersons()')) {
					do {
						$result = $mysqli->store_result();
						if($result) {
							while ($row = $result->fetch_array()) {
								?>
				<option value="<?php echo $row['Person']; ?>">
				<?php echo $row['Person']; ?>
				</option>
				<?php
							}
							$result->close();
						}
					} while($mysqli->next_result());
				}
				?>
		</select> * </label> <label for="date"> Date: <select name="month"
			id="month">
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
				for ($i=date('Y'); $i>=1990; $i--) {
					?>
				<option value="<?php echo $i; ?>">
				<?php echo $i; ?>
				</option>
				<?php
				}
				?>
		</select> * </label> <label for="time"> Time: <select name="hours"
			id="hours">
				<option value="<?php echo $hours_old ?>">
				<?php echo $hours_old ?>
				</option>
				<option value="">
					hours
				</option>
				<?php
				for ($i=0; $i<24; $i++) {
					?>
				<option value="<?php echo $i; ?>">
				<?php echo $i; ?>
				</option>
				<?php
				}
				?>
		</select> <select name="minutes" id="minutes">
				<option value="<?php echo $minutes_old ?>">
				<?php echo $minutes_old ?>
				</option>
				<option value="">
					min
				</option>
				<?php
				for ($i=0; $i<60; $i++) {
					?>
				<option value="<?php echo $i; ?>">
				<?php echo $i; ?>
				</option>
				<?php
				}
				?>
		</select> <select name="seconds" id="seconds">
				<option value="<?php echo $seconds_old ?>">
				<?php echo $seconds_old ?>
				</option>
				<option value="">
					sec
				</option>
				<?php
				for ($i=0; $i<60; $i++) {
					?>
				<option value="<?php echo $i; ?>">
				<?php echo $i; ?>
				</option>
				<?php
				}
				?>
		</select> * </label> <label for="sport"> Sport: <select name="sport"
			id="sport">
				<option value="<?php echo $sport_old ?>">
				<?php echo $sport_old ?>
				</option>
				<option value="">
					Select
				</option>
				<?php
				if($mysqli->multi_query('CALL GetSports()')) {
					do {
						$result = $mysqli->store_result();
						if($result) {
							while ($row = $result->fetch_array()) {
								?>
				<option value="<?php echo $row['Sport']; ?>">
				<?php echo $row['Sport']; ?>
				</option>
				<?php
							}
							$result->close();
						}
					} while($mysqli->next_result());
				}
				?>
		</select> * </label> <label for="equipment_brand"> Equipment brand: <select
			name="equipment_brand" id="equipment_brand"
			onchange="show_models(this.value)">
				<option value="<?php echo $brand_old ?>">
				<?php echo $brand_old ?>
				</option>
				<option value="">
					-----
				</option>
				<?php
				if($mysqli->multi_query('CALL GetEquipmentBrands()')) {
					do {
						$result = $mysqli->store_result();
						if($result) {
							while ($row = $result->fetch_array()) {
								?>
				<option value="<?php echo $row['Brand']; ?>">
				<?php echo $row['Brand']; ?>
				</option>
				<?php
							}
							$result->close();
						}
					} while($mysqli->next_result());
				}
				?>
		</select> </label>
		<div id="models">
			<label for="equipment_model"> Equipment model: <select
				name="equipment_model" id="equipment_model" style="width: 85px">
					<option value="<?php echo $model_old ?>">
					<?php echo $model_old ?>
					</option>
			</select> </label>
		</div>
		<label for="location"> Location: <input type="text" name="location"
			id="location" value="<?php echo $location_old ?>"
			style="width: 200px" /> </label> <label for="distance"> Distance
			(km): <input type="text" name="distance" id="distance"
			style="width: 100px" value="<?php echo $distance_old ?>" /> </label>
		<label for="duration"> Duration: <select name="duration_hours"
			id="duration_hours">
				<option value="<?php echo $hours_old ?>">
				<?php echo $hours_old ?>
				</option>
				<option value="">
					hours
				</option>
				<?php
				for ($i=0; $i<25; $i++) {
					?>
				<option value="<?php echo $i; ?>">
				<?php echo $i; ?>
				</option>
				<?php
				}
				?>
		</select> <select name="duration_minutes" id="duration_minutes">
				<option value="<?php echo $minutes_old ?>">
				<?php echo $minutes_old ?>
				</option>
				<option value="">
					min
				</option>
				<?php
				for ($i=0; $i<60; $i++) {
					?>
				<option value="<?php echo $i; ?>">
				<?php echo $i; ?>
				</option>
				<?php
				}
				?>
		</select> <select name="duration_seconds" id="duration_seconds">
				<option value="<?php echo $seconds_old ?>">
				<?php echo $seconds_old ?>
				</option>
				<option value="">
					sec
				</option>
				<?php
				for ($i=0; $i<60; $i++) {
					?>
				<option value="<?php echo $i; ?>">
				<?php echo $i; ?>
				</option>
				<?php
				}
				?>
		</select> </label> <label for="calories"> Calories (kcal): <input
			type="text" name="calories" id="calories" style="width: 100px"
			value="<?php echo $calories_old ?>" /> </label> <label for="avg_hr">
			Average heartrate: <input type="text" name="avg_hr" id="avg_hr"
			style="width: 50px" value="<?php echo $avghr_old ?>" /> </label> <label
			for="temperature"> Temperature (&#8451;): <input type="text"
			name="temperature" id="temperature" style="width: 50px"
			value="<?php echo $temp_old ?>" /> </label> <label for="notes">
			Notes: <br /> <textarea name="notes" id="notes" rows="5" cols="40">
			<?php echo $notes_old ?>
			</textarea> </label>
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
