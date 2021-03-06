# Registry to Group Policy Preferences XML Converter

The purpose of this tool is to convert Windows registry files (*.reg) into a
format usable by Group Policy Preferences (*.xml), which is used in Windows
Active Directory environments to propagate settings across to multiple Windows-
based computers (usually in a corporate environment). Apparently, Microsoft
didn't feel it necessary to create an official way to do this, and the task of
manually importing each registry setting from one system to the other can
become extremely tedious, time-consuming, and prone to error. Reg2GPP was
designed as the bridge between these two systems, saving potentially hours of
work.

All that should be required to make this work on your server is PHP 5.1.*. Just
store the files in a suitable directory, adjust the settings in config.php to
suit, and you should be right to go. Otherwise if all you need is to convert
some registry files, you can always upload and convert them straight away at
the live version hosted at http://www.runecasters.com.au/reg2gpp/.
