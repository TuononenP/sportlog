<!-- Hide from javascript disabled browsers
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
function formReset(frm) {
	for(var i=0; i<frm.elements.length; i++) {
		if(!(frm.elements[i].type && frm.elements[i].type == "submit")
				&& !(frm.elements[i].type && frm.elements[i].type == "reset")) {
			frm.elements[i].value = "";
		}
	}
}