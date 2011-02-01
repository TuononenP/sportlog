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
include("./php_functions/redirect.php");
if (isset($_POST['submit_del'])) {
	$brand = $mysqli->real_escape_string($_POST['brand']);
	$model = $mysqli->real_escape_string($_POST['model']);
	$sport = $mysqli->real_escape_string($_POST['sport']);
	$query = "CALL DeleteEquipment('$brand', '$model', '$sport')";
	$result = $mysqli->query($query)
	or die(mysqli_error($mysqli));
}
redirect($_POST['url']);
?>