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
 

class plugins 
{
	public static function globalInit()
	{
		if($GLOBALS['enablePlugins']==true)
		{
			if(!isset($_SESSION['loaded_plugins']))
			{
				$loaded_plugins = array();
				
				$bad = array('.','..','index.html');
				$count = 0;
				
				$folder = scandir('plugins/');
				foreach($folder as $folderName)
				{
					if(!in_array($folderName,$bad))
					{
						connect::selectDB('webdb');
						if(file_exists('plugins/'.$folderName.'/config.php'))
							include('plugins/'.$folderName.'/config.php');
						
						$loaded_plugins[] = $folderName;
						$count++;
					}
				}
				
				if($count==0)
					$_SESSION['loaded_plugins'] = NULL;
				else
					$_SESSION['loaded_plugins'] = $loaded_plugins;
			}
		}
	}
	
	public static function init($type)
	{
		if($GLOBALS['enablePlugins']==true)
		{
			if($_SESSION['loaded_plugins']!=NULL)
			{
				$bad = array('.','..','index.html');
				$loaded = array();
				foreach($_SESSION['loaded_plugins'] as $folderName)
				{	
					connect::selectDB('webdb');
					$chk = mysql_query("SELECT COUNT(*) FROM disabled_plugins WHERE foldername='".mysql_real_escape_string($folderName)."'");
					if(mysql_result($chk,0)==0 && file_exists('plugins/' . $folderName . '/'. $type . '/'))
					{	
						$folder = scandir('plugins/' . $folderName . '/'. $type . '/');
						
						foreach($folder as $fileName)
						{
							if(!in_array($fileName,$bad))
								$loaded[] = 'plugins/' . $folderName . '/'. $type . '/'.$fileName;
						}
						
						$_SESSION['loaded_plugins_' . $type] = $loaded;
					}
				}
			}
		}
	}
	
	public static function load($type)
	{
		if($GLOBALS['enablePlugins']==true && isset($_SESSION['loaded_plugins_' . $type]))
		{
		  ##########################
		  if($type == 'pages')
		  {	
		  		$count = 0;
				foreach($_SESSION['loaded_plugins_' . $type] as $filename)
				{
					$name = basename(substr($filename,0,-4));
					if($name == $_GET['p'])
					{
						include($filename);
						$count = 1;
					}
				}
				if($count == 0)
					include('pages/404.php');	  
			}
			###########################
			elseif($type == 'javascript')
			{
				foreach($_SESSION['loaded_plugins_' . $type] as $filename)
				{
					echo '<script type="text/javascript" src="'.$filename.'"></script>';
				}
			}
			###########################
			elseif($type == 'styles')
			{
				foreach($_SESSION['loaded_plugins_' . $type] as $filename)
				{
					echo '<link rel="stylesheet" href="'.$filename.'" />';
				}
			}
			###########################
			elseif($type == 'classes')
			{
				foreach($_SESSION['loaded_plugins_' . $type] as $filename)
				{
					include($filename);
				}
			}
		}
	}
}

?>