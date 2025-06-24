<?php defined('BASEPATH') OR exit('No direct script access allowed');

include APPPATH . 'libraries/GeoIP/src/geoipcity.inc';

class GeoIp{

	public function info($ip){
		$gi = geoip_open(APPPATH . 'libraries/GeoIp/data/GeoLiteCity.dat', GEOIP_STANDARD);
		$record = geoip_record_by_addr($gi, $ip);
		geoip_close($gi);
		return $record;
	}

}