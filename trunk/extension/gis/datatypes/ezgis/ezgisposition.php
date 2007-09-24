<?php
/*
DROP TABLE IF EXISTS ezgis_position;
CREATE TABLE `ezgis_position` (
`contentobject_attribute_id` INT( 11 ) NOT NULL ,
`contentobject_attribute_version` INT( 11 ) NOT NULL ,
`latitude` FLOAT NOT NULL ,
`longitude` float NOT NULL,
`street` varchar(255) default NULL,
`zip` varchar(20) default NULL,
`city` varchar(255) default NULL,
`state` varchar(255) default NULL,
`country` varchar(255) default NULL
);
*/
include_once( "kernel/classes/ezpersistentobject.php" );

class eZGISPosition extends eZPersistentObject
{
    /*!
     Initializes a new URL alias.
    */
    function eZGISPosition( $row )
    {
        $this->eZPersistentObject( $row );
    }

    /*!
     \reimp
    */
    function &definition()
    {
        return array( "fields" => array( "contentobject_attribute_id" => array( 'name' => 'contentobject_attribute_id',
                                                        'datatype' => 'integer',
                                                        'default' => 0,
                                                        'required' => true ),
                                         "contentobject_attribute_version" => array( 'name' => 'contentobject_attribute_version',
                                                        'datatype' => 'integer',
                                                        'default' => 0,
                                                        'required' => true ),
                                         "latitude" => array( 'name' => "latitude",
                                                                 'datatype' => 'float',
                                                                 'default' => '0',
                                                                 'required' => true ),
                                         "longitude" => array( 'name' => "longitude",
                                                                   'datatype' => 'float',
                                                                   'default' => '0',
                                                                   'required' => true ),
                                         "street" => array( 'name' => "street",
                                                                 'datatype' => 'string',
                                                                 'default' => '',
                                                                 'required' => false ),
                                         "zip" => array( 'name' => "zip",
                                                                 'datatype' => 'string',
                                                                 'default' => '',
                                                                 'required' => false ),
                                         "city" => array( 'name' => "city",
                                                                 'datatype' => 'string',
                                                                 'default' => '',
                                                                 'required' => false ),
                                         "state" => array( 'name' => "state",
                                                                 'datatype' => 'string',
                                                                 'default' => '',
                                                                 'required' => false ),
                                         "country" => array( 'name' => "country",
                                                                 'datatype' => 'string',
                                                                 'default' => '',
                                                                 'required' => false )
                                                                 
                                                                    ),
                      "keys" => array( "contentobject_attribute_id", "contentobject_attribute_version" ),
                      'function_attributes' => array( 'is_valid' => 'isValid' ),
                      "class_name" => "eZGISPosition",
                      "name" => "ezgis_position" );
    }
    function isValid()
    {
    	//todo
    	return true;
    }
    // works only with guass krüger
    function &fetchByDistance(  $fromx, $fromy, $distance=100000, $limit=null )
    {
    	$asObject = true;
    	$minx = $fromx - $distance/2;
    	$maxx = $fromx + $distance/2;
    	$miny = $fromy - $distance/2;
    	$maxy = $fromy + $distance/2;
    	$db =& eZDB::instance();
    	$list =& eZPersistentObject::fetchObjectList( 
    	eZGISPosition::definition(), null,
    	array( 'x' => array( false, array( $minx, $maxx ) ),
    	'y' => array( false, array( $miny, $maxy ) ) ), 
    	null, $limit, $asObject
 );  

     	$list_count =& eZPersistentObject::fetchObjectList( 
    	eZGISPosition::definition(), null,
    	array( 'x' => array( false, array( $minx, $maxx ) ),
    	'y' => array( false, array( $miny, $maxy ) ) ), 
    	null, null, true
 );
		foreach( $list as $row )
		{
			$coa =& eZContentObjectAttribute::fetch( $row->attribute('contentobject_attribute_id'), $row->attribute('contentobject_attribute_version') );
			if ( is_object( $coa ) )
				$co =& eZContentObject::fetch( $coa->attribute( 'contentobject_id' ) );
			if ( is_object( $co ) )
				$result[] =& $co->attribute( 'main_node' );
		}
		return array( "SearchResult" => $result,
                          "SearchCount" => count( $list_count ),
                          "StopWordArray" => $stopWordArray );
    }
     // works only with guass krüger
	function &search( $x, $y, $distance, $params = array(), $searchTypes = array() )
	{
		$x=(float)$x;
		$y=(float)$y;
        if ( isset( $params['SearchLimit'] ) )
			$limit["limit"] = $params['SearchLimit'];

        if ( isset( $params['SearchOffset'] ) )
			$limit["offset"] = $params['SearchOffset'];

		if ( isset( $limit ) )
			$result =& eZGISPosition::fetchByDistance( $x, $y, $distance, $limit );
		else 
			$result =& eZGISPosition::fetchByDistance( $x, $y, $distance );
		return $result;
	}
    function fetch( $attribute_id, $version )
    {
    	$list =& eZPersistentObject::fetchObjectList( 
    	eZGISPosition::definition(), null,
    	array( 'contentobject_attribute_id' => $attribute_id, 'contentobject_attribute_version' => $version  ), 
    	null, null, true
 );  
		if ( $list[0] )
			return $list[0];
    }
}
?>