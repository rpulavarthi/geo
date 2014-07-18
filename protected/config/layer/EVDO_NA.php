<?php
class EVDO_NA {


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
					 <li><FONT COLOR=#0000cc>Site ID:<FONT COLOR=#000000\>" . $attributes["key"] . "</li>
					 <li><FONT COLOR=#0000cc>SID/NID/Switch_Id: <FONT COLOR=#000000\>" . $attributes["sid"]."/" .$attributes["nid"]."/".$attributes["sw_id"]. "</li>
					  <li><FONT COLOR=#0000cc>RNC/RNC Id:<FONT COLOR=#000000\>" . $attributes["switch_id"]."/". $attributes["sw_id"]. "</li>
					  <li><FONT COLOR=#0000cc>RNC IP:<FONT COLOR=#000000\>" . $attributes["rncip"] . "</li>
					  <li><FONT COLOR=#0000cc>No. of carriers:<FONT COLOR=#000000\>" . $attributes["total_carrier_count"] . "</li>
					  <li><FONT COLOR=#0000cc>Cluster:<FONT COLOR=#000000\>" . $attributes["cluster"] . "</li>
					  <li><FONT COLOR=#0000cc>Market/Area/Region: <FONT COLOR=#000000\>" . $attributes["market"]."/" .$attributes["area"]."/".$attributes["region"]. "</li>
					  <li><FONT COLOR=#0000cc>RF Manager:<FONT COLOR=#000000\>" . $attributes["rfmanager"] . "</li>
					  <li><FONT COLOR=#0000cc>RF Engineer:<FONT COLOR=#000000\>" . $attributes["rfengineer"] . "</li>
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
					<td>S</td>
					<td>Antenna</td>
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
					$html .= "<td> </td>";
					$html .= "<td>" . $attr["antenna"] . "</td>";
					$html .= "</tr>";
				}

				$html .= "</table>
				</div>
				<div id=\"tabs-3\">
					<ul>
					  <li><FONT COLOR=#0000cc>Vendor/Equipment:<FONT COLOR=#000000\>" . $attributes["vendor"]."/" .$attributes["equipment"] . "</li>
					  <li><FONT COLOR=#0000cc>Carrier count:<FONT COLOR=#000000\>".$attributes["total_carrier_count"] ."</li>
					  <li><FONT COLOR=#0000cc>RNC Name/Disaster Class:<FONT COLOR=#000000\>" . $attributes["switch_id"]."/".$attributes["dc"] . "</li>
					  <li><FONT COLOR=#0000cc>BTS Status: <FONT COLOR=#000000\>" . $attributes["bts_status"] . "</li>
					  <li><FONT COLOR=#0000cc>Address:<FONT COLOR=#000000\>".$attributes["address"] ."</li>
					  <li><FONT COLOR=#0000cc>Location:<FONT COLOR=#000000\> " . $attributes["latitude"] . "," . $attributes["longitude"] ."</li>
					  <li><FONT COLOR=#0000cc>F_PN/MOU_Date:<FONT COLOR=#000000\>" . $attributes["f_pn"]."/".$attributes["moudate"] . "</li>
					</ul>
				</div>
			</div>";
		}
		return $html;
	}

}
?>
