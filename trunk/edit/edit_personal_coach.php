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
print ('<body onload="document.edit_personal_coach.person.focus()">');
include "./middle.php";
if (isset($_POST['submit_edit'])) {
	$_SESSION['coach_edit'] = $mysqli->real_escape_string($_POST['coach']);
	$_SESSION['person_edit'] = $mysqli->real_escape_string($_POST['person']);
	$_SESSION['url'] = $mysqli->real_escape_string($_POST['url']);
}
//execute if submit button is pressed
if (isset($_POST['submit']) && $_POST['submit'] == "Submit") {
	//check that mandatory fields are not empty
	if (!empty($_POST['person']) && !empty($_POST['coach'])) {
		$person = $mysqli->real_escape_string($_POST['person']);
		$coach = $mysqli->real_escape_string($_POST['coach']);
		$query = "CALL IsPersonalCoach('$person', '$coach') ";
		if($mysqli->multi_query($query)) {
			do {
				$result = $mysqli->store_result();
				if($result) {
					$rows = $result->num_rows;
					$result->close();
				}
			} while($mysqli->next_result());
		}
		function IsSamePersonalCoach() {
			global $coach, $person;
			$comp1 = strcmp($coach, $_SESSION['coach_edit']);
			$comp2 = strcmp($person, $_SESSION['person_edit']);
			if ($comp1 == 0 && $comp2 == 0) {
				return true;
			} else {
				return false;
			}
		}
		//check if the chosen person-coach combination is already in the database
		if ($rows != 0 && IsSamePersonalCoach() == false) {
			?>
<h2>
	Edit personal coach
</h2>
<p>
	<b><i>Chosen person (<?php echo $_POST['person'] ?>) has already coach
			(<?php echo $_POST['coach'] ?>).<br /> Please choose another
			combination. </i> </b>
</p>
<form name="edit_personal_coach" id="edit_personal_coach"
	action="<?php echo $_SERVER['$PHP_SELF']; ?>" method="post"
	onreset="formReset(this); return false;">
	<fieldset>
		<label for="person"> Person: <select name="person">
				<option value="<?php echo $_POST['person']; ?>">
				<?php echo $_POST['person']; ?>
				</option>
				<option value="">
					Select
				</option>
				<?php
				if($mysqli->multi_query('Call GetPersons()')) {
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
		
		</select> * </label> <label for="coach"> Coach: <select name="coach">
				<option value="<?php echo $_POST['coach']; ?>">
				<?php echo $_POST['coach']; ?>
				</option>
				<option value="">
					Select
				</option>
				<?php
				if($mysqli->multi_query('CALL GetCoaches()')) {
					do {
						$result = $mysqli->store_result();
						if($result) {
							while ($row = $result->fetch_array()) {
								?>
				<option value="<?php echo
$row['Coach']; ?>">
<?php echo $row['Coach']; ?>
				</option>
				<?php
							}
							$result->close();
						}
					} while($mysqli->next_result());
				}
				?>
		</select> * </label>
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
			$query = "CALL EditPersonalCoach('" .$_SESSION['person_edit']. "',
'" .$_SESSION['coach_edit']. "', '$person', '$coach')";
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
	}
} else {
	$query = "CALL IsPersonalCoach('" .$_SESSION['person_edit']. "',
'" .$_SESSION['coach_edit']. "') ";
	if($mysqli->multi_query($query)) {
		do {
			$result = $mysqli->store_result();
			if($result) {
				$person_old = $_SESSION['person_edit'];
				$coach_old = $_SESSION['coach_edit'];
				$result->close();
			}
		} while($mysqli->next_result());
	}
	?>
<h2>
	Edit personal coach
</h2>
<form name="edit_personal_coach" id="edit_personal_coach"
	action="<?php echo $_SERVER['$PHP_SELF']; ?>" method="post"
	onreset="formReset(this); return false;">
	<fieldset>
		<label for="person"> Person: <select name="person">
				<option value="<?php echo $person_old ?>">
				<?php echo $person_old ?>
				</option>
				<?php
				if($mysqli->multi_query('Call GetPersons()')) {
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
		</select> * </label> <label for="coach"> Coach: <select name="coach">
				<option value="<?php echo $coach_old ?>">
				<?php echo $coach_old ?>
				</option>
				<?php
				if($mysqli->multi_query('CALL GetCoaches()')) {
do {
$result = $mysqli->store_result();
if($result) {
while ($row = $result->fetch_array()) {
?>
				<option value="<?php echo $row['Coach']; ?>">
					<?php echo $row['Coach']; ?>
				</option>
				<?php
}
$result->close();
}
} while($mysqli->next_result());
}
?>
		</select> * </label>
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