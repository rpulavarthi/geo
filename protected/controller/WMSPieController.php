<?php
class WMSPieController extends WMSController {

    public function beforeRun($resource, $action){
ob_start();
header("Access-Control-Allow-Origin:  *");
        //set default schema
        include(Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . 'config/db.conf.php');
        $this->schema = $dbconfig[Doo::conf()->APP_MODE][6];
    }
	//override
	function GetMap(){

		$this->buildGetMapRequestParams();

		// Get Layer config
		include(Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . 'config/layer.config.php');
		//only support single layer currently
		list($group, $layer) = explode( ':' , $this->requestParams['LAYERS'][0]);
        // echo $group.'-'.$layer;
		// exit;
		$this->layerConfig = $layer_config[$group][$layer];

		//load stylesa
		$this->loadSLD($this->requestParams['STYLES'], $this->layerConfig['styles']['pie']);

		//render pies
		$this->pies();
	}

	protected function genColorCodeFromText($text,$min_brightness=100,$spec=10)
	{
		// Check inputs
		if(!is_int($min_brightness)) throw new Exception("$min_brightness is not an integer");
		if(!is_int($spec)) throw new Exception("$spec is not an integer");
		if($spec < 2 or $spec > 10) throw new Exception("$spec is out of range");
		if($min_brightness < 0 or $min_brightness > 255) throw new Exception("$min_brightness is out of range");
		
		
		$hash = md5($text);  //Gen hash of text
		$colors = array();
		for($i=0;$i<3;$i++)
			$colors[$i] = max(array(round(((hexdec(substr($hash,$spec*$i,$spec)))/hexdec(str_pad('',$spec,'F')))*255),$min_brightness)); //convert hash into 3 decimal values between 0 and 255
			
		if($min_brightness > 0)  //only check brightness requirements if min_brightness is about 100
			while( array_sum($colors)/3 < $min_brightness )  //loop until brightness is above or equal to min_brightness
				for($i=0;$i<3;$i++)
					$colors[$i] += 10;	//increase each color by 10
					
		$output = '';
		
		for($i=0;$i<3;$i++)
			$output .= str_pad(dechex($colors[$i]),2,0,STR_PAD_LEFT);  //convert each color to hex and append to output
		
		return '#'.$output;
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
	
	private function pies(){
		$schema = $this->schema;

		//get params
		$srid = $this->requestParams['SRID'];
		$minx = $this->requestParams['BBOX']['minx'];
		$miny = $this->requestParams['BBOX']['miny'];
		$maxx = $this->requestParams['BBOX']['maxx'];
		$maxy = $this->requestParams['BBOX']['maxy'];
		$width = $this->requestParams['WIDTH'];
		$height = $this->requestParams['HEIGHT'];
		$layer = $this->requestParams['LAYERS'][0];
       // $label = $this->requestParams['LABEL'];

		//derive zoom level from  BBOX and image size
		$ratio = ($this->requestParams['BBOX']['maxx'] - $this->requestParams['BBOX']['minx']) / $this->requestParams['WIDTH'];
		$sql = "SELECT zoom, abs($ratio-scale) as delta, scale FROM $schema.resolutions ORDER BY delta ASC LIMIT 1";
		$zoomData = Doo::db()->fetchRow($sql);
		$zoom = $zoomData["zoom"];

		//cluster!
		//session_start();
		//$cluster_zoom_level = isset($_SESSION['user']['cluster_zoom_level']) ? $_SESSION['user']['cluster_zoom_level'] : 15;
		$cluster_zoom_level =  $this->params['cluster_zoom_level'];
		if($zoom <= $cluster_zoom_level){

			//get non cluster sites in current bounding box
			$sql = "SELECT key FROM $schema.clusters
					WHERE
					zoom = " . intval($zoom) . "
					AND layer = '$layer'
					AND size = 1
					AND geom && SetSRID('BOX( $minx $miny, $maxx $maxy)'::box2d, $srid)";
		} else {
			$sql = "SELECT key FROM $schema.cells
					WHERE
					layer = '$layer'
					AND geom && SetSRID('BOX( $minx $miny, $maxx $maxy)'::box2d, $srid) order by key";

		}

		
		//get pie list
		//var_dump(($this->layerConfig['isCarrier']));exit;
		if(isset($this->layerConfig['isCarrier']) && $this->layerConfig['isCarrier']) {
			$sql = str_replace('$srid', $srid, str_replace('$schema', $this->schema, str_replace('$sql', $sql, $this->layerConfig['carrierSQL'])));
            $carrierData = Doo::db()->fetchAll($sql);
			// echo $sql;
			// exit;
			//var_dump($carrierData);
			//echo "!!!!!!\n";
			foreach($carrierData as $carrier) {
			  
				$sectorKey= $carrier['key'] . $carrier['sector'];
				if(!isset($pieData[$sectorKey]))
					$pieData[$sectorKey] = $carrier;

				//$pieData[$sectorKey]['carrier_data'][]=$carrier;
				if(!isset($pieData[$sectorKey]['carrier_data']))
					$pieData[$sectorKey]['carrier_data']=array();

				array_push($pieData[$sectorKey]['carrier_data'],$carrier);

			}
			//var_dump($pieData);exit;
		} else {
		   
			$sql = str_replace('$srid', $srid, str_replace('$schema', $this->schema, str_replace('$sql', $sql, $this->layerConfig['sectorSQL'])));
			$pieData = Doo::db()->fetchAll($sql);
		}
        
		//get KPI data
                $kpiClass = $this->layerConfig['kpiClass'];
		//exit;
                //$kpiData = $kpiClass::generateKPIData($pieData, $this->requestParams['VIEWPARAMS']);

                //var_dump($pieData);exit;
	 if(isset($this->requestParams['VIEWPARAMS']['type']))
	 {
		 
		//absolute kpi
        if($this->requestParams['VIEWPARAMS']['type'] == 'kpia'){
                $kpiData = $kpiClass::generateKPIData($pieData, $this->requestParams['VIEWPARAMS']['tech'], $this->requestParams['VIEWPARAMS']['tw'], $this->requestParams['VIEWPARAMS']['date']);
        }
		if($this->requestParams['VIEWPARAMS']['type'] == 'kpic'){
//var_dump('AAA<pre>' . print_r($kpiClass, true) . '</pre>');exit;
                $kpiData = $kpiClass::generateKPIData($pieData, str_replace(' ', '', $this->requestParams['VIEWPARAMS']['tech']), $this->requestParams['VIEWPARAMS']['tw'], $this->requestParams['VIEWPARAMS']['date']);
        }
        //delta kpi
        if($this->requestParams['VIEWPARAMS']['type'] == 'kpid' || $this->requestParams['VIEWPARAMS']['type'] == 'kpidrange'){
                $kpiData1 = $kpiClass::generateKPIData($pieData, $this->requestParams['VIEWPARAMS']['tech'], $this->requestParams['VIEWPARAMS']['tw1'], $this->requestParams['VIEWPARAMS']['date1']);
                $kpiData2 = $kpiClass::generateKPIData($pieData, $this->requestParams['VIEWPARAMS']['tech'], $this->requestParams['VIEWPARAMS']['tw2'], $this->requestParams['VIEWPARAMS']['date2']);
                if( $this->requestParams['VIEWPARAMS']['type'] == 'kpidrange') {
                        for($i = 1; $i < 4; $i++){
                                $max_value = -2147483647; //PHP_INT_MIN;
                                $min_value = 2147483647; //PHP_INT_MAX;
                                $values = array();
                                $kpi_name = '';
                                $count = 0;

                                foreach($pieData as $r){
                                        $key            = trim($r['key']);
                                        $sector         = $r['sector'];
                                        $kpi_data1 = isset($kpiData1[$key]) ? $kpiData1[$key][$sector]: null;
                                        $kpi_data2 = isset($kpiData2[$key]) ? $kpiData2[$key][$sector]: null;
                                        //calc delta and check if value in thresholds
                                        $kpi_name = $this->requestParams['VIEWPARAMS']["kpi" . $i];
                                        if(isset($kpi_data1[$kpi_name]) && isset($kpi_data2[$kpi_name])){
                                                $delta = $kpi_data2[$kpi_name] - $kpi_data1[$kpi_name];

                                                if($delta > $max_value) $max_value = $delta;
                                                if($delta < $min_value) $min_value = $delta;
                                                $count++;
                                                $values[] = $delta;
                                        }
                                }
                                $kpi_range[$kpi_name] = array('min' => $min_value, 'max' => $max_value, 'count' => $count, 'values' => $values);
                        }

                        //generate ranges
                        $threshold_zoom_count = 5;
                        foreach($kpi_range as &$kpi){
                                //generate equal ranges
                                $ratio = ($kpi['max'] - $kpi['min']) / $threshold_zoom_count;
                                $kpi['equal_interval'][] = $kpi['min'];
                                for( $i = 1; $i <= $threshold_zoom_count; $i++){
                                        $kpi['equal_interval'][] = $kpi['min'] + ($ratio * $i) ;
                                }

                                //generate quantile range
                                $count = $kpi['count'];
                                $ratio_index = $count / $threshold_zoom_count;
                                $values = $kpi['values'];
                                sort($values, SORT_NUMERIC);

                                if(isset($values[0])) {
                                     $kpi['quantile'][] = $values[0];
                                }

                                if($ratio_index > 0) {
                                    for( $i = 1; $i <= $threshold_zoom_count; $i++){
                                            $kpi['quantile'][] = $values[($ratio_index * $i) - 1];
                                    }
                                }

                                //remove un needed
                                unset($kpi['values']);
                                //unset($kpi['count']);
                                unset($kpi['min']);
                                unset($kpi['max']);
                        }

                        //output
						//$this->render_KPI_D_Legend($kpi_range);  //  mrt...
                        echo json_encode($kpi_range);
                        return;
                }
        }
       
	 }
		//init image
		include (Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . 'class/imageSmoothArc.php');
		$im = imagecreatetruecolor($width, $height);
		//$im = imagecreate($width,$height);
		imagealphablending($im, true);
		$trans = imagecolorallocate($im,0,0,255);
		$black = imagecolorallocate($im,0,0,0);
		$green = imagecolorallocate($im,0,128,0);
		$cyan =  imagecolorallocate($im,0,220,220);
		$backgrnd = imagecolorallocate($im , 255,255,255);

		//fill backgrnd
		imagefill($im,0,0,$backgrnd);
		imagecolortransparent($im, $trans);
//var_dump('<pre>' . print_r($pieData, true) . '</pre>'); exit;
        
		foreach($pieData as $r) {
			$key 		= $r['key'];
			$sector		= $r['sector'];
			$lng 		= $r['longitude'];
			$lat 		= $r['latitude'];
			$azimuth 	= $r['azimuth'];
			$hbw 		= $r['hbw'];
		    $pn         = $r['pn'];
		    $switch_id   = $r['switch_id'];

            $start = $azimuth - 90 - $hbw / 2;
            $end = $azimuth - 90 + $hbw / 2;

			$startsa = $azimuth - 30  - $hbw /2;
            $endsa = $azimuth - 30 +  $hbw / 2;

			//kpi values for sector
                        //$kpi_data = isset($kpiData[$key]) ? $kpiData[$key][$sector]: null;

			//var_dump($kpi_data);exit;
			//omni
			if ($hbw == 360) { $start = 0; $end = 360;}


			//pie center
			list($x, $y) = $this->coords2pixels(array(array($lng,$lat)));
			//var_dump($x);exit;
			//radius
			$z = $this->layerConfig['WMS']['pieRadius'];


			//style
			//$symbolizer = SLD::getStyleSymbolizer($this->sld, $r, 'Polygon');
			//if($symbolizer == null)
			//	continue;

			//$param_color_1 = $this->rgb2array($this->thematicKPI($kpi_data, 1));
                        //$param_color_2 = $this->rgb2array($this->thematicKPI($kpi_data, 2));
                        //$param_color_3 = $this->rgb2array($this->thematicKPI($kpi_data, 3));


			$kpi_data = null;
            $kpi_data1 = null;
            $kpi_data2 = null;
			$analysis = false;
			$analysis_param = '';
			$analysis_text = '';
			//var_dump($this->requestParams['VIEWPARAMS']['type']);exit;
			if(isset($this->requestParams['VIEWPARAMS']['type']))
			 {
					if(isset($this->requestParams['VIEWPARAMS']['text']))
						{
							$analysis_text = $this->requestParams['VIEWPARAMS']['text'];
						}
					switch($this->requestParams['VIEWPARAMS']['type']){
							case 'kpia':
									//kpi values for sector
									$kpi_data = isset($kpiData[$key]) ? $kpiData[$key][$sector]: null;

									$param_color_1 = $this->rgb2array($this->thematicKPIAbs($kpi_data, 1));
									$param_color_2 = $this->rgb2array($this->thematicKPIAbs($kpi_data, 2));
									$param_color_3 = $this->rgb2array($this->thematicKPIAbs($kpi_data, 3));
							break;
							case 'kpid':
									//kpi values for sector
									$kpi_data1 = isset($kpiData1[$key]) ? $kpiData1[$key][$sector]: null;
									$kpi_data2 = isset($kpiData2[$key]) ? $kpiData2[$key][$sector]: null;

									$param_color_1 = $this->rgb2array($this->thematicKPIDelta($kpi_data1, $kpi_data2, 1));
									$param_color_2 = $this->rgb2array($this->thematicKPIDelta($kpi_data1, $kpi_data2, 2));
									$param_color_3 = $this->rgb2array($this->thematicKPIDelta($kpi_data1, $kpi_data2, 3));
							break;
                            case 'pn':
							$analysis = true;
							$analysis_param = $r['pn'];
							break;
							case 'dc':
							$analysis = true;
							$analysis_param = $r['dc'];
							break;
							case 'rnc':
							$analysis = true;
							$analysis_param = $r['switch_id'];
							break;
							case 'bts':
							$analysis = true;
							$analysis_param = $r['bts_equip'];
							break;
							case 'ecp':
							$analysis = true;
							$analysis_param = $r['switch_id'];
							break;
				  //   case 'kpic';

				//		 $kpi_data = isset($kpiData[$key]) ? $kpiData[$key][$sector][$carrier][$band]: null;

						  //          	$param_color_1 = $this->rgb2array($this->thematicKPIAbs($kpi_data, 1));
							//        	$param_color_2 = $this->rgb2array($this->thematicKPIAbs($kpi_data, 2));
							  //      	$param_color_3 = $this->rgb2array($this->thematicKPIAbs($kpi_data, 3));

					 // break;

							default:
									//$symbolizer = SLD::getStyleSymbolizer($this->sld, $r, 'Polygon');
									//if($symbolizer == null) continue;
					}

			 }



		//var_dump('test');exit;
			//$color = $this->rgb2array($symbolizer->fill);
			if(method_exists($layer,'renderPie')) {
				//custom render
			} else {
				//var_dump($kpi_data);exit;
				 if($kpi_data != null ||  ($kpi_data1 != null && $kpi_data2 != null)) {
                        imageSmoothArc ( $im, $x, $y, ($z * 1), ($z * 1),  array($param_color_3[0], $param_color_3[1], $param_color_3[2], 0), deg2rad((-1 * $end)), deg2rad((-1 * $start)));
                        imageSmoothArc ( $im, $x, $y, ($z * 0.85), ($z * 0.85),  array($param_color_2[0], $param_color_2[1], $param_color_2[2], 0), deg2rad((-1 * $end)), deg2rad((-1 * $start)));
                        imageSmoothArc ( $im, $x, $y, ($z * 0.6), ($z * 0.6),  array($param_color_1[0], $param_color_1[1], $param_color_1[2], 0), deg2rad((-1 * $end)), deg2rad((-1 * $start)));
                 }
				 else if($analysis && ($analysis_text == ''))
				 {
					if(isset($r['carrier_data'])) {
						
    						$carr_count = count($r['carrier_data'])+1;
    						foreach($r['carrier_data'] as $carrdata) {
							if($this->requestParams['VIEWPARAMS']['type']=='kpic') {
                                $carrier = $carrdata['carrier'];
                                $band =  $carrdata['band'];
//var_dump('<pre>' . print_r($kpiData, true) . '</pre>');exit;
                                $kpi_data = isset($kpiData[$key]) ? $kpiData[$key][$sector][$carrier][$band]: null;

                                $color_array = $this->rgb2array($this->thematicKPIAbs($kpi_data, 1));
                                $param_color_1 = imagecolorallocate($im,$color_array[0],$color_array[1],$color_array[2]);
							}
							else {
    								if($carrdata['band']=='PCS') {
    								 	$param_color_1=$green;

                                				} else {
    									$param_color_1= $cyan;
                                				}		
							}
							 //imageSmoothArc ( $im, $x, $y, $z * 0.6*$carr_count, $z * 0.6* $carr_count,  array($param_color_1[0], $param_color_1[1], $param_color_1[2], 0),deg2rad($startsa) , deg2rad($endsa));
							 imagearc($im, $x, $y, (0.3 * $carr_count * $z + 1), (0.3 * $carr_count * $z + 1),$start, $end, $black);
							 imagearc($im, $x, $y, (0.3 * $carr_count * $z + 2), (0.3 * $carr_count * $z + 2), $start, $end, $black);
							 imagefilledarc($im, $x, $y, (0.3 * $carr_count * $z), (0.3 * $carr_count * $z), $start, $end, $param_color_1, IMG_ARC_EDGED);
							 imageline($im, $x, $y, ($x + cos(deg2rad($start)) * (0.4 * $z)), ($y + sin(deg2rad($start)) * (0.4 * $z)), $black);
                             imageline($im, $x, $y, ($x + cos(deg2rad($end)) * $z * 0.4), ($y + sin(deg2rad($end)) * $z * 0.4), $black);
							 $carr_count--;
    						}
    					} else {
						
						    $colorcode = $this->genColorCodeFromText("$analysis_param",100,10);
							$rgbarray = $this->rgba2array($colorcode);
						    $piecolor = imagecolorallocate($im,$rgbarray[0],$rgbarray[1],$rgbarray[2]);
							imagefilledarc($im, $x, $y, (0.8 * $z), (0.8 * $z), $start, $end, $piecolor, IMG_ARC_EDGED);
                        	imagearc($im, $x, $y, (0.8 * $z + 1), (0.8 * $z + 1), $start, $end, $black);
                        	imageline($im, $x, $y, ($x + cos(deg2rad($start)) * (0.4 * $z)), ($y + sin(deg2rad($start)) * (0.4 * $z)), $black);
                        	imageline($im, $x, $y, ($x + cos(deg2rad($end)) * $z * 0.4), ($y + sin(deg2rad($end)) * $z * 0.4), $black);
    					}
				 }
				 else if($analysis && ($analysis_text != ''))
				 {
					if(isset($r['carrier_data'])) {
						
    						$carr_count = count($r['carrier_data'])+1;
    						foreach($r['carrier_data'] as $carrdata) {
							if($this->requestParams['VIEWPARAMS']['type']=='kpic') {
                                $carrier = $carrdata['carrier'];
                                $band =  $carrdata['band'];
                                $kpi_data = isset($kpiData[$key]) ? $kpiData[$key][$sector][$carrier][$band]: null;

                                $color_array = $this->rgb2array($this->thematicKPIAbs($kpi_data, 1));
                                $param_color_1 = imagecolorallocate($im,$color_array[0],$color_array[1],$color_array[2]);
							}
							else {
    								if($carrdata['band']=='PCS') {
    								 	$param_color_1=$green;

                                				} else {
    									$param_color_1= $cyan;
                                				}		
							}
							 imagearc($im, $x, $y, (0.3 * $carr_count * $z + 1), (0.3 * $carr_count * $z + 1),$start, $end, $black);
							 imagearc($im, $x, $y, (0.3 * $carr_count * $z + 2), (0.3 * $carr_count * $z + 2), $start, $end, $black);
							 imagefilledarc($im, $x, $y, (0.3 * $carr_count * $z), (0.3 * $carr_count * $z), $start, $end, $param_color_1, IMG_ARC_EDGED);
							 imageline($im, $x, $y, ($x + cos(deg2rad($start)) * (0.4 * $z)), ($y + sin(deg2rad($start)) * (0.4 * $z)), $black);
                             imageline($im, $x, $y, ($x + cos(deg2rad($end)) * $z * 0.4), ($y + sin(deg2rad($end)) * $z * 0.4), $black);
							 $carr_count--;
    						}
    					}
				    else
					{
						if($analysis_param == $analysis_text)
						{
							$piecolor = imagecolorallocate($im,200,0,0);
							imagefilledarc($im, $x, $y, (0.8 * $z), (0.8 * $z), $start, $end, $piecolor, IMG_ARC_EDGED);
                        	imagearc($im, $x, $y, (0.8 * $z + 1), (0.8 * $z + 1), $start, $end, $black);
                        	imageline($im, $x, $y, ($x + cos(deg2rad($start)) * (0.4 * $z)), ($y + sin(deg2rad($start)) * (0.4 * $z)), $black);
                        	imageline($im, $x, $y, ($x + cos(deg2rad($end)) * $z * 0.4), ($y + sin(deg2rad($end)) * $z * 0.4), $black);
						}
						else
						{
							$onecarrierColor = imagecolorallocate($im,0,128,0);
							$twocarrierColor = imagecolorallocate($im,7,247,239);
							$threecarrierColor = imagecolorallocate($im,39,18,115);
							$fourcarrierColor = imagecolorallocate($im,228,0,204);
							$piecolor = $onecarrierColor;
							
							switch($r['carrier_count'])
							{
								case 1:
								$piecolor = $onecarrierColor;
								break;
								case 2:
								$piecolor = $twocarrierColor;
								break;
								case 3:
								$piecolor = $threecarrierColor;
								break;
								default:
								$piecolor = $fourcarrierColor;
								break;
							}
							
                        	imagefilledarc($im, $x, $y, (0.8 * $z), (0.8 * $z), $start, $end, $piecolor, IMG_ARC_EDGED);
                        	imagearc($im, $x, $y, (0.8 * $z + 1), (0.8 * $z + 1), $start, $end, $black);
                        	imageline($im, $x, $y, ($x + cos(deg2rad($start)) * (0.4 * $z)), ($y + sin(deg2rad($start)) * (0.4 * $z)), $black);
                        	imageline($im, $x, $y, ($x + cos(deg2rad($end)) * $z * 0.4), ($y + sin(deg2rad($end)) * $z * 0.4), $black);
						}
					}
				 }
				 else {
					   //var_dump($r['carrier_data']);exit;
					   
    					if(isset($r['carrier_data'])) {
						
    						$carr_count = count($r['carrier_data'])+1;
    						foreach($r['carrier_data'] as $carrdata) {
							if($this->requestParams['VIEWPARAMS']['type']=='kpic') {
                                $carrier = $carrdata['carrier'];
                                $band =  $carrdata['band'];
//var_dump('<pre>' . print_r($kpiData, true) . '</pre>');exit;
                                $kpi_data = isset($kpiData[$key]) ? $kpiData[$key][$sector][$carrier][$band]: null;

                                $color_array = $this->rgb2array($this->thematicKPIAbs($kpi_data, 1));
                                $param_color_1 = imagecolorallocate($im,$color_array[0],$color_array[1],$color_array[2]);
							}
							else {

    								if($carrdata['band']=='PCS') {
    								 	$param_color_1=$green;

                                				} else {
    									$param_color_1= $cyan;
                                				}
											
											
							}
							 //imageSmoothArc ( $im, $x, $y, $z * 0.6*$carr_count, $z * 0.6* $carr_count,  array($param_color_1[0], $param_color_1[1], $param_color_1[2], 0),deg2rad($startsa) , deg2rad($endsa));
							 imagearc($im, $x, $y, (0.3 * $carr_count * $z + 1), (0.3 * $carr_count * $z + 1),$start, $end, $black);
							 imagearc($im, $x, $y, (0.3 * $carr_count * $z + 2), (0.3 * $carr_count * $z + 2), $start, $end, $black);
							 imagefilledarc($im, $x, $y, (0.3 * $carr_count * $z), (0.3 * $carr_count * $z), $start, $end, $param_color_1, IMG_ARC_EDGED);
							 imageline($im, $x, $y, ($x + cos(deg2rad($start)) * (0.4 * $z)), ($y + sin(deg2rad($start)) * (0.4 * $z)), $black);
                             imageline($im, $x, $y, ($x + cos(deg2rad($end)) * $z * 0.4), ($y + sin(deg2rad($end)) * $z * 0.4), $black);
							 $carr_count--;
    						}
    					} else {
						    $onecarrierColor = imagecolorallocate($im,0,128,0);
							$twocarrierColor = imagecolorallocate($im,7,247,239);
							$threecarrierColor = imagecolorallocate($im,39,18,115);
							$fourcarrierColor = imagecolorallocate($im,228,0,204);
							$piecolor = $onecarrierColor;
							
							switch($r['carrier_count'])
							{
								case 1:
								$piecolor = $onecarrierColor;
								break;
								case 2:
								$piecolor = $twocarrierColor;
								break;
								case 3:
								$piecolor = $threecarrierColor;
								break;
								default:
								$piecolor = $fourcarrierColor;
								break;
							}
							if(isset($r['outagetype']))
							{
								$colorcode = $this->genColorCodeFromText($r['outagetype'],100,10);
								$rgbarray = $this->rgba2array($colorcode);
								$piecolor = imagecolorallocate($im,$rgbarray[0],$rgbarray[1],$rgbarray[2]);
							}
							else if(isset($r['splitsectorflag']))
							{
							    if($r['splitsectorflag']  == 'Y')
								{
								    if(isset($r['split_sector_status']))
									{
										if($r['split_sector_status'] == "SECONDARY")
										{
											$piecolor = imagecolorallocate($im,100,100,100);
										}
									}
								}
							}else if(isset($r['donor_site_id']))
							{
							$piecolor = imagecolorallocate($im,243,252,78);
							}
                        	imagefilledarc($im, $x, $y, (0.8 * $z), (0.8 * $z), $start, $end, $piecolor, IMG_ARC_EDGED);
                        	imagearc($im, $x, $y, (0.8 * $z + 1), (0.8 * $z + 1), $start, $end, $black);
                        	imageline($im, $x, $y, ($x + cos(deg2rad($start)) * (0.4 * $z)), ($y + sin(deg2rad($start)) * (0.4 * $z)), $black);
                        	imageline($im, $x, $y, ($x + cos(deg2rad($end)) * $z * 0.4), ($y + sin(deg2rad($end)) * $z * 0.4), $black);
    					}
                 }

			}

			//label
			if($zoom > $cluster_zoom_level){
				$font = Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . 'class/fonts/timesbd.ttf';
				$blue = imagecolorallocate($im,0,0,255);
				$red = imagecolorallocate($im,255,0,0);
				$black = imagecolorallocate($im,0,0,0);
				$green = imagecolorallocate($im,0,255,0);
				
                $bgwhite=imagecolorallocate($im,230,230,230);
				//Check for param 'label' in viewparams, else cascad is default label
				if(isset($this->requestParams['VIEWPARAMS']['label']))
				{
				   switch($this->requestParams['VIEWPARAMS']['label'])
				   {
					case "siteid":
						imagefilledrectangle($im,$x-30,$y+30,$x+40,$y+20,$bgwhite);
						imagettftext($im, 11, 0, $x - 30, $y + 30, $blue, $font, $key);
					break;
					case "pn":
						switch($sector)
						{
						case 1:
						$color = $black;
						break;
						case 2:
						$color = $red;
						break;
						case 3:
						$color = $blue;
						break;
						default:
						$color = $green;
						break;
						}
						if($azimuth < 180)
						{
						imagefilledrectangle($im,$x + (30*cos(deg2rad($azimuth -90))),$y + (30*sin(deg2rad($azimuth -90))),$x +10*(strlen($pn)) +(30*cos(deg2rad($azimuth -90))),$y - 10 +(30*sin(deg2rad($azimuth -90))),$bgwhite);
						imagettftext($im, 11, 0, $x + (30*cos(deg2rad($azimuth -90))), $y + (30*sin(deg2rad($azimuth -90))), $color, $font, $pn);
						}
						else
						{
						$offsetLength = ((strlen($pn))*10) + 30;
						imagefilledrectangle($im,$x + ($offsetLength*cos(deg2rad($azimuth -90))), $y + (30*sin(deg2rad($azimuth -90))),$x +10*(strlen($pn)) +($offsetLength*cos(deg2rad($azimuth -90))),$y - 10 +(30*sin(deg2rad($azimuth -90))),$bgwhite);
						imagettftext($im, 11, 0, $x + ($offsetLength*cos(deg2rad($azimuth -90))), $y + (30*sin(deg2rad($azimuth -90))), $color, $font, $pn);
						}
					break;
					case "switchid":
					    imagefilledrectangle($im,$x-30,$y+30,$x-30+(9*(strlen($switch_id))),$y+20,$bgwhite);
						imagettftext($im, 11, 0, $x - 30, $y + 30, $blue, $font, $switch_id.' : '.$key);
					break;
					default:
						imagefilledrectangle($im,$x-30,$y+30,$x+40,$y+20,$bgwhite);
						imagettftext($im, 11, 0, $x - 30, $y + 30, $blue, $font, $key);
					break;
				   }
				}
				else
				{
					imagefilledrectangle($im,$x-30,$y+30,$x+40,$y+20,$bgwhite);
					imagettftext($im, 11, 0, $x - 30, $y + 30, $blue, $font, $key);
				}
		        
				
				
			}
		}

		//set backgrnd transparent
		imagecolortransparent($im, $backgrnd);

		//dump to browser
        header_remove();
        ob_end_flush();
		header('Content-Type: image/png');
		//var_dump($im);exit;
		imagepng($im);
		imagedestroy($im);

	}

	function thematicKPIDelta($kpi_data1, $kpi_data2, $id) {

            //default cyan
            $hex_color =  '#00FFFF';
            $vp = $this->requestParams['VIEWPARAMS'];

            //scan colors thresholds
            if(isset($vp["kpi{$id}"])){
                    $kpi = $vp["kpi{$id}"];

                    //calc delta and check if value in thresholds
                    if(!isset($kpi_data1[$kpi]) || !isset($kpi_data2[$kpi])){
                            return $hex_color;
                    }

                    $delta = $kpi_data2[$kpi] - $kpi_data1[$kpi];

                    for($i = 1; $i < $vp["tcount{$id}"]; $i += 2){

                            $color = $vp["color{$id}{$i}"];
                            $t1 = $vp["t{$id}{$i}"];
                            $t2 = $vp["t{$id}" . ($i + 1)];

                            if($this->checkKPI($delta, "gt", $t1) && $this->checkKPI($delta, "le", $t2))
                                    return $color;

                    }
            }
            return $hex_color;
        }




	function thematicKPIAbs($kpi_data, $id){
                //default cyan
                $hex_color =  '#00FFFF';
                $vp = $this->requestParams['VIEWPARAMS'];

            if(isset($vp["kpi$id"])){
                    $kpi = $vp["kpi$id"];
                    $opt = $vp["op$id"];
                    $val = $vp["val$id"];
                    if(isset($kpi_data[$kpi])){
                            $hex_color = $this->checkKPI($kpi_data[$kpi], $opt, $val) ? '#009900' : '#FF0000';
                    }
            }
            return $hex_color;
        }

    function checkKPI($val1, $opt, $val2){
                switch($opt){
                        //equal
                        case 'eq':
                                return ($val1 == $val2) ? true : false;
                        //not equal
                        case 'ne':
                                return ($val1 != $val2) ? true : false;
                        //greater than
                        case 'gt':
                                return ($val1 > $val2) ? true : false;
                        //grearter than or equal
                        case 'ge':
                                return ($val1 >= $val2) ? true : false;
                        //less than
                        case 'lt':
                                return ($val1 < $val2) ? true : false;
                        //les than or equal
                        case 'le':
                                return ($val1 <= $val2) ? true : false;

                        default:
                                return false;
                }
        }
}
?>
