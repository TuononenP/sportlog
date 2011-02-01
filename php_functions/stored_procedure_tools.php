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
/*
 * Returns NULL if parameter variable is empty.
 * Otherwise returns parameter variable as string.
 * Prepares the variable to be used in a stored procedure.
 * Used on optional fields that can be NULL.
 */
function prepare_optional($post_var) {
	if(empty($post_var)) {
		$var = "NULL";
	} else {
		$var = $post_var;
		$var = "'$var'";
	}
	return $var;
}
/*
 * Returns "00" if parameter variable is empty.
 * Otherwise returns parameter variable as string.
 * Used on time and date fields that can be NULL.
 */
function prepare_timedate($post_var) {
	if (empty($post_var)) {
		$var = 00;
	} else {
		$var = $post_var;
	}
	return $var;
}
//Shows field names and data fields
function show_table($result) {
	print "<table border=1 bgcolor='e1e1e1'>";
	print "<tr bgcolor='c0c0c0'>";
	$i=0;
	//print field names
	while ($finfo = $result->fetch_field()) {
		print "<td>";
		printf($finfo->name);
		print "</td>";
		$i++;
	}
	print "</tr>";
	$numfields = $result->field_count;
	//print data rows
	while ($row = $result->fetch_array()) {
		print "<tr>";
		for ($i=0; $i<$numfields; $i++) {
			print "<td>$row[$i]</td>";
		}
		print "</tr>";
	}
	print "</table\n>";
}
?>