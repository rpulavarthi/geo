<?php

class KMLController extends DooController {

	public function beforeRun($resource, $action){
header("Access-Control-Allow-Origin:  *");
		//set default schema
		include(Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . 'config/db.conf.php');
		$this->schema = $dbconfig[Doo::conf()->APP_MODE][6];
	}

	function siteCluster(){
		$schema = $this->schema;
		include(Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . 'config/layer.config.php');

		//load layer
		list($group, $layer) = explode( ':' , $this->params['layer']);

		if(!isset($layer_config[$group][$layer])){
			echo 'unable to find layer in config';
			exit;
		}
		$this->layerConfig = $layer_config[$group][$layer];

		//load style
		$style = $this->params['style'];
		$this->loadSLD($style, $this->layerConfig['styles']['cell']);

		$bbox = explode(',',$_GET['bbox']);

		$minx = $bbox[0];
		$miny = $bbox[1];
		$maxx = $bbox[2];
		$maxy = $bbox[3];

		$zoom = $_GET['zoom'];

		$clustersql = '';
		$sitesql = '';
		$clusterdata = array();
		$sitedata = array();

		//cluster!
		//check user settings otherwize use default (15)
		//session_start();
		//$cluster_zoom_level = isset($_SESSION['user']['cluster_zoom_level']) ? $_SESSION['user']['cluster_zoom_level'] : 12;
		$cluster_zoom_level =  $this->params['cluster_zoom_level'];
		//echo  $cluster_zoom_level;
		//var_dump($_SESSION);
		if($zoom <= $cluster_zoom_level){

			$clustersql = "SELECT latitude, longitude, size FROM $schema.clusters WHERE
					zoom = $zoom
					AND size > 1
					AND layer = '".$this->params['layer']. "'
					AND latitude between $miny and $maxy
					AND longitude between $minx and $maxx";

			$sitesql = "SELECT key  FROM $schema.clusters WHERE
					zoom = $zoom
					AND size = 1
					AND layer = '".$this->params['layer']. "'
					AND latitude between $miny and $maxy
					AND longitude between $minx and $maxx";
		} else {
			//no cluster pull from cells table
			$sitesql = "SELECT key FROM $schema.cells WHERE
					layer = '".$this->params['layer']. "'
					AND latitude between $miny and $maxy
					AND longitude between $minx and $maxx";
		}

		if($clustersql != '')
			$clusterdata = Doo::db()->fetchAll($clustersql);

		$srid = 4326;

		if(isset($this->layerConfig['isCarrier']) && $this->layerConfig['isCarrier']) {
                        $sql = str_replace('$srid', $srid, str_replace('$schema', $this->schema, str_replace('$sql', $sitesql, $this->layerConfig['carrierSQL'])));
                        $carrierData = Doo::db()->fetchAll($sql);
                        //echo "!!!!!!\n";
                        foreach($carrierData as $carrier) {


                                $cellKey= $carrier['key'];
                                if(!isset($sitedata[$cellKey]))
                                        $sitedata[$cellKey] = $carrier;

                                //$pieData[$sectorKey]['carrier_data'][]=$carrier;
                                if(!isset($sitedata[$cellKey]['carrier_data']))
                                        $sitedata[$cellKey]['carrier_data']=array();

                                array_push($sitedata[$cellKey]['carrier_data'],$carrier);

                        }
                        //var_dump($pieData);exit;
                } else {
                        
                        $sql = str_replace('$srid', $srid, str_replace('$schema', $this->schema, str_replace('$sql', $sitesql, $this->layerConfig['sectorSQL'])));
						
                        $sectorData = Doo::db()->fetchAll($sql);
                        foreach($sectorData as $sector) {
                            $cellKey = $sector['key'];
                            if(!isset($sitedata[$cellKey]))
                                    $sitedata[$cellKey] = $sector;

                            //$pieData[$sectorKey]['carrier_data'][]=$carrier;
                            if(!isset($sitedata[$cellKey]['sector_data']))
                                $sitedata[$cellKey]['sector_data']=array();

                            array_push($sitedata[$cellKey]['sector_data'], $sector);

                        }
//var_dump('B<pre>' . print_r($sitedata, true) . '</pre>');exit;
                }




		//if($sitesql != ''){
		//	$srid = 4326;
			//get pie list
		//	$sitesql = str_replace('$srid', $srid, str_replace('$schema', $this->schema, str_replace('$sql', $sitesql, $this->layerConfig['sectorSQL'])));
			//var_dump($sitesql);exit;
		//	$sitedata = Doo::db()->fetchAll($sitesql);
		//}

		//KML header
		$kml = array('<?xml version="1.0" encoding="UTF-8"?>');
		$kml[] = '<kml xmlns="http://earth.google.com/kml/2.2">';
		$kml[] = '	<Document >';

		foreach($clusterdata as $r){
			$lat = $r['latitude'];
			$lon = $r['longitude'];
			$size = $r['size'];

			//add cluster size SLD
			$color = 'FF0000';

			if($size > 1 && $size <= 5)
				$color = '00FF00';

			if($size > 5 && $size <= 15)
				$color = '00cc00';

			if($size > 15 && $size <= 30)
				$color = '00aa00';

			if($size > 30 && $size <= 60)
				$color = '009900';

			if($size > 60 && $size <= 120)
				$color = '006600';

			if($size > 120)
				$color = '003300';


			$kml[] = '		<Placemark>';
			$kml[] = '		<Style >';
			$kml[] = '			<IconStyle>';
			$kml[] = '				<Icon>';
			$kml[] = '					<href><![CDATA[http://chart.apis.google.com/chart?cht=it&chs=32x32&chco='.$color.',000000ff,ffffff01&chl=' . $size . '&chx=' . ($color == 'CCFF33' ? '000000': 'FFFFFF') . ',0&chf=bg,s,00000000&ext=.png]]></href>';
			$kml[] = '					<scale>.75</scale>';
			$kml[] = '				</Icon>';
			$kml[] = '			</IconStyle>';
			$kml[] = '		</Style>';
			$kml[] = '			<Point>';
			$kml[] = '				<coordinates>' .$lon.', ' . $lat . ',0</coordinates>';
			$kml[] = '			</Point>';
			$kml[] = '		</Placemark>';
		}

		$attributes = array();
//var_dump(class_exists($layer));exit;
//var_dump('B<pre>' . print_r($sitedata, true) . '</pre>');exit;
		foreach($sitedata as $key=>$r){

		//	if( $key > count($sitedata)-2 || ($sitedata[$key]['key'] != $sitedata[$key+1]['key'] )){

				$attributes[] = $r;
				$lat = $r['latitude'];
				$lon = $r['longitude'];
				$key = $r['key'];

				$kml[] = '		<Placemark>';
                $kml[] = '          <name>'. $key . '</name>';
                $kml[] = '          <technology>'. substr($this->params['layer'], (strpos($this->params['layer'], ':') + 1)) . '</technology>';
				$kml[] = ' 			<description>';
			//	$kml[] = ' 				<![CDATA[Site Attribute Info Here]]>';
				$kml[] = ' 				<![CDATA[' . (class_exists($layer) ? $layer::buildCellSectorDescription($r) : '') . ']]>';
				$kml[] = ' 			</description>';
				$kml[] = '		<Style >';


				$sym = SLD::getStyleSymbolizer($this->sld, $r, 'Point');
				if(is_object($sym)){
					$kml[] = '			<IconStyle>';
					$kml[] = '				<Icon>';
					$kml[] = '					<href><![CDATA[http://'.$_SERVER["HTTP_HOST"] .'/geo/kml/site/icon/' . str_replace('#','',$sym->fill) .'/' . $sym->size . ']]></href>';
				//	$kml[] = '					<href><![CDATA[http://'.$_SERVER["HTTP_HOST"] .'/geo/kml/site/icon/00B000/20]]></href>';
					//$kml[] = '                                       <href><![CDATA[http://'.$_SERVER["HTTP_HOST"] .'/images/mm_12_cgreen.png]]></href>';

					$kml[] = '					<scale>.40</scale>';
					$kml[] = '				</Icon>';
					$kml[] = '			</IconStyle>';
				}
				else {
					$kml[] = '                      <IconStyle>';
                                        $kml[] = '                              <Icon>';
                                //        $kml[] = '                                      <href><![CDATA[http://'.$_SERVER["HTTP_HOST"] .'/geo/kml/site/icon/' . str_replace('#','',$sym->fill) .'/' . $sym->size . ']]></href>';
                                      $kml[] = '                                      <href><![CDATA[http://'.$_SERVER["HTTP_HOST"] .'/geo/kml/site/icon/00B000/20]]></href>';
                                        //$kml[] = '                                       <href><![CDATA[http://'.$_SERVER["HTTP_HOST"] .'/images/mm_12_cgreen.png]]></href>';

                                        $kml[] = '                                      <scale>.40</scale>';
                                        $kml[] = '                              </Icon>';
                                        $kml[] = '                      </IconStyle>';

				}
				$kml[] = '		</Style>';
				$kml[] = '			<Point>';
				$kml[] = '				<coordinates>' .$lon.', ' . $lat . ',0</coordinates>';
				$kml[] = '			</Point>';
				$kml[] = '		</Placemark>';

				//reset
				$attributes = array();

		//	}else{
		//	  $attributes[] = $r;
		//	}

		}

		//Close Tags
		$kml[] = '	</Document>';
		$kml[] = '</kml>';
//var_dump('BB' . join("\n", $kml));exit;

		//Output
		//Prevent Cache
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
		header("Content-Type: application/vnd.google-earth.kml+xml");
		header("Access-Control-Allow-Origin: *");
		echo join("\n", $kml);

	}

	function siteIcon(){
		//RGBA
		$color = $this->params['color'];
		$rgbaArray = $this->rgba2array($color);

		//size
		$size = $this->params['size'];
		$width = $size;
		$height =  $size;

		include (Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . 'class/imageSmoothArc.php');
		$im = imagecreatetruecolor($width, $height);
		imagealphablending($im, true);
		$black = imagecolorallocate($im,0,0,0);
		$green = imagecolorallocate($im,0,128,0);
		$backgrnd = imagecolorallocate($im , 255,255,255);
		//fill backgrnd
		imagefill($im,0,0,$backgrnd);

		//create circle
		//imageSmoothArc ( $im, $x, $y, $z*1.8, $z*1.8,  array($color[1], $color[0], $color[2], 0),deg2rad($start) , deg2rad($end));

		imageSmoothArc ( $im, ($width/2)-2 , ($height/2)-1, $width-4, $height-4, $rgbaArray, deg2rad(0) , deg2rad(360));
	 	imagearc($im, ($width/2)-2, ($height/2)-1, $width-2, $height-2,0,360,$black);
		imagearc($im, ($width/2)-2, ($height/2)-1, $width-3, $height-3,0,360,$black);
		//set backgrnd transparent
		imagecolortransparent($im, $backgrnd);

		//dump to browser
		header('Content-Type: image/png');
		imagepng($im);
		imagedestroy($im);
	}

	protected function rgba2array($rgba) {
		//strip # from leading RGB if exist
		$rgba = str_replace('#','',$rgba);
		return array(
			base_convert(substr($rgba, 0, 2), 16, 10),
			base_convert(substr($rgba, 2, 2), 16, 10),
			base_convert(substr($rgba, 4, 2), 16, 10),
			base_convert(substr($rgba, 6, 2), 16, 10),
		);
	}


	protected function loadSLD($style, $styleList){
		// init
		$sld = '';

		if(is_array($style))
			$style = $style[0];

		// Check if style is defined
		if(!isset($style) || $style == ""){
			// Use default style
			$sld = $styleList[0];
		}else{
			// check if requsted style is available
			if(isset( $styleList) && !in_array($style, $styleList)){
				// Error!
				echo 'Invalid Style'; exit;
			}else{
				$sld = $style;
			}
		}

		// Load SLD (XML) to array
		$this->sld = Utils::xml2array(file_get_contents(Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . "config/style/$sld.sld"));

	}
}
?>
