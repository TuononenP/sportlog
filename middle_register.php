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
print("<body>");
print("<div class='container'>\n");
print("<div class='main_block'>\n");
print("<div class='top_bar'>\n");
print("<div class='logo_top'>\n");
print("</div> <!-- end of logo_top -->\n");
print("</div> <!-- end of top_bar -->\n");
if (!empty($_SESSION['user_logged']) &&
(!empty($_SESSION['user_password']))) {
	print("<div class='navi'>\n");
	include('menu.php');
	print("</div> <!-- end of navi -->\n");
}
print("<div class='content_block'>\n");
?>