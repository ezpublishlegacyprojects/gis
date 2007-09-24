<?php
class GeoCoder {
	var $street;
	var $zip;
	var $city;
	var $state;
	var $country;
	function GeoCoder( $street, $zip, $city, $state, $country ) 
	{
		$this->street = $street;
		$this->zip = $zip;
		$this->city = $city;
		$this->state = $state;
		$this->country = $country;
	}
	function request( )
	{
		$url  = $this->buildURL();
		#$data = file_get_contents( $url );
		#$result = $this->convertData();
		#if ( is_array( $result ) )
		#	return $result;
		#else
		#	return false;
	}
}
?>