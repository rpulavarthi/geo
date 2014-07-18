<?php
class UMTSCell {


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
					 <li><FONT COLOR=#0000cc>Site ID:<FONT COLOR=#000000\> {$attributes['key']}</li>
					  <li><FONT COLOR=#0000cc>RNC_Name:<FONT COLOR=#000000\> {$attributes["switch_id"]}</li>
					  <li><FONT COLOR=#0000cc>Vendor:<FONT COLOR=#000000\> Ericsson </li>
					  <li><a href=\"http://www.w3schools.com\" target=\"_blank\">Neighbors</a></li>
                                          <li><a href=\"http://138.85.245.148/pp/export_xlsx.php?projectid={$attributes['key']}\" target=\"_blank\"><FONT COLOR=#0000cc>Parameters<FONT COLOR=#000000\></a></li>
                                          <li><a href=\"http://www.w3schools.com\" target=\"_blank\">Performance</a></li>
					</ul>
				</div>
				<div id=\"tabs-2\">
				<table class=\"modalTable\">
				  <p><table border=1 cellpadding=0 style=border-spacing:0 0;>
				  <div align=center>
				  <tr>
					<td>Sector</td>
					<td>Cell_UID</td>
					<td>HBW</td>
					<td>Azimuth</td>
					<td>UL</td>
                                        <td>DL</td>
                                        <td>Band</td>
				  </tr>
				  <tr>";
				 $tablebgcolor="";
				foreach($attributes['carrier_data'] as $attr){
					if($tablebgcolor == "")
				        	$tablebgcolor = "#BBBBBB";
					else
						 $tablebgcolor="";
					$html .= "<tr  bgcolor=".$tablebgcolor.">";
					$html .= "<td>{$attr["key"]}</td>";
					$html .= "<td>{$attr["cell_uid"]}</td>";
					$html .= "<td>{$attr["hbw"]}</td>";
					$html .= "<td>{$attr["azimuth"]}</td>";
					$html .= "<td>{$attr["uarfcnul"]} </td>";
                                        $html .= "<td>{$attr["uarfcndl"]} </td>";
                                        $html .= "<td>{$attr["band"]}</td>";
					$html .= "</tr>";
				}

				$html .= "</table>
				</div>
				<div id=\"tabs-3\">
					<ul>
					  <li><FONT COLOR=#0000cc>Vendor:<FONT COLOR=#000000\> Ericsson</li>
					  <li><FONT COLOR=#0000cc>Equip:<FONT COLOR=#000000\> </li>
					  <li><FONT COLOR=#0000cc>RNC Name:<FONT COLOR=#000000\> {$attributes["switch_id"]}</li>
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
