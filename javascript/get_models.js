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
var xmlhttp;
function show_models(brand) {
	xmlhttp=GetXmlHttpObject();
	if (xmlhttp==null) {
		alert ("Browser does not support HTTP Request");
		return;
	}
	var url="get_models.php";
	url=url+"?brand="+brand;
	url=url+"&sid="+Math.random();
	xmlhttp.onreadystatechange=function() {
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
			document.getElementById("models").innerHTML=xmlhttp.responseText;
			delete xmlhttp;
			xmlhttp = null;
		}
	};
	xmlhttp.open("GET",url,true);
	xmlhttp.send(null);
}
function GetXmlHttpObject() {
	if (window.XMLHttpRequest) {
//		code for IE7+, Firefox, Chrome, Opera, Safari
		return new XMLHttpRequest();
	}
	if (window.ActiveXObject) {
//		code for IE6, IE5
		return new ActiveXObject("Microsoft.XMLHTTP");
	}
	return null;
}