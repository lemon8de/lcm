<!-- User Bar -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
	<a href="#" class="brand-link">
		<div class="d-flex flex-column justify-content-start align-items-start">
    		<div class="d-flex flex-row justify-content-center align-items-center"> <!-- Centering the logo -->
        		<img src="../static/img/text_logo.png" style="width:75px;" alt="Logo">
    		</div>
    		<div class="text-left"> <!-- Added a new div for text alignment -->
        		<span class="brand-text">u:&nbsp;<?php echo $_SESSION['username']?></span><br>
        		<span class="brand-text font-weight-light" style="font-size:70%;">EDIT GROUP: <?php echo $_SESSION['editing_privileges']; ?></span>
    		</div>
		</div>
	</a>
	<div class="sidebar">
		<nav class="mt-2" style="min-height:150vh;">
			<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
				<li class="nav-item">
					<a href="dashboard.php" class="nav-link<?php echo ($bar_whois_active == "userdashboard" ? ' active': '');?>">
					<i class="fas fa-chart-line"></i><p>&nbsp;Dashboard</p>
					</a>
				</li>
				<li class="nav-header" style="margin:1px 0px; background-color:#727f8c;border-radius:0.450rem;padding:1px;"></li>
				<li class="nav-item<?php echo in_array($bar_whois_active, ['edit_import_sea', 'edit_shipment_sea', 'add_shipment', 'shipment_documentation', 'incoming_sea']) ? ' menu-is-opening menu-open' : ''?>">
					<a href="#" class="nav-link<?php echo in_array($bar_whois_active, ['edit_import_sea', 'edit_shipment_sea', 'add_shipment', 'shipment_documentation', 'incoming_sea']) ? ' active ' : ''?>">
					<i class="fas fa-ship"></i></i><p>&nbsp;Sea Incoming Delivery<i class="right fas fa-angle-left" style="color:#535c66;"></i></p>
					</a>
					<ul class="nav nav-treeview"<?php echo in_array($bar_whois_active, ['edit_import_sea', 'edit_shipment_sea', 'add_shipment', 'shipment_documentation', 'incoming_sea']) ? ' style="display:block;"' : '';?>>
						<?php if ($_SESSION['editing_privileges'] == 'INCOMING' || $_SESSION['editing_privileges'] == 'ALL') { ?>
						<li class="nav-item">
							<a href="add_shipment_sea.php" / Update class="nav-link<?php echo ($bar_whois_active == "add_shipment" ? ' active': '');?>">
							<i class="fas fa-plus"></i></i>&nbsp;<p>Add / Update Shipment</p>
							</a>
						</li>
						<?php } ?>
						<li class="nav-item">
							<a href="documentation_sea.php" class="nav-link<?php echo ($bar_whois_active == "shipment_documentation" ? ' active': '');?>">
							<i class="fas fa-file-alt"></i>&nbsp;<p>Documentation</p>
							</a>
						</li>
						<!-- <li class="nav-item">
							<a href="incoming_sea.php" class="nav-link<?php echo ($bar_whois_active == "incoming_sea" ? ' active': '');?>">
								<i class="far fa-circle nav-icon"></i><p>Sea Shipments</p>
							</a>
						</li> -->
						<!-- <li class="nav-item">
							<a href="edit_shipment_sea.php" class="nav-link<?php echo ($bar_whois_active == "edit_shipment_sea" ? ' active': '');?>">
								<i class="far fa-circle nav-icon"></i><p>Edit Shipment Data</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="edit_import_sea.php" class="nav-link<?php echo ($bar_whois_active == "edit_import_sea" ? ' active': '');?>">
								<i class="far fa-circle nav-icon"></i><p>Edit Import Data</p>
							</a>
						</li> -->
					</ul>
				</li>
				<li class="nav-item<?php echo in_array($bar_whois_active, ['sea_active_details', 'sea_details', 'sea_import_details']) ? ' menu-is-opening menu-open' : ''?>">
					<a href="#" class="nav-link<?php echo in_array($bar_whois_active, ['sea_active_details', 'sea_details', 'sea_import_details']) ? ' active ' : ''?>">
					<i class="fas fa-file-export"></i>&nbsp;<p>Sea Report Generation<i class="right fas fa-angle-left" style="color:#535c66;"></i></p>
					</a>
					<ul class="nav nav-treeview"<?php echo in_array($bar_whois_active, ['sea_import_details', 'sea_details', 'sea_active_details']) ? ' style="display:block;"' : '';?>>
						<li class="nav-item">
							<a href="active_report_sea.php" class="nav-link<?php echo ($bar_whois_active == "sea_active_details" ? ' active': '');?>">
							<i class="fas fa-box-open"></i>&nbsp;<p>Active</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="sea_details.php" class="nav-link<?php echo ($bar_whois_active == "sea_details" ? ' active': '');?>">
							<i class="fas fa-boxes"></i>&nbsp;<p>Polytainer</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="sea_import_details.php" class="nav-link<?php echo ($bar_whois_active == "sea_import_details" ? ' active': '');?>">
							<i class="fas fa-money-check-alt"></i>&nbsp;<p>Import</p>
							</a>
						</li>
					</ul>
				</li>
				<li class="nav-header" style="margin:1px 0px; background-color:#727f8c;border-radius:0.450rem;padding:1px;"></li>
				<li class="nav-item<?php echo in_array($bar_whois_active, ['add_shipment_air', 'shipment_documentation_air']) ? ' menu-is-opening menu-open' : ''?>">
					<a href="#" class="nav-link<?php echo in_array($bar_whois_active, ['add_shipment_air', 'shipment_documentation_air']) ? ' active ' : ''?>">
					<i class="fas fa-plane-departure"></i><p>&nbsp;Air Incoming Delivery<i class="right fas fa-angle-left" style="color:#535c66;"></i></p>
					</a>
					<ul class="nav nav-treeview"<?php echo in_array($bar_whois_active, ['add_shipment_air', 'shipment_documentation_air']) ? ' style="display:block;"' : '';?>>
						<?php if ($_SESSION['editing_privileges'] == 'INCOMING' || $_SESSION['editing_privileges'] == 'ALL') { ?>
						<li class="nav-item">
							<a href="add_shipment_air.php" class="nav-link<?php echo ($bar_whois_active == "add_shipment_air" ? ' active': '');?>">
							<i class="fas fa-plus"></i></i>&nbsp;<p>Add / Update Shipment</p>
							</a>
						</li>
						<?php } ?>
						<li class="nav-item">
							<a href="documentation_air.php" class="nav-link<?php echo ($bar_whois_active == "shipment_documentation_air" ? ' active': '');?>">
							<i class="fas fa-file-alt"></i>&nbsp;<p>Documentation</p>
							</a>
						</li>
					</ul>
				</li>
				<li class="nav-item<?php echo in_array($bar_whois_active, ['air_active_details', 'air_import_details']) ? ' menu-is-opening menu-open' : ''?>">
					<a href="#" class="nav-link<?php echo in_array($bar_whois_active, ['air_active_details', 'air_import_details']) ? ' active ' : ''?>">
					<i class="fas fa-file-export"></i>&nbsp;<p>Air Report Generation<i class="right fas fa-angle-left" style="color:#535c66;"></i></p>
					</a>
					<ul class="nav nav-treeview"<?php echo in_array($bar_whois_active, ['air_active_details', 'air_import_details']) ? ' style="display:block;"' : '';?>>
						<li class="nav-item">
							<a href="air_active_details.php" class="nav-link<?php echo ($bar_whois_active == "air_active_details" ? ' active': '');?>">
							<i class="fas fa-box-open"></i>&nbsp;<p>Active</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="air_import_details.php" class="nav-link<?php echo ($bar_whois_active == "air_import_details" ? ' active': '');?>">
							<i class="fas fa-money-check-alt"></i>&nbsp;<p>Import</p>
							</a>
						</li>
					</ul>
				</li>
				<li class="nav-header" style="margin:1px 0px; background-color:#727f8c;border-radius:0.450rem;padding:1px;"></li>
				<li class="nav-item<?php echo in_array($bar_whois_active, ['edit_outgoing', 'add_outgoing']) ? ' menu-is-opening menu-open' : ''?>">
					<a href="#" class="nav-link<?php echo in_array($bar_whois_active, ['edit_outgoing' ,'add_outgoing']) ? ' active ' : ''?>">
					<i class="fas fa-industry" style="margin-right:3px;"></i>&nbsp;<p>Outgoing Shipment<i class="right fas fa-angle-left" style="color:#535c66;"></i></p>
					</a>
					<ul class="nav nav-treeview"<?php echo in_array($bar_whois_active, ['edit_outgoing', 'add_outgoing']) ? ' style="display:block;"' : '';?>>
					<?php if ($_SESSION['editing_privileges'] == 'OUTGOING' || $_SESSION['editing_privileges'] == 'ALL') { ?>
						<li class="nav-item">
							<a href="add_outgoing.php" class="nav-link<?php echo ($bar_whois_active == "add_outgoing" ? ' active': '');?>">
							<i class="fas fa-plus"></i></i>&nbsp;<p>Add / Update Outgoing</p>
							</a>
						</li>
						<?php } ?>
						<li class="nav-item">
							<a href="edit_outgoing.php" class="nav-link<?php echo ($bar_whois_active == "edit_outgoing" ? ' active': '');?>">
							<i class="fas fa-file-alt"></i>&nbsp;<p>Documentation</p>
							</a>
						</li>
					</ul>
				</li>
				<li class="nav-item<?php echo in_array($bar_whois_active, ["fsi_jp", "fsi_la", "outgoing_invoice"]) ? ' menu-is-opening menu-open' : ''?>">
					<a href="#" class="nav-link<?php echo in_array($bar_whois_active, ["fsi_jp", "fsi_la", "outgoing_invoice"]) ? ' active ' : ''?>">
					<i class="fas fa-file-export"></i>&nbsp;<p>Outgoing Report Generation<i class="right fas fa-angle-left" style="color:#535c66;"></i></p>
					</a>
					<ul class="nav nav-treeview"<?php echo in_array($bar_whois_active, ["fsi_jp", "fsi_la", "outgoing_invoice"]) ? ' style="display:block;"' : '';?>>
						<li class="nav-item">
							<a href="outgoing_fsi_jp.php" class="nav-link<?php echo ($bar_whois_active == "fsi_jp" ? ' active': '');?>">
							<i class="fas fa-file-archive"></i>&nbsp;<p>FSI JP</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="outgoing_fsi_la.php" class="nav-link<?php echo ($bar_whois_active == "fsi_la" ? ' active': '');?>">
							<i class="fas fa-file-archive"></i>&nbsp;<p>FSI LA</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="outgoing_invoice.php" class="nav-link<?php echo ($bar_whois_active == "outgoing_invoice" ?  ' active': '');?>">
							<i class="fas fa-money-check-alt"></i>&nbsp;<p>Invoice Data</p>
							</a>
						</li>
					</ul>
				</li>
				<li class="nav-header" style="margin:1px 0px; background-color:#727f8c;border-radius:0.450rem;padding:1px;"></li>
				<li class="nav-item<?php echo in_array($bar_whois_active, ['bill_monitoring_add', 'bl_drop', 'bill_currency_add', 'bill_details_of_charge', 'edit_billing_forwarder']) ? ' menu-is-opening menu-open' : ''?>">
					<a href="#" class="nav-link<?php echo in_array($bar_whois_active, ['bill_monitoring_add', 'bl_drop', 'bill_currency_add', 'bill_details_of_charge', 'edit_billing_forwarder']) ? ' active ' : ''?>">
					<i class="fas fa-wallet"></i>&nbsp;<p>Billing Preparation<i class="right fas fa-angle-left" style="color:#535c66;"></i></p>
					</a>
					<ul class="nav nav-treeview"<?php echo in_array($bar_whois_active, ['bill_monitoring_add', 'bl_drop', 'bill_currency_add', 'bill_details_of_charge', 'edit_billing_forwarder']) ? ' style="display:block;"' : '';?>>
					<?php if ($_SESSION['editing_privileges'] == 'BILLING' || $_SESSION['editing_privileges'] == 'ALL') { ?>
						<li class="nav-item">
							<a href="details_of_charge.php" class="nav-link<?php echo ($bar_whois_active == "bill_details_of_charge" ? ' active': '');?>">
							<i class="fas fa-pencil-alt"></i>&nbsp;<p>Delete Charges</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="edit_billing_forwarder.php" class="nav-link<?php echo ($bar_whois_active == "edit_billing_forwarder" ? ' active': '');?>">
							<i class="fas fa-pencil-alt"></i>&nbsp;<p>Add / Delete Forwarders</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="add_billing.php" class="nav-link<?php echo ($bar_whois_active == "bill_monitoring_add" ? ' active': '');?>">
							<i class="fas fa-pencil-alt"></i>&nbsp;<p>Rates</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="add_currency.php" class="nav-link<?php echo ($bar_whois_active == "bill_currency_add" ? ' active': '');?>">
							<i class="fas fa-pencil-alt"></i>&nbsp;<p>Exchange Rates</p>
							</a>
						</li>
						<?php } ?>
						<!-- <li class="nav-item">
							<a href="" class="nav-link<?php echo ($bar_whois_active == "billing_monitoring_details" ? ' active': '');?>">
							<i class="fas fa-file-alt"></i>&nbsp;<p>Documentation</p>
							</a>
						</li> -->
						<li class="nav-item">
							<a href="billing_preparation.php" class="nav-link<?php echo ($bar_whois_active == "bl_drop" ? ' active': '');?>">
							<i class="fas fa-fire-extinguisher"></i>&nbsp;<p>BL Drop Test</p>
							</a>
						</li>
					</ul>
				</li>
				<?php if ($_SESSION['site_role'] == "ADMIN") { ?>
				<li class="nav-header" style="margin:1px 0px; background-color:#727f8c;border-radius:0.450rem;padding:1px;"></li>
				<li class="nav-item">
					<a href="account_management.php" class="nav-link<?php echo ($bar_whois_active == "account_management" ?' active': '');?>">
					<i class="fas fa-user-alt"></i>&nbsp;<p>Account Management</p>
					</a>
				</li>
				<?php } ?>
				<li class="nav-header" style="margin:1px 0px; background-color:#727f8c;border-radius:0.450rem;padding:1px;"></li>
				<li class="nav-item">
					<a href="../php_api/logout_api.php" class="nav-link">
					<i class="fas fa-sign-out-alt"></i>&nbsp;<p>Logout</p>
					</a>
				</li>
			</ul>
		</nav>
	</div>
</aside>
<!-- END User Bar -->