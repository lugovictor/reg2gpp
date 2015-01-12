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
include 'error.php'; ?>
<html>
<body>
<h1>Reg2GPP</h1>
<?php
if ($_FILES["file"]["error"] > 0)
	{
	echo "<strong>Error: ";
	try
		{
		throw new UploadException($_FILES['file']['error']);
		}
	catch(UploadException $e)
		{
		echo $e->getMessage();
		}
	echo "</strong><br />";
	}
else
	{
	if ($_FILES["file"]["name"] == $_SESSION["file"]["name"] and md5(serialize(file($_FILES["file"]["tmp_name"], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES))) == md5(serialize($_SESSION["reg_data"])))
		{
		echo "<em>File already uploaded!</em><br /><br />";
		$newfile = 0;
		}
	else
		{
		$_SESSION["file"] = $_FILES["file"];
		$newfile = 1;
		}

	echo "<strong>Upload:</strong> ".$_SESSION["file"]["name"]."<br />";
	echo "<strong>Type:</strong> ".$_SESSION["file"]["type"]."<br />";
	echo "<strong>Size:</strong> " . round(($_SESSION["file"]["size"] / 1024), 2) . " Kb<br />";

	if ($newfile == 1)
		{
		$_SESSION["reg_data"] = file($_SESSION["file"]["tmp_name"], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) or die('Failed to open file!');
		$_SESSION["reg_data_scrubbed"] = str_replace(array("\x00", "\xFF", "\xFE", chr(13)), "", $_SESSION["reg_data"]);
		}

	if (stripos($_SESSION["reg_data_scrubbed"][0], "Windows Registry Editor Version 5.00") !== false)
		{ ?>
		<script type="text/javascript">
		function DisabActions()
		{
		    if(document.getElementById("removePolicy").checked == true)
		    {
			document.getElementById("actionC").disabled = true;
			document.getElementById("actionU").disabled = true;
			document.getElementById("actionD").disabled = true;
			document.getElementById("actionR").checked = true;
			document.getElementById("applyOnce").disabled = true;
			document.getElementById("applyOnce").checked = false;
		    }
		    else
		    {
			document.getElementById("actionC").disabled = false;
			document.getElementById("actionU").disabled = false;
			document.getElementById("actionD").disabled = false;
			document.getElementById("actionU").checked = true;
			document.getElementById("applyOnce").disabled = false;
		    }
		    document.getElementById("removePolicy").blur();
		}
		</script>
		<form action="<?php echo DOWNLOAD_FILE ?>" method="post">
		<label for="collection">Collection Name:</label>
		<input type="text" name="collection" size="30" value="<?php echo str_ireplace(".reg", "", $_SESSION["file"]["name"]); ?>"/><br />
		<p>Default Action:<br />
		<input type="radio" name="action" value="C" id="actionC" /> Create<br />
		<input type="radio" name="action" value="R" id="actionR" /> Replace<br />
		<input type="radio" name="action" value="U" id="actionU" checked /> Update<br />
		<input type="radio" name="action" value="D" id="actionD" /> Delete
		</p>
		<p>Common Options:<br/>
		<input type="checkbox" name="stoponError" value="1" /> Stop processing items if an error occurs<br/>
		<input type="checkbox" name="userContext" value="1" /> Run in logged-on user's security context<br/>
		<input type="checkbox" name="removePolicy" value="1" id="removePolicy" onclick="DisabActions()" /> Remove item when it's no longer applied<br/>
		<input type="checkbox" name="applyOnce" value="1" id="applyOnce"/> Apply once and do not reapply<br/>
		</p>
		<input type="submit" value="Convert and Download XML">
		</form>
		<?php
		}
	else
		{
		echo "<p></p><p><strong>This file is not a valid .REG file!</strong><br />Please ensure the file you're uploading is valid and try again.</p>";
		}
	}
?>
</body>
</html>
