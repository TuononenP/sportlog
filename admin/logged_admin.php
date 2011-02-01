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
include "./auth_admin.inc.php";
include "./header_admin.php";
include "./middle_admin.php";
?>
<h2>
	Admin Area
</h2>
<p>
	You are currently logged in.<br /> <a href="admin_area.php">Click here</a>
	to access your administrator tools.<br /> <a href="logout.php">Click
		here</a> to logout.
</p>
<?php
include "./footer_admin.php";
?>