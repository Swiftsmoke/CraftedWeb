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
$divide = 40;
##############


account::isNotLoggedIn();
?>

<h2>Currency converter</h2>
<?php echo $GLOBALS['website_title']; ?> now lets you convert your Vote Points into <?php echo $GLOBALS['donation']['coins_name']; ?>!<br/>
Every <?php echo $divide; ?>th Vote Point will give you 1 donation coin, simple! <br/>
You currently have <b><?php echo account::loadVP($_SESSION['cw_user']); ?></b> Vote Points which would give you <b><?php echo floor(account::loadVP($_SESSION['cw_user'])/$divide); ?></b> <?php echo $GLOBALS['donation']['coins_name']; ?>.

<hr/>

<form action="?p=convert" method="post">
<table>
	<tr>
    	<td>
        	Vote Points:
        </td>
        <td>
        	 <select name="conv_vp" onchange="calcConvert(<?php echo $divide; ?>)" id="conv_vp">
                  <option value="40">40</option>
                  <option value="80">80</option>
                  <option value="120">120</option>
                  <option value="160">160</option>
                  <option value="200">200</option>
          	</select>
        </td>
   </tr>
   <tr>
   		<td>
        <?php echo $GLOBALS['donation']['coins_name']; ?>: 
        </td>
        <td>
        	<input type="text" id="conv_dp" style="width: 70px;" value="1" readonly/>
        </td>
   </tr>
    <tr>
   		<td>
        </td>
        <td>
        	<hr/>
        </td>
   </tr>
   <tr>
   		<td>
        </td>
        <td>
        	<input type="submit" value="Convert" name="convert" />
        </td>
   </tr>
</table>   	     
</form>
<?php
if(isset($_POST['convert'])) {
	$vp = round((int)$_POST['conv_vp']);
	
	if(account::hasVP($_SESSION['cw_user'],$vp)==FALSE) 
		echo "<span class='alert'>You do not have enough Vote Points!</span>";
	else {
		$dp = floor($vp / $divide);
		
		account::deductVP(account::getAccountID($_SESSION['cw_user']),$vp);
		account::addDP(account::getAccountID($_SESSION['cw_user']),$dp);	
		
		account::logThis("Converted ".$vp." Vote Points into ".$dp." ".$GLOBALS['donation']['coins_name'],"currencyconvert",NULL);
		
		header("Location: ?p=convert");
        exit;
	}
}
?>