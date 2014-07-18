<?php
class GSM {


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
					 <li><FONT COLOR=#0000cc>Site ID:<FONT COLOR=#000000\> {$attributes["key"]}</li>
					  <li><FONT COLOR=#0000cc>BSC_Name:<FONT COLOR=#000000\> {$attributes["bsc_name"]}</li>
					  <li><FONT COLOR=#0000cc>Vendor:<FONT COLOR=#000000\> {$attributes["vendor"]}</li>
					</ul>
				</div>
				<div id=\"tabs-2\">
				<table class=\"modalTable\">
				  <p><table border=1 cellpadding=0 style=border-spacing:0 0;>
				  <div align=center>
				  <tr>
					<td>Sector</td>
					<td>BSIC</td>
					<td>Cell2</td>
					<td>HBW</td>
					<td>Azimuth</td>
				  </tr>
				  <tr>";
				 $tablebgcolor="";
				foreach($attributes['sector_data'] as $attr){
					if($tablebgcolor == "")
				        	$tablebgcolor = "#BBBBBB";
					else
						 $tablebgcolor="";
					$html .= "<tr  bgcolor=".$tablebgcolor.">";
					$html .= "<td>{$attr["key"]}</td>";
					$html .= "<td>{$attr["bsic"]}</td>";
					$html .= "<td>{$attr["cell2"]}</td>";
					$html .= "<td>{$attr["hbw"]}</td>";
					$html .= "<td>{$attr["azimuth"]}</td>";
					$html .= "</tr>";
				}

				$html .= "</table>
				</div>
				<div id=\"tabs-3\">
					<ul>
					  <li><FONT COLOR=#0000cc>Vendor:<FONT COLOR=#000000\> {$attributes["vendor"]}</li>
					  <li><FONT COLOR=#0000cc>Equip:<FONT COLOR=#000000\> </li>
					  <li><FONT COLOR=#0000cc>Switch Name:<FONT COLOR=#000000\> {$attributes["bsc_name"]}</li>
					  <li><FONT COLOR=#0000cc>Address:<FONT COLOR=#000000\></li>
					  <li><FONT COLOR=#0000cc>City: <FONT COLOR=#000000\></li>
					  <li><FONT COLOR=#0000cc>State: <FONT COLOR=#000000\></li>
					  <li><FONT COLOR=#0000cc>Zip:<FONT COLOR=#000000\> </li>
					  <li><FONT COLOR=#0000cc>Location:<FONT COLOR=#000000\>  {$attributes["latitude"]}, {$attributes["longitude"]}</li>
					</ul>
				</div>
			</div>";
		}
		return $html;
	}

}
?>
