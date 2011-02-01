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
print('<body onload="document.edit_equipment.brand.focus()">');
include "./middle.php";
if (isset($_POST['submit_edit'])) {
	$_SESSION['brand_edit'] = $mysqli->real_escape_string($_POST['brand']);
	$_SESSION['model_edit'] = $mysqli->real_escape_string($_POST['model']);
	$_SESSION['url'] = $mysqli->real_escape_string($_POST['url']);
}
//execute if submit button is pressed
if (isset($_POST['submit']) && $_POST['submit'] == "Submit") {
	//check that mandatory fields are not empty
	if (!empty($_POST['brand']) && !empty($_POST['model']) && !empty($_POST['sport']))
	{
		$brand = $mysqli->real_escape_string($_POST['brand']);
		$model = $mysqli->real_escape_string($_POST['model']);
		$query = "CALL IsEquipment('$brand', '$model') ";
		if($mysqli->multi_query($query)) {
			do {
				$result = $mysqli->store_result();
				if($result) {
					$rows = $result->num_rows;
					$result->close();
				}
			} while($mysqli->next_result());
		}
		function IsSameEquipment() {
			global $brand, $model;
			$comp1 = strcmp($brand, $_SESSION['brand_edit']);
			$comp2 = strcmp($model, $_SESSION['model_edit']);
			if ($comp1 == 0 && $comp2 == 0) {
				return true;
			} else {
				return false;
			}
		}
		/* check if the chosen equipment is already in the database.
		 * primary key fields can be same while editing.
		 */
		if ($rows != 0 && IsSameEquipment() == false) {
			?>
<h2>
	Edit equipment
</h2>
<p>
	<b><i>Same equipment found from the database.<br /> Please choose
			another. </i> </b>
</p>
<form name="edit_equipment" id="edit_equipment"
	action="<?php echo $_SERVER['$PHP_SELF']; ?>" method="post"
	onreset="formReset(this); return false;">
	<fieldset>
		<label for="brand"> Brand: <input type="text" name="brand" id="brand"
			value="<?php echo $_POST['brand'] ?>" style="width: 250px" /> * </label>
		<label for="model"> Model: <input type="text" name="model" id="model"
			value="<?php echo $_POST['model'] ?>" style="width: 250px" /> * </label>
		<label for="sport"> Sport: <select name="sport" id="sport">
				<option value="<?php echo $_POST['sport'] ?>">
				<?php echo $_POST['sport'] ?>
				</option>
				<option value="">
					Select
				</option>
				<?php
				if($mysqli->multi_query('Call GetSports()')) {
					do {
						$result = $mysqli->store_result();
						if($result) {
							while ($row = $result->fetch_array()) {
								?>
				<option value="
<?php echo
$row['Sport']; ?>">
<?php echo $row['Sport']; ?></option>
<?php
							}
							$result->close();
						}
					} while($mysqli->next_result());
				}
				?>
		
		</select> * </label> <label for="notes"> Notes: <textarea name="notes"
				id="notes" rows="3" cols="30">
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
		} else {
			//set fields to null if empty.
			//otherwise place post_variable as string.
			$notes = prepare_optional($mysqli->real_escape_string($_POST['notes']));
			$sport = $mysqli->real_escape_string($_POST['sport']);
			$query = "CALL EditEquipment('" .$_SESSION['brand_edit']. "',
'" .$_SESSION['model_edit']. "', '$brand', '$model',
'$sport', $notes)";
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
	Edit equipment
</h2>
<p>
	<b><i>Brand, Model and Sport fields are required.</i> </b>
</p>
<form name="edit_equipment" id="edit_equipment"
	action="<?php echo $_SERVER['$PHP_SELF']; ?>" method="post"
	onreset="formReset(this); return false;">
	<fieldset>
		<label for="brand"> Brand: <input type="text" name="brand" id="brand"
			value="<?php echo $_POST['brand'] ?>" style="width: 250px" /> * </label>
		<label for="model"> Model: <input type="text" name="model" id="model"
			value="<?php echo $_POST['model'] ?>" style="width: 250px" /> * </label>
		<label for="sport"> Sport: <select name="sport" id="sport">
				<option value="<?php echo $_POST['sport'] ?>">
				<?php echo $_POST['sport'] ?>
				</option>
				<option value="">
					Select
				</option>
				<?php
				if($mysqli->multi_query('Call GetSports()')) {
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
		</select> * </label> <label for="notes"> Notes: <textarea name="notes"
				id="notes" rows="3" cols="30">
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
	$query = "CALL GetEquipment('" .$_SESSION['brand_edit']. "',
'" .$_SESSION['model_edit']. "')";
	//get old values and save to variables
	if($mysqli->multi_query($query)) {
		do {
			$result = $mysqli->store_result();
			if($result) {
				$row = $result->fetch_array();
				$brand_old = $row['Brand'];
				$model_old = $row['Model'];
				$sport_old = $row['Sport'];
				$notes_old = $row['Notes'];
				$result->close();
			}
		} while($mysqli->next_result());
	}
	?>
<h2>
	Edit equipment
</h2>
<form name="edit_equipment" id="edit_equipment"
	action="<?php echo $_SERVER['$PHP_SELF']; ?>" method="post"
	onreset="formReset(this); return false;">
	<fieldset>
		<label for="brand"> Brand: <input type="text" name="brand" id="brand"
			value="<?php echo $brand_old; ?>" style="width: 250px" /> * </label>
		<label for="model"> Model: <input type="text" name="model" id="model"
			value="<?php echo $model_old; ?>" style="width: 250px" /> * </label>
		<label for="sport"> Sport: <select name="sport" id="sport">
				<option value="<?php echo $sport_old; ?>">
				<?php echo $sport_old; ?>
				</option>
				<option value="">
					Select
				</option>
				<?php
				if($mysqli->multi_query('Call GetSports()')) {
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
		</select> * </label> <label for="notes"> Notes: <textarea name="notes"
				id="notes" rows="3" cols="30">
				<?php echo $notes_old; ?>
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