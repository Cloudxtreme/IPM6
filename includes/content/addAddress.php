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


$parentNetwork = new network($_GET['network'],$db);

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
	$address = new address(0,$db);
	
	if (!isset($parentNetwork->_network_v6_mask)) {
		$newAddress = ip2long($_POST['address']);
	}
	else {
		$newAddress = ipv62long(Net_IPv6::Uncompress($_POST['address']));	
	}
	
	$checkAddress = $address->getAddressByNetworkAddress($parentNetwork->_network_id,$newAddress);
	
	if (!isset($parentNetwork->_network_v6_mask) && (!(Net_IPv4::ipInNetwork($_POST['address'], long2ip($parentNetwork->_network).get_slash(long2ip($parentNetwork->_network_mask)))))) { ?>
			
		<div class="ipm_error">Error: You have chosen an address that is outside of the current network, or the address is not a valid IP address.</div>
	<?php
		
	}
	elseif (!(Net_IPv6::isInNetmask($_POST['address'], long2ipv6($parentNetwork->_network), $parentNetwork->_network_v6_mask))) { ?>
			
		<div class="ipm_error">Error: You have chosen an address that is outside of the current network, or the address is not a valid IP address.</div>
	<?php 
			
	}
	elseif ($address->_addresses_total > 0) { ?>
	
		<div class="ipm_error">Error: The address already exists.</div>
	<?php
		
	}
	elseif ($_POST['descr'] == "") { ?>
	
		<div class="ipm_error">Error: The description field is required.</div>
	<?php 
		
	}
	elseif ($_POST['customer'] == "") { ?>
	
		<div class="ipm_error">Error: The customer field is required.</div>

	<?php		
	}
	else {

		$values = array(
				array("col" => "address",
					"val" => $newAddress,
					"type" => "char"),
				array("col" => "descr",
					"val" => $_POST['descr'],
					"type" => "char"),
				array("col" => "network",
					"val" => $_POST['network'],
					"type" => "int"),
				array("col" => "customer",
					"val" => $_POST['customer'],
					"type" => "int"),
				array("col" => "comments",
					"val" => $_POST['comments'],
					"type" => "char"),
				array("col" => "`user`",
					"val" => $_SESSION['MM_Username'],
					"type" => "char"),
				array("col" => "`date`",
					"val" => "now()",
					"type" => "int")
				
			);
		
		$sql = $db->insertInto("addresses",$values);
		
		?>
		<p>Action completed.</p>
		<a href="" onclick="dim('ipm_form',false); ShowContent('networks',<?php echo $parentNetwork->_network_container; ?>,<?php echo $parentNetwork->_network_id; ?><?php if (!isnull($parentNetwork->_network_group)) { echo ",".$parentNetwork->_network_group; } ?>); return false;" title="Close"><br /><div class="ipm_menu_button">Close</div>
	
	<?php
		exit();
		
	}
	
}

 
#Validate network

if (!isset($parentNetwork->_network_v6_mask)) {
	
	$net = find_net(long2ip($parentNetwork->_network),long2ip($parentNetwork->_network_mask));
	
	if (long2ip($parentNetwork->_network_mask) == "255.255.255.254" && ($parentNetwork->_network_addresses == 2)) { ?>
		
		<div class="ipm_error">Error: The network is full.</div>
	<?php 
		exit();
		
	}
	
	elseif (long2ip($parentNetwork->_network_mask) != "255.255.255.254" && $net['total'] == $parentNetwork->_network_addresses) { ?>
		
		<div class="ipm_error">Error: The network is full.</div>
	<?php 
		exit();
		
	}
		
}
else {
	
	if (bcpow(2,(128 - $parentNetwork->_network_v6_mask)) == $parentNetwork->_network_addresses) { ?>
		
		<div class="ipm_error">Error: The network is full.</div>
	<?php 
		exit();
		
	}
	
}

$address = new address(0,$db);

$allCustomers = new customer(0,$db);
$_allCustomers = $allCustomers->getAllCustomers($parentNetwork->_network_container);
$row_allCustomers = mysql_fetch_assoc($_allCustomers);

?>

<h2>Add Address to Network <?php if (isnull($parentNetwork->_network_v6_mask)) { echo long2ip($parentNetwork->_network).get_slash(long2ip($parentNetwork->_network_mask)); } else { echo Net_IPv6::Compress(long2ipv6($parentNetwork->_network))."/".$parentNetwork->_network_v6_mask; } ?></h2>

<div id="ipm_table_2col" style="width: 50%">
	
	<div id="row">
		
		<div id="col1"><strong>Address*</strong></div>
		<div id="col2"><input type="text" class="input_standard" size="50" maxlength="255" id="addAddress_address" value="<?php if (isset($_POST['address'])) { echo $_POST['address']; } elseif (!isset($parentNetwork->_network_v6_mask)) { echo long2ip($address->getNextAddress($parentNetwork->_network_id)); } else { echo long2ipv6($address->getNextAddress($parentNetwork->_network_id)); } ?>"></div>
		<div class="clear"></div>
		
	</div>
	<div id="row">
		
		<div id="col1"><strong>Description*</strong></div>
		<div id="col2"><input type="text" class="input_standard" size="50" maxlength="255" id="addAddress_descr" value="<?php if (isset($_POST['address'])) { echo $_POST['descr']; } ?>"></div>
		<div class="clear"></div>
		
	</div>
	<div id="row">
		
		<div id="col1"><strong>Customer*</strong></div>
		<div id="col2"><select class="input_standard" id="addAddress_customer">
			<?php do { ?>
				<option value="<?php echo $row_allCustomers['id']; ?>" <?php if ($_POST['customer'] == $row_allCustomers['id']) { echo "selected"; } ?>><?php echo $row_allCustomers['name']; ?>
			<?php } while ($row_allCustomers = mysql_fetch_assoc($_allCustomers)); ?>
			</select></div>
		<div class="clear"></div>
		
	</div>
	<div id="row">
		
		<div id="col1"><strong>Comments</strong></div>
		<div id="col2"><textarea class="input_standard" cols="50" rows="5" id="addAddress_comments"><?php if (isset($_POST['comments'])) { echo $_POST['comments']; } ?></textarea></div>
		<div class="clear"></div>
		
	</div>
	<div id="row">
		<div id="col1">&nbsp;</div>
		<div class="clear"></div>
	</div>
	<div id="row">
		
		<div id="col1"><a href="" onclick="PostDialogContent('AddAddress',<?php echo $parentNetwork->_network_id; ?>,'address='+document.getElementById('addAddress_address').value+'&network=<?php echo $parentNetwork->_network_id; ?>&descr='+document.getElementById('addAddress_descr').value+'&customer='+document.getElementById('addAddress_customer').value+'&comments='+document.getElementById('addAddress_comments').value+'&token=insert'); return false;"><div class="ipm_menu_button">Add</</a></div>
		<div class="clear"></div>
		
	</div>
	
</div>

