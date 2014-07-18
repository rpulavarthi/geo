<?php

class SearchController extends DooController {

	public function beforeRun($resource, $action){
		$this->res = new response;
		 header("Access-Control-Allow-Origin:  *");
		//check if authenicated
//		session_start();
//		if(!isset($_SESSION['user'])){
//			$this->res->success = false;
//			$this->res->message = "Not Logged In!";
//			$this->setContentType("json");
//			echo $this->res->to_json();
			//return out of controller and afterRun
//			return 200;
//		}


		//set default schema
		include(Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . 'config/db.conf.php');
		header("Access-Control-Allow-Origin:  *");
		$this->schema = $dbconfig[Doo::conf()->APP_MODE][6];
		
	}
	
	function index() {
		$schema = $this->schema;
		$layer = $this->params['layer'];
		
		$page 		= $_GET['page']; // get the requested page
		$limit 		= $_GET['rows']; // get how many rows we want to have into the grid
		$sidx 		= $_GET['sidx']; // get index row - i.e. user click to sort
		$sord 		= $_GET['sord']; // get the direction
		$centerlat 	= $_GET['centerLat'];
		$centerlng 	= $_GET['centerLng'];		
		$bottom 	= $_GET['bottom'];
		$top 		= $_GET['top'];
		$left 		= $_GET['left'];
		$right 		= $_GET['right'];
		
		$searchTerm = $_GET['searchTerm'];
		
		if(!$sidx) $sidx = 1;
		
		$response = new searchResponse;
		
		//parse prefixes
		
		//Address (Geocode)

		$res = Doo::db()->fetchAll("SELECT  key || '   (' || switch_cell || ')' as key, layer, ((layer like '%$layer%')::boolean::int) as match, latitude as lat, longitude as lng, ST_Distance(ST_Transform(ST_GeomFromText('POINT($centerlng $centerlat)',4326), 900913), geom) as distance
                                                                                FROM $schema.cells
                                                                                WHERE key like '%$searchTerm%' ORDER BY distance asc, match desc limit 10");

			if($res) 
				{
                        	$response->rows = $res;
				}
			else {
				if(strlen($searchTerm) < 4) return;
				$g = new geocoder($searchTerm, $centerlat, $centerlng, abs($top - $bottom), abs($left - $right) );
				$placemarks = $g->geocode($searchTerm);
				 // echo count($placemarks);
				 // exit;
				$data = array();
                        	foreach($placemarks as $p){
                                	$data[] = array('key'=>$p->getAddress(), 'lat'=> $p->getPoint()->getLatitude(), 'lng' => $p->getPoint()->getLongitude());
                        	}

                        	$response->rows = $data;

			}



		
		//default		
		echo json_encode($response);
		
	}
	
	function nearest_cells()
	{
		$schema = $this->schema;
		$centerlat = $_GET['centerLat'];
		$centerlng = $_GET['centerLng'];
		$query = "Select key as label from $schema.cells where layer like '%CDMA%' or layer like '%EVDO%' or layer like '%LTE%' order by ST_Distance(ST_Transform(ST_GeomFromText('POINT($centerlng $centerlat)',900913), 900913), geom) asc limit 20";
		$res = Doo::db()->fetchAll($query);
		echo json_encode($res);
	}

}
?>
