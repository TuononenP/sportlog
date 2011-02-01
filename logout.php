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
session_unset();
session_destroy();
include("./php_functions/redirect.php");
redirect("./index.php", 301);
/*
 //slower method but more informative
 header("Refresh: 2; URL=index.php");
 echo "Logout succesful! " .
 "You are being sent to the login page.<br>";
 echo "(If your browser doesn't support this, " .
 "<a href=\"index.php\">click here</a>)";
 die();
 */
?>