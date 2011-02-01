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
print('<body onload="document.add_match.match.focus()">');
include "./middle.php";
//execute if submit button is pressed
if (isset($_POST['submit']) && $_POST['submit'] == "Submit") {
	/*
	 * check that mandatory fields are not empty and home and
	 * guest teams are not the same.
	 */
	if (!empty($_POST['match']) && !empty($_POST['year']) &&
	!empty($_POST['month']) && !empty($_POST['day']) &&
	(!empty($_POST['hours']) || !empty($_POST['minutes']) ||
	!empty($_POST['seconds'])) && !empty($_POST['sport'])
	&& !empty($_POST['city']) && !empty($_POST['country'])
	&& !empty($_POST['place']) && !empty($_POST['home_team'])
	&& !empty($_POST['guest_team'])
	&& $_POST['home_team']!=$_POST['guest_team']) {
		//form date
		//escape strings
		$year = $mysqli->real_escape_string($_POST['year']);
		$month = $mysqli->real_escape_string($_POST['month']);
		$day = $mysqli->real_escape_string($_POST['day']);
		//form date format
		$date = "{$year}-{$month}-{$day}";
		$date = "'$date'";
		$match = $mysqli->real_escape_string($_POST['match']);
		$query = "CALL IsMatch('$match', $date) ";
		if($mysqli->multi_query($query)) {
			do {
				$result = $mysqli->store_result();
				if($result) {
					$rows = $result->num_rows;
					$result->close();
				}
			} while($mysqli->next_result());
		}
		/* check if a match with same match name and date is
		 * already in the database.
		 */
		if ($rows != 0) { //match with same values found
			?>
<h2>
	Add new exercise
</h2>
<p>
	<b><i>Same match found from the database. </i> </b>
</p>
<form name="add_match" id="add_match"
	action="<?php echo $_SERVER['$PHP_SELF']; ?>" method="post"
	onreset="formReset(this); return false;">
	<fieldset>
		<label for="match"> Match: <input type="text" name="match" id="match"
			value="<?php echo $_POST['match'] ?>" style="width: 250px" /> * </label>
		<label for="date"> Date: <select name="month" id="month">
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
				<?php echo $_POST['minutes'] ?>
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
				if($mysqli->multi_query("CALL GetSports()")) {
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

		</select> * </label> <label for="home_team"> Home Team: <select
			name="home_team" id="home_team">
				<option value="<?php echo $_POST['home_team'] ?>">
				<?php echo $_POST['home_team'] ?>
				</option>
				<option value="">
					Select
				</option>
				<?php
				if($mysqli->multi_query("CALL GetTeams()")) {
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

		</select> * </label> <label for="guest_team"> Guest Team: <select
			name="guest_team" id="guest_team">
				<option value="<?php echo $_POST['guest_team'] ?>">
				<?php echo $_POST['guest_team'] ?>
				</option>
				<option value="">
					Select
				</option>
				<?php
				if($mysqli->multi_query("CALL GetTeams()")) {
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

		</select> * </label> <label for="city"> City: <input type="text"
			name="city" id="city" value="<?php echo $_POST['city'] ?>" /> * </label>
		<label for="country"> Country: <input type="text" name="country"
			id="country" value="<?php echo $_POST['country'] ?>" /> * </label> <label
			for="place"> Place: <input type="text" name="place" id="place"
			value="<?php echo $_POST['place'] ?>" style="width: 250px" /> * </label>
		<label for="description"> Description: <br /> <textarea
				name="description" id="description" rows="5" cols="40">
				<?php echo $_POST['description'] ?>
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
		} else { //insert new match to the database
			/* If time's hours, minutes or seconds field is not selected
			 * the variable in question gets value zero. Otherwise MatchTime saved to
			 * the MySQL database would be incorrect. For instance one hour would become
			 * one second. That's because MySQL time format ignores white spaces.
			 */
			$hours = prepare_timedate($mysqli->real_escape_string($_POST['hours']));
			$minutes = prepare_timedate($mysqli->real_escape_string($_POST['minutes']));
			$seconds = prepare_timedate($mysqli->real_escape_string($_POST['seconds']));
			//form time
			$time = "{$hours}:{$minutes}:{$seconds}";
			$time = "'$time'";
			$description = prepare_optional($mysqli->real_escape_string(
			$_POST['description']));
			$sport = $mysqli->real_escape_string($_POST['sport']);
			$city = $mysqli->real_escape_string($_POST['city']);
			$country =$mysqli->real_escape_string($_POST['country']);
			$place = $mysqli->real_escape_string($_POST['place']);
			$query = "CALL InsertMatch('$match', $date, $time, '$sport',
'$city', '$country', '$place', $description)";
			if($mysqli->multi_query($query)) {
				do {
					$result = $mysqli->store_result();
					if($result) {
						$result->close();
					}
				} while($mysqli->next_result());
			}
			$home_team = $mysqli->real_escape_string($_POST['home_team']);
			$query = "CALL InsertHomeTeam('$home_team', '$match', $date) ";
			if($mysqli->multi_query($query)) {
				do {
					$result = $mysqli->store_result();
					if($result) {
						$result->close();
					}
				} while($mysqli->next_result());
			}
			$guest_team = $mysqli->real_escape_string($_POST['guest_team']);
			$query = "CALL InsertGuestTeam('$guest_team', '$match', $date) ";
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
	Add new match
</h2>
<p>
<?php
if ($_POST['home_team']==$_POST['guest_team']) {
	print "<b><i>Home team and Guest team can't be the same!</i></b>";
} else {
	print "<b><i>Match name, Date, Time, Sport, City,<br />
Country and Place fields are required.</i></b>";
}
?>
</p>
<form name="add_match" id="add_match"
	action="<?php echo $_SERVER['$PHP_SELF']; ?>" method="post"
	onreset="formReset(this); return false;">
	<fieldset>
		<label for="match"> Match: <input type="text" name="match" id="match"
			value="<?php echo $_POST['match'] ?>" style="width: 250px" /> * </label>
		<label for="date"> Date: <select name="month" id="month">
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
				<?php echo $_POST['minutes'] ?>
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
				if($mysqli->multi_query("CALL GetSports()")) {
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
		</select> * </label> <label for="home_team"> Home Team: <select
			name="home_team" id="home_team">
				<option value="<?php echo $_POST['home_team'] ?>">
				<?php echo $_POST['home_team'] ?>
				</option>
				<option value="">
					Select
				</option>
				<?php
				if($mysqli->multi_query("CALL GetTeams()")) {
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
		</select> * </label> <label for="guest_team"> Guest Team: <select
			name="guest_team" id="guest_team">
				<option value="<?php echo $_POST['guest_team'] ?>">
				<?php echo $_POST['guest_team'] ?>
				</option>
				<option value="">
					Select
				</option>
				<?php
				if($mysqli->multi_query("CALL GetTeams()")) {
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
		</select> * </label> <label for="city"> City: <input type="text"
			name="city" id="city" value="<?php echo $_POST['city'] ?>" /> * </label>
		<label for="country"> Country: <input type="text" name="country"
			id="country" value="<?php echo $_POST['country'] ?>" /> * </label> <label
			for="place"> Place: <input type="text" name="place" id="place"
			value="<?php echo $_POST['place'] ?>" style="width: 250px" /> * </label>
		<label for="description"> Description: <br /> <textarea
				name="description" id="description" rows="5" cols="40">
				<?php echo $_POST['description'] ?>
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
<h2>
	Add new match
</h2>
<form name="add_match" id="add_match"
	action="<?php echo $_SERVER['$PHP_SELF']; ?>" method="post"
	onreset="formReset(this); return false;">
	<fieldset>
		<label for="match"> Match: <input type="text" name="match" id="match"
			style="width: 250px" /> * </label> <label for="date"> Date: <select
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
				if($mysqli->multi_query("CALL GetSports()")) {
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
		</select> * </label> <label for="home_team"> Home Team: <select
			name="home_team" id="home_team">
				<option value="">
					Select
				</option>
				<?php
				if($mysqli->multi_query("CALL GetTeams()")) {
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
		</select> * </label> <label for="guest_team"> Guest Team: <select
			name="guest_team" id="guest_team">
				<option value="">
					Select
				</option>
				<?php
				if($mysqli->multi_query("CALL GetTeams()")) {
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
		</select> * </label> <label for="city"> City: <input type="text"
			name="city" id="city" /> * </label> <label for="country"> Country: <input
			type="text" name="country" id="country" /> * </label> <label
			for="place"> Place: <input type="text" name="place" id="place"
			style="width: 250px" /> * </label> <label for="description">
			Description: <br /> <textarea name="description" id="description"
				rows="5" cols="40"></textarea> </label>
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