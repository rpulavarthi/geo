<?php
class CDMA_NA {


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
					<li><a href=\"#tabs-3\">Config</a></li>
				</ul>
				<div id=\"tabs-1\">
					<ul>
					 <li><FONT COLOR=#0000cc>Site ID:<FONT COLOR=#000000\>" . $attributes["key"] .",".$attributes["sid"]."_".$attributes["sw_id"]."_".$attributes["cid"]. "</li>
					  <li><FONT COLOR=#0000cc>Switch Name:<FONT COLOR=#000000\>" . $attributes["switch_id"] . "</li>
					  <li><FONT COLOR=#0000cc>Vendor:<FONT COLOR=#000000\>" . $attributes["vendor"] . "</li>
					  <li><FONT COLOR=#0000cc>Cluster:<FONT COLOR=#000000\>" . $attributes["cluster"] . "</li>
					  <li><FONT COLOR=#0000cc>Market/Area/Region:<FONT COLOR=#000000\>" . $attributes["market"]."/".$attributes["area"]."/".$attributes["region"] . "</li>
					  <li><FONT COLOR=#0000cc>Rad:<FONT COLOR=#000000\>" . $attributes["fb"] . "</li>
					  <li><FONT COLOR=#0000cc>Elevation:<FONT COLOR=#000000\>" . $attributes["elevation"] . "</li>
					  <li><FONT COLOR=#0000cc>On-Air Date:<FONT COLOR=#000000\>" . $attributes["oad"] . "</li>
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
					<td>Azimuth</td>
					<td>M</td>
					<td>E</td>
					<td>HBW</td>
					<td>VBW</td>
					<td>PN</td>
					<td>G</td>
					<td>Antenna Model</td>
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
					$html .= "<td>" . $attr["azimuth"] . "</td>";
					$html .= "<td>" . $attr["m"] . "</td>";
					$html .= "<td>" . $attr["e"] . "</td>";
					$html .= "<td>" . $attr["hbw"] . "</td>";
					$html .= "<td>" . $attr["vbw"] . "</td>";
					$html .= "<td>" . $attr["pn"] . "</td>";
					$html .= "<td>" . $attr["gain"] . "</td>";
					$html .= "<td>" . $attr["antenna"] . "</td>";
					$html .= "</tr>";
				}

				$html .= "</table>
				</div>
				<div id=\"tabs-3\">
					<ul>
					   <li><FONT COLOR=#0000cc>Vendor/Equipment: <FONT COLOR=#000000\>" . $attributes["vendor"]."/".$attributes["equipment"] . "</li>
					  <li><FONT COLOR=#0000cc>Carrier Count: <FONT COLOR=#000000\>" . $attributes["total_carrier_count"] . "</li>
					  <li><FONT COLOR=#0000cc>Switch Name: <FONT COLOR=#000000\>" . $attributes["switch_id"] . "</li>
					  <li><FONT COLOR=#0000cc>BTS Status: <FONT COLOR=#000000\>" . $attributes["bts_status"] . "</li>
					  <li><FONT COLOR=#0000cc>Disaster Class: <FONT COLOR=#000000\>" . $attributes["dc"] . "</li>
					  <li><FONT COLOR=#0000cc>Address: <FONT COLOR=#000000\>" . $attributes["address"] . "</li>
					  <li><FONT COLOR=#0000cc>Location: <FONT COLOR=#000000\> " . $attributes["latitude"] . "," . $attributes["longitude"] ."</li>
					</ul>
				</div>
			</div>";
		}
		return $html;
	}

}
?>
