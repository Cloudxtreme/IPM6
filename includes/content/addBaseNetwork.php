<?php include('../ipm_class.php'); ?>
<?php include('Net/IPv4.php'); ?>
<?php include('Net/IPv6.php'); ?>

<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "";
$MM_donotCheckaccess = "true";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && true) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "login.php?status=permissionfail";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($QUERY_STRING) && strlen($QUERY_STRING) > 0) 
  $MM_referrer .= "?" . $QUERY_STRING;
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}

$ipm = new ipm();
$db = new Mysql();

$user = new user($_SESSION['MM_Username'], $db);

$title = $ipm->getApp()." ".$ipm->getVer();


if (isset($_GET['container'])) {

	$authContainer = $user->getAuthContainer($_GET['container']);
	
}

?>

<a href="" onclick="dim('ipm_form',false); ShowContent('networks',<?php echo $_GET['container']; ?>); return false;" title="Close"><img src="images/cancel.gif" alt="Close" id="cancel"></a>

<?php if ($authContainer < 1) { ?>

	<div class="ipm_error">Error: You are not authorized to view the selected content.</div>
	<?php 
		exit();
} ?>

<?php
if (!isnull($_GET['container']) && !($user->getAuthContainer($_GET['container'],$_SESSION['MM_Username']) > 10)) { ?>

	<div class="ipm_error">Error: You are not authorized to view the selected content.</div>
	<?php 
		exit();
} ?>

<?php

#Process POST data


if (isset($_POST['token'])) {
	
	#Validate POST data
	
	$newNetwork = new network(0,$db);
		
	if (!(Net_IPv6::checkIPv6($_POST['network']))) {
		
		$ip_calc = new Net_IPv4();
		$ip_calc->ip = $_POST['network'];
		$ip_calc->netmask = get_dotted_mask($_POST['mask']);
		$ip_calc->calculate();
		
		$_POST['network'] = $ip_calc->network;

			
		if (!(Net_IPv4::validateIP($_POST['network']))) {?>
			<div class="ipm_error">Error: The network you entered is invalid.</div>
		<?php 
		}
		elseif ($_POST['mask'] < 8 || $_POST['mask'] > 32) { ?>
			
			<div class="ipm_error">Error: The mask you entered is invalid for IPv4.</div>
		<?php
		
		}
		elseif ($newNetwork->checkBaseOverlap($_GET['container'],$_POST['network'],get_dotted_mask($_POST['mask']))) { ?>
			
			<div class="ipm_error">Error: The network overlaps with other networks in this container.</div>
		<?php
			
		}
		elseif (isnull($_POST['descr'])) { ?>
			
			<div class="ipm_error">Error: The description field is required.</div>
		<?php
			
		}
		elseif (isnull($_POST['network'])) { ?>
			
			<div class="ipm_error">Error: The network field is required.</div>
		<?php
			
		}
		else {
				
			$values = array(
				array("col" => "network",
					"val" => ip2long($_POST['network']),
					"type" => "char"),
				array("col" => "short",
					"val" => $_POST['network'],
					"type" => "char"),
				array("col" => "mask",
					"val" => get_dotted_mask($_POST['mask']),
					"type" => "char"),
				array("col" => "descr",
					"val" => $_POST['descr'],
					"type" => "char"),
				array("col" => "maskLong",
					"val" => ip2long(get_dotted_mask($_POST['mask'])),
					"type" => "char"),
				array("col" => "`user`",
					"val" => $_SESSION['MM_Username'],
					"type" => "char"),
				array("col" => "`date`",
					"val" => "now()",
					"type" => "int"),
				array("col" => "comments",
					"val" => $_POST['comments'],
					"type" => "char"),
				array("col" => "container",
					"val" => $_POST['container'],
					"type" => "char"),
				array("col" => "networkGroup",
					"val" => $_POST['netgroup'],
					"type" => "int")
				
					);
			
			$sql = $db->insertInto("networks",$values);

			?>
			<p>Action completed.</p>
			<a href="" onclick="dim('ipm_form',false); ShowContent('networks',<?php echo $_GET['container']; ?>); return false;" title="Close"><br /><div class="ipm_menu_button">Close</div>
		
			<?php
			exit();
				
		}
		
	}
	else {
			
		$_POST['network'] = Net_IPv6::getNetmask(Net_IPv6::uncompress($_POST['network']), $_POST['mask']);
		
		if (!Net_IPv6::checkIPv6($_POST['network'])) { ?>
		
			<div class="ipm_error">Error: The network you entered is invalid.</div>
		<?php 
		}
		elseif ($_POST['mask'] < 8 || $_POST['mask'] > 128) { ?>
			
			<div class="ipm_error">Error: The mask you entered is invalid for IPv6.</div>
		<?php
		
		}
		elseif ($newNetwork->checkOverlap($_GET['container'],$_POST['network'],$_POST['mask'])) { ?>
		
		<div class="ipm_error">Error: The network overlaps with other networks in this container.</div>
		<?php
			
		}
		elseif (isnull($_POST['descr'])) { ?>
			
			<div class="ipm_error">Error: The description field is required.</div>
		<?php
			
		}
		elseif (isnull($_POST['network'])) { ?>
			
			<div class="ipm_error">Error: The network field is required.</div>
		<?php
			
		}
		else {
				
			$values = array(
				array("col" => "network",
					"val" => ipv62long($_POST['network']),
					"type" => "char"),
				array("col" => "short",
					"val" => $_POST['network'],
					"type" => "char"),
				array("col" => "v6mask",
					"val" => $_POST['mask'],
					"type" => "char"),
				array("col" => "descr",
					"val" => $_POST['descr'],
					"type" => "char"),
				array("col" => "`user`",
					"val" => $_SESSION['MM_Username'],
					"type" => "char"),
				array("col" => "`date`",
					"val" => "now()",
					"type" => "int"),
				array("col" => "comments",
					"val" => $_POST['comments'],
					"type" => "char"),
				array("col" => "container",
					"val" => $_POST['container'],
					"type" => "char"),
				array("col" => "networkGroup",
					"val" => $_POST['netgroup'],
					"type" => "int")
				
					);
							
			$sql = $db->insertInto("networks",$values);
			?>
			<p>Action completed.</p>
			<a href="" onclick="dim('ipm_form',false); ShowContent('networks',<?php echo $_GET['container']; ?>); return false;" title="Close"><br /><div class="ipm_menu_button">Close</div>
		
			<?php
			exit();
			
		}
	
	}
	
}

$allCustomers = new customer(0,$db);
$_allCustomers = $allCustomers->getAllCustomers($_GET['container']);
$row_allCustomers = mysql_fetch_assoc($_allCustomers);

$allNetworkGroups = new networkgroup(0,$db);
$_allNetworkGroups = $allNetworkGroups->getAllNetworkgroups($_GET['container']);
$row_allNetworkGroups = mysql_fetch_assoc($_allNetworkGroups);

?>

<h2>Add a Base Network</h2>

<div id="ipm_table_2col">

	<div id="row">
		
		<div id="col1"><strong>Mask Bits* (e.g. 24 for /24 or 64 for IPv6 /64)</strong></div>
		<div id="col2">
			<input type="text" id="addNetwork_mask" class="input_standard" size="10" maxlength="3" value="<?php echo $_POST['mask']; ?>">
		</div>
	</div>
	<div class="clear"></div>
	<div id="row">
		
		<div id="col1"><strong>Network* (IPv4 or IPv6)</strong></div>
		<div id="col2"><input type="text" size="50" class="input_standard" id="addNetwork_network" value="<?php echo $_POST['network']; ?>"></div>
		<div class="clear"></div>
		
	</div>
	<div id="row">
	<div id="row">
		
		<div id="col1"><strong>Description*</strong></div>
		<div id="col2"><input type="text" size="50" class="input_standard" id="addNetwork_descr" value="<?php echo $_POST['descr']; ?>"></div>
		<div class="clear"></div>
		
	</div>
	<div id="row">
		
		<div id="col1"><strong>Network Group</strong></div>
		<div id="col2">
			<?php if ($allNetworkGroups->_networkgroups_total) { ?>
				
				<select id="addNetwork_group" class="input_standard">
					
					<option value="">None</option>
					<?php do { ?>
					
						<option value="<?php echo $row_allNetworkGroups['id']; ?>" <?php if ($_POST['netgroup'] == $row_allNetworkGroups['id']) { echo "SELECTED"; } elseif ($parentNetwork->_network_group == $row_allNetworkGroups['id']) { echo "SELECTED"; } ?>><?php echo $row_allNetworkGroups['name']; ?></option>
					
					<?php } while ($row_allNetworkGroups = mysql_fetch_assoc($_allNetworkGroups)); ?>
					
				</select>
				
			<?php } else { ?>
				
				There are no network groups defined for this container.
				
			<?php } ?>
		</div>
		<div class="clear"></div>
		
	</div>
	<div id="row">
		
		<div id="col1"><strong>Comments</strong></div>
		<div id="col2"><textarea class="input_standard" cols="50" rows="5" id="addNetwork_comments"><?php echo $_POST['comments']; ?></textarea></div>
		<div class="clear"></div>
		
	</div>
	<div id="row">
	
		<div id="col1"><a href="" onclick="PostDialogContent('AddBaseNetwork',<?php echo $_GET['container']; ?>,'network='+document.getElementById('addNetwork_network').value+'&mask='+document.getElementById('addNetwork_mask').value+'&descr='+document.getElementById('addNetwork_descr').value+<?php if ($allNetworkGroups->_networkgroups_total > 0) { ?>'&netgroup='+document.getElementById('addNetwork_group').options[document.getElementById('addNetwork_group').selectedIndex].value+<?php } ?>'&container=<?php echo $_GET['container']; ?>&token=insert&comments='+document.getElementById('addNetwork_comments').value); return false;"><div class="ipm_menu_button">Add</</a></div>
		<div class="clear"></div>
		
	</div>
	
</div>

