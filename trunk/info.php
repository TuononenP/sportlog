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
include "./middle.php";
?>
<h2>
	Info
</h2>
<h3>
	Basic Info
</h3>
<p>
	This service is originally made for Relational Databases course held in
	Turku University of Applies Sciences. Everything you can see on this
	website including the underlying database architecture is made
	exclusively by Petri Tuononen.
</p>
<h3>
	Mission
</h3>
<p>
	This website acts as a conventional way to log physical exercises by
	individuals or groups such as football teams. It contains the most
	necessary things to log. Afterwards it's a great source of information
	for coaches and keep-fit enthusiastics.
</p>
<h3>
	Technical info
</h3>
<p>
	Used techniques include PHP, MySQL, XHTML, CSS, Javascript and AJAX.
	Service is run on LAMP configuration which includes a Linux computer
	running Apache web server, MySQL server+client and PHP. AJAX is used in
	situations where refreshing the site is not conventional. One example
	is that when the user selects an equipment brand then immediately the
	selection box below shows all models by that brand. Aside AJAX,
	Javascript is used only in less important tasks such as clearing the
	form and gaining focus to a specific field when refreshing the page.
	XHTML uses certain standards and I have done my best to obey them. For
	example XHTML doesn't allow style definitions and that's the reason why
	all styles are kept in specific stylesheets. It's always a great idea
	to separate things to simplify the whole process. PHP's role is
	remarkable in this site, because it provides the connection to the
	database and processes everything that needs to be processed. PHP is an
	important link that makes this site so dynamic. Everything was done
	using open source software. Eclipse editor was also a very practical
	tool with PHP plugin.
</p>
<h3>
	Security
</h3>
<p>
	I have worked hard to make this happen. The site has user
	authentication and the password is encrypted with MD5 (Message-Digest
	algorithm 5) which can't be decrypted (without brute force or decrypted
	MD5 hash library database), because it's a one-way hash algorithm. This
	ensures that the password is saved in encrypted format to the database
	and even the admin is unable to see the password. The whole site is SSL
	(Secure Socket Layer) secured. SSL is a series of cryptographic
	protocols that provide security for communications over networks. SSL
	makes eavesdropping impossible so nobody is able to catch user
	authentication traffic. All user input is always filtered before
	executing any SQL queries. This ensures that nobody is able to use any
	SQL specific characters like ; " ' and alike to make their own
	hazardous remove all tables query. Access to the database is very
	limited in such a way that executing only stored procedures is
	permitted.
</p>
<?php
include "./footer.php";
?>