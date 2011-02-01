<?
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
//prevents that browser doesn't cache
header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header ("Cache-Control: no-cache, must-revalidate");
header ("Pragma: no-cache");
header("content-type: application/x-javascript; charset=tis-620");
$data=$_GET['data'];
$val=$_GET['val'];
if ($data=='person') {
	echo "<select name='person' id='person'
onChange=\"dochange('brand', this.value); dochange('model', '');\">\n";
	echo "<option value=''>Select</option>\n";
	if($mysqli->multi_query('CALL GetPersons()')) {
		do {
			$result = $mysqli->store_result();
			if($result) {
				while ($row = $result->fetch_array()) {
					echo "<option value='" .$row['Person']. "'>";
					echo $row['Person'];
					echo "</option>\n";
				}
				$result->close();
			}
		} while($mysqli->next_result());
	}
} else if ($data=='brand') {
	echo "<select name='brand' id='brand' style='min-width:100px'
onChange=\"dochange('model', this.value)\">\n";
	echo "<option value=''>Select</option>\n";
	$_SESSION['person'] = $val;
	if($mysqli->multi_query('CALL GetOwnedEquipmentBrands("'.$val.'")')) {
		do {
			$result = $mysqli->store_result();
			if($result) {
				while ($row = $result->fetch_array()) {
					echo "<option value='" .$row['EquipmentBrand']. "'>";
					echo $row['EquipmentBrand'];
					echo "</option>\n";
				}
				$result->close();
			}
		} while($mysqli->next_result());
	}
} else if ($data=='model') {
	echo "<select name='model' id='model' style='min-width:100px'>\n";
	echo "<option value=''>Select</option>\n";
	if($mysqli->multi_query("CALL GetOwnedEquipmentModelsByBrand(
'".$_SESSION['person']."', '".$val."')")) {
	do {
		$result = $mysqli->store_result();
		if($result) {
			while ($row = $result->fetch_array()) {
				echo "<option value='" .$row['EquipmentModel']. "'>";
				echo $row['EquipmentModel'];
				echo "</option>\n";
			}
			$result->close();
		}
	} while($mysqli->next_result());
}
}
echo "</select>\n";
?>