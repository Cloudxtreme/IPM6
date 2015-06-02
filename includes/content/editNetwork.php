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

if (isset($_GET['network'])) {
	
	$network = new network($_GET['network'],$db);
	
}

$parentNetwork = new network($network->_network_id,$db);

if (isset($parentNetwork->_network_container)) {

	$authContainer = $user->getAuthContainer($parentNetwork->_network_container);
	
}

?>

<a href="" onclick="dim('ipm_form',false); ShowContent('networks',<?php echo $parentNetwork->_network_container; ?>,<?php echo $parentNetwork->_network_id; ?><?php if (!isnull($parentNetwork->_network_group)) { echo ",".$parentNetwork->_network_group; } ?>); return false;" title="Close"><img src="images/cancel.gif" alt="Close" id="cancel"></a>

<?php if ($authContainer < 1) { ?>

	<div class="ipm_error">Error: You are not authorized to view the selected content.</div>
	<?php 
		exit();
} ?>

<?php
if (!isnull($_GET['network']) && !($user->getAuthNetwork($parentNetwork->_network_id, $_SESSION['MM_Username']) > 10 || ($user->getAuthNetworkgroup($parentNetwork->_network_group,$_SESSION['MM_Username']) > 10 && $user->getAuthNetwork($parentNetwork->_network_id,$_SESSION['MM_Username']) == "") || ($user->getAuthContainer($parentNetwork->_network_container,$_SESSION['MM_Username']) > 10 && $user->getAuthNetworkGroup($parentNetwork->_network_group,$_SESSION['MM_Username']) == "" && $user->getAuthNetwork($parentNetwork->_network_id,$_SESSION['MM_Username']) == ""))) { ?>

	<div class="ipm_error">Error: You are not authorized to view the selected content.</div>
	<?php 
		exit();
} ?>

<?php

#Process POST data


if (isset($_POST['token'])) {

	#Validate POST data
	
	if ($_POST['descr'] == "") { ?>
	
		<div class="ipm_error">Error: The description field is required.</div>
	<?php 
		
	}
	else {

		$values = array(
				array("col" => "descr",
					"val" => $_POST['descr'],
					"type" => "char"),
				array("col" => "networkGroup",
					"val" => $_POST['netgroup'],
					"type" => "int"),
				array("col" => "comments",
					"val" => $_POST['comments'],
					"type" => "char"),
				array("col" => "`updateUser`",
					"val" => $_SESSION['MM_Username'],
					"type" => "char"),
				array("col" => "updateDate",
					"val" => "now()",
					"type" => "int")
				
			);
		
		$sql = $db->update("networks",$network->_network_id,$values);
		
		?>
		<p>Action completed.</p>
		<a href="" onclick="dim('ipm_form',false); ShowContent('networks',<?php echo $parentNetwork->_network_container; ?>,<?php echo $parentNetwork->_network_id; ?><?php if (!isnull($parentNetwork->_network_group)) { echo ",".$parentNetwork->_network_group; } ?>); return false;" title="Close"><br /><div class="ipm_menu_button">Close</div>
	
	<?php
		exit();
		
	}
	
}

$allNetworkGroups = new networkgroup(0,$db);
$_allNetworkGroups = $allNetworkGroups->getAllNetworkgroups($parentNetwork->_network_container);
$row_allNetworkGroups = mysql_fetch_assoc($_allNetworkGroups);

?>

<h2>Edit Network <?php if (isnull($parentNetwork->_network_v6_mask)) { echo long2ip($network->_network).get_slash(long2ip($network->_network_mask)); } else { echo Net_IPv6::Compress(long2ipv6($network->_network))."/".$network->_network_v6_mask; } ?></h2>

<div id="ipm_table_2col" style="width: 50%">
	
	<div id="row">
		
		<div id="col1"><strong>Description*</strong></div>
		<div id="col2"><input type="text" class="input_standard" size="50" maxlength="255" id="editNetwork_descr" value="<?php echo $network->_network_description; ?>"></div>
		<div class="clear"></div>
		
	</div>
	
	<div id="row">
		<div id="col1"><strong>Network Group</strong></div>
		<div id="col2">
			<?php if ($allNetworkGroups->_networkgroups_total) { ?>
				
				<select id="editNetwork_group" class="input_standard">
					
					<option value="">None</option>
					<?php do { ?>
					
						<option value="<?php echo $row_allNetworkGroups['id']; ?>" <?php if ($network->_network_group == $row_allNetworkGroups['id']) { echo "SELECTED"; } ?>><?php echo $row_allNetworkGroups['name']; ?></option>
					
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
		<div id="col2"><textarea class="input_standard" cols="50" rows="5" id="editNetwork_comments"><?php echo $network->_network_comments; ?></textarea></div>
		<div class="clear"></div>
		
	</div>
	<div id="row">
		<div id="col1">&nbsp;</div>
		<div class="clear"></div>
	</div>
	<div id="row">
		
		<div id="col1"><a href="" onclick="PostDialogContent('EditNetwork',<?php echo $network->_network_id; ?>,'descr='+document.getElementById('editNetwork_descr').value+'&comments='+document.getElementById('editNetwork_comments').value+<?php if ($allNetworkGroups->_networkgroups_total > 0) { ?>'&netgroup='+document.getElementById('editNetwork_group').options[document.getElementById('editNetwork_group').selectedIndex].value+<?php } ?>'&token=update'); return false;"><div class="ipm_menu_button">Update</div></a></div>
		<div class="clear"></div>
		
	</div>
	
</div>

