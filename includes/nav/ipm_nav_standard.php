<?php

$container = new container(0,$db);

$allContainers = $container->getAllContainers();
$row_allContainers = mysql_fetch_assoc($allContainers);
									   
?>

<li><a class="NOLINK">Container</a>
        <?php if ($container->getContainerTotal() > 0) { // Show if recordset not empty ?>
<ul>
<?php do { 
	
	$thisContainer = new container($row_allContainers['id'], $db);
    $authContainer = $user->getAuthContainer($row_allContainers['id']);
    
	
if ($authContainer > 0) { 
?>
        <li><a href="containerView.php?container=<?php echo $row_allContainers['id']; ?>" title="<?php echo $row_allContainers['descr']; ?>">
<?php if (strlen($row_allContainers['name']) < 25) { echo $row_allContainers['name']; } else { echo substr_replace($row_allContainers['name'],'...',25); } ?>
</a></li>
<?php }
	else { ?>
    	<li><a class="NOLINK"><?php if (strlen($row_allContainers['name']) < 25) { echo $row_allContainers['name']; } else { echo substr_replace($row_allContainers['name'],'...',25); } ?></a></li>
  <?php } ?>
  
    <?php } while ($row_allContainers = mysql_fetch_assoc($allContainers)); ?>
    </ul>
<?php } ?>
</li>