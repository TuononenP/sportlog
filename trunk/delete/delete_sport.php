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
include "./middle.php";
if (isset($_POST['submit_del'])) {
	$sport = $mysqli->real_escape_string($_POST['sport']);
	//check how many exercises are found with chosen sport
	$query = "CALL GetExerciseWithSpecificSport('$sport')";
	if($mysqli->multi_query($query)) {
		do {
			$result = $mysqli->store_result();
			if($result) {
				$exercise_rows = $result->num_rows;
				$result->close();
			}
		} while($mysqli->next_result());
	}
	//check how many equipment are found with chosen sport
	$query = "CALL GetEquipmentWithSpecificSport('$sport')";
	if($mysqli->multi_query($query)) {
		do {
			$result = $mysqli->store_result();
			if($result) {
				$equipment_rows = $result->num_rows;
				$result->close();
			}
		} while($mysqli->next_result());
	}
	//check how many matches are found with chosen sport
	$query = "CALL GetMatchesWithSpecificSport('$sport')";
	if($mysqli->multi_query($query)) {
		do {
			$result = $mysqli->store_result();
			if($result) {
				$match_rows = $result->num_rows;
				$result->close();
			}
		} while($mysqli->next_result());
	}
	//can't delete sport if any exercises are found
	if ($exercise_rows==0 && $equipment_rows==0 && $match_rows==0) {
		$query = "CALL DeleteSport('$sport')";
		if($mysqli->multi_query($query)) {
			do {
				$result = $mysqli->store_result();
				if($result) {
					$result->close();
				}
			} while($mysqli->next_result());
		}
		redirect($_POST['url']);
	} else {
		if ($exercise_rows!=0) {
			print "Can't delete sport, because exercises were found with chosen sport.
<br />";
			print "It's possible to delete sport after you have deleted exercises in
question.<br />";
			print "<a href='./show_all_exercises.php'>Click here</a> to see all
exercises<br /><br />";
		}
		if ($equipment_rows!=0) {
			print "Can't delete sport, because equipment were found with chosen sport.
<br />";
			print "It's possible to delete sport after you have deleted equipment in
question.<br />";
			print "<a href='./show_equipment.php'>Click here</a> to see all
equipment<br /><br />";
		}
		if ($match_rows!=0) {
			print "Can't delete sport, because matches were found with chosen sport.
<br />";
			print "It's possible to delete sport after you have deleted matches in
question.<br />";
			print "<a href='./show_matches.php'>Click here</a> to see all
matches<br /><br />";
		}
		print "<a href='" .$_POST['url']. "'>Click here</a> to go back";
	}
}
include "./footer.php";
$mysqli->close();
?>
}
