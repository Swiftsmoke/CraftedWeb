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
 
if (isset($_GET['newsid'])) 
{
	$id = (int)$_GET['newsid'];
	connect::selectDB('webdb');
	
	$result = mysql_query("SELECT * FROM news WHERE id='".$id."'");
	$row = mysql_fetch_assoc($result); ?>
    <div class='box_two_title'><?php echo $row['title']; ?></div>
    
    <?php 
	$text = preg_replace("#((http|https|ftp)://(\S*?\.\S*?))(\s|\;|\)|\]|\[|\{|\}|,|\"|'|:|\<|$|\.\s)#ie",
	  "'<a href=\"$1\" target=\"_blank\">http://$3</a>$4'",$row['body']);
	echo nl2br($text); 
	?> 
   
    <br/><br/>
    <span class='yellow_text'>Written by <b><?php echo $row['author'];?></b> | <?php echo $row['date']; ?></span>
    <?php 
	 if ($GLOBALS['news']['enableComments']==true) 
	 { 
		 $result = mysql_query("SELECT poster FROM news_comments WHERE newsid='".$id."' ORDER BY id DESC LIMIT 1");
		 $rows = mysql_fetch_assoc($result);
		 
		 if($rows['poster'] == $_SESSION['cw_user_id'] && isset($_SESSION['cw_user']) && isset($_SESSION['cw_user_id'])) 
			echo '<span class="attention">You can\'t post 2 comments in a row!</span>'; 
	 else 
	 {
	?>
    <hr/>
    <h4 class="yellow_text">Comments</h4>
    <?php 
	connect::selectDB('webdb'); 
	
	$chk = mysql_query("SELECT poster FROM `news_comments` WHERE `newsid` = " . $id . " ORDER BY id DESC LIMTI 1");
	$chkrow = mysql_fetch_assoc($chk);
	if ($_SESSION['cw_user'] and $chkrow['poster'] != $_SESSION['cw_user_id']) { 
	?>
    <form action="?p=news&newsid=<?php echo $id; ?>" method="post">
    <table width="100%"> 
    	<tr> 
    		<td>
   			    <textarea id="newscomment_textarea" name="text">Comment this post...</textarea> 
            </td>
   		    <td>
            	<input type="submit" value="Post" name="comment"> 
            </td>
    	</tr>
    </table>
    </form>
    <br/>
    
    <?php
	} 
	elseif($chkrow['poster'] == $_SESSION['cw_user_id'])
	{
		echo '<span class="note">You can not post comments in a row!</span>';	
	}
	else
		echo '<span class="note">Log in to comment!</span>';
	}
	if (isset($_POST['comment'])) 
	{
		if (isset($_POST['text']) && isset($_SESSION['cw_user']) && strlen($_POST['text']) <= 1000) 
		{
			$text = mysql_real_escape_string(trim(htmlentities($_POST['text'])));
			
			if(!empty($text) and $text != 'Comment this post...')
			{
				connect::selectDB('webdb'); 
				
				$chk = mysql_query("SELECT poster FROM `news_comments` WHERE `newsid` = " . $id . " ORDER BY id DESC LIMTI 1");
				$chkrow = mysql_fetch_assoc($chk);
				if($chkrow['poster'] != $_SESSION['cw_user_id'])
				{
					mysql_query("INSERT INTO news_comments (newsid,text,poster,ip) 
					VALUES ('".$id."','".$text."','".$_SESSION['cw_user_id']."','".$_SERVER['REMOTE_ADDR']."')");
				}
			}	
			
			header("Location: ?p=news&newsid=".$id);
            exit;
		}
	}
	
    $result = mysql_query("SELECT * FROM news_comments WHERE newsid='".$row['id']."' ORDER BY id ASC");
	if (mysql_num_rows($result)==0)
		echo "<span class='alert'>No comments has been made yet!</span>";
	else 
	{
		$c = 0;
		while($row = mysql_fetch_assoc($result))
		{
			$c++;
			$text = preg_replace("#((http|https|ftp)://(\S*?\.\S*?))(\s|\;|\)|\]|\[|\{|\}|,|\"|'|:|\<|$|\.\s)#ie",
             "'<a href=\"$1\" target=\"_blank\">http://$3</a>$4'",$row['text']);
			 
			connect::selectDB('logondb');
			$query = mysql_query("SELECT username,id FROM account WHERE id='".$row['poster']."'"); 
			$pi = mysql_fetch_assoc($query); 
			$user = ucfirst(strtolower($pi['username']));
			
			$getGM = mysql_query("SELECT COUNT(gmlevel) FROM account_access WHERE id='".$pi['id']."' AND gmlevel>'0'");
			?>
			<div class="news_comment" id="comment-<?php echo $row['id']; ?>"> 
                <div class="news_comment_user"><?php echo $user; 
					if(mysql_result($getGM,0)>0)
						echo "<br/><span class='blue_text' style='font-size: 11px;'>Staff</span>";
					?>
                </div> 
                <div class="news_comment_body">
                
                <?php if(mysql_result($getGM,0)>0) { echo "<span class='blue_text'>"; } ?>
                
                <span id="comment-<?php echo $row['id']; ?>-content">
				
					<?php echo nl2br(htmlentities($text));
					
                if(mysql_result($getGM,0)>0) { echo "</span>"; }
				
				echo '</span>';
				
				if(isset($_SESSION['cw_gmlevel']) && $_SESSION['cw_gmlevel']>=$GLOBALS['adminPanel_minlvl'] || 
				isset($_SESSION['cw_gmlevel']) && $_SESSION['cw_gmlevel']>=$GLOBALS['staffPanel_minlvl'] && $GLOBALS['editNewsComments']==true)
				 	echo '<br/><br/> ( <a href="#" onclick="editNewsComment('.$row['id'].')">Edit</a> | <a href="#remove" onclick="removeNewsComment('.$row['id'].')">Remove</a> )';  
			   ?>
               <div class='news_count'>
               		<?php echo '#'.$c; ?>
               </div>
              </div>
            </div>
            <?php
		}
	}
}  
}
else
{
	 $result = mysql_query("SELECT * FROM news ORDER BY id DESC");
	 while($row = mysql_fetch_assoc($result)) 
	 {
			if(file_exists($row['image']))
			{
			 ?>
             	<table class="news" width="100%"> 
                    <tr>
                        <td><h3 class="yellow_text"><?php echo $row['title']; ?></h3></td>
                    </tr>
				</table>
				<table class="news_content" cellpadding="4"> 
                   <tr>
                      <td><img src="<?php echo $row['image']; ?>" alt=""/></td> 
                      <td>
              <?php            
			}
			else
			{
				?>
                <table class="news" width="100%"> 
                    <tr>
                        <td><h3 class="yellow_text"><?php echo $row['title']; ?></h3></td>
                    </tr>
               </table>
               <table class="news_content" cellpadding="4"> 
                   <tr>
                       <td>
                <?php       
			}
			
			$text = preg_replace("#((http|https|ftp)://(\S*?\.\S*?))(\s|\;|\)|\]|\[|\{|\}|,|\"|'|:|\<|$|\.\s)#ie",
			"'<a href=\"$1\" target=\"_blank\">http://$3</a>$4'",$row['body']);
			
			if ($GLOBALS['news']['limitHomeCharacters']==true) 
			{ 		
				echo website::limit_characters($text,200);
				$output.= website::limit_characters($row['body'],200);
			} 
			else 
			{
				 echo nl2br($text); 
				 $output .= nl2br($row['body']); 
			}
			
			$commentsNum = mysql_query("SELECT COUNT(id) FROM news_comments WHERE newsid='".$row['id']."'");
							 
			if($GLOBALS['news']['enableComments']==TRUE) 
				$comments = '| <a href="?p=news&amp;newsid='.$row['id'].'">Comments ('.mysql_result($commentsNum,0).')</a>';
			else
				$comments = NULL;
			 
			echo '
			<br/><br/><br/>
			<i class="gray_text"> Written by '.$row['author'].' | '.$row['date'].' '.$comments.'</i>
			</td> 
			</tr>
			</table>';
					
	 }
}