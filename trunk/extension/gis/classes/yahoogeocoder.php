<?php

include_once( "extension/gis/classes/geocoder.php" );

class YahooGeocoder extends GeoCoder {

	function YahooGeocoder() {
		
	}
	function buildURL ()
	{
		$url= "http://api.local.yahoo.com/MapsService/V1/geocode?appid=YahooDemo&street=701+First+Street&city=Sunnyvale&state=CA";
		$city= $http->postVariable( $base . '_ezgis_street_' . $contentObjectAttribute->attribute( 'id' ) );
		return $url;
		
	}
	function convertData ()
	{
		
	}
}
?>