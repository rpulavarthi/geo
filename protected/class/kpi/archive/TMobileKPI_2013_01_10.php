<?php

class TMobileKPI{

	static function generateKPIData($pieData, $tech,$tw,$date){

		$data = array();
		//var_dump($pieData); exit;
		//var_dump($viewParams); exit;
		
		//technology
		if($tech == 'GSM'){
		
			$kpiData = array();
			$kpiSQL = array();
			 
			//loop sectors
			foreach($pieData as $p){
			
			//	if(($val = $memcache->get($cache_key)) != FALSE){
			//		$kpiData[] = $val;
			//	}else{
				
				$db = substr($p["switch_id"], 0,2);
				$distrib[$db][$p["key"]] = $p["key"];
				
			}
			//var_dump($distrib);exit;
			//build request for each db
			foreach($distrib as $db=>$keys){
				$kpiRequest[] = array('key' => $db, 'post' => array('ip' => self::lookupRNCIP($db), 'db' => $db, 'sql' => self::getgsmKPISQL(join("','", array_keys($keys)),$tw,$date, $db)));
			}	
		}
		
		if($tech == 'UMTS'){
	
			//loop sectors
			foreach($pieData as $p){
			
			//	if(($val = $memcache->get($cache_key)) != FALSE){
			//		$kpiData[] = $val;
			//	}else{
				
				$db = substr($p["switch_id"], 0,2);
				$distrib[$db][$p["key"]] = $p["key"];
				
			}		
			
			//build request for each db
			foreach($distrib as $db=>$keys){
				$kpiRequest[] = array('key' => $db, 'post' => array('ip' => self::lookupRNCIP($db), 'db' => $db, 'sql' => self::getumtsKPISQL(join("','", array_keys($keys)),  $tw,$date, $db)));
			}	
		}
		
		//var_dump($kpiRequest); exit;
		
		if(isset($kpiRequest)){
			//build request
			foreach($kpiRequest as $r){
				$data[$r['key']] = array('url' => 'http://localhost/geo/query', 'post' => $r['post']);
			}
		
			//run Sql Async 
			$res = curl::multiRequest($data);
				
		//	var_dump($res); exit;
			
			//build array from result
			foreach($res as $db =>$rr){
				foreach(json_decode($rr, true) as $r){
					//get keys
					$key = trim($r['key']);
					$sector = $r['sector'];

					//remove
					unset( $r['key']);
					unset( $r['sector']);
							
					//save
					$data[$key][$sector] = $r; 
				}
			}
			
			//save cache
			/*foreach($d as $key=>$r){
				$cache_key = self::buildKPICacheKey($key, $viewParams['tw'], $viewParams['date'], 'cdma');				
				//$memcache->set($cache_key,  array($key => $r));
			}*/
		}
	 //var_dump($data); exit;
		return $data;
		
	}
	
	static function lookupRNCIP($db){
		include(Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . 'config/tmobile.rnc.config.php');
		return $rnc_conf[$db];
	}
	static function buildKPICacheKey($key, $tw, $date, $tech){
	
		//build key
		switch($tw){
			case 'Month':
				$cache_key = "$key:$tw:" . date('mY', strtotime($date)) . ":$tech";
			break;
			case 'Week':
				$cache_key = "$key:$tw:" . date('WY', strtotime($date)) . ":$tech";
			break;
			case 'Day':
				$cache_key = "$key:$tw:" . date('mdY', strtotime($date)) . ":$tech";
			break;
		}
		
		return $cache_key;
	}
	
	static function getgsmKPISQL($key, $tw, $date, $db){
		$sql = "";
		//convert
		$mysql_date = trim(date('Y-m-d ', strtotime($date)));
		if($tw == 'Month'){
			$sql = "SELECT 
					Cascad as 'key',
					SECTOR_NUMBER as sector,
					SUM(TOT_Primary_Drops) AS Drops,
					SUM(TOT_Primary_Drops) / SUM(TOT_Setup_attempts) * 100 AS Drop_perc,
					(SUM(TOT_ACCESS_FAILURES) + SUM(TOT_EQUIPMENT_BLOCKS)) AS Blocks,
					(SUM(TOT_ACCESS_FAILURES) + SUM(TOT_EQUIPMENT_BLOCKS)) / SUM(TOT_SETUP_ATTEMPTS) * 100 AS Block_perc,
					SUM(TOT_MOU) AS MOU,
					SUM(TOT_SETUP_ATTEMPTS) AS ATT
				FROM
					$db.chaddRFISS_1xPerf
				WHERE
					Cascad IN ( '$key' ) AND MONTH('$mysql_date') = MONTH(Daykey) AND YEAR('$mysql_date') AND YEAR(Daykey)
				GROUP BY Cascad , SECTOR_NUMBER";
		}
		
		if($tw == 'Week'){
			$sql = "SELECT 
					Cascad as 'key',
					SECTOR_NUMBER as sector,
					SUM(TOT_Primary_Drops) AS Drops,
					SUM(TOT_Primary_Drops) / SUM(TOT_Setup_attempts) * 100 AS Drop_perc,
					(SUM(TOT_ACCESS_FAILURES) + SUM(TOT_EQUIPMENT_BLOCKS)) AS Blocks,
					(SUM(TOT_ACCESS_FAILURES) + SUM(TOT_EQUIPMENT_BLOCKS)) / SUM(TOT_SETUP_ATTEMPTS) * 100 AS Block_perc,
					SUM(TOT_MOU) AS MOU,
					SUM(TOT_SETUP_ATTEMPTS) AS ATT
				FROM
					$db.chaddRFISS_1xPerf
				WHERE
					Cascad IN ( '$key' ) AND WEEK('$mysql_date') = WEEK(Daykey) AND YEAR('$mysql_date') AND YEAR(Daykey)
				GROUP BY Cascad , SECTOR_NUMBER";
	
		}
		
		if($tw == 'Day'){
			$sql = "SELECT 
					Cascad as 'key',
					SECTOR_NUMBER as sector,
					SUM(TOT_Primary_Drops) AS Drops,
					SUM(TOT_Primary_Drops) / SUM(TOT_Setup_attempts) * 100 AS Drop_perc,
					(SUM(TOT_ACCESS_FAILURES) + SUM(TOT_EQUIPMENT_BLOCKS)) AS Blocks,
					(SUM(TOT_ACCESS_FAILURES) + SUM(TOT_EQUIPMENT_BLOCKS)) / SUM(TOT_SETUP_ATTEMPTS) * 100 AS Block_perc,
					SUM(TOT_MOU) AS MOU,
					SUM(TOT_SETUP_ATTEMPTS) AS ATT
				FROM
					$db.chaddRFISS_1xPerf
				WHERE
					Cascad IN ( '$key' ) AND Daykey = '$mysql_date'
				GROUP BY Cascad , SECTOR_NUMBER";
		}
		
		return $sql;
	}

	static function getumtsKPISQL($key, $tw, $date, $db){
	
	
		$sql = "";
		//convert
		$mysql_date = trim(date('Y-m-d ', strtotime($date)));
		if($tw == 'Month'){
			$sql = "SELECT 
					Site_Name as 'key',
					Sector_Id as sector,
					SUM(Voice_Drop_Rate_N) / SUM(Voice_Drop_Rate_D) as Voice_DR,
					SUM(Voice_Drop_Rate_N) / SUM(Voice_Drop_Rate_D) as Voice_AFR,
					SUM(HSDPA_Drop_Rate_N) / SUM(HSDPA_Drop_Rate_D) as HSDPA_PS_DCR,
                                        SUM(HSDPA_interactive_Access_Failure_rate_N) / SUM(HSDPA_interactive_Access_Failure_rate_D) as HSDPA_PS_AFR,
					SUM(PS_Interactive_Drop_Rate_N) / SUM(PS_Interactive_Drop_Rate_D) as PS_RAB_DR,
                                        SUM(PS_Interactive_Access_Failure_Rate_N) / SUM(PS_Interactive_Access_Failure_Rate_D) as PS_AFR
				FROM
					$db.umtsRNCperf
				WHERE
					trim(Site_Name) IN ( '$key' ) AND MONTH('$mysql_date') = MONTH(Daykey) AND YEAR('$mysql_date') AND YEAR(Daykey)
				GROUP BY Site_Name , Sector_Id";
		}
		
		if($tw == 'Week'){
			$sql = "SELECT 
					Site_Name as 'key',
                                        Sector_Id as sector,
                                        SUM(Voice_Drop_Rate_N) / SUM(Voice_Drop_Rate_D) as Voice_DR,
                                        SUM(Voice_Drop_Rate_N) / SUM(Voice_Drop_Rate_D) as Voice_AFR,
                                        SUM(HSDPA_Drop_Rate_N) / SUM(HSDPA_Drop_Rate_D) as HSDPA_PS_DCR,
                                        SUM(HSDPA_interactive_Access_Failure_rate_N) / SUM(HSDPA_interactive_Access_Failure_rate_D) as HSDPA_PS_AFR,
                                        SUM(PS_Interactive_Drop_Rate_N) / SUM(PS_Interactive_Drop_Rate_D) as PS_RAB_DR,
                                        SUM(PS_Interactive_Access_Failure_Rate_N) / SUM(PS_Interactive_Access_Failure_Rate_D) as PS_AFR
                                FROM
                                        $db.umtsRNCperf
                                WHERE
                                        trim(Site_Name) IN ( '$key' ) AND week('$mysql_date') = week(Daykey) AND YEAR('$mysql_date') AND YEAR(Daykey)
                                GROUP BY Site_Name , Sector_Id";
	
		}
		
		if($tw == 'Day'){
			$sql = "SELECT
					 Site_Name as 'key',
                                        Sector_Id as sector,
                                        SUM(Voice_Drop_Rate_N) / SUM(Voice_Drop_Rate_D) as Voice_DR,
                                        SUM(Voice_Drop_Rate_N) / SUM(Voice_Drop_Rate_D) as Voice_AFR,
                                        SUM(HSDPA_Drop_Rate_N) / SUM(HSDPA_Drop_Rate_D) as HSDPA_PS_DCR,
                                        SUM(HSDPA_interactive_Access_Failure_rate_N) / SUM(HSDPA_interactive_Access_Failure_rate_D) as HSDPA_PS_AFR,
                                        SUM(PS_Interactive_Drop_Rate_N) / SUM(PS_Interactive_Drop_Rate_D) as PS_RAB_DR,
                                        SUM(PS_Interactive_Access_Failure_Rate_N) / SUM(PS_Interactive_Access_Failure_Rate_D) as PS_AFR
                                FROM
                                        $db.umtsRNCperf
                                WHERE
                                        trim(Site_Name) IN ( '$key' ) AND Daykey = '$mysql_date'
                                GROUP BY Site_Name , Sector_Id"; 
		}
		
		return $sql;
	}


	static function checkKPIData(){

		/*
		$viewParams['kpi1']
		$viewParams['op1']
		$viewParams['val1']

		$viewParams['kpi2']
		$viewParams['op2']
		$viewParams['val2']
		
		$viewParams['kpi3']
		$viewParams['op3']
		$viewParams['val3']
	*/

	}


}
?>
