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
function Init_AJAX() {
	try { return new ActiveXObject("Msxml2.XMLHTTP"); } catch(e) {} //IE
	try { return new ActiveXObject("Microsoft.XMLHTTP"); } catch(e) {} //IE
	try { return new XMLHttpRequest(); } catch(e) {} //Native Javascript
	alert("XMLHttpRequest not supported");
	return null;
};

function dochange(src, val) {
	var req = Init_AJAX();
	req.onreadystatechange = function () {
		if (req.readyState==4) {
			if (req.status==200) {
				document.getElementById(src).innerHTML=req.responseText;
			}
		}
	};
	req.open("GET", "person_brand_model_dropdown.php?data="+src+"&val="+val);
	req.setRequestHeader("Content-Type",
	"application/x-www-form-urlencoded;charset=tis-620"); // set Header
	req.send(null);
};