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
$query = "CALL GetMatches()";
$result = $mysqli->query($query)
or die(mysqli_error($mysqli));
print "<p class='show_title'>Matches</p>";
print "<table class='datatable'>";
$i=0;
print "<tr class='titlefields_tr'>";
//print one empty column for edit and delete buttons
print "<td class='buttons_td'></td>";
//print field names
while ($finfo = $result->fetch_field()) {
	print "<td class='titlefields_td'>";
	printf($finfo->name);
	if ($finfo->name == "Sport") {
		$sport_column = $i;
	}
	if ($finfo->name == "Home Team") {
		$hteam_column = $i;
	}
	if ($finfo->name == "Guest Team") {
		$gteam_column = $i;
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
	if ($row['Match'] != "") {
		print "<td class='buttons_td'>";
		print "<form class='buttons_form' method = 'post'
action = './edit_match.php'>";
		print "<input type = submit name = 'submit_edit' value = 'edit' />";
		print "<input type = 'hidden' name = 'match' value = '".$row['Match']."' />";
		print "<input type = 'hidden' name = 'date' value = '".$row['Date']."' />";
		print "<input type = 'hidden' name = 'url' value = './show_matches.php' />";
		print "</form>";
		print "<form class='buttons_form' method = 'post'
action = './delete_match.php'>";
		print "<input type = submit name = 'submit_del' value = 'del'
onClick='return confirmDelete()' />";
		print "<input type = 'hidden' name = 'match' value = '".$row['Match']."' />";
		print "<input type = 'hidden' name = 'date' value = '".$row['Date']."' />";
		print "<input type = 'hidden' name = 'url' value = './show_matches.php' />";
		print "</form>";
		print "</td>";
	}
	for ($i=0; $i<$numfields; $i++) {
		if ($i==$sport_column && $row[$i]!="") {
			$rand_val = rand();
			$url="show_sport.php?sport=$row[$i]&sid=$rand_val";
			print "<td class='datafields_td'><a href='$url'>$row[$i]</a></td>";
		} elseif ($i==$hteam_column && $row[$i]!="") {
			$rand_val = rand();
			$url="show_team.php?team=$row[$i]&sid=$rand_val";
			print "<td class='datafields_td'><a href='$url'>$row[$i]</a></td>";
		} elseif ($i==$gteam_column && $row[$i]!="") {
			$rand_val = rand();
			$url="show_team.php?team=$row[$i]&sid=$rand_val";
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