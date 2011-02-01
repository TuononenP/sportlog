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
print('<body onload="document.add_sport.sport.focus()">');
include "./middle.php";
//execute if submit button is pressed
if (isset($_POST['submit']) && $_POST['submit'] == "Submit") {
	//check that mandatory fields are not empty
	if (!empty($_POST['sport'])) {
		$sport = $mysqli->real_escape_string($_POST['sport']);
		$query = "CALL IsSport('$sport') ";
		if($mysqli->multi_query($query)) {
			do {
				$result = $mysqli->store_result();
				if($result) {
					$rows = $result->num_rows;
					$result->close();
				}
			} while($mysqli->next_result());
		}
		//check if the chosen sport is already in the database
		if ($rows != 0) {
			?>
<h2>
	Add new sport
</h2>
<p>
	<b><i>Same sport found from the database.<br /> Please choose another.
	</i> </b>
</p>
<form name="add_sport" id="add_sport"
	action="<?php echo $_SERVER['$PHP_SELF']; ?>" method="post"
	onreset="formReset(this); return false;">
	<fieldset>
		<label for="sport"> Sport: <input type="text" name="sport" id="sport" />
			* </label> <label for="description"> Description: <br /> <textarea
				name="description" id="description" rows="3" cols="30">
				<?php echo trim($_POST['description']) ?>
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
			//set optional fields to null if empty.
			//otherwise place post_variable as string.
			$description = prepare_optional($mysqli->real_escape_string(
			$_POST['description']));
			$query = "CALL InsertSport('$sport', $description)";
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
	Add new sport
</h2>
<p>
	<b><i>Sport field is required.</i> </b>
</p>
<form name="add_sport" id="add_sport"
	action="<?php echo $_SERVER['$PHP_SELF']; ?>" method="post"
	onreset="formReset(this); return false;">
	<fieldset>
		<label for="sport"> Sport: <input type="text" name="sport" id="sport" />
			* </label> <label for="description"> Description: <br /> <textarea
				name="description" id="description" rows="3" cols="30">
				<?php echo trim($_POST['description']) ?>
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
	Add new sport
</h2>
<form name="add_sport" id="add_sport"
	action="<?php echo $_SERVER['$PHP_SELF']; ?>" method="post"
	onreset="formReset(this); return false;">
	<fieldset>
		<label for="sport"> Sport: <input type="text" name="sport" id="sport" />
			* </label> <label for="description"> Description: <br /> <textarea
				name="description" id="description" rows="3" cols="30"></textarea> </label>
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