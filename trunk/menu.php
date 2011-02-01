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
?>
<div class="menu">
	<ul>
		<li><a href="#">SELECT</a>
			<ul>
				<li><a href="./select_athlete.php">Athlete</a></li>
				<li><a href="./select_all_menu_option.php">All</a></li>
			</ul>
		</li>
		<li><a href="#">SHOW</a>
			<ul>
			<?php
			if ($_SESSION['select_all'] == 1) {
				?>
				<li><a href="./show_all_athletes.php">Athletes</a></li>
				<li><a href="./show_all_exercises.php">Exercises</a></li>
				<li><a href="./show_equipment.php">Equipment</a></li>
				<li><a href="./show_all_owned_equipment.php">Owned Equipment</a></li>
				<li><a href="./show_coaches.php">Coaches</a></li>
				<li><a href="./show_all_personal_coaches.php">Personal Coaches</a></li>
				<li><a href="./show_teams.php">Teams</a></li>
				<li><a href="./show_matches.php">Matches</a></li>
				<li><a href="./show_sports.php">Sports</a></li>
				<li><a href="./show_summary_all.php">Summary</a></li>
				<?php
			} else {
				?>
				<li><a href="./show_selected_athlete.php">Athlete</a></li>
				<li><a href="./show_exercises.php">Exercises</a></li>
				<li><a href="./show_owned_equipment.php">Equipment</a></li>
				<li><a href="./show_coaches.php">Coaches</a></li>
				<li><a href="./show_personal_coaches.php">Personal Coaches</a></li>
				<li><a href="./show_teams.php">Teams</a></li>
				<li><a href="./show_matches.php">Matches</a></li>
				<li><a href="./show_summary.php">Summary</a></li>
				<?php
			}
			?>
			</ul>
		</li>
		<li><a href="#">ADD</a>
			<ul>
				<li><a href="./add_athlete.php">Athlete</a></li>
				<li><a href="./add_exercise.php">Exercise</a></li>
				<li><a href="./add_equipment.php">Equipment</a></li>
				<li><a href="./add_owned_equipment.php">Owned Equipment</a></li>
				<li><a href="./add_sport.php">Sport</a></li>
				<li><a href="./add_coach.php">Coach</a></li>
				<li><a href="./add_personal_coach.php">Personal Coach</a></li>
				<li><a href="./add_team.php">Team</a></li>
				<li><a href="./add_match.php">Match</a></li>
			</ul>
		</li>
		<li><a href="#">ACCOUNT</a>
			<ul>
				<li><a href="./user_personal.php">Show/Change User Info</a></li>
				<li><a href="./register.php">Register</a></li>
				<li><a href="./logout.php">Logout</a></li>
			</ul>
		</li>
		<li><a href="./info.php">INFO</a>
		</li>
	</ul>
</div>
