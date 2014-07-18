<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="EN">
	<head>
		<title>EVAS</title>
		<script src='global/js/OpenLayers.js'></script>
		<script src='global/js/jquery-1.7.2.min.js'></script>
		<script src='global/js/bootstrap.min.js'></script>
		<link rel="stylesheet" href="global/css/jquery/themes/base/jquery.ui.all.css">
		<link rel="stylesheet" href="global/css/bootstrap.min.css">
		<script src='global/js/jquery-ui-1.8.20.custom.min.js'></script>
		<script src='global/js/jquery.ui.combogrid-1.6.2.js'></script>
		<script src='global/js/jquery.jqGrid.min.js'></script>
		<script src="global/js/i18n/grid.locale-en.js" type="text/javascript"></script>
		<script src="http://maps.google.com/maps/api/js?sensor=false"></script>
		<script type="text/javascript" src="https://www.google.com/jsapi"></script>
		<script src='global/js/desktop-app.js'></script>
		<link rel="stylesheet" href="global/css/desktop-app.css">
		<link rel="stylesheet" href="global/css/ui.jqgrid.css">
		<script type="text/javascript">
			var layers 				= <?php echo json_encode($this->data['layers']);?>;
			var user = {};
			user.name 				= <?php echo isset($this->data['user']['username']) ? "'".$this->data['user']['username']."'" : 'unknown'; ?>;
			user.home_zoom			= <?php echo isset($this->data['user']['home_zoom']) ? $this->data['user']['home_zoom'] : 4; ?>;
			user.home_latitude		= <?php echo isset($this->data['user']['home_latitude']) ?$this->data['user']['home_latitude'] : 40.00 ; ?>;
			user.home_longitude 	= <?php echo isset($this->data['user']['home_longitude']) ? $this->data['user']['home_longitude'] : -100.00; ?>;
			user.cluster_zoom_level = <?php echo isset($this->data['user']['cluster_zoom_level']) ? $this->data['user']['cluster_zoom_level'] : 15; ?>;
			user.default_basemap	= <?php echo isset($this->data['user']['default_basemap']) ? "'".$this->data['user']['default_basemap']."'" : 'null'; ?>;
			user.default_layer		= <?php echo isset($this->data['user']['default_layer']) ?  "'".$this->data['user']['default_layer']."'": 'null'; ?>;
		
			var enabledLayer = {};
			enabledLayer.group = 'null';
			enabledLayer.layer = 'null';
			var enabledBaseLayer = 'null';
		</script>
	</head>
	<body onload="init()">
		<div id='map'></div>
		<!-- Feature Modal -->
		<div id="dialog" title="Basic dialog"></div>
		
		<!-- Profile Modal -->
		<div class="modal" id="profile-modal" style="display: none">
			<div class="modal-header">
				<button class="close" data-dismiss="modal">×</button>
				<h3>User Profile</h3>
			</div>
			<div class="modal-body">
			<form class="form-horizontal">
				<fieldset>
					<div class="control-group">
						<label class="control-label" for="default-basemap">Default Base Map</label>
						<div class="controls">
							<select id="default-basemap" class="span2">
								<option>Google Streets</option>
								<option>Google Terrain</option>
								<option>Google Satellite</option>
								<option>Bing Road</option>
								<option>Bing Aerial</option>
								<option>Open Street Map</option>
							</select>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="default-layer">Default Layer</label>
						<div class="controls">
							<select id="default-layer" class="span4">
								<option></option>
							<?php
							foreach($this->data['layers'] as $gkey=>$group){
								foreach($group as $lkey=>$layer){
									echo "<option>$gkey - $lkey</option>";
								}
							}
							?>
							</select>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="cluster-zoom">Clustering Zoom Level</label>
						<div class="controls">
							<select id="cluster-zoom" class="span2">
								<option>15</option>
								<option>14</option>
								<option>13</option>
								<option>12</option>
								<option>11</option>
								<option>10</option>
							</select>
							<p class="help-block">Disable site clusters at zoom level. Lower is zoomed out more. </p>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label">Save View As Home</label>
						<div class="controls">
							<a id="get-viewport" class="btn btn-primary" href="#">Get Viewport</a>
							<p id="get-viewport-msg" class="help-inline"></p>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label">Change Password</label>
						<div class="controls">
							<label class="checkbox">
								<input id="change-password" type="checkbox">
								Change on next Login
							</label>
						</div>
					</div>
				</fieldset>
				</form>
			</div>
			<div class="modal-footer">
				<a href="#" id="profile-modal-close" class="btn">Close</a>
				<a href="#" id="profile-modal-save" class="btn btn-primary">Save changes</a>
			</div>
		</div>

		<!-- Contact Modal -->
		<div class="modal" id="contactModal" style="display: none">
			<div class="modal-header">
				<button class="close" data-dismiss="modal">×</button>
				<h3>Contact</h3>
			</div>
			<div class="modal-body">
				<b>Question, Comments, Suggestions?</b>
				<ul>
					<li>alex.hurd@ericsson.com</li>
					<li>sandip.sandhu@ericsson.com</li>
				</ul>
			</div>
		</div>
		
		<!-- StreetView Modal -->
		<div class="modal" id="streetviewModal" style="display: none">
			<div class="modal-header">
				<button class="close" data-dismiss="modal">×</button>
				<h3>Street View</h3>
			</div>
			<div class="modal-body" id="streetview" style="position:relative; height:500px;">
				
			</div>
		</div>
		
		<!-- Terrain Profile Modal -->
		<div class="modal" id="terrain-profile-Modal" style="display: none">
			<div class="modal-header">
				<button class="close" data-dismiss="modal">×</button>
				<h3>Terrain Profile</h3>
			</div>
			<div class="modal-body" id="chart_terrain_profile">
				
			</div>
		</div>
		
		<!-- Navigation Bar -->
		<div class="navbar">
			<div class="navbar-inner">
				<div class="container">
					<a class="brand" href="#">EVAS</a>
					<div class="nav-collapse">
						<ul class="nav">
							<li class="divider-vertical"></li>
							<li id="menu-home">
								<a href="#">Home</a>
							</li>
							<li class="divider-vertical"></li>
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown">Profile<b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li id="profile-settings">
										<a href="#">Settings</a>
									</li>
									<li id="profile-logout">
										<a href="#">Logout</a>
									</li>
								</ul>
							</li>
							<li class="divider-vertical"></li>
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown">Links<b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li>
										<a href="http://edash.nala.lmc.ericsson.se/dashboard/FirstPage.swf" target="_blank">Dashboards</a>
									</li>
									<li>
										<a href="http://yne-p1.corp.sprint.com/dashboard/prepostwithexport.swf" target="_blank">Pre Post</a>
									</li>
								</ul>
							</li>
							<li class="divider-vertical"></li>
							<input type="text" class="nav" id="search" placeholder="Search">
						</ul>
						<ul class="nav pull-right">
							<li class="divider-vertical"></li>
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown">Tools<b class="caret"></b></a>
								<ul class="dropdown-menu" type="tools">
									<li>
										<a href="#">Ruler</a>
									</li>
									<li>
										<a href="#">Elevation</a>
									</li>
									<li>
										<a href="#">Terrain Profile</a>
									</li>
									<li>
										<a href="#">Street View</a>
									</li>
								</ul>
							</li> 
							<li class="divider-vertical"></li>
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown">Base Maps<b class="caret"></b></a>
								<ul class="dropdown-menu" type="base-layers">
									<li>
										<a href="#">Google Streets</a></li>
									<li>
										<a href="#">Google Terrain</a></li>
									<li>
										<a href="#">Google Satellite</a>
									</li>
									<li>
										<a href="#">Bing Road</a>
									</li>
									<li>
										<a href="#">Bing Aerial</a>
									</li>
									<li>
										<a href="#">Open Street Map</a>
									</li>
								</ul>
							</li>
							<li class="divider-vertical"></li>
							<li id="menu-contact">
								<a href="#">Contact</a>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<!-- Left Column -->
		<div id="colleft">
			<ul class="nav nav-list">
			<?php
				foreach($this->data['layers'] as $gkey=>$group){
					echo "<li class=\"nav-header\">$gkey</li>";
					foreach($group as $lkey=>$layer){
						echo "<li class=\"layerText\" group=\"$gkey\" layer=\"$lkey\"><a href=\"#\">$lkey</a></li>";
					}
				}
			?>
			</ul>
		</div>
		<div id="colright">
			<img src="geo/wms?REQUEST=GetLegendGraphic&VERSION=1&FORMAT=image/png&STYLE=pie_carrier_count"  />
			<img src="geo/wms?REQUEST=GetLegendGraphic&VERSION=1&FORMAT=image/png&STYLE=cell_type"  />
			<img src="geo/wms?REQUEST=GetLegendGraphic&VERSION=1&FORMAT=image/png&STYLE=cell_type_gsm"  />
			<img src="geo/wms?REQUEST=GetLegendGraphic&VERSION=1&FORMAT=image/png&STYLE=cell_type_umts"  />
		</div>
		<div id="bottom" >
			<div class="subnav subnav-fixed">
				<ul class="nav nav-pills">
					<li id="data-attributes"><a href="#">Site</a></li>
					<li id="data-attributes"><a href="#">Sector</a></li>
					<li id="data-attributes"><a href="#">Carrier</a></li>
					<li id="data-attributes"><a href="#">Alerts</a></li>
					<li id="data-attributes"><a href="#">Alarms</a></li>
					<li id="data-attributes"><a href="#">Field Ops Tickets</a></li>
					<li id="data-attributes"><a href="#">Neighbors</a></li>
					<li id="data-attributes"><a href="#">Activities</a></li>
					<li id="data-attributes"><a href="#">Comments</a></li>
					<li id="data-attributes"><a href="#">Charts</a></li>
					<li id="data-attributes"><a href="#">Correlate</a></li>
					<li id="data-attributes"><a href="#">Rules</a></li>
					<li id="data-export"class=" pull-right" ><a href="#">Export</a></li>
				</ul>
			</div>
			<table id="list"><tr><td/></tr></table> 
			<div id="pager"></div> 
		</div>
	</body>
</html>
