<?php

class xrowGISServerfunctions extends ezjscServerFunctions
{

    public static function userData()
    {
        return true;
    }

    public static function updateMap()
    {
        $ini = eZINI::instance( 'gis.ini' );
        $result['name'] = $ini->variable( 'GISSettings', 'Interface' );
        
        $data = $_POST;
        
        $attributeID = $data['attr_id'];
        $street = $data['ContentObjectAttribute_xrowgis_street_' . $attributeID];
        $zip = $data['ContentObjectAttribute_xrowgis_zip_' . $attributeID];
        $city = $data['ContentObjectAttribute_xrowgis_city_' . $attributeID];
        $state = $data['ContentObjectAttribute_xrowgis_state_' . $attributeID];
        $country = $data['ContentObjectAttribute_xrowgis_country_' . $attributeID];
        $longitude = $data['ContentObjectAttribute_xrowgis_longitude_' . $attributeID];
        $latitute = $data['ContentObjectAttribute_xrowgis_latitude_' . $attributeID];
        
        $geocoder = GeoCoder::getActiveGeoCoder();
        $geocoder->setAddress( $street, $zip, $city, $state, $country );
        
        if ( $geocoder->request() )
        {
            $result['lon'] = $geocoder->longitude;
            $result['lat'] = $geocoder->latitude;
        }
        else
        {
            $result['lon'] = $longitude;
            $result['lat'] = $latitute;
        }
        $result['name'] = $ini->variable('GISSettings', 'Interface');
        return $result;
    }

    public static function addRelation()
    {
        $data = $_POST;
        $tpl = eZTemplate::factory();
        
        $ini = eZINI::instance( 'gis.ini' );
        $result['name'] = $ini->variable( 'GISSettings', 'Interface' );
        
        $attribute = eZContentObjectAttribute::fetch( (int) $data['attributeID'], (int) $data['version'] );
        
        foreach ( eZContentObject::fetchByNodeID( $data['node_id'] )->attribute( 'contentobject_attributes' ) as $key => $relCoa )
        {
            if ( $relCoa->attribute( 'data_type_string' ) === xrowGIStype::DATATYPE_STRING )
            {
                $tpl->setVariable( 'attribute', $attribute );
                $tpl->setVariable( 'GISRelation', true );
                $tpl->setVariable( 'relAttribute', $relCoa );
                
                $result['lon'] = $relCoa->content()->attribute( 'longitude' );
                $result['lat'] = $relCoa->content()->attribute( 'latitude' );
                
                $result['template'] = $tpl->fetch( 'design:xrowgis/xrowgis.tpl' );
                
                return $result;
            }
        }
    }

    public static function releaseRelation()
    {
        $ini = eZINI::instance( 'gis.ini' );
        $result['name'] = $ini->variable( 'GISSettings', 'Interface' );
        $data = $_POST;
        
        $attribute = eZContentObjectAttribute::fetch( (int) $data['attributeID'], (int) $data['version'] );
        $attribute->setAttribute( 'data_int', null );
        
        $tpl = eZTemplate::factory();
        
        if ( $attribute->hasContent() )
        {
            $result['lon'] = $attribute->content()->attribute( 'longitude' );
            $result['lat'] = $attribute->content()->attribute( 'latitude' );
        }
        else
        {
            foreach ( eZContentObject::fetch( $data['relObjectID'] )->attribute( 'contentobject_attributes' ) as $key => $relCoa )
            {
                if ( $relCoa->attribute( 'data_type_string' ) === xrowGIStype::DATATYPE_STRING )
                {
                    $relCoa->content()->setAttribute('contentobject_attribute_id', $attribute->attribute('id'));
                    $relCoa->content()->setAttribute('contentobject_attribute_version', $attribute->attribute('version'));

                    $attribute = $relCoa;

                    $result['lon'] = $relCoa->content()->attribute( 'longitude' );
                    $result['lat'] = $relCoa->content()->attribute( 'latitude' );
                
                }
            }
            
        }
        $tpl->setVariable( 'attribute', $attribute );
        $result['template'] = $tpl->fetch( 'design:xrowgis/xrowgis.tpl' );
        
        return $result;
    }

}