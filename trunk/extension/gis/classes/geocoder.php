<?php
class GeoCoder
{
    public $street;
    public $zip;
    public $city;
    public $state;
    public $country;
    public $longitude;
    public $latitude;
    public $accuracy;
    public $location;
    const ACCURACY_STREET = 'address';
    const ACCURACY_CITY = 'city';
    const ACCURACY_ZIP = 'zip';
/**
 * Useage:
 * <code>
 * $coder = GeoCoder::getActiveGeoCoder();
 * $coder->setAddress( 'Am Lindener Berge 22', '30449', 'Hannover', 'NI', 'Germany' );
 * if ( $coder->request() )
 * {
 *     //success
 *     echo $coder->lat;
 *     echo $coder->long;
 * }
 * else
 * {
 *     //error
 * }
 * </code>
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
    public function setAddress( $street, $zip, $city, $state, $country, $location = false )
    {
        if ( strlen ( $street ) > 1 )
            $this->street = trim( $street );
        if ( strlen( $zip ) > 1 )
            $this->zip = trim( $zip );
        if ( strlen( $city ) > 1 )
            $this->city = trim( $city );
        if ( strlen( $state ) > 1 )
            $this->state = trim( $state );
        if ( strlen( $country ) > 1 )
            $this->country = trim( $country );
        if ( $location !== false )
            $this->location = trim( $location );
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
    static function getActiveGeoCoder()
    {
        $type = eZINI::instance( 'gis.ini' )->variable( 'GISSettings', 'Interface' )."GeoCoder";
        return new $type;
        
    }
}
?>