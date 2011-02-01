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
include "./header_show.php";
include "./auth_user.inc.php";
include "./conn_sportlog.inc.php";
print('<body>');
include "./middle.php";
$query = "CALL GetTotalDistanceAndDuration()";
$result = $mysqli->query($query)
or die(mysqli_error($mysqli));
print "<p class='show_title'>Summary</p>";
print "<table class='datatable'>";
print "<tr class='titlefields_tr'>";
$i=0;
//print field names
while ($finfo = $result->fetch_field()) {
	print "<td class='titlefields_td'>";
	$fname = $finfo->name;
	if ($fname == 'Total_Distance') {
		$fname = "Total Distance";
	}
	if ($fname == 'Total_Duration') {
		$fname = "Total Duration";
	}
	printf($fname);
	if ($fname == "Sport") {
		$sport_column = $i;
	}
	print "</td>";
	$i++;
}
print "</tr>";
$numfields = $result->field_count;
//print data rows
$j=1;
while ($row = $result->fetch_array()) {
	//define different classes for odd and even rows
	if ($j%2==0) {
		$tr_class = "class='datafields_tr_even'";
	} else {
		$tr_class = "class='datafields_tr_odd'";
	}
	print "<tr $tr_class>";
	//datafields
	for ($i=0; $i<$numfields; $i++) {
		if ($i==$sport_column && $row[$i]!="") {
			$rand_val = rand();
			$url="show_sport.php?sport=$row[$i]&sid=$rand_val";
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