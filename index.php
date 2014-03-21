<?php
#    Copyright 2012 Sean Mackedie
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

session_start();
include 'hide_email.php';
if (isset($_SESSION["existing"]) == false)
  {
  if (file_exists('counter'))
    {
    $file = file('counter', FILE_IGNORE_NEW_LINES);
    $file[0]++;
    file_put_contents('counter', implode("\n", $file));
    }
  }
$_SESSION["existing"] = true;
?>
<html>
<head>
	<title>Registry to Group Policy Preferences XML Converter</title>
</head>
<body>
<p><div align="center"><strong>Registry to Group Policy Preferences XML Converter Version 0.6 Alpha</strong><br />
<em>Because fuck spending &euro;59 on a &lt; 1MB program!</em></p></div>
<p></p>
<p><strong>TO USE:</strong> Choose the .reg file you want to convert and hit Upload.<br />
The next page will verify it uploaded successfully, and give you a couple of options.<br />
When happy, click Convert and Download XML and you should get a prompt to download your XML file.</p>
<p><strong>TO APPLY:</strong> Copy the file to your clipboard (just find the file, and either right-click -> "Copy" or select it and Ctrl+C)<br />
In Group Policy Management Editor, under Computer/User Configuration -> Preferences -> Windows Settings -> Registry, right click anywhere in whitespace and click "Paste"<br />
The settings SHOULD then be imported directly as collections of Registry settings. Hopefully.</p>
<p>I am not responsible for your domain network or any of the computers on said network spontaneously losing data, crashing, spontaneously combusting, being confiscated by Microsoft footsoldiers or otherwise becoming unusable, inaccessible, or explody.</p>
<p>If you're messing with this sort of stuff, I expect you to know what the hell you're doing. Don't blame me if it breaks.</p>
<form action="upload.php" method="post" enctype="multipart/form-data">
<label for="file">Filename:</label>
<input type="file" name="file" id="file" />
<br />
<input type="submit" name="submit" value="Upload" />
</form>
<p>&nbsp;</p>
<p>This program is Alpha quality. If it's not generating your file correctly, please contact me at <?php echo hide_email('webmaster@example.com') ?> with a copy of the registry file you're trying to convert and hopefully I can find the problem and fix it.</p>
<p>On the other hand, if you think I'm awesome, you are welcome (although not obliged by any means) to buy me a beer :) <button class="bitcoinate" data-size="30" data-address="[BITCOINADDRESS]" title="Please donate bitcoins to: [BITCOINADDRESS]"><img src="https://raw.github.com/adius/bitcoinate/master/img/bitcoinate30.png" alt="B">bitcoinate</button></p>
<?php
if (file_exists('counter'))
  {
  $counter = file('counter', FILE_IGNORE_NEW_LINES);
  echo "<p>This tool has been visited on ".$counter[0]." separate occasions, and has converted ".$counter[1]." files since 26th August 2012.</p>";
  }
?>
<p>Get the source code <a href="reg2gpp.zip">here</a>! <a href="change.log">Changelog</a></p>
<p>This tool is released under the condition of the <a href="COPYING">GNU Affero General Public Licence</a>.</p>
<script src="https://raw.github.com/adius/bitcoinate/master/js/bitcoinate.min.js"></script>
</body>
</html>
