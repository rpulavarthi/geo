<?php
class Outage {


	//function wms(){}


	//function kml(){}


	static function buildCellSectorDescription($attributes){

		$html = '';
		if(count($attributes) > 0){
			$html = "
			<div id=\"tabs\">
				<ul>
					<li><a href=\"#tabs-1\">Site</a></li>
					<li><a href=\"#tabs-2\">Sector</a></li>
					<li><a href=\"#tabs-3\">Active Alarm</a></li>
				</ul>
				<div id=\"tabs-1\">
					<ul>
					 <li><FONT COLOR=#0000cc>Site ID:<FONT COLOR=#000000\>" . $attributes["key"] . "</li>
					  <li><FONT COLOR=#0000cc>Switch/RNC Name:<FONT COLOR=#000000\>" . $attributes["switch_id"] . "</li>
					  <li><a href=\"http://www.w3schools.com\">Neighbors</a></li>
					  <li><a href=\"http://www.w3schools.com\">Parameters</a></li>
					  <li><a href=\"http://www.w3schools.com\">Performance</a></li>
					</ul>
				</div>
				<div id=\"tabs-2\">
				<table class=\"modalTable\">
				  <p><table border=1 cellpadding=0 style=\"border-spacing:0 0;\">
				  <div align=center>
				  <tr>
					<td>Sector</td>
					<td>HBW</td>
					<td>Azimuth</td>
					<td>PN</td>
				  </tr>
				  <tr>";
				 $tablebgcolor="";
				foreach($attributes['sector_data'] as $attr){
					if($tablebgcolor == "")
				        	$tablebgcolor = "#BBBBBB";
					else
						 $tablebgcolor="";
					$html .= "<tr  bgcolor=".$tablebgcolor.">";
					$html .= "<td>" . $attr["sector"] . "</td>";
					$html .= "<td>" . $attr["hbw"] . "</td>";
					$html .= "<td>" . $attr["azimuth"] . "</td>";
					$html .= "<td>" . $attr["pn"] . "</td>";
					$html .= "</tr>";
				}

				$html .= "</table>
				</div>
				<div id=\"tabs-3\">
					<ul>
					   <li><FONT COLOR=#0000cc>Element:<FONT COLOR=#000000\>" . $attributes["element"] . "</li>
					  <li><FONT COLOR=#0000cc>Error Type:<FONT COLOR=#000000\>" . $attributes["errortype"] . "</li>
					  <li><FONT COLOR=#0000cc>Alarm:<FONT COLOR=#000000\> " . $attributes["alarm"] ."</li>
					</ul>
				</div>
			</div>";
		}
		return $html;
	}

}
?>
