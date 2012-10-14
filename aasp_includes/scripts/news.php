<?php
/* ___           __ _           _ __    __     _
  / __\ __ __ _ / _| |_ ___  __| / / /\ \ \___| |__
 / / | '__/ _` | |_| __/ _ \/ _` \ \/  \/ / _ \ '_ \
/ /__| | | (_| |  _| ||  __/ (_| |\  /\  /  __/ |_) |
\____/_|  \__,_|_|  \__\___|\__,_| \/  \/ \___|_.__/

		-[ Created by ©Nomsoft
		  `-[ Original core by Anthony (Aka. CraftedDev)

				-CraftedWeb Generation II-
			 __                           __ _
		  /\ \ \___  _ __ ___  ___  ___  / _| |_
		 /  \/ / _ \| '_ ` _ \/ __|/ _ \| |_| __|
		/ /\  / (_) | | | | | \__ \ (_) |  _| |_
		\_\ \/ \___/|_| |_| |_|___/\___/|_|  \__|	- www.Nomsoftware.com -
                  The policy of Nomsoftware states: Releasing our software
                  or any other files are protected. You cannot re-release
                  anywhere unless you were given permission.
                  © Nomsoftware 'Nomsoft' 2011-2012. All rights reserved.  */

define('INIT_SITE', TRUE);
include('../../includes/misc/headers.php');
include('../../includes/configuration.php');
include('../functions.php');
$server = new server;
$account = new account;

$server->selectDB('webdb');

###############################
if($_POST['function']=='post')
{
	if(empty($_POST['title']) || empty($_POST['author']) || empty($_POST['content']))
		die('<span class="red_text">Please enter all fields.</span>');

	mysql_query("INSERT INTO news (title,body,author,image,date) VALUES
	('".mysql_real_escape_string($_POST['title'])."','".mysql_real_escape_string(trim(htmlentities($_POST['content'])))."',
	'".mysql_real_escape_string($_POST['author'])."','".mysql_real_escape_string($_POST['image'])."',
	'".date("Y-m-d H:i:s")."')");

	$server->logThis("Posted a news post");
	echo "Successfully posted news.";
}
################################
elseif($_POST['function']=='delete')
{
	if(empty($_POST['id']))
		die('No ID specified. Aborting...');

	mysql_query("DELETE FROM news WHERE id='".mysql_real_escape_string($_POST['id'])."'");
	mysql_query("DELETE FROM news_comments WHERE id='".mysql_real_escape_string($_POST['id'])."'");
	$server->logThis("Deleted a news post");
}
##############################
elseif($_POST['function']=='edit')
{
	$id = (int)$_POST['id'];
	$title = ucfirst(mysql_real_escape_string($_POST['title']));
	$author = ucfirst(mysql_real_escape_string($_POST['author']));
	$content = mysql_real_escape_string(trim(htmlentities($_POST['content'])));

	if(empty($id) || empty($title) || empty($content))
	 	die("Please enter both fields.");
    else
	{
		mysql_query("UPDATE news SET title='".$title."', author='".$author."', body='".$content."' WHERE id='".$id."'");
		$server->logThis("Updated news post with ID: <b>".$id."</b>");
		die("success");
	}
}
#############################
elseif($_POST['function']=='getNewsContent')
{
	$result = mysql_query("SELECT * FROM news WHERE id='".(int)$_POST['id']."'");
	$row = mysql_fetch_assoc($result);
	$content = str_replace('<br />', "\n", $row['body']);

	echo "Title: <br/><input type='text' id='editnews_title' value='".$row['title']."'><br/>Content:<br/><textarea cols='55' rows='8' id='wysiwyg'>"
	.$content."</textarea><br/>Author:<br/><input type='text' id='editnews_author' value='".$row['author']."'><br/><input type='submit' value='Save' onclick='editNewsNow(".$row['id'].")'>";
}

?>