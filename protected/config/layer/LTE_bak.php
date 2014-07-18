<?php
class LTE {


	//function wms(){}


	//function kml(){}


	static function buildCellSectorDescription($attributes){

		$html = '';
		if(count($attributes) > 0){

            $db_conn = @mysql_connect("localhost", "envuser", "ericsson1");
            if(!($db_conn)){
                echo( "ERROR: Could not connect to the database.");
                exit;
            }
            @mysql_select_db("network");
            $sql = 'SELECT';
            $sql .= ' (SUM(L_SESSION_SETUP_SUCCESS_RATE_N) + SUM(L_SESSION_SETUP_SUCCESS_RATE_D) / 100) AS setup_success,';
            $sql .= ' (SUM(L_CSFB_TO_WCDMA_ACTIVITY_RATE_N) + SUM(L_CSFB_TO_WCDMA_ACTIVITY_RATE_D) / 100) AS csfb,';
            $sql .= ' (SUM(L_IRAT_HANDOVER_RATE_N) + SUM(L_IRAT_HANDOVER_RATE_D) / 100) AS irat,';
            $sql .= ' (SUM(L_ERAB_DROP_RATE_N) + SUM(L_ERAB_DROP_RATE_D) / 100) AS erab';
            $sql .= ' from `lteENBperf`';
            $sql .= ' WHERE `ENB` = "' . $attributes["key"] . '";';
            $result = @mysql_query($sql);
            $row = @mysql_fetch_array($result);

			$html = "
			<div id=\"tabs\">
				<ul>
					<li><a href=\"#tabs-1\">Site</a></li>
					<li><a href=\"#tabs-2\">Sector</a></li>
					<li><a href=\"#tabs-3\">Config</a></li>
				</ul>
				<div id=\"tabs-1\">
					<ul>
					 <li><FONT COLOR=#0000cc>Site ID:&nbsp;<FONT COLOR=#000000\>" . $attributes["key"] . "</li>
					  <li><FONT COLOR=#0000cc>MME:&nbsp;<FONT COLOR=#000000\>" . $attributes["switch_id"] . "</li>
                      <li><FONT COLOR=#0000cc>Vendor:&nbsp;<FONT COLOR=#000000\>" . $attributes["vendor"] . "</li>
                      <li><FONT COLOR=#0000cc>Session Setup Success Rate:&nbsp;<FONT COLOR=#000000\>" . $row['setup_success'] . "</li>
                      <li><FONT COLOR=#0000cc>CSFB Rate:&nbsp;<FONT COLOR=#000000\>" . $row['csfb'] . "</li>
                      <li><FONT COLOR=#0000cc>IRAT Rate:&nbsp;<FONT COLOR=#000000\>" . $row['irat'] . "</li>
                      <li><FONT COLOR=#0000cc>ERAB Drop Rate:&nbsp;<FONT COLOR=#000000\>" .$row['erab'] . "</li>
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
					<td>Cell Name</td>
					<td>HBW</td>
                    <td>Azimuth</td>
                    <td>PCI</td>
				  </tr>
				  <tr>";
				 $tablebgcolor="";
				foreach($attributes['sector_data'] as $attr){
					if($tablebgcolor == "")
				        	$tablebgcolor = "#BBBBBB";
					else
						 $tablebgcolor="";
					$html .= "<tr  bgcolor=".$tablebgcolor.">";
					$html .= "<td>" . $attr["key"] . "</td>";
					$html .= "<td>" . $attr["cellname"] . "</td>";
					$html .= "<td>" . $attr["hbw"] . "</td>";
                    $html .= "<td>" . $attr["azimuth"] . "</td>";
                    $html .= "<td>" . $attr["pci"] . "</td>";
					$html .= "</tr>";
				}

				$html .= "</table>
				</div>
				<div id=\"tabs-3\">
					<ul>
					  <li><FONT COLOR=#0000cc>Vendor:<FONT COLOR=#000000\>" . $attributes["vendor"] . "</li>
					  <li><FONT COLOR=#0000cc>Equip:<FONT COLOR=#000000\> </li>
					  <li><FONT COLOR=#0000cc>MME:<FONT COLOR=#000000\>" . $attributes["switch_id"] . "</li>
					  <li><FONT COLOR=#0000cc>Address:<FONT COLOR=#000000\></li>
					  <li><FONT COLOR=#0000cc>City: <FONT COLOR=#000000\></li>
					  <li><FONT COLOR=#0000cc>State: <FONT COLOR=#000000\></li>
					  <li><FONT COLOR=#0000cc>Zip:<FONT COLOR=#000000\> </li>
					  <li><FONT COLOR=#0000cc>Location:<FONT COLOR=#000000\> " . $attributes["latitude"] . "," . $attributes["longitude"] ."</li>
					</ul>
				</div>
			</div>";
		}
		return $html;
	}

}
?>
