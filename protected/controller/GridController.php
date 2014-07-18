<?php

class GridController extends DooController {

	public function beforeRun($resource, $action){

		header("Access-Control-Allow-Origin:  *");
		//set default schema
		include(Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . 'config/db.conf.php');
		$this->schema = $dbconfig[Doo::conf()->APP_MODE][6];
	}

	function index() {
		$schema = $this->schema;

		//Navigation
		$page = $_GET['page'];
		$limit = $_GET['rows'];
		$sidx = $_GET['sidx'];
		$sord = $_GET['sord'];

		//GIS
		$layer = $this->params['layer'];
		$minx = $_GET["minx"];
		$maxx = $_GET["maxx"];
		$miny = $_GET["miny"];
		$maxy = $_GET["maxy"];

		$srid = 4326;

		//get total results
		$sql = "SELECT count(*) as cnt FROM $schema.cells
					WHERE
					layer = '$layer'
					AND latitude between $minx and $maxx
					AND longitude between $miny and $maxy";

		$res = Doo::db()->fetchRow($sql);
		$count = $res["cnt"];

		// calculate the total pages for the query
		if( $count > 0 && $limit > 0) {
			$total_pages = ceil($count/$limit);
		} else {
			$total_pages = 0;
		}

		if ($page > $total_pages)
			$page = $total_pages;

		$start = $limit * $page - $limit;
		if($start <0) $start = 0;

		//full sql
		$sitesql = 'SELECT key, latitude, longitude, switch_cell FROM ' . $schema . '.cells';
		$sitesql .= ' WHERE';
        $sitesql .= ' layer = \'' . $layer . '\'';
		$sitesql .= ' AND latitude between ' . $minx . ' and ' . $maxx;
		$sitesql .= ' AND longitude between ' . $miny . ' and ' . $maxy;
		$sitesql .= ' ORDER BY ' . $sidx . ' ' . $sord;
		$sitesql .= ' LIMIT ' . $limit . ' OFFSET ' . $start;

		$sitedata = Doo::db()->fetchAll($sitesql);

		$rows = array();
		foreach($sitedata as $site){
			$rows[] = array('id' => $site['key'],  'cell' => array($site['key'], $site['switch_cell'], $site['latitude'], $site['longitude']));

		}

		echo json_encode(array('page' => $page, 'total' => $total_pages, 'records'=> $count , 'rows' => $rows));
	}
}
?>
