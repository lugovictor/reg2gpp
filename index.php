<?php
#    Copyright 2013 Sean Mackedie
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
include 'config.php';
chdir(BASEDIR);
include 'hide_email.php';
?>
<html>
<head>
	<title>Registry to Group Policy Preferences XML Converter</title>
</head>
<body>
<h1>Reg2GPP</h1>
<h2>Registry to Group Policy Preferences XML Converter Version 0.6.5 Alpha</h2>
<p></p>
<p>The purpose of this tool is to convert Windows registry files (*.reg) into a format usable by Group Policy Preferences (*.xml), which is used in Windows Active Directory environments to propagate sttings across to multiple Windows-based computers (usually in a corporate envionment). Apparently, Microsoft didn't feel it necessary to create an official way to do this, and the task of manually importing each registry setting from one system to the other can become extremely tedious, time-consuming, and prone to error. Reg2GPP was designed as the bridge between these two systems, saving potentially hours of work.</p>
<p><strong>TO USE:</strong> Choose the .reg file you want to convert and hit Upload. The next page will verify it uploaded successfully, and give you a couple of options. When happy, click Convert and Download XML and you should get a prompt to download your XML file.</p>
<p><strong>TO APPLY:</strong> Copy the file to your clipboard (just find the file, and either right-click -> "Copy" or select it and Ctrl+C). In Group Policy Management Editor, under Computer/User Configuration -> Preferences -> Windows Settings -> Registry, right click anywhere in whitespace and click "Paste". The settings SHOULD then be imported directly as collections of Registry settings. Hopefully.</p>
<p>I am not responsible for your domain network or any of the computers on said network spontaneously losing data, crashing, spontaneously combusting, being confiscated by Microsoft footsoldiers or otherwise becoming unusable, inaccessible, or explody.</p>
<p>If you're messing with this sort of stuff, I expect you to know what you're doing. Don't blame me if it breaks. On the plus side, this online tool is and always will be 100% free (as in freedom, and as in beer).</p>
<form action="<?php echo UPLOAD_FILE ?>" method="post" enctype="multipart/form-data">
<label for="file">Filename:</label>
<input type="file" name="file" id="file" />
<br />
<input type="submit" name="submit" value="Upload" />
</form>
<p>&nbsp;</p>
<p>This tool is Alpha quality. If it's not generating your file correctly, please contact me at <?php echo hide_email(WEBMASTER_EMAIL) ?> with a copy of the registry file you're trying to convert and hopefully I can find the problem and fix it.</p>
<?php
if (file_exists('counter'))
  {
  $counter = file('counter', FILE_IGNORE_NEW_LINES);
  echo "<p>This tool has converted ".$counter[0]." files since 26th August 2012.</p>";
  }
?>
<p>Get the source code <a href="<? echo BASEDIR ?>reg2gpp-0.6.5.zip">here</a>! <a href="<? echo BASEDIR ?>change.log">Changelog</a></p>
<p>This tool is released under the condition of the <a href="<? echo BASEDIR ?>COPYING">GNU Affero General Public Licence</a>.</p>
</body>
</html>