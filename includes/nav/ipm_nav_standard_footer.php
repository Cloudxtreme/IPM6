<li><a class="NOLINK">Admin</a>
	<?php if ($user->getAuthPage(3) > 0 || $user->getAuthPage(4) > 0) { ?>
    <ul>
        <?php if ($user->getAuthPage(3) > 0) { ?>
        <li><a href="userGroupView.php" title="Manage user and group access.">User Groups</a></li>
        <?php } ?>
        <?php if ($user->getAuthPage(4) > 0) { ?>
        <li><a href="containerMgmtView.php" title="Manage containers.">Container Management</a></li>
        <?php } ?>
    </ul>
    <?php } ?>
</li>
<li><a href="index.php?doLogout=true" title="Logout"><?php echo $user->_user_firstname; ?> <?php echo $user->_user_lastname; ?></a>
	<ul>
		<li><a href="index.php?doLogout=true" title="Logout">Logout</a></li>
    	<li><a href="containerView.php?browse=useradmin&user=<?php echo $user->_user_username; ?>" title="Edit your profile">Edit profile</a></li>
    </ul>
</li>
