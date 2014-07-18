<?php

	//
	// T-Mobile:GSM
	//
	$layer_config['T-Mobile']['GSM']['kpiClass'] = 'TMobileKPI';
	$layer_config['T-Mobile']['GSM']['styles'] = array('cluster' => array('cluster_size'), 'cell' => array('cell_type_gsm'), 'pie'=> array('pie_carrier_count'));
	//required fields => key , lat, long, geom in 900913
	$layer_config['T-Mobile']['GSM']['cellSQL'] = "select site_id as key, max(bsc_name) as switch_id, max(cell) as cell_id, max(latitude) as lat, max(longitude) as lon,  ST_Transform(ST_SetSRID(ST_MakePoint(max(longitude), max(latitude)), 4326), 900913) as geom
														FROM
														gis_schema.t_mobile_gsm_sectors
														GROUP BY site_id";

	$layer_config['T-Mobile']['GSM']['WMS']['pieRadius'] = 40;
	$layer_config['T-Mobile']['GSM']['sectorSQL'] = 'SELECT site_id as key,
																bsc_name,
																St_y(St_transform(geom, 4326)) AS latitude,
																St_x(St_transform(geom, 4326)) AS longitude, 																	 substring((site_id),8,1) as sector,
																cell,
																azimuth,
																\'65\' as hbw,
																\'1\' AS carrier_count,
																bsic,
																vendor,
																cell2
															FROM
															gis_schema.t_mobile_gsm_sectors
															WHERE site_id IN ($sql)';



															//
	// T-Mobile:UMTS
	//
	$layer_config['T-Mobile']['UMTS']['kpiClass'] = 'TMobileKPI';
	$layer_config['T-Mobile']['UMTS']['styles'] = array('cluster' => array('cluster_size'), 'cell' => array('cell_type_umts'), 'pie'=> array('pie_carrier_count'));
	//required fields => key , lat, long, geom in 900913
	$layer_config['T-Mobile']['UMTS']['cellSQL'] = "select site_id as key, max(rnc) as switch_id, max(cell) as cell_id, max(latitude) as lat, max(longitude) as lon, ST_Transform(ST_SetSRID(ST_MakePoint(max(longitude), max(latitude)), 4326), 900913) as geom
														FROM
													gis_schema.t_mobile_umts_sectors
													GROUP BY site_id";

	$layer_config['T-Mobile']['UMTS']['WMS']['pieRadius'] = 40;
	$layer_config['T-Mobile']['UMTS']['sectorSQL'] = 'SELECT site_id as key,
														max(rnc) as switch_id,
														St_y(St_transform(MAX(geom), $srid)) AS latitude,
														St_x(St_transform(MAX(geom), $srid)) AS longitude, 																 substring(max(cell),10,1) as sector,
														max(cell) as cell,
														azimuth,
														\'65\' as hbw,
														count(*) AS carrier_count,
														max(vendor) as vendor ,
														max(tech) as tech,
														max(cell_uid) cell_uid
													FROM
														gis_schema.t_mobile_umts_sectors
														WHERE site_id IN ($sql)
													group by site_id, azimuth';

	$layer_config['T-Mobile']['UMTSCell']['kpiClass'] = 'TMobileKPICell';
        $layer_config['T-Mobile']['UMTSCell']['styles'] = array('cluster' => array('cluster_size'), 'cell' => array('cell_type_umtscell'), 'pie'=> array('pie_carrier_count'));
        //required fields => key , lat, long, geom in 900913
        $layer_config['T-Mobile']['UMTSCell']['cellSQL'] = "select projectid as key, max(nodename) as switch_id, max(utrancellid) as cell_id, max(antennaposition_latitude) as lat, max(antennaposition_longitude) as lon, ST_Transform(ST_SetSRID(ST_MakePoint(max(antennaposition_longitude), max(antennaposition_latitude)), 4326), 900913) as geom
                                                                                                                FROM
                                                                                                         gis_schema.umtscmsites
                                                                                                        GROUP BY projectid";

        $layer_config['T-Mobile']['UMTSCell']['WMS']['pieRadius'] = 40;
	$layer_config['T-Mobile']['UMTSCell']['isCarrier'] = true;
        $layer_config['T-Mobile']['UMTSCell']['sectorSQL'] = 'SELECT max(projectid) as key,
                                                                                                                max(nodename) as switch_id,
                                                                                                                St_y(St_transform(MAX(geom), $srid)) AS latitude,
                                                                                                                St_x(St_transform(MAX(geom), $srid)) AS longitude,
																												max(sector_id) as sector,
                                                                                                                max(utrancellid) as cell,
                                                                                                                azimuth,
                                                                                                                \'65\' as hbw,
                                                                                                                count(*) AS carrier_count,
                                                                                                                max(market) as market ,
                                                                                                                max(region) as region,
                                                                                                                max(utrancellid) cell_uid
                                                                                                        FROM
                                                                                                                 gis_schema.umtscmsites
                                                                                                                WHERE projectid IN ($sql)
                                                                                                        group by site_name, azimuth';


	$layer_config['T-Mobile']['UMTSCell']['carrierSQL'] =	'SELECT max(projectid) as key,
                                                                                                                max(nodename) as switch_id,
                                                                                                                St_y(St_transform(max(geom), $srid)) AS latitude,
                                                                                                                St_x(St_transform(max(geom), $srid)) AS longitude,
                                                                                                                max(sector_id) as sector,
                                                                                                                max(carrier_id) as carrier,
                                                                                                                utrancellid as cell,
                                                                                                                max(band) as band,
                                                                                                                max(uarfcnul) as uarfcnul,
                                                                                                                max(uarfcndl) as uarfcndl,
                                                                                                                max(azimuth) as azimuth,
                                                                                                                \'65\' as hbw,
                                                                                                                max(market) as market ,
                                                                                                                max(region) as region,
                                                                                                                utrancellid cell_uid
                                                                                                        FROM
                                                                                                                gis_schema.umtscmsites
														WHERE projectid IN ($sql)
                                                                                                                 group by utrancellid order by sector,band';



// T-Mobile:LTE
	//
	$layer_config['T-Mobile']['LTE']['kpiClass'] = 'TMobileKPI';
	$layer_config['T-Mobile']['LTE']['styles'] = array('cluster' => array('cluster_size'), 'cell' => array('cell_type_lte'), 'pie'=> array('pie_carrier_count'));
	//required fields => key , lat, long, geom in 900913
	$layer_config['T-Mobile']['LTE']['cellSQL'] = 'select siteid as key, max(mme) as switch_id, max(cellname) as cell_id, max(latitude) as lat, max(longitude) as lon, ST_Transform(ST_SetSRID(ST_MakePoint(max(longitude), max(latitude)), 4326), 900913) as geom
														FROM
													gis_schema.t_mobile_lte_sectors
													GROUP BY siteid';

	$layer_config['T-Mobile']['LTE']['WMS']['pieRadius'] = 40;
	$layer_config['T-Mobile']['LTE']['sectorSQL'] = 'SELECT siteid as key,
														max(mme) as switch_id,
														St_y(St_transform(MAX(geom), $srid)) AS latitude,
														St_x(St_transform(MAX(geom), $srid)) AS longitude,
 														substring(max(cell),10,1) as sector,
														max(cellname) as cell,
														azimuth,
														\'65\' as hbw,
														count(*) AS carrier_count,
														\'\' as vendor ,
														\'LTE\' as tech,
														\'\' as cell_uid
													FROM
														gis_schema.t_mobile_lte_sectors
														WHERE site_id IN ($sql)
													group by siteid, azimuth';

?>
