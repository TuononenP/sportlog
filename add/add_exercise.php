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
print('<body onload="document.add_exercise.person.focus()">');
include "./middle.php";
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
		$date = "'$date'";
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
		$time = "'$time'";
		$person = $mysqli->real_escape_string($_POST['person']);
		$query = "CALL IsExercise('$person', $date, $time) ";
		if($mysqli->multi_query($query)) {
			do {
				$result = $mysqli->store_result();
				if($result) {
					$rows = $result->num_rows;
					$result->close();
				}
			} while($mysqli->next_result());
		}
		/* check if an exercise with same person, date, time is
		 * already in the database. These values combine T_Exercises
		 * * table's primary key.
		 */
		if ($rows != 0) { //exercise with same values found
			if (empty($_POST['person']) || empty($_POST['brand']) ||
			empty($_POST['model'])) {
				?>
<script type="text/javascript">
<!--
window.onLoad=dochange('person', -1);
//-->
</script>
				<?php
			}
			?>
<h2>
	Add new exercise
</h2>
<p>
	<b><i>Same exercise found from the database.<br /> Please choose
			another. </i> </b>
</p>
<form name="add_exercise" id="add_exercise"
	action="<?php echo $_SERVER['$PHP_SELF']; ?>" method="post"
	onreset="formReset(this); return false;">
	<fieldset>
		<label for='person'> Person: <font id=person> <select name="person"
				id="person">
					<option value="<?php echo $_POST['person'] ?>">
					<?php echo $_POST['person'] ?>
					</option>
			</select> *</font> </label> <label for="date"> Date: <select
			name="month" id="month">
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
<?php echo $row['Sport']; ?></option>
<?php
							}
							$result->close();
						}
					} while($mysqli->next_result());
				}
				?>
		
		</select> * </label> <label for='brand'> Equipment brand: <font
			id=brand> <select name="brand" id="brand">
					<option value="<?php echo $_POST['brand'] ?>">
					<?php echo $_POST['brand'] ?>
					</option>
			</select> </font> </label> <label for='model'> Equipment model: <font
			id=model> <select name="model" id="model">
					<option value="<?php echo $_POST['model'] ?>">
					<?php echo $_POST['model'] ?>
					</option>
			</select> </font> </label> <label for="location"> Location: <input
			type="text" name="location" id="location"
			value="<?php echo $_POST['location'] ?>" style="width: 200px" /> </label>
		<label for="distance"> Distance (km): <input type="text"
			name="distance" id="distance"
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
			$query = "CALL InsertExercise('$person', $date, $time,
'$sport', $location, $distance, $duration, $calories, $avg_hr,
			$temperature, $equipment_brand, $equipment_model, $notes)";
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
		if (empty($_POST['person']) || empty($_POST['brand']) ||
		empty($_POST['model'])) {
			?>
<script type="text/javascript">
<!--
window.onLoad=dochange('person', -1);
//-->
</script>
			<?php
		}
		?>
<h2>
	Add new exercise
</h2>
<p>
	<b><i>Person, Date, Time and Sport fields are required.</i> </b>
</p>
<form name="add_exercise" id="add_exercise"
	action="<?php echo $_SERVER['$PHP_SELF']; ?>" method="post"
	onreset="formReset(this); return false;">
	<fieldset>
		<label for='person'> Person: <font id=person> <select name="person"
				id="person">
					<option value="<?php echo $_POST['person'] ?>">
					<?php echo $_POST['person'] ?>
					</option>
			</select> *</font> </label> <label for="date"> Date: <select
			name="month" id="month">
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
		</select> * </label> <label for='brand'> Equipment brand: <font
			id=brand> <select name="brand" id="brand">
					<option value="<?php echo $_POST['brand'] ?>">
					<?php echo $_POST['brand'] ?>
					</option>
			</select> </font> </label> <label for='model'> Equipment model: <font
			id=model> <select name="model" id="model">
					<option value="<?php echo $_POST['model'] ?>">
					<?php echo $_POST['model'] ?>
					</option>
			</select> </font> </label> <label for="location"> Location: <input
			type="text" name="location" id="location"
			value="<?php echo $_POST['location'] ?>" style="width: 200px" /> </label>
		<label for="distance"> Distance (km): <input type="text"
			name="distance" id="distance"
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
	?>
<script type="text/javascript">
<!--
window.onLoad=dochange('person', -1);
//-->
</script>
<h2>
	Add new exercise
</h2>
<form name="add_exercise" id="add_exercise"
	action="<?php echo $_SERVER['$PHP_SELF']; ?>" method="post"
	onreset="formReset(this); return false;">
	<fieldset>
		<label for='person'> Person: <font id=person> <select name="person"
				id="person">
					<option value=''>
						Select
					</option>
			</select> *</font> </label> <label for="date"> Date: <select
			name="month" id="month">
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
		</select> * </label> <label for='brand'> Equipment brand: <font
			id=brand> <select name="brand" id="brand">
					<option value=''></option>
			</select> </font> </label> <label for='model'> Equipment model: <font
			id=model> <select name="model" id="model">
					<option value=''></option>
			</select> </font> </label> <label for="location"> Location: <input
			type="text" name="location" id="location" style="width: 200px" /> </label>
		<label for="distance"> Distance (km): <input type="text"
			name="distance" id="distance" style="width: 100px" /> </label> <label
			for="duration"> Duration: <select name="duration_hours"
			id="duration_hours">
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
			type="text" name="calories" id="calories" style="width: 100px" /> </label>
		<label for="avg_hr"> Average heartrate: <input type="text"
			name="avg_hr" id="avg_hr" style="width: 50px" /> </label> <label
			for="temperature"> Temperature (&#8451;): <input type="text"
			name="temperature" id="temperature" style="width: 50px" /> </label> <label
			for="notes"> Notes: <br /> <textarea name="notes" id="notes" rows="5"
				cols="40"></textarea> </label>
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