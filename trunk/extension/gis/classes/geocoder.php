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
	var $location;
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
	 * @param string $location Optional. Defines a potential location you are looking for e.g. "Central Railway Station", "Airport"
	 */
	function setAddress( $street, $zip, $city, $state, $country, $location = false )
	{
	    $this->street = $street;
		$this->zip = $zip;
		$this->city = $city;
		$this->state = $state;
		$this->country = $country;
		$this->location = $location;
	}
	/**
	 * This function processes the request if a faulure is noticed this function will return false.
	 * 
	 * This fucntion sets $long and $lat and and updates the address if needed
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