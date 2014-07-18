<?php

class WMSController extends DooController {

	protected $requestParam;
	protected $sld;
	protected $layerConfig;

	public function beforeRun($resource, $action){

//require_once('/usr/share/php/FirePHPCore/fb.php');
//fb("138.85.245.136.WMSController beforeRun *****************************************");
//$test = print_r($_GET,true);
//fb($test,"-------------> _GET");

		//set default schema
		include(Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . 'config/db.conf.php');
		$this->schema = $dbconfig[Doo::conf()->APP_MODE][6];
	}

	function wms(){

//require_once('c:\kpiProject\xampp\php\FirePHPCore\FirePHP.class.php');
//$firephp = FirePHP::getInstance(true);
//fb("138.85.245.136.WMSController wms *****************************************");
//$test = print_r($_GET,true);
//fb($test,"-------------> _GET");

		//Request Name
		$request = $_GET['REQUEST'];
		$this->requestParams = array();
//var_dump('HERE ln32 wmscontroller ' . $request);

		//Route Request
		switch($request){
			case 'GetMap': 
			$this->GetMap(); 
			$this->GetLegendGraphic(); 
			break;
			case 'GetFeatureInfo': $this->GetFeatureInfo(); break;
			case 'GetLegendGraphic': $this->GetLegendGraphic();break;
		}
	}


	protected function buildGetMapRequestParams(){
/*
require_once('c:\kpiProject\xampp\php\FirePHPCore\FirePHP.class.php');
$firephp = FirePHP::getInstance(true);
fb("138.85.245.136.WMSController buildGetMapRequestParams *****************************************");
$test = print_r($_GET,true);
fb($test,"-------------> _GET");
*/
		//Request Version
		$this->requestParams['VERSION'] = $_GET['VERSION'];

		//only support single layer & style currently
		$this->requestParams['LAYERS'] = explode(',', $_GET['LAYERS']);
		$this->requestParams['STYLES'] = isset($_GET['STYLES']) ? explode(',', $_GET['STYLES']) : null;

		//Output format of Map ['image/png' , 'image/gif', 'image/jpeg']
		$this->requestParams['FORMAT'] = $_GET['FORMAT'];

		//Width in pixles of map picture
		$this->requestParams['WIDTH'] = (int)$_GET['WIDTH'];

		//Height in pixles of map picture
		$this->requestParams['HEIGHT'] = (int)$_GET['HEIGHT'];

		//Spatial Reference System
		$this->requestParams['SRS'] = $_GET['SRS'];

		//Spatial Reference Identifier
		preg_match('/\d.*/', $_GET['SRS'], $match);
		$this->requestParams['SRID'] = (int)$match[0];

		//Bounding box corners(lower left, upper right) in SRS units
		$this->requestParams['BBOX'] = explode(',',$_GET['BBOX']);
		$this->requestParams['BBOX']['minx'] = (float)$this->requestParams['BBOX'][0];
		$this->requestParams['BBOX']['miny'] = (float)$this->requestParams['BBOX'][1];
		$this->requestParams['BBOX']['maxx'] = (float)$this->requestParams['BBOX'][2];
		$this->requestParams['BBOX']['maxy'] = (float)$this->requestParams['BBOX'][3];

		//Background transparency of map TRUE|FALSE / optional (Default=FALSE)
		$this->requestParams['TRANSPARENT'] = isset($_GET['TRANSPARENT']) ? $_GET['TRANSPARENT'] : 'FALSE';

		//ViewParams - use for parameterized sql and themiatic mapping / optional
		if(isset($_GET['VIEWPARAMS'])){
			foreach(explode(';',$_GET['VIEWPARAMS']) as $param){
				list($key, $value) = explode(':', $param);
				$data[$key] = $value;
			}
			$this->requestParams['VIEWPARAMS'] = $data;
		}else{
			$this->requestParams['VIEWPARAMS']= null;
		}
	}

	protected function GetFeatureInfo(){
/*
require_once('c:\kpiProject\xampp\php\FirePHPCore\FirePHP.class.php');
$firephp = FirePHP::getInstance(true);
fb("138.85.245.136.WMSController GetFeatureInfo *****************************************");
$test = print_r($_GET,true);
fb($test,"-------------> _GET");
*/
		//Request Version
		$this->requestParams['VERSION'] = $_GET['VERSION'];

		//Bounding box corners(lower left, upper right) in SRS units
		$this->requestParams['BBOX'] = explode(',',$_GET['BBOX']);
		$this->requestParams['BBOX']['minx'] = (float)$this->requestParams['BBOX'][0];
		$this->requestParams['BBOX']['miny'] = (float)$this->requestParams['BBOX'][1];
		$this->requestParams['BBOX']['maxx'] = (float)$this->requestParams['BBOX'][2];
		$this->requestParams['BBOX']['maxy'] = (float)$this->requestParams['BBOX'][3];
		$this->requestParams['QUERY_LAYERS'] = explode(',', $_GET['QUERY_LAYERS']);
		$this->requestParams['INFO_FORMAT'] = $_GET['INFO_FORMAT'];
		preg_match('/\d.*/', $_GET['SRS'], $match);
		$this->requestParams['SRID'] = (int)$match[0];
		$this->requestParams['FEATURE_COUNT'] = $_GET['FEATURE_COUNT'];
		$this->requestParams['WIDTH'] = (int)$_GET['WIDTH'];
		$this->requestParams['HEIGHT'] = (int)$_GET['HEIGHT'];
		$this->requestParams['X'] = $_GET['X'];
		$this->requestParams['Y'] = $_GET['Y'];

		// Get Layer config
		include './protected/config/layers.conf.php';
		//only support single layer currently
		list($group, $layer) = explode( ':' , $this->requestParams['QUERY_LAYERS'][0]);
		$this->layerConfig = $layer_config[$group][$layer];

		list($lat, $lng) = $this->pixels2coords($this->requestParams['X'], $this->requestParams['Y']);

		$master_collection = $this->queryInfoGIS($lat, $lng);

		// Query Attributes via MySQL (Optional)
		if(isset($this->layerConfig['MySQL'])){
			$attributes = $this->queryMySQLAttributes();
			//match attributes to master_collection and save
			$myGeom = $this->layerConfig['MySQL']['Geom'];
			foreach($attributes as $row){
				$master_collection[$row[$myGeom]]['data'] =  $row;
			}
		}

		$this->log->lwrite('end loading queryMySQLAttributes : ' . (float)(Utils::timer() - $this->start) );


		// Query Attributes via API (Optional)
		if(isset($this->layerConfig['API'])){
			$attributes = $this->queryAPIAttributes();
			//match attributes to master_collection and save
			$myGeom = $this->layerConfig['API']['Geom'];
			foreach($attributes as $row){
				$master_collection[$row[$myGeom]]['data'] =  $row;
			}
		}


		$attributes = array();
		foreach($master_collection as $row){
			if(isset($row['geom']))
				$attributes[] = $row['data'];
		}

		$this->setContentType('xml');
		echo Utils::toXml($attributes, 'root', 'data');

	}

	protected function GetLegendGraphic(){

		//Request Version
		$this->requestParams['VERSION'] = $_GET['VERSION'];
		$this->requestParams['FORMAT']	= $_GET['FORMAT'];

		//optional
		$this->requestParams['STYLE'] 		= isset($_GET['STYLE']) ? $_GET['STYLE'] : null;
		$this->requestParams['SLD_BODY'] 	= isset($_GET['SLD_BODY']) ? $_GET['SLD_BODY'] : null;
		$this->requestParams['WIDTH'] 		= isset($_GET['WIDTH']) ? (int)$_GET['WIDTH'] : 20;
		$this->requestParams['HEIGHT'] 		= isset($_GET['HEIGHT']) ? (int)$_GET['HEIGHT'] : 20;
		//Load SLD
		//$this->loadSLD($this->requestParams['STYLE'], array($this->requestParams['STYLE']));

		$im = $this->renderLegendImage();  //  initializes $im...
		if(preg_match("/^undefined/", $_GET['VIEWPARAMS'])) {
			$im = $this->generateBasicLegend();
		} else if(preg_match("/type\:kpid\;/", $_GET['VIEWPARAMS'])) {
			$im = $this->generateKPIDLegend();
		}

		// Output according to requested format

		// Destroy
        //imagejpeg($im);
        //imagedestroy($im);

	}

	/*
	protected function generateKPIDLegend() {

require_once('c:\kpiProject\xampp\php\FirePHPCore\FirePHP.class.php');
$firephp = FirePHP::getInstance(true);
fb("138.85.245.136.WMSController generateKPIDLegend *****************************************");

		try {
			//init vars
			$font           = Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . 'class/fonts/cour.ttf';
			$font_file      = Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . 'class/fonts/arial.ttf';
			$bold_font_file = Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . 'class/fonts/arialbd.ttf';

			//var_dump($font); exit;
			$count = count($this->sld['sld:StyledLayerDescriptor']['sld:NamedLayer']['sld:UserStyle']['sld:FeatureTypeStyle']['sld:Rule']);
			$height = $this->requestParams['HEIGHT'];
			$width = $this->requestParams['WIDTH'];
			*/
			/**
			 * Not having any data to work with, create a basic Legend ...
			 */
			 /*
			$width = 250;
			$height = 410;

			// create image
			$legend_image = imagecreatetruecolor($width, $height);

			//	get colors
			$black   = imagecolorallocate($legend_image, 0x00, 0x00, 0x00);
			$white   = imagecolorallocate($legend_image, 0xff, 0xff, 0xff);
			$gray    = imagecolorallocate($legend_image, 0xaa, 0xaa, 0xaa);
			$magenta = imagecolorallocate($legend_image, 0xff, 0x00, 0xff);
			$cyan    = imagecolorallocate($legend_image, 0x00, 0xff, 0xff);
			$yellow  = imagecolorallocate($legend_image, 0xff, 0xff, 0x00);

			//white background
			imagefilledrectangle($legend_image, 0, 0, $width, $height, $white);

			// Legend Title
			imagettftext($legend_image, 12, 0, 10, 20, $black, $font, "KPI D Legend");

			//
			fb("_GET['VIEWPARAMS'] -->".$_GET['VIEWPARAMS']."<-- *****************************************");
			$key_value_pairs = preg_split("/\;/", $_GET['VIEWPARAMS']);

			$key_value_array = array();

			$key_value_array["tcount1"] = "?";
			$key_value_array["tcount2"] = "?";
			$key_value_array["tcount3"] = "?";
			$key_value_array["tcount4"] = "?";
			$key_value_array["tcount5"] = "?";

			$key_value_array["tcount21"] = "?";
			$key_value_array["tcount22"] = "?";
			$key_value_array["tcount23"] = "?";
			$key_value_array["tcount24"] = "?";
			$key_value_array["tcount25"] = "?";

			$key_value_array["tcount31"] = "?";
			$key_value_array["tcount32"] = "?";
			$key_value_array["tcount33"] = "?";
			$key_value_array["tcount34"] = "?";
			$key_value_array["tcount35"] = "?";

			$key_value_array["t11"] = "?";
			$key_value_array["t12"] = "?";
			$key_value_array["t14"] = "?";
			$key_value_array["t16"] = "?";
			$key_value_array["t18"] = "?";
			$key_value_array["kpi110"] = "?";

			$key_value_array["t21"] = "?";
			$key_value_array["t22"] = "?";
			$key_value_array["t24"] = "?";
			$key_value_array["t26"] = "?";
			$key_value_array["t28"] = "?";
			$key_value_array["kpi210"] = "?";

			$key_value_array["t31"] = "?";
			$key_value_array["t32"] = "?";
			$key_value_array["t34"] = "?";
			$key_value_array["t36"] = "?";
			$key_value_array["t38"] = "?";
			$key_value_array["kpi310"] = "?";

			foreach ($key_value_pairs as $key_value) {
				fb("key_value -->".$key_value."<-- *****************************************");

				$key_value_element = array();
				$key_value_element = preg_split("/\:/", $key_value);
				$key_value_array[$key_value_element[0]] = $key_value_element[1];


			}

			//
			//	band - 1:
			//
			imagefttext($legend_image, 8, 0, 0, 8, $black, $font_file, $key_value_array["kpi1"]);

			$r_color = hexdec(substr($key_value_array["color11"],1,2));
			$g_color = hexdec(substr($key_value_array["color11"],3,2));
			$b_color = hexdec(substr($key_value_array["color11"],5,2));
			imagefilledrectangle($legend_image,   0, 8,  50, 20, imagecolorallocate($legend_image, $r_color, $g_color, $b_color));
			imagefttext($legend_image, 8, 0, 20, 19, $black, $font_file, $key_value_array["tcount1"]);

			$r_color = hexdec(substr($key_value_array["color13"],1,2));
			$g_color = hexdec(substr($key_value_array["color13"],3,2));
			$b_color = hexdec(substr($key_value_array["color13"],5,2));
			imagefilledrectangle($legend_image,  50, 8, 100, 20,  imagecolorallocate($legend_image, $r_color, $g_color, $b_color));
			imagefttext($legend_image, 8, 0, 70, 19, $black, $font_file, $key_value_array["tcount2"]);

			$r_color = hexdec(substr($key_value_array["color15"],1,2));
			$g_color = hexdec(substr($key_value_array["color15"],3,2));
			$b_color = hexdec(substr($key_value_array["color15"],5,2));
			imagefilledrectangle($legend_image, 100, 8, 150, 20,  imagecolorallocate($legend_image, $r_color, $g_color, $b_color));
			imagefttext($legend_image, 8, 0, 120, 19, $black, $font_file, $key_value_array["tcount3"]);

			$r_color = hexdec(substr($key_value_array["color17"],1,2));
			$g_color = hexdec(substr($key_value_array["color17"],3,2));
			$b_color = hexdec(substr($key_value_array["color17"],5,2));
			imagefilledrectangle($legend_image, 150, 8, 200, 20,  imagecolorallocate($legend_image, $r_color, $g_color, $b_color));
			imagefttext($legend_image, 8, 0, 170, 19, $black, $font_file, $key_value_array["tcount4"]);

			$r_color = hexdec(substr($key_value_array["color19"],1,2));
			$g_color = hexdec(substr($key_value_array["color19"],3,2));
			$b_color = hexdec(substr($key_value_array["color19"],5,2));
			imagefilledrectangle($legend_image, 200, 8, 250, 20,  imagecolorallocate($legend_image, $r_color, $g_color, $b_color));
			imagefttext($legend_image, 8, 0, 220, 19, $black, $font_file, $key_value_array["tcount5"]);

			imagefttext($legend_image, 8, 0,   0, 30, $black, $font_file, $key_value_array["t11"]);
			imagefttext($legend_image, 8, 0,  40, 30, $black, $font_file, $key_value_array["t13"]);
			imagefttext($legend_image, 8, 0,  90, 30, $black, $font_file, $key_value_array["t15"]);
			imagefttext($legend_image, 8, 0, 140, 30, $black, $font_file, $key_value_array["t17"]);
			imagefttext($legend_image, 8, 0, 190, 30, $black, $font_file, $key_value_array["t19"]);
			imagefttext($legend_image, 8, 0, 230, 30, $black, $font_file, $key_value_array["kpi110"]);

			imageline($legend_image, 0, 35, 250, 35, $cyan);

			//
			//	band - 2:
			//
			imagefttext($legend_image, 8, 0, 0, 48, $black, $font_file, 'Band 2 - HSOPA ACR');

			$r_color = hexdec(substr($key_value_array["color21"],1,2));
			$g_color = hexdec(substr($key_value_array["color21"],3,2));
			$b_color = hexdec(substr($key_value_array["color21"],5,2));
			imagefilledrectangle($legend_image,   0, 48,  50, 60,   imagecolorallocate($legend_image, $r_color, $g_color, $b_color));
			imagefttext($legend_image, 8, 0, 20, 60, $black, $font_file, $key_value_array["tcount21"]);

			$r_color = hexdec(substr($key_value_array["color23"],1,2));
			$g_color = hexdec(substr($key_value_array["color23"],3,2));
			$b_color = hexdec(substr($key_value_array["color23"],5,2));
			imagefilledrectangle($legend_image,  50, 48, 100, 60,   imagecolorallocate($legend_image, $r_color, $g_color, $b_color));
			imagefttext($legend_image, 8, 0, 70, 60, $black, $font_file, $key_value_array["tcount22"]);

			$r_color = hexdec(substr($key_value_array["color25"],1,2));
			$g_color = hexdec(substr($key_value_array["color25"],3,2));
			$b_color = hexdec(substr($key_value_array["color25"],5,2));
			imagefilledrectangle($legend_image, 100, 48, 150, 60,   imagecolorallocate($legend_image, $r_color, $g_color, $b_color));
			imagefttext($legend_image, 8, 0, 120, 60, $black, $font_file, $key_value_array["tcount23"]);

			$r_color = hexdec(substr($key_value_array["color27"],1,2));
			$g_color = hexdec(substr($key_value_array["color27"],3,2));
			$b_color = hexdec(substr($key_value_array["color27"],5,2));
			imagefilledrectangle($legend_image, 150, 48, 200, 60,   imagecolorallocate($legend_image, $r_color, $g_color, $b_color));
			imagefttext($legend_image, 8, 0, 170, 60, $black, $font_file, $key_value_array["tcount24"]);

			$r_color = hexdec(substr($key_value_array["color29"],1,2));
			$g_color = hexdec(substr($key_value_array["color29"],3,2));
			$b_color = hexdec(substr($key_value_array["color29"],5,2));
			imagefilledrectangle($legend_image, 200, 48, 250, 60,   imagecolorallocate($legend_image, $r_color, $g_color, $b_color));
			imagefttext($legend_image, 8, 0, 220, 60, $black, $font_file, $key_value_array["tcount25"]);

			imagefttext($legend_image, 8, 0,   0, 70, $black, $font_file, $key_value_array["t21"]);
			imagefttext($legend_image, 8, 0,  40, 70, $black, $font_file, $key_value_array["t23"]);
			imagefttext($legend_image, 8, 0,  90, 70, $black, $font_file, $key_value_array["t25"]);
			imagefttext($legend_image, 8, 0, 140, 70, $black, $font_file, $key_value_array["t27"]);
			imagefttext($legend_image, 8, 0, 190, 70, $black, $font_file, $key_value_array["t29"]);
			imagefttext($legend_image, 8, 0, 230, 70, $black, $font_file, $key_value_array["kpi210"]);

			imageline($legend_image, 0, 75, 250, 75, $cyan);

			//
			//	band - 3:
			//
			imagefttext($legend_image, 8, 0, 0, 88, $black, $font_file, 'Band 3 - HSOPA ACR');

			$r_color = hexdec(substr($key_value_array["color31"],1,2));
			$g_color = hexdec(substr($key_value_array["color31"],3,2));
			$b_color = hexdec(substr($key_value_array["color31"],5,2));
			imagefilledrectangle($legend_image,   0, 88,  50, 100,   imagecolorallocate($legend_image, $r_color, $g_color, $b_color));
			imagefttext($legend_image, 8, 0, 20, 100, $black, $font_file, $key_value_array["tcount31"]);

			$r_color = hexdec(substr($key_value_array["color33"],1,2));
			$g_color = hexdec(substr($key_value_array["color33"],3,2));
			$b_color = hexdec(substr($key_value_array["color33"],5,2));
			imagefilledrectangle($legend_image,  50, 88, 100, 100,   imagecolorallocate($legend_image, $r_color, $g_color, $b_color));
			imagefttext($legend_image, 8, 0, 70, 100, $black, $font_file, $key_value_array["tcount32"]);

			$r_color = hexdec(substr($key_value_array["color35"],1,2));
			$g_color = hexdec(substr($key_value_array["color35"],3,2));
			$b_color = hexdec(substr($key_value_array["color35"],5,2));
			imagefilledrectangle($legend_image, 100, 88, 150, 100,   imagecolorallocate($legend_image, $r_color, $g_color, $b_color));
			imagefttext($legend_image, 8, 0, 120, 100, $black, $font_file, $key_value_array["tcount33"]);

			$r_color = hexdec(substr($key_value_array["color37"],1,2));
			$g_color = hexdec(substr($key_value_array["color37"],3,2));
			$b_color = hexdec(substr($key_value_array["color37"],5,2));
			imagefilledrectangle($legend_image, 150, 88, 200, 100,   imagecolorallocate($legend_image, $r_color, $g_color, $b_color));
			imagefttext($legend_image, 8, 0, 170, 100, $black, $font_file, $key_value_array["tcount34"]);

			$r_color = hexdec(substr($key_value_array["color39"],1,2));
			$g_color = hexdec(substr($key_value_array["color39"],3,2));
			$b_color = hexdec(substr($key_value_array["color39"],5,2));
			imagefilledrectangle($legend_image, 200, 88, 250, 100,   imagecolorallocate($legend_image, $r_color, $g_color, $b_color));
			imagefttext($legend_image, 8, 0, 220, 100, $black, $font_file, $key_value_array["tcount35"]);

			imagefttext($legend_image, 8, 0,   0, 110, $black, $font_file, $key_value_array["t31"]);
			imagefttext($legend_image, 8, 0,  40, 110, $black, $font_file, $key_value_array["t33"]);
			imagefttext($legend_image, 8, 0,  90, 110, $black, $font_file, $key_value_array["t35"]);
			imagefttext($legend_image, 8, 0, 140, 110, $black, $font_file, $key_value_array["t37"]);
			imagefttext($legend_image, 8, 0, 190, 110, $black, $font_file, $key_value_array["t39"]);
			imagefttext($legend_image, 8, 0, 230, 110, $black, $font_file, $key_value_array["kpi310"]);

			imageline($legend_image, 0, 115, 250, 115, $cyan);

			//
			imagefilledrectangle($legend_image, 0, 133, 50, 153, $cyan);
			imagefttext($legend_image, 8, 0, 0, 163, $black, $font_file, 'Unknown');

			imagefilledarc($legend_image, 100, 147, 10, 10, 0, 359, $gray, IMG_ARC_PIE);
			imagefttext($legend_image, 8, 0, 120, 152, $black, $font_file, 'Cell Tower');

			imagefilledarc($legend_image, 100, 177, 40, 40, -45, 45, $cyan, IMG_ARC_PIE);
			imagefilledarc($legend_image, 100, 177, 10, 10, 0, 359, $gray, IMG_ARC_PIE);
			imagefttext($legend_image, 8, 0, 120, 182, $black, $font_file, 'Sector on Cell Tower');

			imagefilledarc($legend_image, 100, 207, 40, 40, -45, 45, $yellow, IMG_ARC_PIE);
			imagefilledarc($legend_image, 100, 207, 30, 30, -45, 45, $magenta, IMG_ARC_PIE);
			imagefilledarc($legend_image, 100, 207, 20, 20, -45, 45, $cyan, IMG_ARC_PIE);
			imagefilledarc($legend_image, 100, 207, 10, 10, 0, 359, $gray, IMG_ARC_PIE);
			imagefttext($legend_image, 8, 0, 120, 212, $black, $font_file, 'Bands in Sector');

			imagefilledarc($legend_image, 20, 257, 450, 450, -10, 10, $yellow, IMG_ARC_PIE);
			imagefilledarc($legend_image, 20, 257, 330, 330, -10, 10, $magenta, IMG_ARC_PIE);
			imagefilledarc($legend_image, 20, 257, 180, 180, -10, 10, $cyan, IMG_ARC_PIE);
			imagefilledarc($legend_image, 20, 257, 20, 20, 0, 359, $gray, IMG_ARC_PIE);

			imagefttext($legend_image, 8, 10,  50, 250, $black, $font_file, 'Band 1');
			imagefttext($legend_image, 8, 10, 120, 240, $black, $font_file, 'Band 2');
			imagefttext($legend_image, 8, 10, 190, 228, $black, $font_file, 'Band 3');

			imagefttext($legend_image, 8, 00,  50, 260, $black, $font_file, 'HSOPA ACR');
			imagefttext($legend_image, 8, 00, 120, 260, $black, $font_file, 'HSOPA ACR');
			imagefttext($legend_image, 8, 00, 190, 260, $black, $font_file, 'HSOPA ACR');

			imagefttext($legend_image, 10, 00, 10, 295, $black, $bold_font_file, 'Bands Selected:');
			imagefttext($legend_image,  8, 00, 10, 310, $black, $font_file, 'HSOPA ACR - HSOPA, Access Fail Rate');
			imagefttext($legend_image,  8, 00, 10, 325, $black, $font_file, 'HSOPA ACR - HSOPA, Access Fail Rate');
			imagefttext($legend_image,  8, 00, 10, 340, $black, $font_file, 'HSOPA ACR - HSOPA, Access Fail Rate');

			imagefttext($legend_image, 10, 00, 10, 360, $black, $bold_font_file, 'Layers Selected:');
			imagefttext($legend_image,  8, 00, 10, 375, $black, $font_file, 'GSM - GSM');
			imagefttext($legend_image,  8, 00, 10, 390, $black, $font_file, 'UMTS - UMTS');
			imagefttext($legend_image,  8, 00, 10, 405, $black, $font_file, 'UMTS Cell - UMTS Cell');

			//
			return $legend_image;

		} catch (Exception $error) {
fb($error->getMessage(),"==============> at generateKPIDLegend, error->getMessage()");
		}

fb("138.85.245.136.WMSController generateKPIDLegend ***************************************** exit");

	}
	*/
	/*
	protected function generateBasicLegend() {

require_once('c:\kpiProject\xampp\php\FirePHPCore\FirePHP.class.php');
$firephp = FirePHP::getInstance(true);
fb("138.85.245.136.WMSController generateBasicLegend *****************************************");

		try {
			//init vars
			$font           = Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . 'class/fonts/cour.ttf';
			$font_file      = Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . 'class/fonts/arial.ttf';
			$bold_font_file = Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . 'class/fonts/arialbd.ttf';


			//var_dump($font); exit;
			$count = count($this->sld['sld:StyledLayerDescriptor']['sld:NamedLayer']['sld:UserStyle']['sld:FeatureTypeStyle']['sld:Rule']);
			$height = $this->requestParams['HEIGHT'];
			$width = $this->requestParams['WIDTH'];
			*/
			/**
			 * Not having any data to work with, create a basic Legend ...
			 */
			 /*
			$width = 250;
			$height = 410;

			// create image
			$legend_image = imagecreatetruecolor($width, $height);

			//	get colors
			$black   = imagecolorallocate($legend_image, 0x00, 0x00, 0x00);
			$white   = imagecolorallocate($legend_image, 0xff, 0xff, 0xff);
			$gray    = imagecolorallocate($legend_image, 0xaa, 0xaa, 0xaa);
			$magenta = imagecolorallocate($legend_image, 0xff, 0x00, 0xff);
			$cyan    = imagecolorallocate($legend_image, 0x00, 0xff, 0xff);
			$yellow  = imagecolorallocate($legend_image, 0xff, 0xff, 0x00);

			//white background
			imagefilledrectangle($legend_image, 0, 0, $width, $height, $white);

			// Legend Title
			imagettftext($legend_image, 12, 0, 10, 20, $black, $font, "Basic Legend");

			//
			imagefilledrectangle($legend_image, 0, 133, 50, 153, $cyan);
			imagefttext($legend_image, 8, 0, 0, 163, $black, $font_file, 'Unknown');

			imagefilledarc($legend_image, 100, 147, 10, 10, 0, 359, $gray, IMG_ARC_PIE);
			imagefttext($legend_image, 8, 0, 120, 152, $black, $font_file, 'Cell Tower');

			imagefilledarc($legend_image, 100, 177, 40, 40, -45, 45, $cyan, IMG_ARC_PIE);
			imagefilledarc($legend_image, 100, 177, 10, 10, 0, 359, $gray, IMG_ARC_PIE);
			imagefttext($legend_image, 8, 0, 120, 182, $black, $font_file, 'Sector on Cell Tower');

			imagefilledarc($legend_image, 100, 207, 40, 40, -45, 45, $yellow, IMG_ARC_PIE);
			imagefilledarc($legend_image, 100, 207, 30, 30, -45, 45, $magenta, IMG_ARC_PIE);
			imagefilledarc($legend_image, 100, 207, 20, 20, -45, 45, $cyan, IMG_ARC_PIE);
			imagefilledarc($legend_image, 100, 207, 10, 10, 0, 359, $gray, IMG_ARC_PIE);
			imagefttext($legend_image, 8, 0, 120, 212, $black, $font_file, 'Bands in Sector');

			imagefilledarc($legend_image, 20, 257, 450, 450, -10, 10, $yellow, IMG_ARC_PIE);
			imagefilledarc($legend_image, 20, 257, 330, 330, -10, 10, $magenta, IMG_ARC_PIE);
			imagefilledarc($legend_image, 20, 257, 180, 180, -10, 10, $cyan, IMG_ARC_PIE);
			imagefilledarc($legend_image, 20, 257, 20, 20, 0, 359, $gray, IMG_ARC_PIE);

			imagefttext($legend_image, 8, 10,  50, 250, $black, $font_file, 'Band 1');
			imagefttext($legend_image, 8, 10, 120, 240, $black, $font_file, 'Band 2');
			imagefttext($legend_image, 8, 10, 190, 228, $black, $font_file, 'Band 3');

			imagefttext($legend_image, 8, 00,  50, 260, $black, $font_file, 'HSOPA ACR');
			imagefttext($legend_image, 8, 00, 120, 260, $black, $font_file, 'HSOPA ACR');
			imagefttext($legend_image, 8, 00, 190, 260, $black, $font_file, 'HSOPA ACR');

			imagefttext($legend_image, 10, 00, 10, 295, $black, $bold_font_file, 'Bands Selected:');
			imagefttext($legend_image,  8, 00, 10, 310, $black, $font_file, 'HSOPA ACR - HSOPA, Access Fail Rate');
			imagefttext($legend_image,  8, 00, 10, 325, $black, $font_file, 'HSOPA ACR - HSOPA, Access Fail Rate');
			imagefttext($legend_image,  8, 00, 10, 340, $black, $font_file, 'HSOPA ACR - HSOPA, Access Fail Rate');

			imagefttext($legend_image, 10, 00, 10, 360, $black, $bold_font_file, 'Layers Selected:');
			imagefttext($legend_image,  8, 00, 10, 375, $black, $font_file, 'GSM - GSM');
			imagefttext($legend_image,  8, 00, 10, 390, $black, $font_file, 'UMTS - UMTS');
			imagefttext($legend_image,  8, 00, 10, 405, $black, $font_file, 'UMTS Cell - UMTS Cell');

			//
			return $legend_image;

		} catch (Exception $error) {
fb($error->getMessage(),"==============> at generateBasicLegend error->getMessage()");
		}

fb("138.85.245.136.WMSController generateBasicLegend ***************************************** exit");

}
*/


	protected function GetMap(){
/*
require_once('c:\kpiProject\xampp\php\FirePHPCore\FirePHP.class.php');
$firephp = FirePHP::getInstance(true);
fb("138.85.245.136.WMSController GetMap *****************************************");
$test = print_r($_GET,true);
fb($test,"-------------> _GET");
*/

		//get params
		$this->buildGetMapRequestParams();

		// Get Layer config
		include './protected/config/layers.conf.php';
		//only support single layer currently
		list($group, $layer) = explode( ':' , $this->requestParams['LAYERS'][0]);
		$this->layerConfig = $layer_config[$group][$layer];


		// Load Style SLD
		$this->loadSLD($this->requestParams['STYLES']);

		$this->log->lwrite('end loading SLD : ' . (float)(Utils::timer() - $this->start) );

		// Query GIS Data
		$master_collection = $this->queryGIS();

		$this->log->lwrite('end loading queryGIS : ' . (float)(Utils::timer() - $this->start) );


		// Query Attributes via MySQL (Optional)
		if(isset($this->layerConfig['MySQL'])){
			$attributes = $this->queryMySQLAttributes();
			//match attributes to master_collection and save
			$myGeom = $this->layerConfig['MySQL']['Geom'];
			foreach($attributes as $row){
				$master_collection[$row[$myGeom]]['data'] =  $row;
			}
		}

		$this->log->lwrite('end loading queryMySQLAttributes : ' . (float)(Utils::timer() - $this->start) );


		// Query Attributes via API (Optional)
		if(isset($this->layerConfig['API'])){
			$attributes = $this->queryAPIAttributes();
			//match attributes to master_collection and save
			$myGeom = $this->layerConfig['API']['Geom'];
			foreach($attributes as $row){
				$master_collection[$row[$myGeom]]['data'] =  $row;
			}
		}

		// Use GD to Render Image from Master Data
		$im = $this->renderMapImage($master_collection);

		$this->log->lwrite('end loading renderImage : ' . (float)(Utils::timer() - $this->start) );

		// Output according to requested format
		switch($this->requestParams['FORMAT']){
			case 'image/png':
				$this->setContentType('png');
				imagepng($im);
			break;
			case 'image/jpeg':
				$this->setContentType('jpg');
				imagejpeg($im);
			break;
			case 'image/gif':
				$this->setContentType('gif');
				imagegif($im);
			break;
		}

		// Destroy
        imagedestroy($im);
	}


	protected function renderLegendImage(){
/*
require_once('c:\kpiProject\xampp\php\FirePHPCore\FirePHP.class.php');
$firephp = FirePHP::getInstance(true);
fb("138.85.245.136.WMSController renderLegendImage *****************************************");
$test = print_r($_GET,true);
fb($test,"-------------> _GET");
*/

		//init vars
		$font = Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER  . 'class/fonts/cour.ttf';
		//var_dump($font); exit;
		$count = count($this->sld['sld:StyledLayerDescriptor']['sld:NamedLayer']['sld:UserStyle']['sld:FeatureTypeStyle']['sld:Rule']);
		$height = $this->requestParams['HEIGHT'];
		$width = $this->requestParams['WIDTH'];

		$width = 230;
		$height = 26;

		// create image
		$im = imagecreatetruecolor($width, $height * ($count + 1));

		//black border
		imagefilledrectangle($im, 0, 0, $width, $height * ($count + 1), imagecolorallocate($im, 255, 255, 255));
		//white background
		imagerectangle($im, 0, 0, $width - 1, $height * ($count + 1) - 1, imagecolorallocate($im, 0, 0, 0));

		// Legend Title
		imagettftext($im, 11, 0, 10, 20, imagecolorallocate($im, 0, 0, 0), $font, "Legend + " . $this->sld['sld:StyledLayerDescriptor']['sld:NamedLayer']['sld:Name']);


		//Build Legend Body
		foreach ($this->sld['sld:StyledLayerDescriptor']['sld:NamedLayer']['sld:UserStyle']['sld:FeatureTypeStyle']['sld:Rule'] as $key => $rule)
		{

			//Get Symbolizer
			$syms[] = isset($rule['sld:PolygonSymbolizer']) ? SLD::polygonSymbolizer($rule['sld:PolygonSymbolizer']) : null;
			$syms[] = isset($rule['sld:PointSymbolizer']) ? SLD::pointSymbolizer($rule['sld:PointSymbolizer']) : null;


			foreach($syms as $sym){

				if(!isset($sym))
					continue;

				list($r, $g, $b ) = $this->rgb2array($sym->fill);

				//color rectangle
				imagefilledrectangle($im, 10, $height * ($key + 1), $this->requestParams['WIDTH'] + 10, ($height * ($key + 1)) + 15,  imagecolorallocate($im, $r, $g, $b));
				imagerectangle($im, 10, $height * ($key + 1), $this->requestParams['WIDTH'] + 10, ($height * ($key + 1)) + 15,  imagecolorallocate($im, 0, 0, 0));

			}

			//set legend category name
			imagettftext($im, 10, 0, 80,  ($height * ($key + 1)) + 12, imagecolorallocate($im, 0, 0, 0), $font, $rule['sld:Title']);
		}

		return $im;

	}
	protected function renderMapImage($master_collection){
/*
require_once('c:\kpiProject\xampp\php\FirePHPCore\FirePHP.class.php');
$firephp = FirePHP::getInstance(true);
fb("138.85.245.136.WMSController renderMapImage *****************************************");
$test = print_r($_GET,true);
fb($test,"-------------> _GET");
*/

		//Initalize Image
		$im = imagecreatetruecolor($this->requestParams['WIDTH'], $this->requestParams['HEIGHT']);
		$blank = imagecolorallocate( $im, 255, 255, 254 );
		imagefilledrectangle( $im, 0, 0, $this->requestParams['WIDTH'], $this->requestParams['HEIGHT'], $blank );

		//Loop though master_collection
		foreach($master_collection as $r){

			if(!isset($r["geom"])) break;

			$data = $r["data"];
			$geometry_collection = $r["geom"];
			$geom_type = get_class($geometry_collection);

			//Build Array of pointlist from Coords
			$pointlist = array();
			if(preg_match('/Multi/', $geom_type)){
				foreach($geometry_collection->getComponents() as $component){

					$linear_ring = $component->getComponents();
					$coords = $linear_ring[0]->getCoordinates();
					$pointlist  = $this->coords2pixels($coords);

				}
			}else{
			//TODO
			}

			$symbolizer = SLD::getStyleSymbolizer($this->sld, $data, $geom_type);

			$this->log->lwrite('end loading got Style Symbolizer : ' . (float)(Utils::timer() - $this->start) );


			switch(get_class($symbolizer)){
				case 'polygonSymbolizer':
					$this->generatePolygon($im, $pointlist, $symbolizer);
				break;
				case 'lineSymbolizer';
					$this->generateLine($im, $pointlist, $symbolizer);
				break;
				case 'pointSymbolizer';
					$this->generatePoint($im, $pointlist, $symbolizer);
				break;
			}

			$this->log->lwrite('end loading render poly : ' . (float)(Utils::timer() - $this->start) );

		}

		// Add transparency
		if($this->requestParams['TRANSPARENT'] == 'TRUE')
			imagecolortransparent($im, $blank);

		return $im;

	}
	protected function generatePolygon(&$im, $pointlist, $symbolizer){
/*
require_once('c:\kpiProject\xampp\php\FirePHPCore\FirePHP.class.php');
$firephp = FirePHP::getInstance(true);
fb("138.85.245.136.WMSController generatePolygon *****************************************");
$test = print_r($_GET,true);
fb($test,"-------------> _GET");
*/

		//allocate fill
		list($r, $g, $b ) = $this->rgb2array($symbolizer->fill);
		$fill = imagecolorallocatealpha($im, $r, $g, $b, $this->opacity2transparency($symbolizer->fill_opacity));

		//allocate border
		list($r, $g, $b ) = $this->rgb2array($symbolizer->stroke);
		$stroke = imagecolorallocatealpha($im, $r, $g, $b, $this->opacity2transparency($symbolizer->stroke_opacity));

		//Polygon
		// Need at least 3 points to plot a polygon
		if(count($pointlist) > 6){
			//fill
			imagefilledpolygon($im, $pointlist, count($pointlist)/2, $fill);
			//border
			imagepolygon($im, $pointlist, count($pointlist)/2, $stroke);
			imagesetthickness($im, $symbolizer->stroke_width);
		}

	}
	protected function opacity2transparency($opacity){
/*
require_once('c:\kpiProject\xampp\php\FirePHPCore\FirePHP.class.php');
$firephp = FirePHP::getInstance(true);
fb("138.85.245.136.WMSController opacity2transparency *****************************************");
$test = print_r($_GET,true);
fb($test,"-------------> _GET");
*/
		return floor((1-$opacity) * 127);
	}
	protected function color2rgb($color) {
/*
require_once('c:\kpiProject\xampp\php\FirePHPCore\FirePHP.class.php');
$firephp = FirePHP::getInstance(true);
fb("138.85.245.136.WMSController color2rgb *****************************************");
$test = print_r($_GET,true);
fb($test,"-------------> _GET");
*/
		return str_pad(base_convert($color, 10, 16), 6, 0, STR_PAD_LEFT);
	}
	protected function rgb2array($rgb) {
/*
require_once('c:\kpiProject\xampp\php\FirePHPCore\FirePHP.class.php');
$firephp = FirePHP::getInstance(true);
fb("138.85.245.136.WMSController rgb2array *****************************************");
$test = print_r($_GET,true);
fb($test,"-------------> _GET");
*/
		//strip # from leading RGB if exist
		$rgb = str_replace('#','',$rgb);
		return array(
			base_convert(substr($rgb, 0, 2), 16, 10),
			base_convert(substr($rgb, 2, 2), 16, 10),
			base_convert(substr($rgb, 4, 2), 16, 10),
		);
	}

	protected function loadSLD($style, $styleList){
/*
require_once('c:\kpiProject\xampp\php\FirePHPCore\FirePHP.class.php');
$firephp = FirePHP::getInstance(true);
fb("138.85.245.136.WMSController loadSLD *****************************************");
$test = print_r($_GET,true);
fb($test,"-------------> _GET");
*/
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
	protected function pixels2coords($x, $y){
/*
require_once('c:\kpiProject\xampp\php\FirePHPCore\FirePHP.class.php');
$firephp = FirePHP::getInstance(true);
fb("138.85.245.136.WMSController pixels2coords *****************************************");
$test = print_r($_GET,true);
fb($test,"-------------> _GET");
*/
		//set variables
		$minx = $this->requestParams['BBOX']['minx'];
		$miny = $this->requestParams['BBOX']['miny'];
		$maxx = $this->requestParams['BBOX']['maxx'];
		$maxy = $this->requestParams['BBOX']['maxy'];

		$height = $this->requestParams['HEIGHT'];
		$width = $this->requestParams['WIDTH'];

		//calc lng coord
		$lngSpan = $maxx - $minx;
		$pixelsPerDegLng = abs($width/$lngSpan);
		$lng = ($x / $pixelsPerDegLng) + $minx;

		//calc lat coord
		$latSpan = $maxy-$miny;
		$pixelsPerDegLat = abs($height/$latSpan);
		$lat = (($height - $y)/$pixelsPerDegLat) + $miny;

		return array( $lat, $lng);

	}
	protected function coords2pixels($coords){
/*
require_once('c:\kpiProject\xampp\php\FirePHPCore\FirePHP.class.php');
$firephp = FirePHP::getInstance(true);
fb("138.85.245.136.WMSController coords2pixels *****************************************");
$test = print_r($_GET,true);
fb($test,"-------------> _GET");
*/
		//set variables
		$minx = $this->requestParams['BBOX']['minx'];
		$miny = $this->requestParams['BBOX']['miny'];
		$maxx = $this->requestParams['BBOX']['maxx'];
		$maxy = $this->requestParams['BBOX']['maxy'];

		$height = $this->requestParams['HEIGHT'];
		$width = $this->requestParams['WIDTH'];

		$pointlist= array();

		foreach($coords as $c){

			$lng = (float)$c[0];
			$lat = (float)$c[1];

			//calc x pixel
			$lngSpan = $maxx - $minx;
			$pixelsPerDegLng = abs($width/$lngSpan);
			$x = ceil(( $lng - $minx  )* $pixelsPerDegLng);

			//calc y pixel
			$latSpan = $maxy-$miny;
			$pixelsPerDegLat = abs($height/$latSpan);
			$y = ceil(($lat-$miny) * $pixelsPerDegLat);
			$y = $height - $y;

			//save xy
			$pointlist[] = (int)$x;
			$pointlist[] = (int)$y;
		}

		return $pointlist;
	}

	//
	// Query PostGIS Data
	//
	protected function queryGIS(){
/*
require_once('c:\kpiProject\xampp\php\FirePHPCore\FirePHP.class.php');
$firephp = FirePHP::getInstance(true);
fb("138.85.245.136.WMSController queryGIS *****************************************");
$test = print_r($_GET,true);
fb($test,"-------------> _GET");
*/
		$pgDB = new DooSqlMagic;
		$pgDB->setDb($this->dbconfig, $this->layerConfig['GIS']['DB_Config']);
		$pgDB->connect();
		$pgPK = $this->layerConfig['GIS']['PK'];
		$pgGeom = $this->layerConfig['GIS']['Geom'];
		$pgSelect = $this->layerConfig['GIS']['Select'];
		$srid = $this->requestParams['SRID'];
		$minx = $this->requestParams['BBOX']['minx'];
		$miny = $this->requestParams['BBOX']['miny'];
		$maxx = $this->requestParams['BBOX']['maxx'];
		$maxy = $this->requestParams['BBOX']['maxy'];

		//fetch items inside bounding box
		$sql = "SELECT $pgPK, ST_AsText(ST_Transform($pgGeom, $srid)) as Coords from ($pgSelect) t1 WHERE ST_Transform($pgGeom, $srid) && SetSRID('BOX( $minx $miny, $maxx $maxy)'::box2d, $srid)";

		$data = $pgDB->fetchAll($sql);

		$this->log->lwrite('end loading queryGIS fetchALL : ' . (float)(Utils::timer() - $this->start) );

		//parse Well Known Text
		$p = new WKT();

		//build master collection
		$master_collection = array();

		foreach($data as $r){

			if(!$geom = $this->cache->get($r[$pgPK])){
				$geom = $p->read($r["coords"]);
				$this->cache->set($r[$pgPK], $geom, 86400 );
			}

			$master_collection[$r[$pgPK]] = array('data'=> array(), 'geom'=> $geom);



			//$master_collection[$r[$pgPK]] = array('data'=> array(), 'geom'=> $p->read($r["coords"]));
		}

		$this->log->lwrite('end loading queryGIS master_collection : ' . (float)(Utils::timer() - $this->start) );

		return $master_collection;
	}

	//
	// Query MySQL Attributes (must be grouped by $myGeom  field and be contained in select statement)
	//
	protected function queryMySQLAttributes(){
/*
require_once('c:\kpiProject\xampp\php\FirePHPCore\FirePHP.class.php');
$firephp = FirePHP::getInstance(true);
fb("138.85.245.136.WMSController queryMySQLAttributes *****************************************");
$test = print_r($_GET,true);
fb($test,"-------------> _GET");
*/
		$myDB = new DooSqlMagic;
		$myDB->setDb($this->dbconfig, $this->layerConfig['MySQL']['DB_Config']);
		$myDB->connect();

		$myGeom 	= $this->layerConfig['MySQL']['Geom'];
		$mySelect 	= $this->layerConfig['MySQL']['Select'];

		//Add PDO view params
		$params = null;
		if(isset($this->requestParams['VIEWPARAMS'])){
			$params = array();
			foreach($this->requestParams['VIEWPARAMS'] as $param){
				list($param, $value) = explode(':', $param);
				$params[":$param"] = $value;
			}
		}

		//fetch attributes
		return $myDB->fetchAll($mySelect, $params);
	}

	protected function queryAPIAttributes(){
/*
require_once('c:\kpiProject\xampp\php\FirePHPCore\FirePHP.class.php');
$firephp = FirePHP::getInstance(true);
fb("138.85.245.136.WMSController queryAPIAttributes *****************************************");
$test = print_r($_GET,true);
fb($test,"-------------> _GET");
*/
		/*TODO Implement + Add Memcache	*/
	}

	protected function queryInfoGIS($lat, $lng){
/*
require_once('c:\kpiProject\xampp\php\FirePHPCore\FirePHP.class.php');
$firephp = FirePHP::getInstance(true);
fb("138.85.245.136.WMSController queryInfoGIS *****************************************");
$test = print_r($_GET,true);
fb($test,"-------------> _GET");
*/
		$pgDB = new DooSqlMagic;
		$pgDB->setDb($this->dbconfig, $this->layerConfig['GIS']['DB_Config']);
		$pgDB->connect();
		$pgPK = $this->layerConfig['GIS']['PK'];
		$pgGeom = $this->layerConfig['GIS']['Geom'];
		$pgSelect = $this->layerConfig['GIS']['Select'];
		$srid = $this->requestParams['SRID'];
		$minx = $this->requestParams['BBOX']['minx'];
		$miny = $this->requestParams['BBOX']['miny'];
		$maxx = $this->requestParams['BBOX']['maxx'];
		$maxy = $this->requestParams['BBOX']['maxy'];
		$count = $this->requestParams['FEATURE_COUNT'];

		//fetch items inside bounding box
		$sql = "SELECT $pgPK, ST_AsText(ST_Transform($pgGeom, $srid)) as Coords from ($pgSelect) t1
			WHERE ST_Transform($pgGeom, $srid) && SetSRID('BOX( $minx $miny, $maxx $maxy)'::box2d, $srid)
				AND ST_Intersects(ST_Transform(the_geom, $srid),ST_SetSRID(ST_Point($lng, $lat), $srid)) LIMIT $count";

		$data = $pgDB->fetchAll($sql);

		//parse Well Known Text
		$p = new WKT();

		//build master collection
		$master_collection = array();
		foreach($data as $r){
			$master_collection[$r[$pgPK]] = array('data'=> array(), 'geom'=> $p->read($r["coords"]));
		}

		return $master_collection;
	}


}
?>
