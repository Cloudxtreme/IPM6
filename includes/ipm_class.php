<?php

include('functions.php');

class ipm {

	const app = "IP Manager";
	const ver = "6";
	const release = "6.0.0 Enterprise";
	const altRowColor = "#F0F7FF";
	
	public function __construct() {
		
		if (!isset($_SESSION)) {
  			session_start();
		}
		
	}
	
	public function __destruct() {
		
		#$this->_database->dbDisconnect();
		
	}

	public function getApp() {
	
		return self::app;
		
	}
	
	public function getVer() {
	
		return self::ver;
		
	}
	
	public function getRelease() {
	
		return self::release;
		
	}
	
	public function getAltRowColor() {
	
		return self::altRowColor;
		
	}
	
}

class module {
	
	private $_auth_min;
	public $_mod_title;
	
	private function __construct($auth_min, $mod_title) {
		
		$this->_auth_min = $auth_min;
		$this->_mod_title = $mod_title;
		
	}
	
}

class container {
	
	public $_container_id;
	public $_container_name;
	public $_container_description;
	protected $_database;
	public $_container_total;
	public $_all_containers;
	public $_container_row;
	
	public function __construct($container, $database) {
		
		$this->_database = $database;
		$this->_database->dbConnect();
		
		$this->_container_id = $container;
		
		$this->_user_username = $user_username;
		
		$qry = "SELECT * FROM `container` WHERE `container`.id = '".$this->_container_id."'";
		
		$this->_database->selectCustom($qry);
		
		$this->_db_row = mysql_fetch_assoc($this->_database->dataSet);
		
		$this->_container_name = $this->_db_row['name'];
		$this->_container_description = $this->_db_row['descr'];
		
	}
	
	public function __destruct() {
		
	}
		
	public function getAllContainers() {
		
		$qry = "SELECT * FROM `container` ORDER BY `container`.name";
		
		$this->_all_containers = $this->_database->selectCustom($qry);
		$this->_container_total = $this->_database->_db_totalrows;
		
		return ($this->_all_containers);
		
	}
	
	public function getContainerID() {
		
		return $this->_container_id;
		
	}
	
	public function getContainerTotal() {
		
		return $this->_container_total;
		
	}
		
}

class user {
	
	public $_user_username;
	public $_user_firstname;
	public $_user_lastname;
	public $_user_email;
	public $user_usrgroup;
	public $_auth_page_level;
	public $_auth_container_level;
	public $_auth_networkgroup_level;
	public $_auth_network_level;
	public $_auth_devicegroup_level;
	public $_auth_device_level;
	private $_user_password;
	public $_user_active;
	protected $_database;
	protected $_db_row;
	
	public function __construct($user_username, $database) {
		
		$this->_database = $database;
		$this->_database->dbConnect();
		
		$this->_user_username = $user_username;
		
		$qry = "SELECT * FROM `user` WHERE `user`.username = '".$this->_user_username."'";
		
		$this->_database->selectCustom($qry);
		
		$this->_db_row = mysql_fetch_assoc($this->_database->dataSet);
		$this->_user_firstname = $this->_db_row['firstname'];
		$this->_user_lastname = $this->_db_row['lastname'];
		$this->_user_email = $this->_db_row['email'];
		$this->_user_active = $this->_db_row['inactive'];
		$this->_user_usrgroup = $this->_db_row['usrgroup'];
		
	}
	
	public function __destruct() {
		
	}
	
	public function getUserByUsername($username) {
		
		$qry = "SELECT * FROM `user` WHERE `user`.username = '".$username."'";
		
		$this->_database->selectCustom($qry);
		
		$this->_db_row = mysql_fetch_assoc($this->_database->dataSet);
		
		return ($this->_db_row);
		
	}
	
	public function getAuthPage($mod) {
		
		$user = $this->_user_username;
		
		$qry = "SELECT usrgrouppermissions.level FROM usrgrouppermissions LEFT JOIN usrgroup ON usrgroup.id = usrgrouppermissions.usrgroup LEFT JOIN module ON module.id = usrgrouppermissions.module LEFT JOIN `user` ON `user`.usrgroup = usrgroup.id WHERE `user`.username = '".$user."' AND module.id = '".$mod."'";
		
		$this->_database->selectCustom($qry);
		
		$this->_db_row = mysql_fetch_assoc($this->_database->dataSet);
		
		$this->_auth_page_level = $this->_db_row['level'];
		
		return ($this->_auth_page_level);
		
	}
	
	public function getAuthContainer($container) {
		
		$user = $this->_user_username;
		
		$qry = "SELECT usrgroupcontainerpermissions.level FROM usrgroupcontainerpermissions LEFT JOIN usrgroup ON usrgroup.id = usrgroupcontainerpermissions.usrgroup LEFT JOIN container ON container.id = usrgroupcontainerpermissions.container LEFT JOIN `user` ON `user`.usrgroup = usrgroup.id WHERE `user`.username = '".$user."' AND container.id = '".$container."';";
		
		$this->_database->selectCustom($qry);
		
		$this->_db_row = mysql_fetch_assoc($this->_database->dataSet);
		
		$this->_auth_container_level = $this->_db_row['level'];
		
		return ($this->_auth_container_level);
		
	}
	
	public function getAuthNetworkgroup($networkgroup) {
		
		$user = $this->_user_username;
		
		$qry = "SELECT usrgroupnetgrouppermissions.level FROM usrgroupnetgrouppermissions LEFT JOIN usrgroup ON usrgroup.id = usrgroupnetgrouppermissions.usrgroup LEFT JOIN networkgroup ON networkgroup.id = usrgroupnetgrouppermissions.networkgroup LEFT JOIN `user` ON `user`.usrgroup = usrgroup.id WHERE `user`.username = '".$user."' AND networkgroup.id = '".$networkgroup."';";
		
		$this->_database->selectCustom($qry);
		
		$this->_db_row = mysql_fetch_assoc($this->_database->dataSet);
		
		$this->_auth_networkgroup_level = $this->_db_row['level'];
		
		return ($this->_auth_networkgroup_level);
		
	}
	
	public function getAuthNetwork($network) {
		
		$user = $this->_user_username;
		
		$qry = "SELECT usrgroupnetworkpermissions.level FROM usrgroupnetworkpermissions LEFT JOIN usrgroup ON usrgroup.id = usrgroupnetworkpermissions.usrgroup LEFT JOIN networks ON networks.id = usrgroupnetworkpermissions.network LEFT JOIN `user` ON `user`.usrgroup = usrgroup.id WHERE `user`.username = '".$user."' AND networks.id = '".$network."';";
		
		$this->_database->selectCustom($qry);
		
		$this->_db_row = mysql_fetch_assoc($this->_database->dataSet);
		
		$this->_auth_network_level = $this->_db_row['level'];
		
		return ($this->_auth_network_level);
		
	}
	
	public function getAuthDevicegroup($devicegroup) {
		
		$user = $this->_user_username;
		
		$qry = "SELECT usrgroupdevicegrouppermissions.level FROM usrgroupdevicegrouppermissions LEFT JOIN usrgroup ON usrgroup.id = usrgroupdevicegrouppermissions.usrgroup LEFT JOIN portgroups ON portgroups.id = usrgroupdevicegrouppermissions.devicegroup LEFT JOIN `user` ON `user`.usrgroup = usrgroup.id WHERE `user`.username = '".$user."' AND portgroups.id = '".$devicegroup."';";
		
		$this->_database->selectCustom($qry);
		
		$this->_db_row = mysql_fetch_assoc($this->_database->dataSet);
		
		$this->_auth_devicegroup_level = $this->_db_row['level'];
		
		return ($this->_auth_devicegroup_level);
		
	}
	
	public function getAuthDevice($device) {
		
		$user = $this->_user_username;
		
		$qry = "SELECT usrgroupdevicepermissions.level FROM usrgroupdevicepermissions LEFT JOIN usrgroup ON usrgroup.id = usrgroupdevicepermissions.usrgroup LEFT JOIN portsdevices ON portsdevices.id = usrgroupdevicepermissions.device LEFT JOIN `user` ON `user`.usrgroup = usrgroup.id WHERE `user`.username = '".$user."' AND portsdevices.id = '".$device."';";
		
		$this->_database->selectCustom($qry);
		
		$this->_db_row = mysql_fetch_assoc($this->_database->dataSet);
		
		$this->_auth_device_level = $this->_db_row['level'];
		
		return ($this->_auth_device_level);
		
	}
	
}

class DBconfig {
    
	protected $_db_serverName;
    protected $_db_userName;
    protected $_db_passCode;
    protected $_db_dbName;

    public function __construct() {
        $this ->_db_serverName = 'localhost';
        $this ->_db_userName = 'root';
        $this ->_db_passCode = '';
        $this ->_db_dbName = 'ipmanager';
	}
    
}

class Mysql extends DBconfig    {
	
	public $connectionString;
	public $dataSet;
	public $_db_row;
	public $_db_totalrows;
	private $sqlQuery;

    protected $databaseName;
    protected $hostName;
    protected $userName;
    protected $passCode;

	public function __construct()    {
		
    	$this -> connectionString = NULL;
    	$this -> sqlQuery = NULL;
    	$this -> dataSet = NULL;

        $dbPara = new DBconfig();
        $this -> databaseName = $dbPara -> _db_dbName;
        $this -> serverName = $dbPara -> _db_serverName;
        $this -> userName = $dbPara -> _db_userName;
        $this -> passCode = $dbPara ->_db_passCode;
        $dbPara = NULL;
            
    }

	public function dbConnect()    {
    	
    	$this -> connectionString = mysql_connect($this -> serverName,$this -> userName,$this -> passCode);
    	mysql_select_db($this -> databaseName,$this -> connectionString);
    	return $this -> connectionString;
	
	}

	public function dbDisconnect() {
    
    	$this -> connectionString = NULL;
    	$this -> sqlQuery = NULL;
	    $this -> dataSet = NULL;
	    $this -> databaseName = NULL;
    	$this -> hostName = NULL;
	    $this -> userName = NULL;
    	$this -> passCode = NULL;
		$this->_db_row = NULL;
		$this->_db_totalrows = NULL;

	}

	public function selectCustom($qry) {
		
		$this -> sqlQuery = $qry;
    	$this -> dataSet = mysql_query($this -> sqlQuery,$this -> connectionString);
		$this->_db_totalrows = mysql_num_rows($this->dataSet);
		
		return $this->dataSet;
		
	}
		
	public function selectAll($tableName)  {
    
    	$this -> sqlQuery = 'SELECT * FROM '.$this -> databaseName.'.'.$tableName;
    	$this -> dataSet = mysql_query($this -> sqlQuery,$this -> connectionString);
        
        return $this -> dataSet;

	}

	public function selectWhere($tableName,$rowName,$operator,$value,$valueType)   {
    	$this -> sqlQuery = 'SELECT * FROM '.$tableName.' WHERE '.$rowName.' '.$operator.' ';
    	if($valueType == 'int') {
        	$this -> sqlQuery .= $value;
    	}
    	else if($valueType == 'char')   {
        	$this -> sqlQuery .= "'".$value."'";
    	}
    	$this -> dataSet = mysql_query($this -> sqlQuery,$this -> connectionString);
    	$this -> sqlQuery = NULL;
    
    	return $this -> dataSet;
    	#return $this -> sqlQuery;

	}

	public function insertInto($tableName,$values) {
    	$i = NULL;

    	$this -> sqlQuery = 'INSERT INTO '.$tableName.' (';
    	
    	foreach($values as $value)    {
	    	
    		if (isset($sqlCols) && !($value["type"] == 'int' && isnull($value['val'])))  {
            	$sqlValues .= ',';
            	$sqlCols .= ',';
        	}
			
			if(!($value["type"] == 'int' && isnull($value['val']))) {
	    		$sqlCols .= $value["col"];
	    	}
        	if($value["type"] == "char")   {
            	$sqlValues .= "'";
            	$sqlValues .= $value["val"];
            	$sqlValues .= "'";
        	}
        	else if($value["type"] == 'int' && !isnull($value['val']))   {
            	$sqlValues .= $value["val"];
        	}
        	
        }
    	
    	$this->sqlQuery .= $sqlCols;
    	$this -> sqlQuery .= ') ';
    	$this -> sqlQuery .='VALUES (';
    	$this->sqlQuery .= $sqlValues;
    	$this -> sqlQuery .= ') ';
    	
    	mysql_query($this -> sqlQuery,$this ->connectionString);
        
        return $this -> sqlQuery;
    	#$this -> sqlQuery = NULL;

	}
	
	public function update($tableName,$id,$values) {

    	$this -> sqlQuery = 'UPDATE '.$tableName.' SET ';
    	
    	foreach($values as $value)    {
	    	
    		if (isset($sqlValues) && !($value["type"] == 'int' && isnull($value['val'])))  {
            	$sqlValues .= ',';
        	}
	    	
	    	if(!($value["type"] == 'int' && isnull($value['val']))) {
		    	$sqlValues .= $value['col']." = ";
			}
			
        	if($value["type"] == "char")   {
            	$sqlValues .= "'";
            	$sqlValues .= $value["val"];
            	$sqlValues .= "'";
        	}
        	else if($value["type"] == 'int' && !isnull($value['val']))   {
            	$sqlValues .= $value["val"];
        	}
        	
        }
    	
    	$this->sqlQuery .= $sqlValues;
    	$this -> sqlQuery .= ' WHERE `'.$tableName.'`.id = '.$id;

    	mysql_query($this -> sqlQuery,$this ->connectionString);
            
        return $this -> sqlQuery;
    	#$this -> sqlQuery = NULL;

	}
	
	public function deleteRecord($tableName,$id,$values) {

    	$this -> sqlQuery = 'DELETE FROM '.$tableName;
    	$this -> sqlQuery .= ' WHERE `'.$tableName.'`.id = '.$id;
    	    	
    	mysql_query($this -> sqlQuery,$this ->connectionString);
        
        return $this -> sqlQuery;
    	#$this -> sqlQuery = NULL;

	}
	public function deleteCustom($tableName,$where,$value) {

    	$this -> sqlQuery = 'DELETE FROM '.$tableName;
    	$this -> sqlQuery .= ' WHERE `'.$tableName.'`.'.$where.' = '.$value;
    	    	
    	mysql_query($this -> sqlQuery,$this ->connectionString);
        
        return $this -> sqlQuery;
    	#$this -> sqlQuery = NULL;

	}
	
	public function getRow() {
		
		return $this->_db_row;
		
	}
	
	public function getTotalRows() {
		
		return $this->_db_totalrows;
		
	}

}
class network {
	
	public $_network_id;
	public $_network;
	public $_network_mask;
	public $_network_v6_mask;
	public $_network_description;
	public $_network_comments;
	public $_network_group;
	public $_network_group_name;
	public $_network_group_min_parent;
	public $_network_parent;
	public $_network_container;
	public $_network_subnetted;
	public $_network_addresses;
	public $_network_user;
	public $_network_user_date;
	public $_network_update_user;
	public $_network_update_user_date;
	public $_all_networks;
	public $_networks_total;
	protected $_database;
	protected $_db_row;
	
	public function __construct($network,$database) {
		
		$this->_database = $database;
		$this->_database->dbConnect();
		
		$this->_network_id = $network;
		
		$qry = "SELECT `networks`.*, networkgroup.name AS groupName FROM `networks` LEFT JOIN networkgroup ON networkgroup.id = networks.networkGroup WHERE `networks`.id = '".$this->_network_id."'";

		$this->_database->selectCustom($qry);
		
		$this->_db_row = mysql_fetch_assoc($this->_database->dataSet);
		$this->_network = $this->_db_row['network'];
		$this->_network_mask = $this->_db_row['maskLong'];
		$this->_network_v6_mask = $this->_db_row['v6mask'];
		$this->_network_description = $this->_db_row['descr'];
		$this->_network_comments = $this->_db_row['comments'];
		$this->_network_group = $this->_db_row['networkGroup'];
		$this->_network_group_name = $this->_db_row['groupName'];
		$this->_network_parent = $this->_db_row['parent'];
		$this->_network_container = $this->_db_row['container'];
		$this->_network_user = $this->_db_row['user'];
		$this->_network_user_date = $this->_db_row['date'];
		$this->_network_update_user = $this->_db_row['updateUser'];
		$this->_network_update_user_date = $this->_db_row['updateDate'];
		
		$qry = "SELECT * FROM `networks` WHERE `networks`.parent = '".$this->_network_id."'";

		$this->_database->selectCustom($qry);
		
		$this->_db_row = mysql_fetch_assoc($this->_database->dataSet);
		
		if ($this->_database->_db_totalrows > 0) {
			
			$this->_network_subnetted = true;
			
		}
		
		$qry = "SELECT COUNT(*) AS total FROM `addresses` WHERE `addresses`.network = '".$this->_network_id."'";

		$this->_database->selectCustom($qry);
		
		$this->_db_row = mysql_fetch_assoc($this->_database->dataSet);
		
		$this->_network_addresses = $this->_db_row['total'];
		
	}
	
	public function __destruct() {
		
	}
	
	public function getAllNetworks($container,$group,$parent) {
		
		if ($group != 0) {
				
			$qry = "SELECT MIN(parent) AS min_parent FROM `networks` WHERE `networks`.container = '".$container."' AND `networks`.networkGroup = '".$group."'";
			
			$this->_database->selectCustom($qry);
			
			$this->_db_row = mysql_fetch_assoc($this->_database->dataSet);
				
			$this->_network_group_min_parent = $this->_db_row['min_parent'];

			if (isnull($parent)) {
							
				$qry = "SELECT * FROM `networks` WHERE `networks`.container = '".$container."' AND `networks`.networkGroup = '".$group."' ORDER BY `networks`.network";
				
			}
			else {	
				
				$qry = "SELECT * FROM `networks` WHERE `networks`.container = '".$container."' AND `networks`.parent = '".$parent."' AND `networks`.networkGroup = '".$group."' ORDER BY `networks`.network";
				
			}
			
		}
		else {
		
			$qry = "SELECT * FROM `networks` WHERE `networks`.container = '".$container."' AND `networks`.parent = '".$parent."' ORDER BY `networks`.network";	
			
		}

		$this->_all_networks = $this->_database->selectCustom($qry);
		$this->_networks_total = $this->_database->_db_totalrows;
		
		return ($this->_all_networks);
		
	}
	
	public function getSubnets($parent) {
		
		$qry = "SELECT * FROM `networks` WHERE `networks`.parent = '".$parent."' ORDER BY `networks`.network";
		
		$this->_all_networks = $this->_database->selectCustom($qry);
		$this->_networks_total = $this->_database->_db_totalrows;
		
		return ($this->_all_networks);
		
	}
	
	public function getNextNetwork($network,$mask) {

		$parentNetwork = new network($network,$this->_database);
		
		if (!isset($parentNetwork->_network_v6_mask)) {
			
			$mask = get_dotted_mask ($mask);
			
			$net = find_net(long2ip($parentNetwork->_network),long2ip($parentNetwork->_network_mask));
		
			$networks = array();
			
			$nextNet = $net['network'];
	
			do {
				
				$count++;
				
				$net1 = find_net(long2ip($nextNet),$mask);
				
				$qry = "SELECT * FROM networks WHERE (((networks.network > ".($nextNet-1)." AND networks.network < ".($net1['broadcast']+1).") AND networks.maskLong > '".$parentNetwork->_network_mask."') AND networks.container = '".$parentNetwork->_network_container."') ORDER BY networks.maskLong ASC LIMIT 1";

				$nextNetwork = $this->_database->selectCustom($qry);
			
				$totalRows_nextNetwork = $this->_database->_db_totalrows;
				$row_subnets = mysql_fetch_assoc($nextNetwork);
			
				if ($totalRows_nextNetwork > 0) {
					
					$net2 = find_net(long2ip($row_subnets['network']),long2ip($row_subnets['maskLong']));
					
					if ($net2['broadcast'] < $net1['broadcast']) {
						$nextNet = $net1['broadcast']+1;
					}
					else {
						$nextNet = $net2['broadcast']+1;
					}
				}
				else {
					array_push($networks,$net1['network']);
					$nextNet = $net1['broadcast']+1;
				}
	
			} while (($nextNet < $net['broadcast']) && ($count < 256));
			
		} else {
			
			$nextNet = $parentNetwork->_network;
			$nextNetMask = $parentNetwork->_network_v6_mask;
			
			$networks = array();
			
			if ($nextNetMask < $mask) {
				
				do {
					
					$count++;
					
					$qry = "SELECT * FROM networks WHERE (((networks.network > ".bcsub($nextNet,1)." AND networks.network < ".bcadd($nextNet,bcpow(2,(128 - $mask))).")) AND networks.v6mask > '".$parentNetwork->_network_v6_mask."' AND networks.container = '".$parentNetwork->_network_container."') ORDER BY networks.v6mask ASC LIMIT 1";
					$nextNetwork = $this->_database->selectCustom($qry);

					$totalRows_nextNetwork = $this->_database->_db_totalrows;
					$row_subnets = mysql_fetch_assoc($nextNetwork);
					
					if ($totalRows_nextNetwork > 0) {

						if (bccomp(bcadd($nextNet,bcpow(2,(128 - $network_mask))), bcadd($row_subnets['network'],bcpow(2,(128 - $row_subnets['v6mask'])))) == 1) {
							$nextNet = bcadd($nextNet,bcpow(2,(128 - $mask)));
						}
						else {
							$nextNet = bcadd($row_subnets['network'],bcpow(2,(128 - $row_subnets['v6mask'])));
						}
					}
					else {
						array_push($networks,$nextNet);
						$nextNet = bcadd($nextNet,bcpow(2,(128 - $mask)));
					}
				
				
				} while (( bccomp((bcadd($parentNetwork->_network,bcpow(2,(128 - $parentNetwork->_network_v6_mask)))),$nextNet) == 1 ) && ($count < 256)); 
					
			}
			
		}
		
		return $networks;
		
	}
	
	public function checkOverlap($network,$newNet,$mask) {
		
		
		$parentNetwork = new network($network,$this->_database);
		
		if (!isset($parentNetwork->_network_v6_mask)) {
			
			$net = find_net($newNet,$mask);

			$qry = "SELECT * FROM networks WHERE ((networks.network > ".($net['network']-1)." AND networks.network < ".($net['broadcast']+1).") AND networks.maskLong > '".ip2long($mask)."' AND networks.container = '".$parentNetwork->_network_container."') OR networks.network = ".$net['network']." AND networks.maskLong = '".ip2long($mask)."' AND networks.container = '".$parentNetwork->_network_container."';";

			$nextNetwork = $this->_database->selectCustom($qry);
	
			$totalRows_nextNetwork = $this->_database->_db_totalrows;
			$row_subnets = mysql_fetch_assoc($nextNetwork);
			
		}
		else {
			
			$maskLong = bcadd(ipv62long($newNet),bcpow(2,(128 - $mask)));
			
			$qry = "SELECT * FROM networks WHERE (networks.network > (".ipv62long($newNet)."-1) AND networks.network < (".$maskLong."+1)) AND networks.v6mask >= ".$mask." AND networks.container = ".$parentNetwork->_network_container.";";

			$nextNetwork = $this->_database->selectCustom($qry);
	
			$totalRows_nextNetwork = $this->_database->_db_totalrows;
			$row_subnets = mysql_fetch_assoc($nextNetwork);
			
		}
		if ($totalRows_nextNetwork > 0) {
			
			return true;
			
		}
		else {
			
			return false;
			
		}
		
	}
	
	public function checkBaseOverlap($container,$newNet,$mask) {
		
		
		if (Net_IPv4::validateIP($newNet)) {
			
			$net = find_net($newNet,$mask);

			$qry = "SELECT * FROM networks WHERE ((networks.network >= ".($net['network']-1)." AND networks.network <= ".($net['broadcast']+1).") AND (networks.parent = 0 OR networks.parent = NULL) AND networks.container = '".$container."') OR networks.network = ".$net['network']." AND networks.maskLong = '".ip2long($mask)."' AND networks.container = '".$container."';";

			$nextNetwork = $this->_database->selectCustom($qry);
	
			$totalRows_nextNetwork = $this->_database->_db_totalrows;
			$row_subnets = mysql_fetch_assoc($nextNetwork);
			
		}
		else {
			
			$maskLong = bcadd(ipv62long($newNet),bcpow(2,(128 - $mask)));
			
			$qry = "SELECT * FROM networks WHERE (networks.network > ".(ipv62long($newNet)-1)." AND networks.network < ".($maskLong+1).") AND networks.v6mask >= ".$mask." AND networks.container = ".$container.";";	
			
			$nextNetwork = $this->_database->selectCustom($qry);
	
			$totalRows_nextNetwork = $this->_database->_db_totalrows;
			$row_subnets = mysql_fetch_assoc($nextNetwork);
			
		}
		if ($totalRows_nextNetwork > 0) {
			
			return true;
			
		}
		else {
			
			return false;
			
		}
					
	}
	
	public function getLinks() {
		
		$qry = "SELECT * FROM links WHERE links.provide_network = '".$this->_network_id."';";	
			
		$links = $this->_database->selectCustom($qry);
		
		$totalRows_links = $this->_database->_db_totalrows;
		
		if ($totalRows_links > 0) {
			
			return $links;
			
		}
		else {
			
			return false;
			
		}
		
	}
	
	public function getLinkNetworks() {
		
		$qry = "SELECT * FROM linknetworks WHERE linknetworks.network = '".$this->_network_id."';";	
			
		$links = $this->_database->selectCustom($qry);
		
		$totalRows_links = $this->_database->_db_totalrows;
		
		if ($totalRows_links > 0) {
			
			return $links;
			
		}
		else {
			
			return false;
			
		}
		
	}
	
}

class networkgroup {
	
	public $_networkgroup_id;
	public $_networkgroup_name;
	public $_networkgroup_description;
	public $_all_networkgroups;
	public $_networkgroups_total;
	public $_networkgroup_container;
	protected $_database;
	protected $_db_row;
		
	public function __construct($networkgroup,$database) {
			
		$this->_database = $database;
		$this->_database->dbConnect();
		
		$this->_networkgroup_id = $networkgroup;
		
		$qry = "SELECT * FROM `networkgroup` WHERE `networkgroup`.id = '".$this->_networkgroup_id."'";
		
		$this->_database->selectCustom($qry);
		
		$this->_db_row = mysql_fetch_assoc($this->_database->dataSet);
		$this->_networkgroup_name = $this->_db_row['name'];
		$this->_networkgroup_description = $this->_db_row['descr'];
		$this->_networkgroup_container = $this->_db_row['container'];
		
	}
	
	public function __destruct() {
		
	}
	
	public function getAllNetworkgroups($container) {
		
		$qry = "SELECT * FROM `networkgroup` WHERE `networkgroup`.container = '".$container."' ORDER BY `networkgroup`.name";
		
		$this->_all_networkgroups = $this->_database->selectCustom($qry);
		$this->_networkgroups_total = $this->_database->_db_totalrows;
		
		return ($this->_all_networkgroups);
		
	}
		
}

class address {
	
	public $_address_id;
	public $_address;
	public $_address_network;
	public $_address_description;
	public $_address_customer_id;
	public $_address_customer_name;
	public $_address_device_name;
	public $_address_card_type_name;
	public $_address_card_rack;
	public $_address_card_slot;
	public $_address_card_module;
	public $_address_card_port;
	public $_address_port_id;
	public $_address_subint_id;
	public $_address_comments;
	public $_address_user;
	public $_address_date;
	public $_address_update_user;
	public $_address_update_date;
	public $_all_addresses;
	public $_addresses_total;
	public $_addresses_sort;
	public $_addresses_sort_dir;
	protected $_database;
	protected $_db_row;
	
	public function __construct($address,$database) {
		
		$this->_database = $database;
		$this->_database->dbConnect();
		
		$this->_address_id = $address;
		
		$qry = "SELECT `addresses`.*, `customer`.name AS customerName, `portsdevices`.name AS deviceName, `cardtypes`.name AS cardTypeName, `cards`.rack, `cards`.slot, `cards`.module, `portsports`.port AS cardPort, `subint`.subint FROM `addresses` LEFT JOIN `customer` ON `customer`.id = `addresses`.customer LEFT JOIN subint ON (subint.router = addresses.id OR subint.id = addresses.subintid) LEFT JOIN portsports ON (portsports.router = addresses.id OR portsports.id = addresses.portid OR portsports.id = subint.port) LEFT JOIN cards ON cards.id = portsports.card LEFT JOIN portsdevices ON portsdevices.id = cards.device LEFT JOIN cardtypes ON cardtypes.id = cards.cardtype WHERE `addresses`.id = '".$this->_address_id."'";

		$this->_database->selectCustom($qry);
		
		$this->_db_row = mysql_fetch_assoc($this->_database->dataSet);
		$this->_address = $this->_db_row['address'];
		$this->_address_network = $this->_db_row['network'];
		$this->_address_description = $this->_db_row['descr'];
		$this->_address_customer_id = $this->_db_row['customer'];
		$this->_address_customer_name = $this->_db_row['customerName'];
		$this->_address_device_name = $this->_db_row['deviceName'];
		$this->_address_card_type_name = $this->_db_row['cardTypeName'];
		$this->_address_card_rack = $this->_db_row['rack'];
		$this->_address_card_slot = $this->_db_row['slot'];
		$this->_address_card_module = $this->_db_row['module'];
		$this->_address_card_port = $this->_db_row['port'];
		$this->_address_port_id = $this->_db_row['portid'];
		$this->_address_subint_id = $this->_db_row['subintid'];
		$this->_address_comments = $this->_db_row['comments'];
		$this->_address_user = $this->_db_row['user'];
		$this->_address_date = $this->_db_row['date'];
		$this->_address_update_user = $this->_db_row['updateUser'];
		$this->_address_update_date = $this->_db_row['updateDate'];
		
	}
	
	public function __destruct() {
		
	}
	
	public function getAllAddresses($network,$sort,$dir) {
		
		if (isnull ($sort)) {
			$this->_addresses_sort = "addresses.address";
		}
		else {
			$this->_addresses_sort = $sort;
		}
		if (isnull ($dir)) {
			$this->_addresses_sort_dir = "ASC";
		}
		else {
			$this->_addresses_sort_dir = $dir;
		}
		
		$qry = "SELECT `addresses`.*, `customer`.name AS customerName, `portsdevices`.name AS deviceName, `cardtypes`.name AS cardTypeName, `cards`.rack, `cards`.slot, `cards`.module, `portsports`.port AS cardPort, `subint`.subint FROM `addresses` LEFT JOIN `customer` ON `customer`.id = `addresses`.customer LEFT JOIN subint ON (subint.router = addresses.id OR subint.id = addresses.subintid) LEFT JOIN portsports ON (portsports.router = addresses.id OR portsports.id = addresses.portid OR portsports.id = subint.port) LEFT JOIN cards ON cards.id = portsports.card LEFT JOIN portsdevices ON portsdevices.id = cards.device LEFT JOIN cardtypes ON cardtypes.id = cards.cardtype WHERE `addresses`.network = '".$network."' ORDER BY ".$this->_addresses_sort." ".$this->_addresses_sort_dir;
		
		$this->_all_addresses = $this->_database->selectCustom($qry);
		$this->_addresses_total = $this->_database->_db_totalrows;
		
		return ($this->_all_addresses);
		
	}
	
	public function getAddressById($address) {
		
		$qry = "SELECT `addresses`.*, `customer`.name AS customerName, `portsdevices`.name AS deviceName, `cardtypes`.name AS cardTypeName, `cards`.rack, `cards`.slot, `cards`.module, `portsports`.port AS cardPort, `subint`.subint FROM `addresses` LEFT JOIN `customer` ON `customer`.id = `addresses`.customer LEFT JOIN subint ON (subint.router = addresses.id OR subint.id = addresses.subintid) LEFT JOIN portsports ON (portsports.router = addresses.id OR portsports.id = addresses.portid OR portsports.id = subint.port) LEFT JOIN cards ON cards.id = portsports.card LEFT JOIN portsdevices ON portsdevices.id = cards.device LEFT JOIN cardtypes ON cardtypes.id = cards.cardtype WHERE `addresses`.id = '".$address."'";
		
		$this->_all_addresses = $this->_database->selectCustom($qry);
		$this->_addresses_total = $this->_database->_db_totalrows;
		
		return ($this->_all_addresses);
		
	}
	
	public function getAddressByNetworkAddress($network, $address) {
		
		$qry = "SELECT `addresses`.*, `customer`.name AS customerName, `portsdevices`.name AS deviceName, `cardtypes`.name AS cardTypeName, `cards`.rack, `cards`.slot, `cards`.module, `portsports`.port AS cardPort, `subint`.subint FROM `addresses` LEFT JOIN `customer` ON `customer`.id = `addresses`.customer LEFT JOIN subint ON (subint.router = addresses.id OR subint.id = addresses.subintid) LEFT JOIN portsports ON (portsports.router = addresses.id OR portsports.id = addresses.portid OR portsports.id = subint.port) LEFT JOIN cards ON cards.id = portsports.card LEFT JOIN portsdevices ON portsdevices.id = cards.device LEFT JOIN cardtypes ON cardtypes.id = cards.cardtype WHERE `addresses`.network = '".$network."' AND `addresses`.address = '".$address."'";
		
		$this->_all_addresses = $this->_database->selectCustom($qry);
		$this->_addresses_total = $this->_database->_db_totalrows;
		
		return ($this->_all_addresses);
		
	}
	
	public function getNextAddress($network) {
		
		$parentNetwork = new network($network,$this->_database);
		
		if (!isset($parentNetwork->_network_v6_mask)) {
			
			$net = find_net(long2ip($parentNetwork->_network),long2ip($parentNetwork->_network_mask));
			
			if (long2ip($parentNetwork->_network_mask) == "255.255.255.254") {
					
				$qry = "SELECT * FROM addresses WHERE addresses.network = ".$network." AND addresses.address = ".$parentNetwork->_network."";
	
				$nextAddr = $this->_database->selectCustom($qry);
		
				$totalRows_nextAddr = $this->_database->_db_totalrows;
	
				if ($totalRows_nextAddr == 0 ) {
					$nextAddr = $parentNetwork->_network;
				}
				else {
					$nextAddr = $parentNetwork->_network+1;
				}
					
			} else {
				
				for ($i = ($net['network'] + 1); $i < $net['broadcast']; $i ++) {
					
					$qry = "SELECT * FROM addresses WHERE addresses.network = ".$network." AND addresses.address = ".$i."";
	
					$nextAddr = $this->_database->selectCustom($qry);
			
					$totalRows_nextAddr = $this->_database->_db_totalrows;
		
					if ( $totalRows_nextAddr == 0 ) {
						$nextAddr = $i;
						$i = ($net['broadcast'] - 1);
					}
					
				}
								
			}
		
		}
		else {
			
			for ($i = (bcadd($parentNetwork->_network, 1)); bccomp((bcadd($parentNetwork->_network,bcpow(2,(128 - $parentNetwork->_network_v6_mask)))),$i) > 0; $i = (bcadd($i,1))) {
	
				$qry = "SELECT * FROM addresses WHERE addresses.network = ".$network." AND addresses.address = ".$i."";
	
				$nextAddr = $this->_database->selectCustom($qry);
			
				$totalRows_nextAddr = $this->_database->_db_totalrows;
		
				if ( $totalRows_nextAddr == 0 ) {
					$nextAddr = $i;
					$i = bcadd($parentNetwork->_network,bcpow(2,(128 - $parentNetwork->_network_v6_mask)));
				}
				
			}
			
		}
		
		return ($nextAddr);
		
	}
	
}

class customer {
	
	public $_customer_name;
	public $_customer_account;
	public $_customer_container;
	public $_customer_id;
	public $_all_customers;
	public $_customer_total;
	protected $_database;
	protected $_db_row;
	
	public function __construct($customer,$database) {
			
		$this->_database = $database;
		$this->_database->dbConnect();
		
		$this->_customer_id = $customer;
		
		$qry = "SELECT * FROM `customer` WHERE `customer`.id = '".$this->_customer_id."'";
		
		$this->_database->selectCustom($qry);
		
		$this->_db_row = mysql_fetch_assoc($this->_database->dataSet);
		$this->_customer_name = $this->_db_row['name'];
		$this->_customer_account = $this->_db_row['account'];
		$this->_customer_container = $this->_db_row['container'];
		
	}
	
	public function __destruct() {
		
	}
	
	public function getAllCustomers($container) {
		
		$qry = "SELECT * FROM `customer` WHERE `customer`.container = '".$container."' ORDER BY `customer`.name";
		
		$this->_all_customers = $this->_database->selectCustom($qry);
		$this->_customer_total = $this->_database->_db_totalrows;
		
		return ($this->_all_customers);
		
	}
	
}

class device {
	
	public $_device_name;
	public $_device_address;
	public $_device_description;
	public $_device_devicetype;
	public $_device_devicegroup;
	public $_device_managementip;
	public $_device_id;
	public $_all_devices;
	public $_device_total;
	protected $_database;
	protected $_db_row;
	
	public function __construct($device,$database) {
			
		$this->_database = $database;
		$this->_database->dbConnect();
		
		$this->_device_id = $device;
		
		$qry = "SELECT * FROM `portsdevices` WHERE `portsdevices`.id = '".$this->_device_id."'";
		
		$this->_database->selectCustom($qry);
		
		$this->_db_row = mysql_fetch_assoc($this->_database->dataSet);
		$this->_device_name = $this->_db_row['name'];
		$this->_device_address = $this->_db_row['address'];
		$this->_device_description = $this->_db_row['descr'];
		$this->_device_devicetype = $this->_db_row['devicetype'];
		$this->_device_devicegroup = $this->_db_row['devicegroup'];
		$this->_device_managementip = $this->_db_row['managementip'];
		
	}
	
	public function __destruct() {
		
	}
	
	public function getAllDevices() {
		
		$qry = "SELECT portsdevices.* FROM portsdevices left join portgroups on portgroups.id = portsdevices.devicegroup WHERE portgroups.container = '".$container."' ORDER BY `portsdevices`.name";
		
		$this->_all_devices = $this->_database->selectCustom($qry);
		$this->_device_total = $this->_database->_db_totalrows;
		
		return ($this->_all_devices);
		
	}
	
	public function getDevicesByType($container,$devicetype) {
		
		$qry = "SELECT portsdevices.* FROM portsdevices left join portgroups on portgroups.id = portsdevices.devicegroup WHERE portgroups.container = '".$container."' AND devicetype = '".$devicetype."' ORDER BY portsdevices.name";

		$this->_all_devices = $this->_database->selectCustom($qry);
		$this->_device_total = $this->_database->_db_totalrows;
		
		return ($this->_all_devices);
		
	}
	
}

class devicetype {
	
	public $_devicetype_name;
	public $_devicetype_image;
	public $_devicetype_vlans;
	public $_devicetype_id;
	public $_all_devicetypes;
	public $_devicetypes_total;
	protected $_database;
	protected $_db_row;
	
	public function __construct($devicetype,$database) {
			
		$this->_database = $database;
		$this->_database->dbConnect();
		
		$this->_devicetype_id = $devicetype;
		
		$qry = "SELECT * FROM `devicetypes` WHERE `devicetypes`.id = '".$this->_devicetype_id."'";
		
		$this->_database->selectCustom($qry);
		
		$this->_db_row = mysql_fetch_assoc($this->_database->dataSet);
		$this->_devicetype_name = $this->_db_row['name'];
		$this->_devicetype_image = $this->_db_row['image'];
		$this->_devicetype_vlans = $this->_db_row['vlans'];
		
	}
	
	public function __destruct() {
		
	}
	
	public function getAllDevicetypes() {
		
		$qry = "SELECT * FROM `devicetypes` ORDER BY `devicetypes`.name";
		
		$this->_all_devicetypes = $this->_database->selectCustom($qry);
		$this->_devicetypes_total = $this->_database->_db_totalrows;
		
		return ($this->_all_devicetypes);
		
	}
	
}

class devicegroup {
	
	public $_devicegroup_name;
	public $_devicegroup_id;
	public $_all_devicegroups;
	public $_devicegroups_total;
	protected $_database;
	protected $_db_row;
	
	public function __construct($devicegroup,$database) {
			
		$this->_database = $database;
		$this->_database->dbConnect();
		
		$this->_devicegroup_id = $devicegroup;
		
		$qry = "SELECT * FROM `portgroups` WHERE `portgroups`.id = '".$this->_devicegroup_id."'";
		
		$this->_database->selectCustom($qry);
		
		$this->_db_row = mysql_fetch_assoc($this->_database->dataSet);
		$this->_devicegroup_name = $this->_db_row['name'];
		
	}
	
	public function __destruct() {
		
	}
	
	public function getAllDevicegroups($container) {
		
		$qry = "SELECT * FROM `portgroups` WHERE `portgroups`.container = '".$container."' ORDER BY `portgroups`.name";
		
		$this->_all_devicegroups = $this->_database->selectCustom($qry);
		$this->_devicegroups_total = $this->_database->_db_totalrows;
		
		return ($this->_all_devicegroups);
		
	}
	
}



?>