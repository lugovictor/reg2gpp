<?php
#    Copyright 2014 Sean Mackedie
#
#
#    This file is part of Reg2GPP.
#
#    Reg2GPP is free software: you can redistribute it and/or modify
#    it under the terms of the GNU Affero General Public License as published by
#    the Free Software Foundation, either version 3 of the License, or
#    (at your option) any later version.
#
#    Reg2GPP is distributed in the hope that it will be useful,
#    but WITHOUT ANY WARRANTY; without even the implied warranty of
#    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#    GNU Affero General Public License for more details.
#
#    You should have received a copy of the GNU Affero General Public License
#    along with Reg2GPP.  If not, see <http://www.gnu.org/licenses/>.

if(!isset($_SESSION)) session_start();
include 'config.php';
include 'hide_email.php';
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Registry to Group Policy Preferences XML Converter</title>
	</head>
	<body>
		<?php include "reg2gpp.php"; ?>
	</body>
</html>