<?php
class Repeaters_iBal {

	static function buildCellSectorDescription($attributes){

		$html = '';
		if(count($attributes) > 0){
			$html = "
			<div id=\"tabs\">
				<ul>
					<li><a href=\"#tabs-1\">Repeater</a></li>
					<li><a href=\"#tabs-2\">Config</a></li>
				</ul>
				<div id=\"tabs-1\">
					<ul>
					 <li><FONT COLOR=#0000cc>Name : <FONT COLOR=#000000\>" . $attributes["key"] . "</li>
					  <li><FONT COLOR=#0000cc>Donor : <FONT COLOR=#000000\>" . $attributes["donor"] . "</li>
					  <li><FONT COLOR=#0000cc>D_Site : <FONT COLOR=#000000\>" . $attributes["donor_site_id"] . "</li>
					  <li><FONT COLOR=#0000cc>D_Sector : <FONT COLOR=#000000\>" . $attributes["donor_sector"] . "</li>
					  <li><FONT COLOR=#0000cc>Azimuth : <FONT COLOR=#000000\>" . $attributes["azimuth"] . "</li>
					  <li><FONT COLOR=#0000cc>RSSI : <FONT COLOR=#000000\>" . $attributes["rssi"] . "</li>
					</ul>
				</div>
				
				<div id=\"tabs-2\">
					<ul>
					  <li><FONT COLOR=#0000cc>Description:<FONT COLOR=#000000\>" . $attributes["description"] . "</li>
					  <li><FONT COLOR=#0000cc>Cell Track Id:<FONT COLOR=#000000\>" . $attributes["celltrackid"] . "</li>
					  <li><FONT COLOR=#0000cc>Location:<FONT COLOR=#000000\> " . $attributes["latitude"] . "," . $attributes["longitude"] ."</li>
					</ul>
				</div>
			</div>";
		}
		return $html;
	}

}
?>
