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
include "./auth_user.inc.php";
include "./conn_sportlog.inc.php";
$brand = $mysqli->real_escape_string($_GET["brand"]);
$query = "CALL GetSpecificEquipmentModels('" .$brand. "')";
$result = $mysqli->query($query)
or die(mysqli_error($mysqli));
?>
Equipment model:
<select name="equipment_model">
<?php
while($row = $result->fetch_array()) {
	?>
	<option value="<?php echo $row['Model'] ?>">
	<?php echo $row['Model'] ?>
	</option>
	<?php
}
?>
</select>
<?php
$mysqli->close();
?>