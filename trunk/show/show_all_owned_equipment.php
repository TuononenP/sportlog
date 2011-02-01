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
include "./header_show.php";
include "./auth_user.inc.php";
include "./conn_sportlog.inc.php";
print('<body>');
include "./middle.php";
$query = "CALL GetAllOwnedEquipment()";
$result = $mysqli->query($query)
or die(mysqli_error($mysqli));
print "<p class='show_title'>Owned Equipment</p>";
print "<table class='datatable'>";
print "<tr class='titlefields_tr'>";
//print one empty column for edit and delete buttons
print "<td class='buttons_td'></td>";
$i=0;
//print field names
while ($finfo = $result->fetch_field()) {
	print "<td class='titlefields_td'>";
	printf($finfo->name);
	if ($finfo->name == "Person") {
		$person_column = $i;
	}
	print "</td>";
	$i++;
}
print "</tr>";
$numfields = $result->field_count;
//print data rows
$j=1;
while ($row = $result->fetch_array()) {
	if ($j%2==0) {
		$tr_class = "class='datafields_tr_even'";
	} else {
		$tr_class = "class='datafields_tr_odd'";
	}
	print "<tr $tr_class>";
	//delete and edit buttons
	if ($row['Person'] != "") {
		print "<td class='buttons_td'>";
		print "<form class='buttons_form' method = 'post'
action = './edit_owned_equipment.php'>";
		print "<input type = submit name = 'submit_edit' value = 'edit' />";
		print "<input type = 'hidden' name = 'person' value = '".$row['Person']."' />";
		print "<input type = 'hidden' name = 'brand' value = '".$row['EquipmentBrand']."' />";
		print "<input type = 'hidden' name = 'model' value = '".$row['EquipmentModel']."' />";
		print "<input type = 'hidden' name = 'url'
value = './show_all_owned_equipment.php' />";
		print "</form>";
		print "<form class='buttons_form' method = 'post'
action = './delete_owned_equipment.php'>";
		print "<input type = submit name = 'submit_del' value = 'del'
onClick='return confirmDelete()' />";
		print "<input type = 'hidden' name = 'person' value = '".$row['Person']."' />";
		print "<input type = 'hidden' name = 'brand' value = '".$row['EquipmentBrand']."' />";
		print "<input type = 'hidden' name = 'model' value = '".$row['EquipmentModel']."' />";
		print "<input type = 'hidden' name = 'url'
value = './show_all_owned_equipment.php' />";
		print "</form>";
		print "</td>";
	}
	for ($i=0; $i<$numfields; $i++) {
		if ($i==$person_column && $row[$i]!="") {
			$rand_val = rand();
			$url="show_athlete.php?person=$row[$i]&sid=$rand_val";
			print "<td class='datafields_td'><a href='$url'>$row[$i]</a></td>";
		} else {
			print "<td class='datafields_td'>$row[$i]</td>";
		}
	}
	print "</tr>";
	$j++;
}
print "</table>";
$result->close();
include "./footer.php";
$mysqli->close();
?>