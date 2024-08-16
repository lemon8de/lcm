<!-- User Bar -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
	<a href="#" class="brand-link">
		<img src="../static/img/wip.png" alt="Logo" class="brand-image elevation-3">
		<span class="brand-text font-weight-light">&nbsp;<?php echo $_SESSION['username']?></span>
	</a>
	<div class="sidebar">
		<nav class="mt-2">
			<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
				<li class="nav-item">
					<a href="dashboard.php" class="nav-link<?php echo ($bar_whois_active == "userdashboard" ? ' active': '');?>">
						<i class="nav-icon far fa-circle"></i><p>Dashboard</p>
					</a>
				</li>
				<li class="nav-item">
					<a href="#" class="nav-link<?php echo ($bar_whois_active == "incoming_sea" || $bar_whois_active == 'incoming_air' ? ' active': '');?>">
						<i class="nav-icon far fa-circle"></i><p>Incoming Delivery<i class="right fas fa-angle-left"></i></p>
					</a>
					<ul class="nav nav-treeview"<?php echo ($bar_whois_active == "incoming_sea" || $bar_whois_active == 'incoming_air' ? ' style="display:block;"': '');?>>
						<li class="nav-item">
							<a href="incoming_sea.php" class="nav-link<?php echo ($bar_whois_active == "incoming_sea" ? ' active': '');?>">
								<i class="far fa-circle nav-icon"></i><p>Sea Shipments</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="incoming_air.php" class="nav-link<?php echo ($bar_whois_active == "incoming_air" ? ' active': '');?>">
								<i class="far fa-circle nav-icon"></i><p>Air Shipments</p>
							</a>
						</li>
					</ul>
				</li>
				<li class="nav-item">
					<a href="exampleuserlink.php" class="nav-link<?php echo ($bar_whois_active == "exampleuserlink" ? ' active': '');?>">
						<i class="nav-icon far fa-circle"></i><p>Example User Link</p>
					</a>
				</li>
				<li class="nav-item">
					<a href="../php_api/logout_api.php" class="nav-link">
						<i class="nav-icon fas fa-circle"></i><p>Logout</p>
					</a>
				</li>
			</ul>
		</nav>
	</div>
</aside>
<!-- END User Bar -->