<?php

include_once( "extension/gis/datatypes/ezgis/ezgisposition.php" );
include_once( "extension/gis/classes/yahoogeocoder.php" );
include_once( "extension/gis/classes/googlegeocoder.php" );

include_once( "lib/ezutils/classes/ezinputvalidator.php");

define( 'EZ_DATATYPESTRING_GIS', "ezgis" );

class ezgistype extends eZDataType
{
    function ezgistype()
    {
        $this->eZDataType( EZ_DATATYPESTRING_GIS,  ezi18n( 'kernel/classes/datatypes', "Geographic Information Systems", 'Datatype name' ),
                           array( 'serialize_supported' => true, 'translation_allowed' => false ) );
    }
    /*!
    Validates all variables given on content class level
     \return EZ_INPUT_VALIDATOR_STATE_ACCEPTED or EZ_INPUT_VALIDATOR_STATE_INVALID if
             the values are accepted or not
    */
    function validateClassAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        return EZ_INPUT_VALIDATOR_STATE_ACCEPTED;
    }

    /*!
     Fetches all variables inputed on content class level
     \return true if fetching of class attributes are successfull, false if not
    */
    function fetchClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        return true;
    }
    /*!
     Validates input on content object level
     \return EZ_INPUT_VALIDATOR_STATE_ACCEPTED or EZ_INPUT_VALIDATOR_STATE_INVALID if
             the values are accepted or not
    */
    function validateObjectAttributeHTTPInput( &$http, &$base, &$contentObjectAttribute )
    {
    	if ( $http->hasPostVariable( $base . '_ezgis_longitude_' . $contentObjectAttribute->attribute( 'id' ) ) and $http->hasPostVariable( $base . '_ezgis_latitude_' . $contentObjectAttribute->attribute( 'id' ) ) )
        {
        	
        	$update = $http->postVariable( $base . '_ezgis_update_' . $contentObjectAttribute->attribute( 'id' ) );
        	$longitude = $http->postVariable( $base . '_ezgis_longitude_' . $contentObjectAttribute->attribute( 'id' ) );
        	$latitude = $http->postVariable( $base . '_ezgis_latitude_' . $contentObjectAttribute->attribute( 'id' ) );
        	$street = $http->postVariable( $base . '_ezgis_street_' . $contentObjectAttribute->attribute( 'id' ) );
        	$zip = $http->postVariable( $base . '_ezgis_zip_' . $contentObjectAttribute->attribute( 'id' ) );
        	$city = $http->postVariable( $base . '_ezgis_city_' . $contentObjectAttribute->attribute( 'id' ) );
        	$state = $http->postVariable( $base . '_ezgis_state_' . $contentObjectAttribute->attribute( 'id' ) );
        	$country = $http->postVariable( $base . '_ezgis_country_' . $contentObjectAttribute->attribute( 'id' ) );
        	
        	
        	if ( !empty( $update )
        	        or empty( $longitude )
					or empty( $latitude ) )
			{
			    $geocoder = GeoCoder::getActiveGeoCoder();
                $geocoder->setAddress( $street, $zip, $city, $state, $country );
				
				if ( $geocoder->request() )
				{
				    $gp = new eZGISPosition( 
    				array( 
    					'contentobject_attribute_id' => $contentObjectAttribute->attribute( 'id' ),
    		 			'contentobject_attribute_version' => $contentObjectAttribute->attribute( 'version' ), 
    					'latitude' => $geocoder->latitude,
    					'longitude' => $geocoder->longitude,
    					'street' => $street,
    					'zip' => $zip,
    					'city' => $city,
    					'state' => $state,
    					'country' => $country ) 
    				);
    				
    			    $contentObjectAttribute->Content = $gp;
            	    return EZ_INPUT_VALIDATOR_STATE_ACCEPTED;
				}
			}
        	
        	$ok = true;
		    if ( $latitude == "" or !settype( $latitude, 'float' ) or !is_float( $latitude ) )
		    {
		        $ok = false;
		        $contentObjectAttribute->setValidationError( ezi18n( 'kernel/classes/datatypes',
		                                                                     'Latitude is no floating point figure.' ) );
		    }
		    if ( $longitude == "" or !settype( $longitude, 'float' ) or !is_float( $longitude ) )
		    {
		        $ok = false;
		        $contentObjectAttribute->setValidationError( ezi18n( 'kernel/classes/datatypes',
		                                                                     'Longitude is no floating point figure.' ) );
		    }

            if ( $ok )
            {
            	$gp = new eZGISPosition( 
    				array( 
    					'contentobject_attribute_id' => $contentObjectAttribute->attribute( 'id' ),
    		 			'contentobject_attribute_version' => $contentObjectAttribute->attribute( 'version' ), 
    					'latitude' => $latitude,
    					'longitude' => $longitude,
    					'street' => $street,
    					'zip' => $zip,
    					'city' => $city,
    					'state' => $state,
    					'country' => $country ) 
    				);
    			$contentObjectAttribute->Content = $gp;
            	return EZ_INPUT_VALIDATOR_STATE_ACCEPTED;
            }
            else
            	return EZ_INPUT_VALIDATOR_STATE_INVALID;

        }
        $contentObjectAttribute->setValidationError( ezi18n( 'kernel/classes/datatypes',
                                                                         'Missing data.' ) );
        return EZ_INPUT_VALIDATOR_STATE_INVALID;
    }
    /*!
     Fetches the http post var string input and stores it in the data instance.
    */
    function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        return true;
    }
    /*!
     Sets the default value.
    */
    function initializeObjectAttribute( $contentObjectAttribute, $currentVersion, $originalContentObjectAttribute )
    {
        if ( $currentVersion != false )
        {
            $data = $originalContentObjectAttribute->attribute( "content" );
            if ( is_object( $data ) )
            {
            	$data->setAttribute( 'contentobject_attribute_version', $contentObjectAttribute->attribute( 'version' ) );
            	$contentObjectAttribute->setAttribute( "content", $data );
            	$contentObjectAttribute->Content = $data;
            	$contentObjectAttribute->store();
            }
            else 
            {
            	$contentObjectAttribute->setAttribute( "content", null );
            }
        }
        else
        {
			$contentObjectAttribute->setAttribute( "content", null );
        }
    }
    function arrayToXML($name,$val)
    {
    	$node = eZDOMDocument::createElementNodeFromArray($name,$val);
    	$doc = new eZDOMDocument();
		$doc->setRoot( $node );
    	return $doc->toString();	
    }
    function xmlToArray( $string )
    {
    	$xml = new eZXML();
    	$doc = $xml->domTree( $string );
    	if (is_object($doc))
    		return eZDOMDocument::createArrayFromDOMNode( $doc->root() );
    }
    function attributeXMLToArray($name)
    {
    	$xml = new eZXML();
    	$doc = $xml->domTree( $this->attribute($name) );
    	if (is_object($doc))
    		return eZDOMDocument::createArrayFromDOMNode( $doc->root() );
    }
    function dataArray()
    {
    	$xml = new eZXML();
    	$doc = $xml->domTree( $this->attribute('data') );
    	if (is_object($doc))
    		return eZDOMDocument::createArrayFromDOMNode( $doc->root() );
    }
    /*!
     Store the content. Since the content has been stored in function 
     fetchObjectAttributeHTTPInput(), this function is with empty code.
    */
    function storeObjectAttribute( $contentObjectAttribute )
    {  	
		$gp = $contentObjectAttribute->Content;
		if ( is_object( $gp ) )
		{
			$gp->store();
			return true;
		}
		return false;
    }
    
    function deleteStoredObjectAttribute( $contentObjectAttribute, $version = null )
    {
		$gp = eZGISPosition::fetch( $contentObjectAttribute->attribute( "id" ), $contentObjectAttribute->attribute( "version" ) );
		if ( is_object($gp) )
			$gp->remove();
    }
    
    /*!
     Store the content. Since the content has been stored in function 
     fetchObjectAttributeHTTPInput(), this function is with empty code.
    */
	function getDataArray( $contentObjectattribute )
	{
		$cv = eZContentObjectVersion::fetchVersion( $contentObjectattribute->attribute('version') , $contentObjectattribute->attribute('contentobject_id') );
		$data_map = $cv->attribute('data_map');
    	
    	$ini = eZINI::instance( 'content.ini' );
    	$key_map = $ini->variable('GISSettings','attributes');
    	$keys = array_keys( $key_map );

    	foreach ( $keys as $key )
    	{
	    if ( is_object( $data_map[$key] ) )
	        $return[$key_map[$key]]=$data_map[$key]->content();
    	}
    	return $return;
	}
    /*!
     Returns the content.
    */
    function objectAttributeContent( $contentObjectAttribute )
    {
    	$gp = eZGISPosition::fetch( $contentObjectAttribute->attribute( "id"), $contentObjectAttribute->attribute( "version") );
    	if ( is_object( $gp ) )
    	{
    		#$contentObjectAttribute->Content = $gp;
    	}
    	else
    	{
    		$gp = null;
    	}
    	return $gp;
    }
    /*!
     \return \c true if the datatype finds any content in the attribute \a $contentObjectAttribute.
    */
    function hasObjectAttributeContent( $contentObjectAttribute )
    {
    	if ( ezgistype::objectAttributeContent( $contentObjectAttribute ) )
        	return true;
    	else
        	return false;
    }
    /*!
     Returns the meta data used for storing search indeces.
    */
    function metaData( $contentObjectAttribute )
    {
        return "";
    }

    /*!
     Returns the value as it will be shown if this attribute is used in the object name pattern.
    */
    function title( $contentObjectAttribute, $name = null )
    {
        return "";
    }

    /*!
     \return true if the datatype can be indexed
    */
    function isIndexable()
    {
        return true;
    }

}

eZDataType::register( EZ_DATATYPESTRING_GIS, "ezgistype" );
?>
