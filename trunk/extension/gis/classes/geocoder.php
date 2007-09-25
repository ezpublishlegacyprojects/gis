<?php
class GeoCoder
{
	var $street;
	var $zip;
	var $city;
	var $state;
	var $country;
	var $long;
	var $lat;
	var $accuracy;
/**
 *
 * @return GeoCoder
 */
	function GeoCoder() 
	{

	}
	/**
	 * Fills the Geocoder with initial data
	 *
	 * @param string $street
	 * @param string $zip
	 * @param string $city
	 * @param string $state
	 * @param string $country
	 */
	function setAddress( $street, $zip, $city, $state, $country )
	{
	    $this->street = $street;
		$this->zip = $zip;
		$this->city = $city;
		$this->state = $state;
		$this->country = $country;
	}
	/**
	 * This function processes the request if a faulure is noticed this function will return false.
	 * 
	 * @return boolean either true or false
	 */
	function request( )
	{
	    return true;
	}
	/**
	 * get the google or yahoo coder class
	 *
	 * @return GeoCoder
	 */
	function getActiveGeoCoder()
	{
	    /**
	     * @todo write code here
	     */
	}
}
?>