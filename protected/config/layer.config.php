<?php

// Sprint: CDMA NA

    $layer_config['Sprint']['CDMA_NA']['kpiClass'] = 'SprintKPI';
    $layer_config['Sprint']['CDMA_NA']['styles'] = array('cluster' => array('cluster_size'), 'cell' => array('cell_type_cdma_na'), 'pie'=> array('pie_carrier_count'));

    $layer_config['Sprint']['CDMA_NA']['cellSQL'] = 'select cascad as key, max(switch_name) as switch_id, max(concat(cascad,sector,carrierid)) as cell_id, max(latitude) as lat, max(longitude) as lon, ST_Transform(ST_SetSRID(ST_MakePoint(max(longitude), max(latitude)), 4326), 900913) as geom
                                                        FROM
                                                    gis_schema.sprint_na_cdma_sectors
                                                    GROUP BY cascad';
    $layer_config['Sprint']['CDMA_NA']['WMS']['pieRadius'] = 40;
    $layer_config['Sprint']['CDMA_NA']['sectorSQL'] = 'SELECT cascad as key,
														max(switch_name) as switch_id, max(sid) as sid, max(Switch_Id) as sw_id, max(Cell_Id) as cid,
														case when(max(sc_type) like \'TRAFFIC CARRIER\') then \'cdma\' else \'evdo\' end as technology,
														St_y(St_transform(MAX(geom), $srid)) AS latitude,
														St_x(St_transform(MAX(geom), $srid)) AS longitude,
														max(sector) as sector,
														max(concat(cascad,sector,carrierid)) as cell,
														max(splitsectorflag) as splitsectorflag,
														max(split_sector_status) as split_sector_status,
														max(pn) as pn,
														max(Cluster) as cluster,
														azimuth,
														hbw,
														count(*) AS carrier_count,
														(select count(*) from gis_schema.sprint_na_cdma_sectors as b where b.cascad = a.cascad) as total_carrier_count,
														max(vendor) as vendor,
														max(concat(cascad,sector,carrierid)) as cell_uid,
														max(disaster) as dc,
														max(bts_equip) as bts_equip,
														max(Market99) as market,
														max(Area_Name) as area,
														max(Region) as region,
														max(OAD) as oad,
														max(FB) as fb,
														max(Elevation) as elevation,
														max(VBW) as vbw,
														max(E_Tilt) as E,
														max(M_Tilt) as M,
														max(Gain) as gain,
														max(Antenna_Model) as Antenna,
														max(bts_status) as bts_status,
														max(BTS_Equip) as equipment,
														Concat(max(Address1),\',\', max(City),\' \', max(State), \' \',max(Zip)) as address
														FROM
														gis_schema.sprint_na_cdma_sectors as a
														WHERE cascad IN ($sql)
														group by cascad, azimuth, hbw';
													
// Possible Outages NA
$layer_config['Sprint']['Possible_Outage_NA']['cellSQL'] = 'select cascad as key, max(switch_name) as switch_id, max(concat(cascad,sector,carrierid)) as cell_id, max(latitude) as lat, max(longitude) as lon, ST_Transform(ST_SetSRID(ST_MakePoint(max(longitude), max(latitude)), 4326), 900913) as geom
                                                        FROM
                                                    gis_schema.possible_outages_na
                                                    GROUP BY cascad';
    
	$layer_config['Sprint']['Possible_Outage_NA']['kpiClass'] = 'SprintKPI';
    $layer_config['Sprint']['Possible_Outage_NA']['styles'] = array('cluster' => array('cluster_size'), 'cell' => array('cell_type_possible_outage_na'), 'pie'=> array('pie_carrier_count'));
    $layer_config['Sprint']['Possible_Outage_NA']['WMS']['pieRadius'] = 40;
    $layer_config['Sprint']['Possible_Outage_NA']['sectorSQL'] = 'SELECT cascad as key,
	max(switch_name) as switch_id, max(sid) as sid, max(Switch_Id) as sw_id, max(Cell_Id) as cid,
	case when(max(sc_type) like \'TRAFFIC CARRIER\') then \'cdma\' else \'evdo\' end as technology,
														St_y(St_transform(MAX(geom), $srid)) AS latitude,
														St_x(St_transform(MAX(geom), $srid)) AS longitude,
														max(sector) as sector,
														max(concat(cascad,sector,carrierid)) as cell,
														max(splitsectorflag) as splitsectorflag,
														max(split_sector_status) as split_sector_status,
														max(pn) as pn,
														max(Cluster) as cluster,
														azimuth,
														hbw,
														count(*) AS carrier_count,
														(select count(*) from gis_schema.sprint_na_cdma_sectors as b where b.cascad = a.cascad) as total_carrier_count,
														max(vendor) as vendor,
														max(concat(cascad,sector,carrierid)) as cell_uid,
														max(disaster) as dc,
														max(bts_equip) as bts_equip,
														max(Market99) as market,
														max(Area_Name) as area,
														max(Region) as region,
														max(OAD) as oad,
														max(FB) as fb,
														max(Elevation) as elevation,
														max(VBW) as vbw,
														max(E_Tilt) as E,
														max(M_Tilt) as M,
														max(Gain) as gain,
														max(Antenna_Model) as Antenna,
														max(BTS_Equip) as equipment,
														Concat(max(Address1),\',\', max(City),\' \', max(State), \' \',max(Zip)) as address,
														max(active_alarm_count) as alarm_count
														FROM
														gis_schema.possible_outages_na as a
														WHERE cascad IN ($sql)
														group by cascad, azimuth, hbw';
														

// Possible Outages NA - CDMA
$layer_config['Sprint']['Possible_Outage_NA_CDMA']['cellSQL'] = 'select cascad as key, max(switch_name) as switch_id, max(concat(cascad,sector,carrierid)) as cell_id, max(latitude) as lat, max(longitude) as lon, ST_Transform(ST_SetSRID(ST_MakePoint(max(longitude), max(latitude)), 4326), 900913) as geom
                                                        FROM
                                                    gis_schema.possible_outages_na where sc_type like \'TRAFFIC CARRIER\'
                                                    GROUP BY cascad';
    
	$layer_config['Sprint']['Possible_Outage_NA_CDMA']['kpiClass'] = 'SprintKPI';
    $layer_config['Sprint']['Possible_Outage_NA_CDMA']['styles'] = array('cluster' => array('cluster_size'), 'cell' => array('cell_type_possible_outage_na'), 'pie'=> array('pie_carrier_count'));
    $layer_config['Sprint']['Possible_Outage_NA_CDMA']['WMS']['pieRadius'] = 40;
    $layer_config['Sprint']['Possible_Outage_NA_CDMA']['sectorSQL'] = 'SELECT cascad as key,
	max(switch_name) as switch_id, max(sid) as sid, max(Switch_Id) as sw_id, max(Cell_Id) as cid,
	case when(max(sc_type) like \'TRAFFIC CARRIER\') then \'cdma\' else \'evdo\' end as technology,
														St_y(St_transform(MAX(geom), $srid)) AS latitude,
														St_x(St_transform(MAX(geom), $srid)) AS longitude,
														max(sector) as sector,
														max(concat(cascad,sector,carrierid)) as cell,
														max(splitsectorflag) as splitsectorflag,
														max(split_sector_status) as split_sector_status,
														max(pn) as pn,
														max(Cluster) as cluster,
														azimuth,
														hbw,
														count(*) AS carrier_count,
														(select count(*) from gis_schema.sprint_na_cdma_sectors as b where b.cascad = a.cascad) as total_carrier_count,
														max(vendor) as vendor,
														max(concat(cascad,sector,carrierid)) as cell_uid,
														max(disaster) as dc,
														max(bts_equip) as bts_equip,
														max(Market99) as market,
														max(Area_Name) as area,
														max(Region) as region,
														max(OAD) as oad,
														max(FB) as fb,
														max(Elevation) as elevation,
														max(VBW) as vbw,
														max(E_Tilt) as E,
														max(M_Tilt) as M,
														max(Gain) as gain,
														max(Antenna_Model) as Antenna,
														max(BTS_Equip) as equipment,
														Concat(max(Address1),\',\', max(City),\' \', max(State), \' \',max(Zip)) as address,
														max(active_alarm_count) as alarm_count
														FROM
														gis_schema.possible_outages_na as a
														WHERE cascad IN ($sql) and sc_type like \'TRAFFIC CARRIER\'
														group by cascad, azimuth, hbw';														


// Possible Outages NA - EVDO
$layer_config['Sprint']['Possible_Outage_NA_EVDO']['cellSQL'] = 'select cascad as key, max(switch_name) as switch_id, max(concat(cascad,sector,carrierid)) as cell_id, max(latitude) as lat, max(longitude) as lon, ST_Transform(ST_SetSRID(ST_MakePoint(max(longitude), max(latitude)), 4326), 900913) as geom
                                                        FROM
                                                    gis_schema.possible_outages_na where sc_type like \'TRAFFIC EV-DO CARRIER\'
                                                    GROUP BY cascad';
    
	$layer_config['Sprint']['Possible_Outage_NA_EVDO']['kpiClass'] = 'SprintKPI';
    $layer_config['Sprint']['Possible_Outage_NA_EVDO']['styles'] = array('cluster' => array('cluster_size'), 'cell' => array('cell_type_possible_outage_na'), 'pie'=> array('pie_carrier_count'));
    $layer_config['Sprint']['Possible_Outage_NA_EVDO']['WMS']['pieRadius'] = 40;
    $layer_config['Sprint']['Possible_Outage_NA_EVDO']['sectorSQL'] = 'SELECT cascad as key,
	max(switch_name) as switch_id, max(sid) as sid, max(Switch_Id) as sw_id, max(Cell_Id) as cid,
	case when(max(sc_type) like \'TRAFFIC CARRIER\') then \'cdma\' else \'evdo\' end as technology,
														St_y(St_transform(MAX(geom), $srid)) AS latitude,
														St_x(St_transform(MAX(geom), $srid)) AS longitude,
														max(sector) as sector,
														max(concat(cascad,sector,carrierid)) as cell,
														max(splitsectorflag) as splitsectorflag,
														max(split_sector_status) as split_sector_status,
														max(pn) as pn,
														max(Cluster) as cluster,
														azimuth,
														hbw,
														count(*) AS carrier_count,
														(select count(*) from gis_schema.sprint_na_cdma_sectors as b where b.cascad = a.cascad) as total_carrier_count,
														max(vendor) as vendor,
														max(concat(cascad,sector,carrierid)) as cell_uid,
														max(disaster) as dc,
														max(bts_equip) as bts_equip,
														max(Market99) as market,
														max(Area_Name) as area,
														max(Region) as region,
														max(OAD) as oad,
														max(FB) as fb,
														max(Elevation) as elevation,
														max(VBW) as vbw,
														max(E_Tilt) as E,
														max(M_Tilt) as M,
														max(Gain) as gain,
														max(Antenna_Model) as Antenna,
														max(BTS_Equip) as equipment,
														Concat(max(Address1),\',\', max(City),\' \', max(State), \' \',max(Zip)) as address,
														max(active_alarm_count) as alarm_count
														FROM
														gis_schema.possible_outages_na as a
														WHERE cascad IN ($sql) and sc_type like \'TRAFFIC EV-DO CARRIER\'
														group by cascad, azimuth, hbw';														
														
													
// Sprint: CDMA NA Cell
    $layer_config['Sprint']['CDMA_NA_Cell']['kpiClass'] = 'SprintKPI';
    $layer_config['Sprint']['CDMA_NA_Cell']['styles'] = array('cluster' => array('cluster_size'), 'cell' => array('cell_type_cdma_na_cell'), 'pie'=> array('pie_carrier_count'));													
    $layer_config['Sprint']['CDMA_NA_Cell']['cellSQL'] = 'select concat(cascad,sector) as key, max(switch_name) as switch_id, max(concat(cascad,sector,carrierid)) as cell_id, max(latitude) as lat, max(longitude) as lon, ST_Transform(ST_SetSRID(ST_MakePoint(max(longitude), max(latitude)), 4326), 900913) as geom
                                                                                                                FROM
                                                                                                         gis_schema.sprint_na_cdma_sectors
                                                                                                        GROUP BY concat(cascad,sector)';

	$layer_config['Sprint']['CDMA_NA_Cell']['WMS']['pieRadius'] = 40;
	$layer_config['Sprint']['CDMA_NA_Cell']['isCarrier'] = true;
    $layer_config['Sprint']['CDMA_NA_Cell']['sectorSQL'] = 'SELECT max(concat(cascad,sector)) as key,
															max(switch_name) as switch_id,
															St_y(St_transform(MAX(geom), $srid)) AS latitude,
															St_x(St_transform(MAX(geom), $srid)) AS longitude,                                                                                                                               
															max(sector) as sector,
															max(concat(cascad,sector,carrierid)) as cell,
															azimuth,
															hbw as hbw,
															count(*) AS carrier_count,
															max(market) as market ,
															max(region) as region,
															max(concat(cascad,sector,carrierid)) cell_uid
															FROM
															gis_schema.sprint_na_cdma_sectors
															WHERE concat(cascad,sector,carrierid) IN ($sql)
															group by cascad, azimuth, hbw';
																
	$layer_config['Sprint']['CDMA_NA_Cell']['carrierSQL'] =   'SELECT max(concat(cascad,sector)) as key,
																max(switch_name) as switch_id,
																St_y(St_transform(max(geom), $srid)) AS latitude,
																St_x(St_transform(max(geom), $srid)) AS longitude,
																max(sector) as sector,
																max(carrierid) as carrier,
																concat(cascad,sector,carrierid) as cell,
																\'\' as band,
																\'\' as uarfcnul,
																\'\' as uarfcndl,
																max(azimuth) as azimuth,
																max(hbw) as hbw,
																max(market) as market ,
																max(region) as region,
																concat(cascad,sector,carrierid) as cell_uid
																FROM
																gis_schema.sprint_na_cdma_sectors
																WHERE concat(cascad,sector,carrierid) IN ($sql)
																 group by concat(cascad,sector,carrierid) order by sector';															
																
// Sprint: EVDO NA																
	$layer_config['Sprint']['EVDO_NA']['cellSQL'] = 'select cascad as key, max(switch_name) as switch_id, max(concat(cascad,sector,carrierid)) as cell_id, max(latitude) as lat, max(longitude) as lon, ST_Transform(ST_SetSRID(ST_MakePoint(max(longitude), max(latitude)), 4326), 900913) as geom
                                                        FROM
                                                    gis_schema.sprint_na_evdo_sectors
                                                    GROUP BY cascad';
	$layer_config['Sprint']['EVDO_NA']['kpiClass'] = 'SprintKPI';
    $layer_config['Sprint']['EVDO_NA']['styles'] = array('cluster' => array('cluster_size'), 'cell' => array('cell_type_evdo_na'), 'pie'=> array('pie_carrier_count'));
    $layer_config['Sprint']['EVDO_NA']['WMS']['pieRadius'] = 40;
    $layer_config['Sprint']['EVDO_NA']['sectorSQL'] = 'SELECT cascad as key,
														max(rncmanagername) as switch_id,
														max(Switch_Id) as sw_id,
														St_y(St_transform(MAX(geom), $srid)) AS latitude,
														St_x(St_transform(MAX(geom), $srid)) AS longitude,
														max(sector) as sector,
														max(concat(cascad,sector,carrierid)) as cell,
														max(splitsectorflag) as splitsectorflag,
														max(split_sector_status) as split_sector_status,
														max(pn) as pn,
														azimuth,
														hbw,
														count(*) AS carrier_count,
														max(vendor) as vendor,
														max(concat(cascad,sector,carrierid)) as cell_uid,
														max(disaster) as dc,
														max(bts_equip) as bts_equip,
														max(rncname) as rnc_id,
														max(RNCIPAddress) as rncip,
														max(SID) as sid,
														max(NID) as nid,
														(select count(*) from gis_schema.sprint_na_evdo_sectors as b where a.cascad = b.cascad) as total_carrier_count,
														max(Cluster) as cluster,
														max(Market99) as market,
														max(Area_Name) as area,
														max(Region) as region,
														max(L4Manager) as rfmanager,
														max(Engineer) as rfengineer,
														max(VBW) as vbw,
														max(E_Tilt) as E,
														max(M_Tilt) as M,
														max(Gain) as gain,
														max(Antenna_Model) as Antenna,
														max(BTS_Equip) as equipment,
														max(Elevation) as elevation,
														max(RadCenter) as rc,
														max(F_MOU_U_Date) as moudate,
														max(Flux_PNOFFSET) as f_pn,
														max(bts_status) as bts_status,
														Concat(max(address1),\',\', max(city),\' \', max(state), \' \',max(zip)) as address
														FROM
														gis_schema.sprint_na_evdo_sectors as a
														WHERE cascad IN ($sql)
														group by cascad, azimuth, hbw';
													
													
													
													
    $layer_config['Sprint']['EVDO_NA_Cell']['cellSQL'] = 'select concat(cascad,sector,carrierid) as key, max(switch_name) as switch_id, max(concat(cascad,sector,carrierid)) as cell_id, max(latitude) as lat, max(longitude) as lon, ST_Transform(ST_SetSRID(ST_MakePoint(max(longitude), max(latitude)), 4326), 900913) as geom
                                                                                                                FROM
                                                                                                         gis_schema.sprint_na_evdo_sectors
                                                                                                        GROUP BY concat(cascad,sector,carrierid)';
 // Sprint: CDMA NV   
	$layer_config['Sprint']['CDMA_NV']['cellSQL'] = 'select domodemcascade as key, max(switch_name) as switch_id, max(concat(domodemcascade,sector,carrierid)) as cell_id, max(latitude) as lat, max(longitude) as lon, ST_Transform(ST_SetSRID(ST_MakePoint(max(longitude), max(latitude)), 4326), 900913) as geom
                                                        FROM
                                                    gis_schema.sprint_nv_cdma_sectors
                                                    GROUP BY domodemcascade';
    
	$layer_config['Sprint']['CDMA_NV']['kpiClass'] = 'SprintKPI';
    $layer_config['Sprint']['CDMA_NV']['styles'] = array('cluster' => array('cluster_size'), 'cell' => array('cell_type_cdma_nv'), 'pie'=> array('pie_carrier_count'));
    $layer_config['Sprint']['CDMA_NV']['WMS']['pieRadius'] = 40;
    $layer_config['Sprint']['CDMA_NV']['sectorSQL'] = 'SELECT domodemcascade as key,max(engineeringcluster) as cluster,
														max(switch_name) as switch_id,max(s_id) as sid, max(switchid) as sw_id, \'\' as cid,
														St_y(St_transform(MAX(geom), $srid)) AS latitude,
														St_x(St_transform(MAX(geom), $srid)) AS longitude,
														max(sector) as sector,
														max(concat(domodemcascade,sector,carrierid)) as cell,
														max(pn_offset) as pn,
														azimuth,
														horizontal_bw as hbw,
														count(*) AS carrier_count,
														(select count(*) from gis_schema.sprint_nv_cdma_sectors as b where b.domodemcascade = a.domodemcascade) as total_carrier_count,
														max(vendorname) as vendor,
														max(concat(domodemcascade,sector,carrierid)) as cell_uid,
														max(disaster_class) as dc,
														max(bts_equip) as bts_equip,
														max(market99) as market,
														\'\' as area,
														max(orgregionname) as region,
														max(on_air_date) as oad,
														max(fb) as fb,
														max(elevation) as elevation,
														max(vbw) as vbw,
														max(e_tilt) as E,
														max(m_tilt) as M,
														max(antenna_gain_db) as gain,
														max(antenna_model) as Antenna,
														max(bts_equip) as equipment,
														Concat(max(address1),\',\', max(city),\' \', max(state), \' \',max(zip)) as address
														FROM
														gis_schema.sprint_nv_cdma_sectors as a
														WHERE domodemcascade IN ($sql)
														group by domodemcascade, azimuth, horizontal_bw';
	
	
	$layer_config['Sprint']['Possible_Outage_NV']['cellSQL'] = 'select domodemcascade as key, max(switch_name) as switch_id, max(concat(domodemcascade,sector,carrierid)) as cell_id, max(latitude) as lat, max(longitude) as lon, ST_Transform(ST_SetSRID(ST_MakePoint(max(longitude), max(latitude)), 4326), 900913) as geom
                                                        FROM
                                                    gis_schema.possible_outages_nv
                                                    GROUP BY domodemcascade';
    
	$layer_config['Sprint']['Possible_Outage_NV']['kpiClass'] = 'SprintKPI';
    $layer_config['Sprint']['Possible_Outage_NV']['styles'] = array('cluster' => array('cluster_size'), 'cell' => array('cell_type_possible_outage_nv'), 'pie'=> array('pie_carrier_count'));
    $layer_config['Sprint']['Possible_Outage_NV']['WMS']['pieRadius'] = 40;
    $layer_config['Sprint']['Possible_Outage_NV']['sectorSQL'] = 'SELECT domodemcascade as key,max(engineeringcluster) as cluster,
														max(switch_name) as switch_id,max(s_id) as sid, max(switchid) as sw_id, \'\' as cid,
														case when(max(sector_carrier_type) like \'CDMA\') then \'cdma\' else \'evdo\' end as technology,
														St_y(St_transform(MAX(geom), $srid)) AS latitude,
														St_x(St_transform(MAX(geom), $srid)) AS longitude,
														max(sector) as sector,
														max(concat(domodemcascade,sector,carrierid)) as cell,
														max(pn_offset) as pn,
														azimuth,
														horizontal_bw as hbw,
														count(*) AS carrier_count,
														(select count(*) from gis_schema.sprint_nv_cdma_sectors as b where b.domodemcascade = a.domodemcascade) as total_carrier_count,
														max(vendorname) as vendor,
														max(concat(domodemcascade,sector,carrierid)) as cell_uid,
														max(disaster_class) as dc,
														max(bts_equip) as bts_equip,
														max(market99) as market,
														\'\' as area,
														max(orgregionname) as region,
														max(on_air_date) as oad,
														max(fb) as fb,
														max(elevation) as elevation,
														max(vbw) as vbw,
														max(e_tilt) as E,
														max(m_tilt) as M,
														max(antenna_gain_db) as gain,
														max(antenna_model) as Antenna,
														max(bts_equip) as equipment,
														max(active_alarm_count) as alarm_count,
														Concat(max(address1),\',\', max(city),\' \', max(state), \' \',max(zip)) as address
														FROM
														gis_schema.possible_outages_nv as a
														WHERE domodemcascade IN ($sql)
														group by domodemcascade, azimuth, horizontal_bw';

	
	  // Possible outages NV - CDMA
	$layer_config['Sprint']['Possible_Outage_NV_CDMA']['cellSQL'] = 'select domodemcascade as key, max(switch_name) as switch_id, max(concat(domodemcascade,sector,carrierid)) as cell_id, max(latitude) as lat, max(longitude) as lon, ST_Transform(ST_SetSRID(ST_MakePoint(max(longitude), max(latitude)), 4326), 900913) as geom
                                                        FROM
                                                    gis_schema.possible_outages_nv where sector_carrier_type like \'CDMA\'
                                                    GROUP BY domodemcascade';
    
	$layer_config['Sprint']['Possible_Outage_NV_CDMA']['kpiClass'] = 'SprintKPI';
    $layer_config['Sprint']['Possible_Outage_NV_CDMA']['styles'] = array('cluster' => array('cluster_size'), 'cell' => array('cell_type_possible_outage_nv'), 'pie'=> array('pie_carrier_count'));
    $layer_config['Sprint']['Possible_Outage_NV_CDMA']['WMS']['pieRadius'] = 40;
    $layer_config['Sprint']['Possible_Outage_NV_CDMA']['sectorSQL'] = 'SELECT domodemcascade as key,max(engineeringcluster) as cluster,
														max(switch_name) as switch_id,max(s_id) as sid, max(switchid) as sw_id, \'\' as cid,
														case when(max(sector_carrier_type) like \'CDMA\') then \'cdma\' else \'evdo\' end as technology,
														St_y(St_transform(MAX(geom), $srid)) AS latitude,
														St_x(St_transform(MAX(geom), $srid)) AS longitude,
														max(sector) as sector,
														max(concat(domodemcascade,sector,carrierid)) as cell,
														max(pn_offset) as pn,
														azimuth,
														horizontal_bw as hbw,
														count(*) AS carrier_count,
														(select count(*) from gis_schema.sprint_nv_cdma_sectors as b where b.domodemcascade = a.domodemcascade) as total_carrier_count,
														max(vendorname) as vendor,
														max(concat(domodemcascade,sector,carrierid)) as cell_uid,
														max(disaster_class) as dc,
														max(bts_equip) as bts_equip,
														max(market99) as market,
														\'\' as area,
														max(orgregionname) as region,
														max(on_air_date) as oad,
														max(fb) as fb,
														max(elevation) as elevation,
														max(vbw) as vbw,
														max(e_tilt) as E,
														max(m_tilt) as M,
														max(antenna_gain_db) as gain,
														max(antenna_model) as Antenna,
														max(bts_equip) as equipment,
														max(active_alarm_count) as alarm_count,
														Concat(max(address1),\',\', max(city),\' \', max(state), \' \',max(zip)) as address
														FROM
														gis_schema.possible_outages_nv as a
														WHERE domodemcascade IN ($sql) and sector_carrier_type like \'CDMA\'
														group by domodemcascade, azimuth, horizontal_bw';
														
	
	// Possible outages NV - EVDO
	$layer_config['Sprint']['Possible_Outage_NV_EVDO']['cellSQL'] = 'select domodemcascade as key, max(switch_name) as switch_id, max(concat(domodemcascade,sector,carrierid)) as cell_id, max(latitude) as lat, max(longitude) as lon, ST_Transform(ST_SetSRID(ST_MakePoint(max(longitude), max(latitude)), 4326), 900913) as geom
                                                        FROM
                                                    gis_schema.possible_outages_nv where sector_carrier_type like \'EVDO\'
                                                    GROUP BY domodemcascade';
    
	$layer_config['Sprint']['Possible_Outage_NV_EVDO']['kpiClass'] = 'SprintKPI';
    $layer_config['Sprint']['Possible_Outage_NV_EVDO']['styles'] = array('cluster' => array('cluster_size'), 'cell' => array('cell_type_possible_outage_nv'), 'pie'=> array('pie_carrier_count'));
    $layer_config['Sprint']['Possible_Outage_NV_EVDO']['WMS']['pieRadius'] = 40;
    $layer_config['Sprint']['Possible_Outage_NV_EVDO']['sectorSQL'] = 'SELECT domodemcascade as key,max(engineeringcluster) as cluster,
														max(switch_name) as switch_id,max(s_id) as sid, max(switchid) as sw_id, \'\' as cid,
														case when(max(sector_carrier_type) like \'CDMA\') then \'cdma\' else \'evdo\' end as technology,
														St_y(St_transform(MAX(geom), $srid)) AS latitude,
														St_x(St_transform(MAX(geom), $srid)) AS longitude,
														max(sector) as sector,
														max(concat(domodemcascade,sector,carrierid)) as cell,
														max(pn_offset) as pn,
														azimuth,
														horizontal_bw as hbw,
														count(*) AS carrier_count,
														(select count(*) from gis_schema.sprint_nv_cdma_sectors as b where b.domodemcascade = a.domodemcascade) as total_carrier_count,
														max(vendorname) as vendor,
														max(concat(domodemcascade,sector,carrierid)) as cell_uid,
														max(disaster_class) as dc,
														max(bts_equip) as bts_equip,
														max(market99) as market,
														\'\' as area,
														max(orgregionname) as region,
														max(on_air_date) as oad,
														max(fb) as fb,
														max(elevation) as elevation,
														max(vbw) as vbw,
														max(e_tilt) as E,
														max(m_tilt) as M,
														max(antenna_gain_db) as gain,
														max(antenna_model) as Antenna,
														max(bts_equip) as equipment,
														max(active_alarm_count) as alarm_count,
														Concat(max(address1),\',\', max(city),\' \', max(state), \' \',max(zip)) as address
														FROM
														gis_schema.possible_outages_nv as a
														WHERE domodemcascade IN ($sql) and sector_carrier_type like \'EVDO\'
														group by domodemcascade, azimuth, horizontal_bw';
	
    $layer_config['Sprint']['CDMA_NV_Cell']['cellSQL'] = 'select concat(domodemcascade,sector,carrierid) as key, max(switch_name) as switch_id, max(concat(domodemcascade,sector,carrierid)) as cell_id, max(latitude) as lat, max(longitude) as lon, ST_Transform(ST_SetSRID(ST_MakePoint(max(longitude), max(latitude)), 4326), 900913) as geom
                                                                                                                FROM
                                                                                                         gis_schema.sprint_nv_cdma_sectors
                                                                                                        GROUP BY concat(domodemcascade,sector,carrierid)';	
	
    $layer_config['Sprint']['EVDO_NV']['cellSQL'] = 'select domodemcascade as key, max(switch_name) as switch_id, max(concat(domodemcascade,sector,carrierid)) as cell_id, max(latitude) as lat, max(longitude) as lon, ST_Transform(ST_SetSRID(ST_MakePoint(max(longitude), max(latitude)), 4326), 900913) as geom
                                                        FROM
                                                    gis_schema.sprint_nv_evdo_sectors
                                                    GROUP BY domodemcascade';
    $layer_config['Sprint']['EVDO_NV']['kpiClass'] = 'SprintKPI';
    $layer_config['Sprint']['EVDO_NV']['styles'] = array('cluster' => array('cluster_size'), 'cell' => array('cell_type_evdo_nv'), 'pie'=> array('pie_carrier_count'));
    $layer_config['Sprint']['EVDO_NV']['WMS']['pieRadius'] = 40;
    $layer_config['Sprint']['EVDO_NV']['sectorSQL'] = 'SELECT domodemcascade as key,
														max(rncmanagername) as switch_id,
														max(switchid) as sw_id,
														St_y(St_transform(MAX(geom), $srid)) AS latitude,
														St_x(St_transform(MAX(geom), $srid)) AS longitude,
														max(sector) as sector,
														max(concat(domodemcascade,sector,carrierid)) as cell,
														max(pn_offset) as pn,
														azimuth,
														horizontal_bw as hbw,
														count(*) AS carrier_count,
														max(vendorname) as vendor,
														max(concat(domodemcascade,sector,carrierid)) as cell_uid,
														max(disaster_class) as dc,
														max(bts_equip) as bts_equip,
														max(rncname) as rnc_id,
														max(rncs_ip) as rncip,
														max(s_id) as sid,
														max(nid) as nid,
														(select count(*) from gis_schema.sprint_nv_evdo_sectors as b where a.domodemcascade = b.domodemcascade) as total_carrier_count,
														max(engineeringcluster) as cluster,
														max(market99) as market,
														max(region_area_name) as area,
														max(orgregionname) as region,
														\'\' as rfmanager,
														\'\' as rfengineer,
														max(vbw) as vbw,
														max(e_tilt) as E,
														max(m_tilt) as M,
														max(antenna_gain_db) as gain,
														max(antenna_model) as Antenna,
														max(bts_equip) as equipment,
														max(elevation) as elevation,
														\'\' as rc,
														max(mou_update_date) as moudate,
														max(flux_pnoffset) as f_pn,
														Concat(max(address1),\',\', max(city),\' \', max(state), \' \',max(zip)) as address
														FROM
														gis_schema.sprint_nv_cdma_sectors as a
														WHERE domodemcascade IN ($sql)
														group by domodemcascade, azimuth, horizontal_bw';
														

	$layer_config['Sprint']['EVDO_NV_Cell']['cellSQL'] = 'select concat(domodemcascade,sector,carrierid) as key, max(switch_name) as switch_id, max(concat(domodemcascade,sector,carrierid)) as cell_id, max(latitude) as lat, max(longitude) as lon, ST_Transform(ST_SetSRID(ST_MakePoint(max(longitude), max(latitude)), 4326), 900913) as geom
                                                                                                                FROM
                                                                                                         gis_schema.sprint_nv_evdo_sectors
                                                                                                        GROUP BY concat(domodemcascade,sector,carrierid)';

    $layer_config['Sprint']['LTE']['cellSQL'] = 'select cascad as key, max(enodebid) as switch_id, max(concat(cascad,sector,0)) as cell_id, max(latitude) as lat, max(longitude) as lon, ST_Transform(ST_SetSRID(ST_MakePoint(max(longitude), max(latitude)), 4326), 900913) as geom
                                                        FROM
                                                    gis_schema.sprint_lte_sectors
                                                    GROUP BY cascad';
    $layer_config['Sprint']['LTE']['kpiClass'] = 'SprintKPI';
    $layer_config['Sprint']['LTE']['styles'] = array('cluster' => array('cluster_size'), 'cell' => array('cell_type_lte'), 'pie'=> array('pie_carrier_count'));
    $layer_config['Sprint']['LTE']['WMS']['pieRadius'] = 40;
    $layer_config['Sprint']['LTE']['sectorSQL'] = 'SELECT cascad as key,
														max(enodebid) as switch_id,
														St_y(St_transform(MAX(geom), $srid)) AS latitude,
														St_x(St_transform(MAX(geom), $srid)) AS longitude,
														max(sector) as sector,
														max(pci) as pci,
														max(concat(cascad,sector,0)) as cell,
														max(downtilt_mechanical) as m_tilt,
														max(antenna_height_ft) as antenna_ht,
														max(zipcode) as zipcode,
														max(antenna_gain) as gain,
														max(antenna_type) as antenna_type,
                                                        \'\' as pn,
														azimuth,
														beamwidth as hbw,
														count(*) AS carrier_count,
														max(vendor_name) as vendor,
														max(concat(cascad,sector,0)) as cell_uid
														FROM
														gis_schema.sprint_lte_sectors
														WHERE cascad IN ($sql)
														group by cascad, azimuth, beamwidth';
   
   
   $layer_config['Sprint']['LTECell']['cellSQL'] = 'select concat(cascad,sector,0) as key, max(enodebid) as switch_id, max(concat(cascad,sector,0)) as cell_id, max(latitude) as lat, max(longitude) as lon, ST_Transform(ST_SetSRID(ST_MakePoint(max(longitude), max(latitude)), 4326), 900913) as geom
                                                                                                                FROM
                                                                                                         gis_schema.sprint_lte_sectors
                                                                                                        GROUP BY concat(cascad,sector,0)';
// Sprint: Repeater Sectors

    $layer_config['Sprint']['Repeater']['kpiClass'] = 'SprintKPI';
    $layer_config['Sprint']['Repeater']['styles'] = array('cluster' => array('cluster_size'), 'cell' => array('cell_type_repeater'), 'pie'=> array('pie_carrier_count'));

	$layer_config['Sprint']['Repeater']['cellSQL'] = 'select cascad as key, case when(max(switch_name) is not null) then max(switch_name) else max(rncmanagername) end as switch_id, 
	max(concat(cascad,sector,carrierid)) as cell_id, max(latitude) as lat, max(longitude) as lon, ST_Transform(ST_SetSRID(ST_MakePoint(max(longitude), max(latitude)), 4326), 900913) as geom
	FROM
	gis_schema.sprint_repeater_sectors
	GROUP BY cascad;';
    $layer_config['Sprint']['Repeater']['WMS']['pieRadius'] = 40;
    $layer_config['Sprint']['Repeater']['sectorSQL'] = 'SELECT cascad as key,
														case when(max(sc_type) like \'TRAFFIC CARRIER\') then max(switch_name) else max(rncmanagername) end as switch_id,
														case when(max(sc_type) like \'TRAFFIC CARRIER\') then \'cdma\' else \'evdo\' end as technology,
														St_y(St_transform(MAX(geom), $srid)) AS latitude,
														St_x(St_transform(MAX(geom), $srid)) AS longitude,
														max(sector) as sector,
														max(concat(cascad,sector,carrierid)) as cell,
														max(pn) as pn,
														azimuth,
														hbw,
														count(*) AS carrier_count,
														max(vendor) as vendor,
														max(concat(cascad,sector,carrierid)) as cell_uid,
														max(disaster) as dc,
														max(bts_equip) as bts_equip,
														max(donor_site_id) as donor_site_id,
														max(bts_status) as bts_status,
														max(donor_sector) as donor_sector
														FROM
														gis_schema.sprint_repeater_sectors
														WHERE cascad IN ($sql)
														group by cascad, azimuth, hbw';
														
														
// Sprint: Outage Sectors

    $layer_config['Sprint']['Outage']['kpiClass'] = 'SprintKPI';
    $layer_config['Sprint']['Outage']['styles'] = array('cluster' => array('cluster_size'), 'cell' => array('cell_type_outage'), 'pie'=> array('pie_outage_type'));

	$layer_config['Sprint']['Outage']['cellSQL'] = 'select cascad as key, case when(max(switch_cilli_code) is not null) then max(switch_cilli_code) else max(rncmanagername) end as switch_id, 
	max(concat(cascad,sector,carrier)) as cell_id, max(latitude) as lat, max(longitude) as lon, ST_Transform(ST_SetSRID(ST_MakePoint(max(longitude), max(latitude)), 4326), 900913) as geom
	FROM
	gis_schema.outage_sites
	GROUP BY cascad;';
    $layer_config['Sprint']['Outage']['WMS']['pieRadius'] = 40;
    $layer_config['Sprint']['Outage']['sectorSQL'] = 'SELECT cascad as key,
														case when(max(switch_cilli_code) is not null) then max(switch_cilli_code) else max(rncmanagername) end as switch_id,
														St_y(St_transform(MAX(geom), $srid)) AS latitude,
														St_x(St_transform(MAX(geom), $srid)) AS longitude,
														max(sector) as sector,
														max(concat(cascad,sector,carrier)) as cell,
														max(pn) as pn,
														max(element) as element,
														max(error_type) as errortype,
														max(alarm) as alarm,
														concat(element,error_type,alarm) as outagetype,
														azimuth,
														hbw,
														count(*) AS carrier_count,
														max(concat(cascad,sector,carrier)) as cell_uid
														FROM
														gis_schema.outage_sites
														WHERE cascad IN ($sql)
														group by cascad, azimuth, hbw,element,error_type,alarm';

														
	$layer_config['Sprint']['Splits']['kpiClass'] = 'SprintKPI';
    $layer_config['Sprint']['Splits']['styles'] = array('cluster' => array('cluster_size'), 'cell' => array('cell_type_splits'), 'pie'=> array('pie_carrier_count'));

    $layer_config['Sprint']['Splits']['cellSQL'] = 'select cascad as key, max(switch_name) as switch_id, max(concat(cascad,sector,carrierid)) as cell_id, max(latitude) as lat, max(longitude) as lon, ST_Transform(ST_SetSRID(ST_MakePoint(max(longitude), max(latitude)), 4326), 900913) as geom
                                                        FROM
                                                    gis_schema.sprint_na_split_sectors
                                                    GROUP BY cascad';
    $layer_config['Sprint']['Splits']['WMS']['pieRadius'] = 40;
    $layer_config['Sprint']['Splits']['sectorSQL'] = 'SELECT cascad as key,
														max(switch_name) as switch_id, max(sid) as sid, max(Switch_Id) as sw_id, max(Cell_Id) as cid,
														case when(max(sc_type) like \'TRAFFIC CARRIER\') then \'cdma\' else \'evdo\' end as technology,
														St_y(St_transform(MAX(geom), $srid)) AS latitude,
														St_x(St_transform(MAX(geom), $srid)) AS longitude,
														max(sector) as sector,
														max(concat(cascad,sector,carrierid)) as cell,
														max(pn) as pn,
														max(Cluster) as cluster,
														azimuth,
														hbw,
														count(*) AS carrier_count,
														(select count(*) from gis_schema.sprint_na_cdma_sectors as b where b.cascad = a.cascad) as total_carrier_count,
														max(vendor) as vendor,
														max(concat(cascad,sector,carrierid)) as cell_uid,
														max(disaster) as dc,
														max(bts_equip) as bts_equip,
														max(Market99) as market,
														max(Area_Name) as area,
														max(Region) as region,
														max(OAD) as oad,
														max(FB) as fb,
														max(Elevation) as elevation,
														max(VBW) as vbw,
														max(E_Tilt) as E,
														max(M_Tilt) as M,
														max(Gain) as gain,
														max(Antenna_Model) as Antenna,
														max(BTS_Equip) as equipment,
														Concat(max(Address1),\',\', max(City),\' \', max(State), \' \',max(Zip)) as address,
														max(splitsectorflag) as splitsectorflag,
														max(split_sector_status) as split_sector_status
														FROM
														gis_schema.sprint_na_split_sectors as a
														WHERE cascad IN ($sql)
														group by cascad, azimuth, hbw';

//Sprint: Repeaters from ibal
$layer_config['Sprint']['Repeaters_iBal']['kpiClass'] = 'SprintKPI';
$layer_config['Sprint']['Repeaters_iBal']['styles'] = array('cluster' => array('cluster_size'), 'cell' => array('cell_type_repeater_ibal'), 'pie'=> array('pie_repeater_ibal_type'));

	$layer_config['Sprint']['Repeaters_iBal']['cellSQL'] = 'select cascad as key,max(cdma_donor_site_name) as donor, max(cdma_donor_site_id) as switch_id, max(cdma_donor_site_id) as donor_site_id, max(donor_sector) as donor_sector,max(cdma_evdo) as cdma_evdo,
	max(id) as cell_id, max(latitude) as lat, max(longitude) as lon,ST_Transform(ST_SetSRID(ST_MakePoint(max(longitude), max(latitude)), 4326), 900913) as geom
	FROM
	gis_schema.ibal_dump where latitude > 25 and longitude < -60 and latitude < 50 and longitude > -180
	GROUP BY cascad;';
    $layer_config['Sprint']['Repeaters_iBal']['WMS']['pieRadius'] = 40;
    $layer_config['Sprint']['Repeaters_iBal']['sectorSQL'] = 'SELECT cascad as key,
														max(cdma_donor_site_name) as donor, max(cdma_donor_site_id) as donor_site_id, 
														max(cdma_rssi) as rssi,
														max(description) as description,
														max(celltrack_id) as celltrackid,
														max(cdma_evdo) as cdma_evdo,
														\'\' as sector,
														\'\' as pn,
														\'\' as switch_id,
														max(donor_sector) as donor_sector,
														St_y(St_transform(MAX(geom), $srid)) AS latitude,
														St_x(St_transform(MAX(geom), $srid)) AS longitude,
														max(id) as cell,
														cdma_antenna_orientation as azimuth,
														30 as hbw,
														1 AS carrier_count,
														max(id) as cell_uid
														FROM
														gis_schema.ibal_dump
														WHERE cascad IN ($sql) and latitude > 25 and longitude < -60 and latitude < 50 and longitude > -180
														group by cascad, cdma_antenna_orientation';

																										
?>
