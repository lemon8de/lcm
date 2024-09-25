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
				<li class="nav-item<?php echo in_array($bar_whois_active, ['edit_import_sea', 'edit_shipment_sea', 'add_shipment', 'shipment_documentation', 'incoming_sea']) ? ' menu-is-opening menu-open' : ''?>">
					<a href="#" class="nav-link<?php echo in_array($bar_whois_active, ['edit_import_sea', 'edit_shipment_sea', 'add_shipment', 'shipment_documentation', 'incoming_sea']) ? ' active ' : ''?>">
						<i class="nav-icon far fa-circle"></i><p>Sea Incoming Delivery<i class="right fas fa-angle-left"></i></p>
					</a>
					<ul class="nav nav-treeview"<?php echo in_array($bar_whois_active, ['edit_import_sea', 'edit_shipment_sea', 'add_shipment', 'shipment_documentation', 'incoming_sea']) ? ' style="display:block;"' : '';?>>
						<li class="nav-item">
							<a href="add_shipment_sea.php" class="nav-link<?php echo ($bar_whois_active == "add_shipment" ? ' active': '');?>">
								<i class="far fa-circle nav-icon"></i><p>Add/Update Shipment</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="documentation_sea.php" class="nav-link<?php echo ($bar_whois_active == "shipment_documentation" ? ' active': '');?>">
								<i class="far fa-circle nav-icon"></i><p>Documentation</p>
							</a>
						</li>
						<!-- <li class="nav-item">
							<a href="incoming_sea.php" class="nav-link<?php echo ($bar_whois_active == "incoming_sea" ? ' active': '');?>">
								<i class="far fa-circle nav-icon"></i><p>Sea Shipments</p>
							</a>
						</li> -->
						<li class="nav-item">
							<a href="edit_shipment_sea.php" class="nav-link<?php echo ($bar_whois_active == "edit_shipment_sea" ? ' active': '');?>">
								<i class="far fa-circle nav-icon"></i><p>Edit Shipment Data</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="edit_import_sea.php" class="nav-link<?php echo ($bar_whois_active == "edit_import_sea" ? ' active': '');?>">
								<i class="far fa-circle nav-icon"></i><p>Edit Import Data</p>
							</a>
						</li>
					</ul>
				</li>
				<li class="nav-item<?php echo in_array($bar_whois_active, ['sea_active_details', 'sea_details', 'sea_import_details']) ? ' menu-is-opening menu-open' : ''?>">
					<a href="#" class="nav-link<?php echo in_array($bar_whois_active, ['sea_active_details', 'sea_details', 'sea_import_details']) ? ' active ' : ''?>">
						<i class="nav-icon far fa-circle"></i><p>Sea Report Generation<i class="right fas fa-angle-left"></i></p>
					</a>
					<ul class="nav nav-treeview"<?php echo in_array($bar_whois_active, ['sea_import_details', 'sea_details', 'sea_active_details']) ? ' style="display:block;"' : '';?>>
						<li class="nav-item">
							<a href="active_report_sea.php" class="nav-link<?php echo ($bar_whois_active == "sea_active_details" ? ' active': '');?>">
								<i class="far fa-circle nav-icon"></i><p>Active</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="sea_details.php" class="nav-link<?php echo ($bar_whois_active == "sea_details" ? ' active': '');?>">
								<i class="far fa-circle nav-icon"></i><p>Polytainer</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="sea_import_details.php" class="nav-link<?php echo ($bar_whois_active == "sea_import_details" ? ' active': '');?>">
								<i class="far fa-circle nav-icon"></i><p>Import</p>
							</a>
						</li>
					</ul>
				</li>
				<li class="nav-item<?php echo in_array($bar_whois_active, ['edit_outgoing', 'add_outgoing']) ? ' menu-is-opening menu-open' : ''?>">
					<a href="#" class="nav-link<?php echo in_array($bar_whois_active, ['edit_outgoing' ,'add_outgoing']) ? ' active ' : ''?>">
						<i class="nav-icon far fa-circle"></i><p>Outgoing Shipment<i class="right fas fa-angle-left"></i></p>
					</a>
					<ul class="nav nav-treeview"<?php echo in_array($bar_whois_active, ['edit_outgoing', 'add_outgoing']) ? ' style="display:block;"' : '';?>>
						<li class="nav-item">
							<a href="add_outgoing.php" class="nav-link<?php echo ($bar_whois_active == "add_outgoing" ? ' active': '');?>">
								<i class="far fa-circle nav-icon"></i><p>Add Outgoing</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="edit_outgoing.php" class="nav-link<?php echo ($bar_whois_active == "edit_outgoing" ? ' active': '');?>">
								<i class="far fa-circle nav-icon"></i><p>Edit Outgoing</p>
							</a>
						</li>
					</ul>
				</li>
				<li class="nav-item<?php echo in_array($bar_whois_active, ["fsi_jp", "fsi_la"]) ? ' menu-is-opening menu-open' : ''?>">
					<a href="#" class="nav-link<?php echo in_array($bar_whois_active, ["fsi_jp", "fsi_la"]) ? ' active ' : ''?>">
						<i class="nav-icon far fa-circle"></i><p>Outgoing Report Generation<i class="right fas fa-angle-left"></i></p>
					</a>
					<ul class="nav nav-treeview"<?php echo in_array($bar_whois_active, ["fsi_jp", "fsi_la"]) ? ' style="display:block;"' : '';?>>
						<li class="nav-item">
							<a href="outgoing_fsi_jp.php" class="nav-link<?php echo ($bar_whois_active == "fsi_jp" ? ' active': '');?>">
								<i class="far fa-circle nav-icon"></i><p>FSI JP</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="outgoing_fsi_la.php" class="nav-link<?php echo ($bar_whois_active == "fsi_la" ? ' active': '');?>">
								<i class="far fa-circle nav-icon"></i><p>FSI LA</p>
							</a>
						</li>
					</ul>
				</li>
				<!-- <li class="nav-item">
					<a href="exampleuserlink.php" class="nav-link<?php echo ($bar_whois_active == "exampleuserlink" ? ' active': '');?>">
						<i class="nav-icon far fa-circle"></i><p>Example User Link</p>
					</a>
				</li> -->
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