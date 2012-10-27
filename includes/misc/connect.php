<?php
#   ___           __ _           _ __    __     _     
#  / __\ __ __ _ / _| |_ ___  __| / / /\ \ \___| |__  
# / / | '__/ _` | |_| __/ _ \/ _` \ \/  \/ / _ \ '_ \ 
#/ /__| | | (_| |  _| ||  __/ (_| |\  /\  /  __/ |_) |
#\____/_|  \__,_|_|  \__\___|\__,_| \/  \/ \___|_.__/ 
#
#		-[ Created by ©Nomsoft
#		  `-[ Original core by Anthony (Aka. CraftedDev)
#
#				-CraftedWeb Generation II-                  
#			 __                           __ _   							   
#		  /\ \ \___  _ __ ___  ___  ___  / _| |_ 							   
#		 /  \/ / _ \| '_ ` _ \/ __|/ _ \| |_| __|							   
#		/ /\  / (_) | | | | | \__ \ (_) |  _| |_ 							   
#		\_\ \/ \___/|_| |_| |_|___/\___/|_|  \__|	- www.Nomsoftware.com -	   
#                  The policy of Nomsoftware states: Releasing our software   
#                  or any other files are protected. You cannot re-release    
#                  anywhere unless you were given permission.                 
#                  © Nomsoftware 'Nomsoft' 2011-2012. All rights reserved.    

 
class connect {
	
	public static $connectedTo = NULL;

     public static function connectToDB() 
	 {
		 if(static::$connectedTo != 'global')
		 {
			 if (!mysql_connect($GLOBALS['connection']['host'],$GLOBALS['connection']['user'],$GLOBALS['connection']['password']))
				 buildError("<b>Database Connection error:</b> A connection could not be established. Error: ".mysql_error(),NULL);
			 static::$connectedTo = 'global';	 
		 }
	 }
	 
	public static function connectToRealmDB($realmid) 
	{ 
		static::selectDB('webdb');
		
			if($GLOBALS['realms'][$realmid]['mysql_host'] != $GLOBALS['connection']['host'] 
			|| $GLOBALS['realms'][$realmid]['mysql_user'] != $GLOBALS['connection']['user'] 
			|| $GLOBALS['realms'][$realmid]['mysql_pass'] != $GLOBALS['connection']['password'])
			{
				mysql_connect($GLOBALS['realms'][$realmid]['mysql_host'],
							  $GLOBALS['realms'][$realmid]['mysql_user'],
							  $GLOBALS['realms'][$realmid]['mysql_pass'])
							  or 
							  buildError("<b>Database Connection error:</b> A connection could not be established to Realm. Error: ".mysql_error(),NULL);
			}
			else
			{
				static::connectToDB();
			}
			mysql_select_db($GLOBALS['realms'][$realmid]['chardb'])or 
			buildError("<b>Database Selection error:</b> The realm database could not be selected. Error: ".mysql_error(),NULL);
			static::$connectedTo = 'chardb';

	}
	 
	 
	 public static function selectDB($db) 
	 {
		 static::connectToDB();
		 
		 switch($db) {
			default: 
				mysql_select_db($db);
			break;
			case('logondb'):
				mysql_select_db($GLOBALS['connection']['logondb']);
			break;
			case('webdb'):
				mysql_select_db($GLOBALS['connection']['webdb']);
			break;
			case('worlddb'):
				mysql_select_db($GLOBALS['connection']['worlddb']);
			break;
		 }
			 return TRUE;
	 }
}

    /*************************/
	/* Realms & service prices automatic settings
	/*************************/
	$realms = array();
	$service = array();
	connect::selectDB('webdb');
	
	//Realms
	$getRealms = mysql_query("SELECT * FROM realms ORDER BY id ASC");
	while($row = mysql_fetch_assoc($getRealms)) 
	{
		$realms[$row['id']] = $row;
		$realms[$row['id']]['chardb'] = $row['char_db'];
    }

     //Service prices
	$getServices = mysql_query("SELECT enabled,price,currency,service FROM service_prices");
	while($row = mysql_fetch_assoc($getServices)) 
	{
		$service[$row['service']]['status'] = $row['enabled'];
		$service[$row['service']]['price'] = $row['price'];
		$service[$row['service']]['currency'] = $row['currency'];
	}
	
	##Unset Magic Quotes
	if (get_magic_quotes_gpc()) {
		$process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
		while (list($key, $val) = each($process)) {
			foreach ($val as $k => $v) {
				unset($process[$key][$k]);
				if (is_array($v)) {
					$process[$key][stripslashes($k)] = $v;
					$process[] = &$process[$key][stripslashes($k)];
				} else {
					$process[$key][stripslashes($k)] = stripslashes($v);
				}
			}
		}
		unset($process);
	}
?>