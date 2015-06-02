<?php

$allNetworkgroups = new networkgroup(0,$db);
$_allNetworkgroups = $allNetworkgroups->getAllNetworkgroups($_GET['container']);
$row_networkgroups = mysql_fetch_assoc($_allNetworkgroups);

$allDevicegroups = new devicegroup(0,$db);
$_allDevicegroups = $allDevicegroups->getAllDevicegroups($_GET['container']);
$row_devicegroups = mysql_fetch_assoc($_allDevicegroups);

$allDevicetypes = new devicetype(0,$db);
$_allDevicetypes = $allDevicetypes->getAllDevicetypes();
$row_devicetypes = mysql_fetch_assoc($_allDevicetypes);

?>

<li><a href="" onclick="ShowContent('networks',<?php echo $_GET['container']; ?>, null, null); return false;">Networks</a>
       	<ul>
           	<li><a href="" onclick="ShowContent('networks',<?php echo $_GET['container']; ?>, null, null); return false;">All Networks</a></li>
            <li><a class="NOLINK">Network Groups</a>
            	
                	<?php if ($allNetworkgroups->_networkgroups_total > 0) { // Show if recordset not empty ?>
                    <ul>
        <?php do { 
				
  			if ($user->getAuthNetworkgroup($row_networkgroups['id']) > 0 || ($user->getAuthContainer($_GET['container']) > 0 && $user->getAuthNetworkgroup($row_networkgroups['id']) == "")) { 
	?>
        <li><a href="" onclick="ShowContent('networks',<?php echo $_GET['container']; ?>, null, <?php echo $row_networkgroups['id']; ?>); return false;">
          <?php if (strlen($row_networkgroups['name']) < 25) { echo $row_networkgroups['name']; } else { echo substr_replace($row_networkgroups['name'],'...',25); } ?>
      </a></li>
        <?php } else { ?>
        	<li><a href="" onclick="ShowContent('networks',<?php echo $_GET['container']; ?>, null, <?php echo $row_networkgroups['id']; ?>); return false;"><?php if (strlen($row_networkgroups['name']) < 25) { echo $row_networkgroups['name']; } else { echo substr_replace($row_networkgroups['name'],'...',25); } ?></a></li>
        <?php }
	} while ($row_networkgroups = mysql_fetch_assoc($_allNetworkgroups)); ?>
    				</ul>
<?php } ?>
            </li>
            <li><a href="" title="Manage Network Groups">Manage Network Groups</a></li>
        </ul>
    </li>
	<li><a href="">Customers</a>
    	<ul>
        	<li><a class="NOLINK" onmouseover="if (document.getElementById('customers09').getElementsByTagName('li').length < 2) { showCustomers('0','9'); }">0 - 9...</a>
        		<ul id="customers09">
        			<li><a class="NOLINK">loading...</a></li>
        			
        		</ul>
        	</li>
        	<li><a class="NOLINK" onmouseover="if (document.getElementById('customersAD').getElementsByTagName('li').length < 2) { showCustomers('A','D'); }">A - D</a>
        		<ul id="customersAD">
        			<li><a class="NOLINK">loading...</a></li>
        			
        		</ul>
        	</li>
            <li><a class="NOLINK" onmouseover="if (document.getElementById('customersEH').getElementsByTagName('li').length < 2) { showCustomers('E','H'); }">E - H</a>
        		<ul id="customersEH">
        			<li><a class="NOLINK">loading...</a></li>
        			
        		</ul>
        	</li>
            <li><a class="NOLINK" onmouseover="if (document.getElementById('customersIL').getElementsByTagName('li').length < 2) { showCustomers('I','L'); }">I - L</a>
        		<ul id="customersIL">
        			<li><a class="NOLINK">loading...</a></li>
        			
        		</ul>
        	</li>
            <li><a class="NOLINK" onmouseover="if (document.getElementById('customersMP').getElementsByTagName('li').length < 2) { showCustomers('M','P'); }">M - P</a>
        		<ul id="customersMP">
        			<li><a class="NOLINK">loading...</a></li>
        			
        		</ul>
        	</li>
            <li><a class="NOLINK" onmouseover="if (document.getElementById('customersQT').getElementsByTagName('li').length < 2) { showCustomers('Q','T'); }">Q - T</a>
        		<ul id="customersQT">
        			<li><a class="NOLINK">loading...</a></li>
        			
        		</ul>
        	</li>
            <li><a class="NOLINK" onmouseover="if (document.getElementById('customersUZ').getElementsByTagName('li').length < 2) { showCustomers('U','Z'); }">U - Z</a>
        		<ul id="customersUZ">
        			<li><a class="NOLINK">loading...</a></li>
        			
        		</ul>
        	</li>
        </ul>
    </li>
	<li><a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>">Devices</a>
    	<ul>
       		<li><a class="NOLINK">By Device Group</a>
            
        	<?php if ($allDevicegroups->_devicegroups_total > 0) { // Show if recordset not empty ?>
            	<ul>
        			<?php do { 
						if ($user->getAuthDevicegroup($row_devicegroups['id']) > 0 || ($user->getAuthContainer($_GET['container']) > 0 && $user->getAuthDevicegroup($row_devicegroups['id']) == "")) { ?>
					<li><a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;group=<?php echo $row_devicegroups['id']; ?>" title="<?php echo $row_devicegroups['name']; ?>">
					  <?php if (strlen($row_devicegroups['name']) < 25) { echo $row_devicegroups['name']; } else { echo substr_replace($row_devicegroups['name'],'...',25); } ?>
				  </a></li>
				<?php 
                    }
                    else { ?>
                    <li><a class="NOLINK"><?php if (strlen($row_devicegroups['name']) < 25) { echo $row_devicegroups['name']; } else { echo substr_replace($row_devicegroups['name'],'...',25); } ?></a></li>
                    <?php }
                    } while ($row_devicegroups = mysql_fetch_assoc($_allDevicegroups)); ?>
            	</ul>
<?php } // Show if recordset not empty ?>
    		</li>
    		
    		<li><a class="NOLINK">By Device Type</a>
            
        	<?php if ($allDevicetypes->_devicetypes_total > 0) { // Show if recordset not empty 
				
					$allDevicetypeDevices = new device(0,$db);
					
					?>
            	<ul>
        			<?php do { 
        				
        				$_allDevicetypeDevices = $allDevicetypeDevices->getDevicesByType($_GET['container'],$row_devicetypes['id']);
						$row_devicetypedevice = mysql_fetch_assoc($_allDevicetypeDevices);

						?>
					<li>
						<a class="NOLINK"><?php if (strlen($row_devicetypes['name']) < 25) { echo $row_devicetypes['name']; } else { echo substr_replace($row_devicetypes['name'],'...',25); } ?></a>
						<ul>
							<?php if ($allDevicetypeDevices->_device_total == 0) { ?>
							<li><a class="NOLINK">No devices to display</a></li>
							<?php } else { ?>
							<?php do { 
								if ($user->getAuthDevice($row_devicetypedevice['id']) > 0 || ($user->getAuthDevicegroup($row_devicetypedevice['devicegroup']) > 0 && $user->getAuthDevice($row_devices['id']) == "") || ($user->getAuthContainer($_GET['container']) > 0 && $user->getAuthDevicegroup($row_device_groups['id']) == "" && $user->getAuthDevice($row_devices['id']) == "")) { 
								?>
							<li><a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&amp;group=<?php echo $row_devicetypedevice['devicegroup']; ?>&amp;device=<?php echo $row_devicetypedevice['id']; ?>" title="<?php echo $row_devicetypedevice['descr']; ?>"><?php echo $row_devicetypedevice['name']; ?></a></li>
				  			<?php 
								}
			                    else { ?>
            			        <li><a class="NOLINK"><?php if (strlen($row_devicetypedevice['name']) < 25) { echo $row_devicetypedevice['name']; } else { echo substr_replace($row_devicetypedevice['name'],'...',25); } ?></a></li>
                    		<?php }
							} while ($row_devicetypedevice = mysql_fetch_assoc($_allDevicetypeDevices)); ?>
				  			<?php } ?>
						</ul>
						</li>
				<?php 
                   
                    } while ($row_devicetypes = mysql_fetch_assoc($_allDevicetypes)); ?>
            	</ul>
<?php } // Show if recordset not empty ?>
    		</li>
        
        <li><a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&browsecardtypes=1" title="Manage Line Card Types">Manage Line Card Types</a></li>
        <li><a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&browsedevicetypes=1" title="Manage Device Types">Manage Device Types</a></li>
        <li><a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&devicegroups=1" title="Manage Device Groups">Manage Device Groups</a></li>
        
    	<li><a class="NOLINK">Templates</a>
            <ul>
                <li><a href="?browse=devices&amp;container=<?php echo $_GET['container']; ?>&browsetemplates=service" title="Manage service templates">Service Templates</a></li>
            </ul>
        </li>
    </ul>
    <li><a class="NOLINK">Links</a>
    	<ul>
        	<?php if ($containerLevel > 10) { ?><li><a href="#" onClick="document.getElementById('frm_providelink').submit()"><img src="images/plus_icon.gif" alt="Provide Link" border="0" align="absmiddle" /> <strong>Provide Link</strong></a></li><?php } ?>
        	<li><a class="NOLINK">By Service Template</a>
            	<?php if ($totalRows_serviceTemplates > 0) { ?>
            	<ul>
                	<?php 
						
						do { ?>
                        
                    	<li><a class="NOLINK" onmouseover="if (document.getElementById('template<?php echo $row_serviceTemplates['id']; ?>').getElementsByTagName('li').length < 2) { showTemplates('<?php echo $row_serviceTemplates['id']; ?>'); }"><?php echo $row_serviceTemplates['name']; ?></a>
        					<ul id="template<?php echo $row_serviceTemplates['id']; ?>">
        						<li><a class="NOLINK">loading...</a></li>
        					</ul>
                        </li>
                    <?php } while ($row_serviceTemplates = mysql_fetch_assoc($serviceTemplates)); ?>
                    
                </ul>
				<?php } ?>
            </li>
            <li>
            	<a class="NOLINK">By Circuit Reference</a>
                <ul>
                    <li><a class="NOLINK" onmouseover="if (document.getElementById('links09').getElementsByTagName('li').length < 2) { showLinks('0','9'); }">0 - 9...</a>
        				<ul id="links09">
        					<li><a class="NOLINK">loading...</a></li>
        				</ul>
        			</li>
                    <li><a class="NOLINK" onmouseover="if (document.getElementById('linksAD').getElementsByTagName('li').length < 2) { showLinks('A','D'); }">A - D</a>
        				<ul id="linksAD">
        					<li><a class="NOLINK">loading...</a></li>
        				</ul>
        			</li>
                    <li><a class="NOLINK" onmouseover="if (document.getElementById('linksEH').getElementsByTagName('li').length < 2) { showLinks('E','H'); }">E - H</a>
        				<ul id="linksEH">
        					<li><a class="NOLINK">loading...</a></li>
        				</ul>
        			</li>
                    <li><a class="NOLINK" onmouseover="if (document.getElementById('linksIL').getElementsByTagName('li').length < 2) { showLinks('I','L'); }">I - L</a>
        				<ul id="linksIL">
        					<li><a class="NOLINK">loading...</a></li>
        				</ul>
        			</li>
                    <li><a class="NOLINK" onmouseover="if (document.getElementById('linksMP').getElementsByTagName('li').length < 2) { showLinks('M','P'); }">M - P</a>
        				<ul id="linksMP">
        					<li><a class="NOLINK">loading...</a></li>
        				</ul>
        			</li>
                    <li><a class="NOLINK" onmouseover="if (document.getElementById('linksQT').getElementsByTagName('li').length < 2) { showLinks('Q','T'); }">Q - T</a>
        				<ul id="linksQT">
        					<li><a class="NOLINK">loading...</a></li>
        				</ul>
        			</li>
                    <li><a class="NOLINK" onmouseover="if (document.getElementById('linksUZ').getElementsByTagName('li').length < 2) { showLinks('U','Z'); }">U - Z</a>
        				<ul id="linksUZ">
        					<li><a class="NOLINK">loading...</a></li>
        				</ul>
        			</li>
                </ul>
            </li>
        </ul>   
    </li>