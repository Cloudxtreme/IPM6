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

if (isnull($_GET['parent'])) {
	
	$_GET['parent'] = 0;
}

$allNetworks = new network(0,$db);
$_allNetworks = $allNetworks->getAllNetworks($_GET['container'],$_GET['group'],$_GET['parent']);
$_allNetworks_arr = $allNetworks->getAllNetworks($_GET['container'],$_GET['group'],$_GET['parent']);
$row_networks = mysql_fetch_assoc($_allNetworks);

$parentNetwork = new network($_GET['parent'],$db);

if (!isnull($_GET['group'])) {
	
	$netgroup = new networkgroup($_GET['group'],$db);
	
}

$allNetworkgroups = new networkgroup(0,$db);
$_allNetworkgroups = $allNetworkgroups->getAllNetworkgroups($_GET['container']);
$row_networkgroups = mysql_fetch_assoc($_allNetworkgroups);

?>


<div class="breadcrumbs" id="breadcrumbs">
	<div id="nav"><strong>Networks</strong></div><div id="arrows">&gt;&gt;</div><div id="nav"><select id="nav_group" onchange="ShowContent('networks',<?php echo $_GET['container']; ?>, null, document.getElementById('nav_group').options[document.getElementById('nav_group').selectedIndex].value); return false;">
		<option value="">All Networks</option>
		<?php if ($allNetworkgroups->_networkgroups_total > 0) { // Show if recordset not empty
			do { ?>
				<option value="<?php echo $row_networkgroups['id']; ?>" <?php if ($row_networkgroups['id'] == $_GET['group']) { echo "selected"; } ?>><?php if (strlen($row_networkgroups['name']) < 25) { echo $row_networkgroups['name']; } else { echo substr_replace($row_networkgroups['name'],'...',25); } ?></option>
			<?php } while ($row_networkgroups = mysql_fetch_assoc($_allNetworkgroups));
			}
			?>
	</select></div><div id="arrows">&gt;&gt;</div><div id="nav"><?php if (isnull($_GET['parent']) || (!isnull($_GET['group']) && $_GET['parent'] == $allNetworks->_network_group_min_parent)) { echo "All Networks"; } else { ?><a href="" title="Return to parent network" onclick="ShowContent('networks',<?php echo $_GET['container']; ?>,<?php echo $parentNetwork->_network_parent; ?>,<?php echo $_GET['group']; ?>); return false;"><?php if ($parentNetwork->_network_v6_mask) { echo Net_IPv6::Compress(long2ipv6($parentNetwork->_network))."/".$parentNetwork->_network_v6_mask; } else { echo long2ip($parentNetwork->_network).get_slash(long2ip($parentNetwork->_network_mask)); } ?></a>&nbsp;<a href="" title="Network info" onclick="dim('ipm_dialog'); ShowDialogContent('ipm_dialog','networkInfo',<?php echo $parentNetwork->_network_id; ?>); return false;"><img src="images/info.png" alt="Network info" title="Network info"></a><?php } ?>&nbsp;<div id="NetworkOptions" class="options"><a href="" onclick="ShowOptions('NetworkOptions','NetworkMenu'); return false;" title="Network options"><img src="images/options.png" alt="Network options" title="Network options" id="cog"></a>&nbsp;
		<span id="NetworkMenu" class="optionsMenu">
		<?php if (isnull($_GET['parent'])) { ?>
		<a href="" onclick="dim('ipm_form'); ShowDialogContent('ipm_form','AddBaseNetwork','<?php echo $_GET['container']; ?>'); return false;"><div class="ipm_menu_button">Add Base Network</div></a>
		<?php }
		elseif ($parentNetwork->_network_addresses == 0 && long2ip($parentNetwork->_network_mask) != "255.255.255.255" && $parentNetwork->_network_v6_mask != "128") { ?>
		<a href="" onclick="dim('ipm_form'); ShowDialogContent('ipm_form','AddNetwork','<?php echo $parentNetwork->_network_id; ?>'); return false;"><div class="ipm_menu_button">Add Network</div></a>
		<?php } ?>
		<?php if (isset($parentNetwork->_network)) { ?>
		<a href="" onclick="dim('ipm_form'); ShowDialogContent('ipm_form','EditNetwork','<?php echo $parentNetwork->_network_id; ?>'); return false;"><div class="ipm_menu_button">Edit</div></a>
		<a href="" onclick="dim('ipm_form'); ShowDialogContent('ipm_form','DeleteNetwork','<?php echo $parentNetwork->_network_id; ?>'); return false;"><div class="ipm_menu_button">Delete</div></a>
		<?php if (!$parentNetwork->_network_subnetted && long2ip($parentNetwork->_network_mask) != "255.255.255.255" && $parentNetwork->_network_v6_mask != "128") { ?>
		<a href="" onclick="dim('ipm_form'); ShowDialogContent('ipm_form','AddAddress','<?php echo $parentNetwork->_network_id; ?>'); return false;"><div class="ipm_menu_button">Add Address</div></a>
		<?php } ?>
		<?php } ?>
		
		</span>		
	</div>
	</div><div class="clear"></div>
</div>
</div><br />





<?php if ($authContainer < 1) { ?>

	<div class="ipm_error">Error: You are not authorized to view the selected content.</div>
	<?php 
		exit();
} ?>

<?php
if (!isnull($_GET['parent']) && !($user->getAuthNetwork($_GET['parent'], $_SESSION['MM_Username']) > 0 || ($user->getAuthNetworkgroup($_GET['group'],$_SESSION['MM_Username']) > 0 && $user->getAuthNetwork($_GET['parent'],$_SESSION['MM_Username']) == "") || ($user->getAuthContainer($_GET['container'],$_SESSION['MM_Username']) > 0 && $user->getAuthNetworkGroup($_GET['group'],$_SESSION['MM_Username']) == "" && $user->getAuthNetwork($_GET['parent'],$_SESSION['MM_Username']) == ""))) { ?>

	<div class="ipm_error">Error: You are not authorized to view the selected content.</div>
	<?php 
		exit();
} ?>
		
<?php
if (isnull($parentNetwork->_network_id) && $allNetworks->_networks_total ==0) { ?>
	
	<p>There are no networks to display.</p>  <a href="" onclick="dim('ipm_form'); ShowDialogContent('ipm_form','AddBaseNetwork','<?php echo $_GET['container']; ?>'); return false;"><div class="ipm_menu_button">Add Base Network</div></a>

<?php	
}
elseif ($allNetworks->_networks_total > 0) { 
	
	$networks_arr = array();
	do {
		
		array_push($networks_arr, $row_networks['id']);
		
	} while ($row_networks = mysql_fetch_assoc($_allNetworks));
	
?>

<?php
		
	$arr_count = 0;
	$count = 0;
	$div_count = 0;
	
	for ($i = $arr_count; $i < count($networks_arr); $i++) {
		
		if ($networks_arr[$i] == 0) {
			
			if ($ount > 0) {
				$count--;
			}
			$i++;
			
		}
		$network = new network($networks_arr[$i], $db);
		$net = find_net(long2ip($network->_network),long2ip($network->_network_mask));
		
		if (!$network->_network_subnetted && isnull($network->_network_v6_mask) && $network->_network_mask == ip2long("255.255.255.254")) { 
			$utilization = intval((($network->_network_addresses / 2 * 100)/10));
		}
		elseif (!$network->_network_subnetted && isnull($network->_network_v6_mask) && $network->_network_mask != ip2long("255.255.255.254") && $network->_network_mask != ip2long("255.255.255.255")) {
			$utilization = intval((($network->_network_addresses / $net['total'] * 100)/10));
		}
		else {
			$utilization = "";
		}
		
		for ($j = 0; $j < $count; $j++) { ?>
		
			<div id="utilization"></div>
			
		<?php	
		}
	?>

		<div id="utilization<?php echo $utilization; ?>"><?php if (!$network->_network_subnetted && isnull($network->_network_v6_mask) && $network->_network_mask == ip2long("255.255.255.254")) { printf("%d%%", $network->_network_addresses / 2 * 100); } elseif (!$network->_network_subnetted && isnull($network->_network_v6_mask) && $network->_network_mask != ip2long("255.255.255.255")) { printf("%d%%", $network->_network_addresses / $net['total'] * 100); } else { echo ""; } ?></div><div id="network" <?php if ($network->_network_subnetted) { echo "style=\"font-weight:bold\""; } ?>><a href="" title="Browse this network" onclick="ShowContent('networks',<?php echo $_GET['container']; ?>,<?php echo $network->_network_id; ?><?php if (!isnull($_GET['group'])) { echo ",".$_GET['group']; } ?>); return false;"><?php if ($network->_network_v6_mask) { echo Net_IPv6::Compress(long2ipv6($network->_network))."/".$network->_network_v6_mask; } else { echo long2ip($network->_network).get_slash(long2ip($network->_network_mask)); } ?></a>&nbsp; &nbsp;&nbsp;<?php echo $network->_network_description; ?></div>
	
		<?php
		
		if ($network->_network_subnetted && $count < $_GET['expand']) {

			$allSubnets = new network(0,$db);
			$_allSubnets = $allSubnets->getAllNetworks($_GET['container'],$_GET['group'],$network->_network_id);
			$row_subnets = mysql_fetch_assoc($_allSubnets);
			
			$subnets_arr = array();
			
			do {
				
				array_push($subnets_arr, $row_subnets['id']);
				
			} while ($row_subnets = mysql_fetch_assoc($_allSubnets));
			
			array_push($subnets_arr, 0);
			array_splice($networks_arr, $i+1, 0, $subnets_arr);
			
			$count++;
			
			#$div_count++;
			
			#print_r($networks_arr);
			
		}
		#elseif (!$network->_network_subnetted && $count > 0) {
			
		#	$count--;
			
		#}
		
	}
	
} else { 
	
	$allAddresses = new address(0,$db);
	$_allAddresses = $allAddresses->getAllAddresses($parentNetwork->_network_id,$_GET['sort'],$_GET['dir']);
	$row_addresses = mysql_fetch_assoc($_allAddresses);
	
?>
	
	<div id="ipm_table">
		
		<div id="header">
			
			<div id="address" style="<?php if ($allAddresses->_addresses_sort == "addresses.address") { echo "background-image: url('images/header_bg_selected.png');"; } ?>"><a href="" title="Sort by address" onclick="ShowContent('networks',<?php echo $_GET['container']; ?>,<?php echo $parentNetwork->_network_id; ?>,<?php echo $_GET['group']; ?>,'addresses.address',<?php if ($_GET['dir'] == "ASC") { echo "'DESC'"; } else { echo "'ASC'"; } ?>); return false;">Address</a></div>
			<div id="descr" style="<?php if ($allAddresses->_addresses_sort == "addresses.descr") { echo "background-image: url('images/header_bg_selected.png');"; } ?>"><a href="" title="Sort by description" onclick="ShowContent('networks',<?php echo $_GET['container']; ?>,<?php echo $parentNetwork->_network_id; ?>,<?php echo $_GET['group']; ?>,'addresses.descr',<?php if ($_GET['dir'] == "ASC") { echo "'DESC'"; } else { echo "'ASC'"; } ?>); return false;">Description</a></div>
			<div id="customer" style="<?php if ($allAddresses->_addresses_sort == "customer.name") { echo "background-image: url('images/header_bg_selected.png');"; } ?>"><a href="" title="Sort by customer" onclick="ShowContent('networks',<?php echo $_GET['container']; ?>,<?php echo $parentNetwork->_network_id; ?>,<?php echo $_GET['group']; ?>,'customer.name',<?php if ($_GET['dir'] == "ASC") { echo "'DESC'"; } else { echo "'ASC'"; } ?>); return false;">Customer</a></div>
			<div id="port" style="<?php if ($allAddresses->_addresses_sort == "portsdevices.name") { echo "background-image: url('images/header_bg_selected.png');"; } ?>"><a href="" title="Sort by device" onclick="ShowContent('networks',<?php echo $_GET['container']; ?>,<?php echo $parentNetwork->_network_id; ?>,<?php echo $_GET['group']; ?>,'portsdevices.name',<?php if ($_GET['dir'] == "ASC") { echo "'DESC'"; } else { echo "'ASC'"; } ?>); return false;">Device Port</a></div>
			<div id="user" style="<?php if ($allAddresses->_addresses_sort == "addresses.user") { echo "background-image: url('images/header_bg_selected.png');"; } ?>"><a href="" title="Sort by user" onclick="ShowContent('networks',<?php echo $_GET['container']; ?>,<?php echo $parentNetwork->_network_id; ?>,<?php echo $_GET['group']; ?>,'addresses.user',<?php if ($_GET['dir'] == "ASC") { echo "'DESC'"; } else { echo "'ASC'"; } ?>); return false;">User Information/Comments</a></div>
			<div class="clear"></div>
			
		</div>
		
		<?php  if (long2ip($parentNetwork->_network_mask) != "255.255.255.255" && $parentNetwork->_network_v6_mask != "128") { ?><div id="subheader"><a href="" onclick="dim('ipm_form'); ShowDialogContent('ipm_form','AddAddress','<?php echo $parentNetwork->_network_id; ?>'); return false;"><div class="ipm_menu_button">Add Address</div></a></div><?php } ?>
		<?php if ($parentNetwork->_network_addresses == 0 && long2ip($parentNetwork->_network_mask) != "255.255.255.255" && $parentNetwork->_network_v6_mask != "128") { ?>
		<a href="" onclick="dim('ipm_form'); ShowDialogContent('ipm_form','AddNetwork','<?php echo $parentNetwork->_network_id; ?>'); return false;"><div class="ipm_menu_button">Add Network</div></a>
		<?php } ?>
		<div class="clear"></div>
		
		<?php if ($allAddresses->_addresses_total > 0) { 
			
			$count = 0;
			
		?>
		
			<?php do { 
				
				$count++;
			?>
		
				<div id="row" style="<?php if (!($count % 2)) { echo "background-color: ".$ipm->getAltRowColor(); } ?>">
					
					<div id="address"><?php if (isset($parentNetwork->_network_v6_mask)) { echo Net_IPv6::Compress(long2ipv6($row_addresses['address'])); } else { echo long2ip($row_addresses['address']); } ?>&nbsp;						<div id="AddressOptions<?php echo $row_addresses['id']; ?>" class="options"><a href="" onclick="ShowOptions('AddressOptions<?php echo $row_addresses['id']; ?>','AddressMenu<?php echo $row_addresses['id']; ?>'); return false;" title="Address options"><img src="images/options.png" alt="Address options" title="Address options" id="cog"></a>&nbsp;
							<span id="AddressMenu<?php echo $row_addresses['id']; ?>" class="optionsMenu">
							<a href="" onclick="dim('ipm_form'); ShowDialogContent('ipm_form','EditAddress','<?php echo $row_addresses['id']; ?>'); return false;"><div class="ipm_menu_button">Edit</div></a>
							<a href="" onclick="dim('ipm_form'); ShowDialogContent('ipm_form','DeleteAddress','<?php echo $row_addresses['id']; ?>'); return false;"><div class="ipm_menu_button">Delete</div></a>
							
							</span>		
						</div><div class="clear"></div>
					</div>
					<div id="descr">&nbsp;<?php echo $row_addresses['descr']; ?></div>
					<div id="customer">&nbsp;<?php echo $row_addresses['customerName']; ?></div>
					<div id="port">&nbsp;<?php if (!isnull($row_addresses['deviceName'])) { echo $row_addresses['deviceName']." &gt;&gt; ".$row_addresses['cardTypeName']." "; if (isset($row_addresses['rack'])) { echo $row_addresses['rack']."/"; } if (isset($row_addresses['module'])) { echo $row_addresses['module']."/"; } if (isset($row_addresses['slot'])) { echo $row_addresses['slot']."/"; } echo $row_addresses['cardPort']; ?><?php if (isset($row_addresses['subint'])) { echo ".".$row_addresses['subint']; } } ?></div>
					<div id="user">&nbsp;<?php if (isset($row_addresses['user']) || isset($row_addresses['updateUser'])) { ?><a title="<?php $createUser = $user->getUserByUsername($row_addresses['user']); echo "Created by ".$createUser['firstname']." ".$createUser['lastname']." on ".$row_addresses['date']; if (isset($row_addresses['updateUser'])) { $updateUser = $user->getUserByUsername($row_addresses['updateUser']); echo ", updated by ".$updateUser['firstname']." ".$updateUser['lastname']." on ".$row_addresses['updateDate']; } ?>"><img src="images/info.png" alt="Address info"></a><?php } ?>&nbsp;<?php if (!isnull($row_addresses['comments'])) { ?><a title="<?php echo $row_addresses['comments']; ?>"><img src="images/comments.png" alt="Comments"></a><?php } ?>
</div>
					<div class="clear"></div>
					
				</div>
				
			<?php } while ($row_addresses = mysql_fetch_assoc($_allAddresses)); ?>

		<?php } elseif (long2ip($parentNetwork->_network_mask) == "255.255.255.255" || $parentNetwork->_network_v6_mask == 128) { ?>
		
			<p>This is a host network.  There are no addresses to display.</p>
			
		<?php } else { ?>
		
			<p>There are no addresses to display.</p>
			<p>You can add an address, or subnet this network further by choosing the <strong>Add Network</strong> option.</p>
		
		<?php } ?>
		
	</div>

<?php	
}
?>