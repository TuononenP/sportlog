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
print('<body onload="document.add_owned_equipment.person.focus()">');
include "./middle.php";
//execute if submit button is pressed
if (isset($_POST['submit']) && $_POST['submit'] == "Submit") {
	//check that mandatory fields are not empty
	if (!empty($_POST['person']) && !empty($_POST['equipment_brand']) &&
	!empty($_POST['equipment_model']) ) {
		$person = $mysqli->real_escape_string($_POST['person']);
		$person = "'$person'";
		$brand = $mysqli->real_escape_string($_POST['equipment_brand']);
		$brand = "'$brand'";
		$model = $mysqli->real_escape_string($_POST['equipment_model']);
		$model = "'$model'";
		$query = "CALL IsOwnedEquipment($person, $brand, $model)";
		if($mysqli->multi_query($query)) {
			do {
				$result = $mysqli->store_result();
				if($result) {
					$rows = $result->num_rows;
					$result->close();
				}
			} while($mysqli->next_result());
		}
		if ($rows != 0) {
			?>
<h2>
	Add new owned equipment
</h2>
<p>
	<b><i>Equipment is already owned by <?php echo $_POST['person'] ?><br />
	</i> </b>
</p>
<form name="add_owned_equipment" id="add_owned_equipment"
	action="<?php echo $_SERVER['$PHP_SELF']; ?>" method="post"
	onreset="formReset(this); return false;">
	<fieldset>
		<label for="person"> Person: <select name="person" id="person">
				<option value="<?php echo $_POST['person'] ?>">
				<?php echo $_POST['person'] ?>
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

		</select> * </label>
		<div id="models">
			<label for="equipment_model"> Equipment model: <select
				name="equipment_model" id="equipment_model" style="width: 85px">
					<option value="<?php echo $_POST['equipment_model'] ?>">
					<?php echo $_POST['equipment_model'] ?>
					</option>
					<option value=""></option>
			</select> * </label>
		</div>
		<label for="total_use"> Total Use: <input type="text" name="total_use"
			id="total_use" value="<?php echo $_POST['total_use'] ?>" /> </label>
		<label for="expected_lifetime"> Expected Lifetime: <input type="text"
			name="expected_lifetime" id="expected_lifetime"
			value="<?php echo $_POST['expected_lifetime'] ?>" /> </label> <label
			for="notes"> Notes: <textarea name="notes" id="notes" rows="3"
				cols="30">
				<?php echo $_POST['notes'] ?>
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
		} else {
			//set fields to null if empty.
			//otherwise place post_variable as string.
			$total_use = prepare_optional($mysqli->real_escape_string(
			$_POST['total_use']));
			//in this case total use can't be NULL otherwise update trigger doesn't work
			if ($total_use == "NULL") {
				$total_use = 0;
			}
			$expected_lifetime = prepare_optional($mysqli->real_escape_string(
			$_POST['expected_lifetime']));
			$notes = prepare_optional($mysqli->real_escape_string($_POST['notes']));
			$query = "CALL InsertOwnedEquipment($person, $brand, $model,
			$total_use, $expected_lifetime, $notes)";
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
	Add new owned equipment
</h2>
<p>
	<b>The person name, brand and model is required!</b>
</p>
<form name="add_owned_equipment" id="add_owned_equipment"
	action="<?php echo $_SERVER['$PHP_SELF']; ?>" method="post"
	onreset="formReset(this); return false;">
	<fieldset>
		<label for="person"> Person: <select name="person" id="person">
				<option value="<?php echo $_POST['person'] ?>">
				<?php echo $_POST['person'] ?>
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
		</select> * </label>
		<div id="models">
			<label for="equipment_model"> Equipment model: <select
				name="equipment_model" id="equipment_model" style="width: 85px">
					<option value="<?php echo $_POST['equipment_model'] ?>">
					<?php echo $_POST['equipment_model'] ?>
					</option>
					<option value=""></option>
			</select> * </label>
		</div>
		<label for="total_use"> Total Use: <input type="text" name="total_use"
			id="total_use" value="<?php echo $_POST['total_use'] ?>" /> </label>
		<label for="expected_lifetime"> Expected Lifetime: <input type="text"
			name="expected_lifetime" id="expected_lifetime"
			value="<?php echo $_POST['expected_lifetime'] ?>" /> </label> <label
			for="notes"> Notes: <textarea name="notes" id="notes" rows="3"
				cols="30">
				<?php echo $_POST['notes'] ?>
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
	Add new owned equipment
</h2>
<form name="add_owned_equipment" id="add_owned_equipment"
	action="<?php echo $_SERVER['$PHP_SELF']; ?>" method="post"
	onreset="formReset(this); return false;">
	<fieldset>
		<label for="person"> Person: <select name="person" id="person">
		<?php
		if ($_SESSION['select_all'] == 0) {
			?>
				<option value="<?php echo $_SESSION['selected_athlete'] ?>">
				<?php echo $_SESSION['selected_athlete'] ?>
				</option>
				<?php
		} else {
			print ('<option value="">Select</option>');
		}
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
		</select> * </label> <label for="equipment_brand"> Equipment brand: <select
			name="equipment_brand" id="equipment_brand"
			onchange="show_models(this.value)">
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
		</select> * </label>
		<div id="models">
			<label for="equipment_model"> Equipment model: <select
				name="equipment_model" id="equipment_model" style="width: 85px">
					<option value=""></option>
			</select> * </label>
		</div>
		<label for="total_use"> Total Use: <input type="text" name="total_use"
			id="total_use" /> </label> <label for="expected_lifetime"> Expected
			Lifetime: <input type="text" name="expected_lifetime"
			id="expected_lifetime" /> </label> <label for="notes"> Notes: <textarea
				name="notes" id="notes" rows="3" cols="30"></textarea> </label>
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
include("./footer.php");
$mysqli->close();
?>

}
