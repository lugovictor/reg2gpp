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

session_start();
include 'config.php';
error_reporting(E_ALL ^ E_NOTICE);
if (file_exists(BASEDIR.'counter'))
  {
  $file = file(BASEDIR.'counter', FILE_IGNORE_NEW_LINES);
  $file[0]++;
  file_put_contents(BASEDIR.'counter', implode("\n", $file));
  }

include 'guid.php';

date_default_timezone_set('UTC');

header("Content-Disposition: attachment; filename=".$_POST["collection"].".xml");
header("Content-Type: text/xml; "); 

function conv_collection($line)
	{
	global $xml;
	global $parent_node;
	global $child_node;
	global $last_regkey;
	$trimmed = substr($line, 1, -1);
	$last_regkey = explode('\\', $trimmed, 2);
	$keys = explode('\\', $trimmed);
	$i = 0;
	foreach ($keys as $key)
		{
		if ($i == 0)
			{
			$parent_node = $xml;
			}
		else
			{
			$parent_node = $child_node;
			}
		if ($parent_node->xpath("Collection[@name='".$key."']") == false)
			{
			$child_node = $parent_node->addChild('Collection');
			$child_node->addAttribute('clsid', '{53B533F5-224C-47e3-B01B-CA3B3F3FF4BF}');
			$child_node->addAttribute('name', $key);
			}
		else
			{
			$child_node = $parent_node->xpath("Collection[@name='".$key."']");
			$child_node = $child_node[0];
			}
		$i = 1;
		}
	}

function del_regkey($line)
	{
	global $xml;
	global $child_node;
	global $last_regkey;
	$hive = $last_regkey[0];
	$key = $last_regkey[1];
	$changed = date('Y-m-d H:i:s');
	$uid = guid();
	$tmpnamestat = explode('\\', substr($line, 1, -1));
	$namestat = end($tmpnamestat);
	$reg_node = $child_node->addChild('Registry');
	$reg_node->addAttribute('clsid', '{9CD4B2F4-923D-47f5-A062-E897DD1DAD50}');
	$reg_node->addAttribute('name', utf8_encode($namestat));
	$reg_node->addAttribute('status', utf8_encode($namestat));
	$reg_node->addAttribute('image', "3");
	$reg_node->addAttribute('changed', $changed);
	$reg_node->addAttribute('uid', $uid);
	if ($_POST['stoponError'] == "1")
		{
		$reg_node->addAttribute('bypassErrors', '0');
		}
	if ($_POST['userContext'] ==  "1")
		{
		$reg_node->addAttribute('userContext', '1');
		}
	$prop_node = $reg_node->addChild('Properties');
	$prop_node->addAttribute('action', "D");
	$prop_node->addAttribute('displayDecimal', "0");
	$prop_node->addAttribute('default', "0");
	$prop_node->addAttribute('hive', $hive);
	$prop_node->addAttribute('key', utf8_encode("$key"));
	$prop_node->addAttribute('name', "");
	$prop_node->addAttribute('type', "REG_SZ");
	$prop_node->addAttribute('value', "");
	$filters_node = $reg_node->addChild('Filters');
	if ($_POST['applyOnce'] == "1")
		{
		$filterrunonce = $filters_node->addChild('FilterRunOnce');
		$filterrunonce->addAttribute('hidden', '1');
		$filterrunonce->addAttribute('not', '0');
		$filterrunonce->addAttribute('bool', 'AND');
		$filterrunonce->addAttribute('id', guid());
		}
	}

function conv_regvalue($line)
	{
	global $xml;
	global $child_node;
	global $last_regkey;
	$hive = $last_regkey[0];
	$key = $last_regkey[1];
	$action[0] = $_POST['action'];
	switch ($action[0])
		{
		case "C":
			$action[1] = 1;
			break;
		case "R":
			$action[1] = 2;
			break;
		case "U":
			$action[1] = 3;
			break;
		case "D":
			$action[1] = 4;
			break;
		}
	$changed = date('Y-m-d H:i:s');
	$uid = guid();
	if (strpos($line, '@') === 0)
		{
		$name = "(Default)";
		$status = "";
		$default = "1";
		$regkey = ltrim($line, '@=');
		}
	else
		{
		$default = "0";
		$regkey = ltrim($line, '"');
		$regkey = explode('"=', $regkey, 2);
		$name = utf8_encode($regkey[0]);
		$status = $name;
		$regkey = $regkey[1];
		}
	if (stripos($regkey, 'hex:') === 0)
		{
		$value = conv_hex($regkey);
		$type = "REG_BINARY";
		$image = 14 + $action[1];
		}
	elseif (stripos($regkey, 'dword:') === 0)
		{
		$splode = explode(":", $regkey, 2);
		$value = $splode[1];
		$type = "REG_DWORD";
		$image = 9 + $action[1];
		}
	elseif (stripos($regkey, 'hex(b):') === 0)
		{
		$value = hex_qword_reverse(conv_hex($regkey));
		$type = "REG_QWORD";
		$image = 9 + $action[1];
		}
	elseif (stripos($regkey, 'hex(7):') === 0)
		{
		$value = utf8_encode(hex2multi_line(hex2multi_scrub(conv_hex($regkey))));
		$type = "REG_MULTI_SZ";
		$image = 4 + $action[1];
		$values = hex2multi_array(hex2multi_scrub(conv_hex($regkey)));
		}
	elseif (stripos($regkey, 'hex(2):') === 0)
		{
		$value = utf8_encode(hex2str(hex2expand_scrub(conv_hex($regkey))));
		$type = "REG_EXPAND_SZ";
		$image = 4 + $action[1];
		}
	else
		{
		$value = stripslashes(utf8_encode(substr($regkey,1,-1)));
		$type = "REG_SZ";
		$image = 4 + $action[1];
		}
	$reg_node = $child_node->addChild('Registry');
	$reg_node->addAttribute('clsid', '{9CD4B2F4-923D-47f5-A062-E897DD1DAD50}');
	$reg_node->addAttribute('name', stripslashes("$name"));
	$reg_node->addAttribute('status', stripslashes("$status"));
	$reg_node->addAttribute('image', $image);
	$reg_node->addAttribute('changed', $changed);
	$reg_node->addAttribute('uid', $uid);
	if ($_POST['stoponError'] == "1")
		{
		$reg_node->addAttribute('bypassErrors', '0');
		}
	if ($_POST['userContext'] ==  "1")
		{
		$reg_node->addAttribute('userContext', '1');
		}
	if ($_POST['removePolicy'] ==  "1")
		{
		$reg_node->addAttribute('removePolicy', '1');
		}

	$prop_node = $reg_node->addChild('Properties');
	$prop_node->addAttribute('action', $action[0]);
	$prop_node->addAttribute('displayDecimal', "0");
	$prop_node->addAttribute('default', $default);
	$prop_node->addAttribute('hive', $hive);
	$prop_node->addAttribute('key', "$key");
	$prop_node->addAttribute('name', stripslashes("$status"));
	$prop_node->addAttribute('type', $type);
	$prop_node->addAttribute('value', "$value");
	$filters_node = $reg_node->addChild('Filters');
	if ($_POST['applyOnce'] == "1")
		{
		$filterrunonce = $filters_node->addChild('FilterRunOnce');
		$filterrunonce->addAttribute('hidden', '1');
		$filterrunonce->addAttribute('not', '0');
		$filterrunonce->addAttribute('bool', 'AND');
		$filterrunonce->addAttribute('id', guid());
	}
	if ($type == "REG_MULTI_SZ")
		{
		$vals_node = $prop_node->addChild('Values');
		foreach ($values as $val)
			{
			$val_node = $vals_node->addChild('Value', htmlspecialchars(utf8_encode($val)));
			}
		}
	}

function conv_hex($line)
	{
	$split = explode(":", $line, 2);
	$split = str_replace(',', '', $split[1]);
	return $split;
	}

function hex2multi_scrub($code)
	{
	$i = 2;
	while ($i<strlen($code))
		{
		$code = substr_replace($code, '', $i, 2);
		$i+=2;
		}
	$code = substr($code, 0, strlen($code)-4);
	return $code;
	}

function hex2expand_scrub($code)
	{
	$i = 2;
	while ($i<strlen($code))
		{
		$code = substr_replace($code, '', $i, 2);
		$i+=2;
		}
	return $code;
	}

function hex2multi_line($hex)
	{
	$i = 0;
	while ($i<strlen($hex))
		{
		if (substr($hex, $i, 2) == '00')
			{
			$hex = substr_replace($hex, '20', $i, 2);
			}
		$i+=2;
		}
	return hex2str($hex);
	}

function hex2multi_array($hex)
	{
	$i = 0;
	$arr = array();
	$lastpos = 0;
	while ($i<strlen($hex))
		{
		if (substr($hex, $i, 2) == '00')
			{
			array_push($arr, hex2str(substr($hex, $lastpos, $i-$lastpos)));
			$lastpos = $i+2;
			}
		$i+=2;
		}
	array_push($arr, hex2str(substr($hex, $lastpos, strlen($hex)-$lastpos)));
	return $arr;
	}

function hex2str($hex)
	{
	$string='';
	for ($i=0; $i < strlen($hex)-1; $i+=2)
		{
		$string .= chr(hexdec($hex[$i].$hex[$i+1]));
		}
	return $string;
	}

function hex_qword_reverse($hex) {
	$reversed = "";
	for ($i = 0; $i < strlen($hex); $i += 2) {
		$reversed = substr($hex, $i, 2).$reversed;
	}
	return $reversed;
}
	
function add_empty_key($xmlseg, $top, $hive, $key)
	{
	foreach ($xmlseg->xpath("Collection") as $node)
		{
		$nodeattr = $node->attributes();
		if ($top == 1)
			{
			$hive = $nodeattr['name'];
			$key = "";
			$oldkey = "";
			}
		else
			{
			if (empty($key))
				{
				$key = $nodeattr['name'];
				}
			else
				{
				$oldkey = $key;
				$key = $key . "\\" . $nodeattr['name'];
				}
			}
		if (@count($node->children()) == 0)
			{
			$namestat = $nodeattr['name'];
			$reg_node = $node->addChild('Registry');
			$reg_node->addAttribute('clsid', '{9CD4B2F4-923D-47f5-A062-E897DD1DAD50}');
			$reg_node->addAttribute('name', $namestat);
			$reg_node->addAttribute('status', $namestat);
			$reg_node->addAttribute('image', "0");
			$reg_node->addAttribute('changed', date('Y-m-d H:i:s'));
			$reg_node->addAttribute('uid', guid());
			if ($_POST['stoponError'] == "1")
				{
				$reg_node->addAttribute('bypassErrors', '0');
				}
			if ($_POST['userContext'] ==  "1")
				{
				$reg_node->addAttribute('userContext', '1');
				}
			if ($_POST['removePolicy'] ==  "1")
				{
				$reg_node->addAttribute('removePolicy', '1');
				}
			$prop_node = $reg_node->addChild('Properties');
			$prop_node->addAttribute('action', "C");
			$prop_node->addAttribute('displayDecimal', "0");
			$prop_node->addAttribute('default', "1");
			$prop_node->addAttribute('hive', $hive);
			$prop_node->addAttribute('key', "$key");
			$prop_node->addAttribute('name', "");
			$prop_node->addAttribute('type', "REG_SZ");
			$prop_node->addAttribute('value', "");
			$filters_node = $reg_node->addChild('Filters');
			if ($_POST['applyOnce'] == "1")
				{
				$filterrunonce = $filters_node->addChild('FilterRunOnce');
				$filterrunonce->addAttribute('hidden', '1');
				$filterrunonce->addAttribute('not', '0');
				$filterrunonce->addAttribute('bool', 'AND');
				$filterrunonce->addAttribute('id', guid());
				}
			$key = $oldkey;
			}
		else
			{
			add_empty_key($node, 0, $hive, $key);
			$key = $oldkey;
			}
		}
	}

$reg_data_scrubbed = str_replace(array("\x00", "\xFF", "\xFE", chr(13)), "", $_SESSION["reg_data"]);

$piece = "";
foreach ($reg_data_scrubbed as &$tmp)
	{
	if ($piece !== false)
		{
		$tmp = trim($piece, ' ').trim($tmp, ' ');
		$piece = "";
		}
	if (substr($tmp, -2) == ",\\")
		{
		$piece = substr($tmp, 0, -1);
		$tmp = "";
		}
	}

global $xml; # Fix for Joomla weirdness
$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?>'.'<Collection clsid="{53B533F5-224C-47e3-B01B-CA3B3F3FF4BF}" name="'.htmlentities($_POST["collection"]).'"></Collection>');

foreach ($reg_data_scrubbed as $reg_line)
	{
	if (stripos($reg_line, "Windows Registry Editor Version 5.00") !== false)
		{
		}
	if (stripos($reg_line, "[H") === 0)
		{
		conv_collection($reg_line);
		}
	if (stripos($reg_line, "[-H") === 0)
		{
		conv_collection(substr_replace($reg_line, "", 1, 1));
		del_regkey($reg_line);
		}
	if (stripos($reg_line, '"') === 0 or stripos($reg_line, '@=') === 0)
		{
		conv_regvalue($reg_line);
		}
	}
	
add_empty_key($xml, 1, "", "");

print $xml->asXML();
exit();
?>