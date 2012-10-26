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
#############################
## STAFF PANEL LOADER FILE ##
## ------------------------##
#############################

require('../includes/misc/headers.php'); //Load headers

define('INIT_SITE', TRUE);
include('../includes/configuration.php');

if($GLOBALS['adminPanel_enable']==FALSE)
	exit();

require('../includes/misc/compress.php'); //Load compression file
include('../aasp_includes/functions.php');

$server = new server;
$account = new account;
$page = new page;

$server->connect();

if(isset($_SESSION['cw_staff']) && !isset($_SESSION['cw_staff_id']) && $_GET['p']!='notice')
{ 
  header("Location: ?p=notice&e=It seems like a session was not created! You were logged out to prevent any threat against the site.");
  exit;
}
?>