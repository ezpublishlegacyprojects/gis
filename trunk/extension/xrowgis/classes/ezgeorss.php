<?php

class eZGEORSS extends eZRSSExport
{
    const EZ_GEORSS_NS_GEO = "http://www.w3.org/2003/01/geo/wgs84_pos#";
    const EZ_GEORSS_NS_YMAPS = "http://api.maps.yahoo.com/Maps/V1/AnnotatedMaps.xsd";
    const EZ_GEORSS_NS_GEORSS = "http://www.georss.org/georss";
    public $version = '2.0';
    public $url;
    public $description;
    public $language;

    function eZGEORSS( $row )
    {
        $this->eZPersistentObject( $row );
    }

    static function fetchByName( $access_url, $asObject = true )
    {
        return eZPersistentObject::fetchObject( eZGEORSS::definition(), null, array( 
            'access_url' => $access_url , 
            'active' => 1 , 
            'status' => 1 
        ), $asObject );
    }

    static function definition()
    {
        $array = parent::definition();
        $array['class_name'] = 'eZGEORSS';
        return $array;
    
    }

    function fetchGEORSS2_0( $id = null )
    {
        $locale = eZLocale::instance();

        // Get URL Translation settings.
        $config = & eZINI::instance();
        if ( $config->variable( 'URLTranslator', 'Translation' ) == 'enabled' )
        {
            $useURLAlias = true;
        }
        else
        {
            $useURLAlias = false;
        }

        $baseItemURL = $this->attribute( 'url' ) . '/'; //.$this->attribute( 'site_access' ).'/';
        

        $doc = new eZDOMDocument( );
        $doc->setName( 'eZ publish RSS Export' );
        $root = $doc->createElementNode( 'rss', array( 
            'version' => '2.0' 
        ) );
        $root->appendAttribute( eZDOMDocument::createAttributeNode( 'geo', self::EZ_GEORSS_NS_GEO, 'xmlns' ) );
        $root->appendAttribute( eZDOMDocument::createAttributeNode( 'ymaps', self::EZ_GEORSS_NS_YMAPS, 'xmlns' ) );
        $root->appendAttribute( eZDOMDocument::createAttributeNode( 'georss', self::EZ_GEORSS_NS_GEORSS, 'xmlns' ) );
        
        $doc->setRoot( $root );
        
        $channel = $doc->createElementNode( 'channel' );
        $root->appendChild( $channel );
        
        $channelTitle = $doc->createElementTextNode( 'title', $this->attribute( 'title' ) );
        $channel->appendChild( $channelTitle );
        
        $channelLink = $doc->createElementTextNode( 'link', $this->attribute( 'url' ) );
        $channel->appendChild( $channelLink );
        if ( $this->attribute( 'description' ) )
        {
            $channelDescription = $doc->createElementTextNode( 'description', $this->attribute( 'description' ) );
            $channel->appendChild( $channelDescription );
        }
        /*
        $channel->appendChild( $doc->createElementTextNode( 'language', $locale->httpLocaleCode() ) );
        $channel->appendChild( $channelLanguage );
*/
        $groups = new eZDOMNode( );
        $groups->setName( 'Groups' );
        $groups->setPrefix( 'ymaps' );
        $classes = eZContentClass::fetchList();
        $gisini = & eZINI::instance( "xrowgis.ini" );
        
        foreach ( $classes as $class )
        {
            $icon = $this->getIcon( "class_icon", $class->attribute( 'identifier' ) );
            
            $group = new eZDOMNode( );
            $group->setName( 'group' );
            $group->appendChild( $doc->createElementTextNode( 'Title', $class->attribute( 'name' ) ) );
            $group->appendChild( $doc->createElementTextNode( 'Id', $class->attribute( 'identifier' ) ) );
            
            $classid = $class->attribute( 'identifier' );
            $class_var = $gisini->variable( "Icon", $classid );
            
            if ( $icon )
            {
                if ( empty( $class_var["path"] ) )
                {
                    $icon = $gisini->variable( "GISSettings", "PublicURL" ) . $icon;
                    $group->appendChild( $doc->createElementCDATANode( 'BaseIcon', $icon ) );
                }
                else
                {
                    $group->appendChild( $doc->createElementCDATANode( 'BaseIcon', $gisini->variable( "GISSettings", "PublicURL" ) . $class_var[path], array( 
                        'height' => $class_var[height] , 
                        'width' => $class_var[width] 
                    ) ) );
                }
            }
            $groups->appendChild( $group );
            unset( $group );
            unset( $icon );
        }
        $channel->appendChild( $groups );
        
        $imageURL = $this->fetchImageURL();
        if ( $imageURL !== false )
        {
            $image = $doc->createElementNode( 'image' );
            
            $imageUrlNode = $doc->createElementTextNode( 'url', $imageURL );
            $image->appendChild( $imageUrlNode );
            
            $imageTitle = $doc->createElementTextNode( 'title', $this->attribute( 'title' ) );
            $image->appendChild( $imageTitle );
            
            $imageLink = $doc->createElementTextNode( 'link', $this->attribute( 'url' ) );
            $image->appendChild( $imageLink );
            
            $channel->appendChild( $image );
        }
        $cond = array( 
            'rssexport_id' => $this->ID , 
            'status' => $this->Status 
        );
        $rssSources = eZRSSExportItem::fetchFilteredList( $cond );
        
        # added by Soeren
        $limitation = array();
        $limitation = $this->getObjectListFilter();
        $limitation["number_of_objects"] = 5000;
        
        # modified by Sören for more items.
        # $nodeArray = eZRSSExportItem::fetchNodeList( $rssSources, $this->getObjectListFilter() );
        # Unless there are not more than 1000 objects (depends on system architecture)
        

        $nodeArray = eZRSSExportItem::fetchNodeList( $rssSources, $limitation );
        
        $db = & eZDB::instance();
        $db->begin();
        $gisarray = $db->arrayQuery( "SELECT f.contentobject_id  FROM ezxgis_position e, ezcontentobject_attribute f WHERE f.id = e.contentobject_attribute_id and f.version=e.contentobject_attribute_version" );
        $db->commit();
        
        $nodeGisArray = array();
        foreach ( $gisarray as $gisitem )
        {
            $nodeGisArray[] = eZContentObject::fetch( $gisitem["contentobject_id"] );
        }
        
        $coid = array();
        foreach ( $nodeGisArray as $nodeitem )
        {
            $coid[] = $nodeitem->ID;
        
        }
        $newarray = array();
        foreach ( $nodeArray as $node )
        {
            if ( in_array( $node->ContentObjectID, $coid ) )
                $newarray[] = $node;
        
        }
        $nodeArray = $newarray;
        
        if ( is_array( $nodeArray ) && count( $nodeArray ) )
        {
            $attributeMappings = eZRSSExportItem::getAttributeMappings( $rssSources );
        }
        
        foreach ( $nodeArray as $node )
        {
            
            $object = $node->attribute( 'object' );
            $dataMap = $object->dataMap();
            
            if ( $useURLAlias === true )
            {
                $nodeURL = $baseItemURL . $node->urlAlias();
            }
            else
            {
                $nodeURL = $baseItemURL . 'content/view/full/' . $object->attribute( 'id' );
            }
            
            // keep track if there's any match
            $doesMatch = false;
            // start mapping the class attribute to the respective RSS field
            foreach ( $attributeMappings as $attributeMapping )
            {
                // search for correct mapping by path
                if ( $attributeMapping[0]->attribute( 'class_id' ) == $object->attribute( 'contentclass_id' ) and in_array( $attributeMapping[0]->attribute( 'source_node_id' ), $node->attribute( 'path_array' ) ) )
                {
                    // found it
                    $doesMatch = true;
                    // now fetch the attributes
                    $title = $dataMap[$attributeMapping[0]->attribute( 'title' )];
                    $description = $dataMap[$attributeMapping[0]->attribute( 'description' )];
                    break;
                }
            }
            
            if ( ! $doesMatch )
            {
                // no match
                eZDebug::writeWarning( __CLASS__ . '::' . __FUNCTION__ . ': Cannot find matching RSS source node for content object in ' . __FILE__ . ', Line ' . __LINE__ );
                $retValue = null;
                return $retValue;
            }
            
            // title RSS element with respective class attribute content
            unset( $itemTitle );
            $itemTitle = $doc->createElementNode( 'title' );
            $titleContent = $title->attribute( 'content' );
            if ( get_class( $titleContent ) == 'ezxmltext' )
            {
                $outputHandler = $titleContent->attribute( 'output' );
                unset( $itemTitleText );
                $itemTitleText = $doc->createTextNode( $outputHandler->attribute( 'output_text' ) );
                $itemTitle->appendChild( $itemTitleText );
            }
            else
            {
                unset( $itemTitleText );
                $itemTitleText = $doc->createTextNode( $titleContent );
                $itemTitle->appendChild( $itemTitleText );
            }
            
            // title RSS element with respective class attribute content
            unset( $itemDescription );
            $itemDescription = $doc->createElementNode( 'description' );
            $descriptionContent = $description->attribute( 'content' );
            if ( get_class( $descriptionContent ) == 'ezxmltext' )
            {
                $outputHandler = $descriptionContent->attribute( 'output' );
                unset( $itemDescriptionText );
                $itemDescriptionText = $doc->createTextNode( $outputHandler->attribute( 'output_text' ) );
                $itemDescription->appendChild( $itemDescriptionText );
            }
            else
            {
                unset( $itemDescriptionText );
                $itemDescriptionText = $doc->createTextNode( $descriptionContent );
                $itemDescription->appendChild( $itemDescriptionText );
            }
            
            unset( $itemLink );
            $itemLink = $doc->createElementNode( 'link' );
            
            unset( $itemLinkUrl );
            $itemLinkUrl = $doc->createTextNode( $nodeURL );
            $itemLink->appendChild( $itemLinkUrl );
            
            unset( $item );
            $item = $doc->createElementNode( 'item' );
            
            unset( $itemPubDate );
            $itemPubDate = $doc->createElementTextNode( 'pubDate', gmdate( 'D, d M Y H:i:s', $object->attribute( 'published' ) ) . 'GMT' );
            
            $item->appendChild( $itemPubDate );
            $item->appendChild( $itemTitle );
            $item->appendChild( $itemLink );
            $item->appendChild( $itemDescription );
            
            foreach ( $dataMap as $key => $attribute )
            {
                if ( $attribute->attribute( 'data_type_string' ) == EZ_DATATYPESTRING_GIS )
                {
                    $data = & $attribute->content();
                    if ( is_object( $data ) )
                    {
                        $data->attribute( 'longitude' );
                        
                        $latitude = $doc->createElementTextNode( 'lat', $data->attribute( 'latitude' ) );
                        $latitude->setPrefix( 'geo' );
                        $item->appendChild( $latitude );
                        unset( $latitude );
                        
                        $longitude = $doc->createElementTextNode( 'long', $data->attribute( 'longitude' ) );
                        $longitude->setPrefix( 'geo' );
                        $item->appendChild( $longitude );
                        unset( $longitude );
                        
                        $georss = $doc->createElementTextNode( 'point', $data->attribute( 'latitude' ) . ' ' . $data->attribute( 'longitude' ) );
                        $georss->setPrefix( 'georss' );
                        $item->appendChild( $georss );
                        unset( $georss );
                        
                        break;
                    }
                }
            }
            
            $GroupId = $doc->createElementTextNode( 'GroupId', $object->attribute( 'class_identifier' ) );
            $GroupId->setPrefix( 'ymaps' );
            $item->appendChild( $GroupId );
            unset( $GroupId );
            
            $channel->appendChild( $item );
        }
        
        return $doc;
    }

    function iconDirectMapping( &$ini, &$themeINI, $iniGroup, $mapName, $matchItem )
    {
        $map = array();
        
        // Load mapping from theme
        if ( $themeINI->hasVariable( $iniGroup, $mapName ) )
        {
            $map = array_merge( $map, $themeINI->variable( $iniGroup, $mapName ) );
        }
        // Load override mappings if they exist
        if ( $ini->hasVariable( $iniGroup, $mapName ) )
        {
            $map = array_merge( $map, $ini->variable( $iniGroup, $mapName ) );
        }
        
        $icon = false;
        if ( isset( $map[$matchItem] ) )
        {
            $icon = $map[$matchItem];
        }
        if ( $icon === false )
        {
            if ( $themeINI->hasVariable( $iniGroup, 'Default' ) )
                $icon = $themeINI->variable( $iniGroup, 'Default' );
            if ( $ini->hasVariable( $iniGroup, 'Default' ) )
                $icon = $ini->variable( $iniGroup, 'Default' );
        }
        return $icon;
    }

    function getIcon( $operatorName, $operatorValue )
    {
        
        $ini = & eZINI::instance( 'icon.ini' );
        $repository = $ini->variable( 'IconSettings', 'Repository' );
        $theme = $ini->variable( 'IconSettings', 'Theme' );
        $groups = array( 
            'mimetype_icon' => 'MimeIcons' , 
            'class_icon' => 'ClassIcons' , 
            'classgroup_icon' => 'ClassGroupIcons' , 
            'action_icon' => 'ActionIcons' , 
            'icon' => 'Icons' 
        );
        $configGroup = $groups[$operatorName];
        
        // Check if the specific icon type has a theme setting
        if ( $ini->hasVariable( $configGroup, 'Theme' ) )
        {
            $theme = $ini->variable( $configGroup, 'Theme' );
        }
        
        // Load icon settings from the theme
        $themeINI = & eZINI::instance( 'icon.ini', $repository . '/' . $theme );
        
        if ( isset( $operatorParameters[0] ) )
        {
            $sizeName = $tpl->elementValue( $operatorParameters[0], $rootNamespace, $currentNamespace );
        }
        else
        {
            $sizeName = $ini->variable( 'IconSettings', 'Size' );
            // Check if the specific icon type has a size setting
            if ( $ini->hasVariable( $configGroup, 'Size' ) )
            {
                $theme = $ini->variable( $configGroup, 'Size' );
            }
        }
        
        $sizes = $themeINI->variable( 'IconSettings', 'Sizes' );
        if ( $ini->hasVariable( 'IconSettings', 'Sizes' ) )
        {
            $sizes = array_merge( $sizes, $ini->variable( 'IconSettings', 'Sizes' ) );
        }
        
        if ( isset( $sizes[$sizeName] ) )
        {
            $size = $sizes[$sizeName];
        }
        else
        {
            $size = $sizes[0];
        }
        
        $pathDivider = strpos( $size, ';' );
        if ( $pathDivider !== false )
        {
            $sizePath = substr( $size, $pathDivider + 1 );
            $size = substr( $size, 0, $pathDivider );
        }
        else
        {
            $sizePath = $size;
        }
        
        $width = false;
        $height = false;
        $xDivider = strpos( $size, 'x' );
        if ( $xDivider !== false )
        {
            $width = (int) substr( $size, 0, $xDivider );
            $height = (int) substr( $size, $xDivider + 1 );
        }
        
        if ( isset( $operatorParameters[1] ) )
        {
            $altText = $tpl->elementValue( $operatorParameters[1], $rootNamespace, $currentNamespace );
        }
        else
        {
            $altText = $operatorValue;
        }
        
        if ( $operatorName == 'mimetype_icon' )
        {
            $icon = $this->iconGroupMapping( $ini, $themeINI, 'MimeIcons', 'MimeMap', strtolower( $operatorValue ) );
        }
        else 
            if ( $operatorName == 'class_icon' )
            {
                $icon = $this->iconDirectMapping( $ini, $themeINI, 'ClassIcons', 'ClassMap', strtolower( $operatorValue ) );
            }
            else 
                if ( $operatorName == 'classgroup_icon' )
                {
                    $icon = $this->iconDirectMapping( $ini, $themeINI, 'ClassGroupIcons', 'ClassGroupMap', strtolower( $operatorValue ) );
                }
                else 
                    if ( $operatorName == 'action_icon' )
                    {
                        $icon = $this->iconDirectMapping( $ini, $themeINI, 'ActionIcons', 'ActionMap', strtolower( $operatorValue ) );
                    }
                    else 
                        if ( $operatorName == 'icon' )
                        {
                            $icon = $this->iconDirectMapping( $ini, $themeINI, 'Icons', 'IconMap', strtolower( $operatorValue ) );
                        }
        
        $iconPath = '/' . $repository . '/' . $theme;
        $iconPath .= '/' . $sizePath;
        $iconPath .= '/' . $icon;
        
        $wwwDirPrefix = "";
        if ( strlen( eZSys::wwwDir() ) > 0 )
            $wwwDirPrefix = eZSys::wwwDir();
        $sizeText = '';
        if ( $width !== false and $height !== false )
        {
            $sizeText = ' width="' . $width . '" height="' . $height . '"';
        }
        
        // The class will be detected by ezpngfix.js, which will force alpha blending in IE.
        if ( ( ! isset( $sizeName ) || $sizeName == 'normal' || $sizeName == 'original' ) && strstr( strtolower( $iconPath ), ".png" ) )
        {
            $class = 'class="transparent-png-icon" ';
        }
        else
        {
            $class = '';
        }
        
        return $wwwDirPrefix . $iconPath;
    }

    // returns doc
    function buildFromObjectArray( $nodeArray, $root )
    {
        
        $locale = eZLocale::instance();
        
        // Get URL Translation settings.
        $config = & eZINI::instance();
        if ( $config->variable( 'URLTranslator', 'Translation' ) == 'enabled' )
        {
            $useURLAlias = true;
        }
        else
        {
            $useURLAlias = false;
        }
        
        $baseItemURL = $this->attribute( 'url' ) . '/'; //.$this->attribute( 'site_access' ).'/';
        

        $doc = new eZDOMDocument( );
        $doc->setName( 'eZ publish GEORSS Export' );
        $root = $doc->createElementNode( 'rss', array( 
            'version' => $this->version 
        ) );
        $doc->setRoot( $root );
        
        $channel = $doc->createElementNode( 'channel' );
        $root->appendChild( $channel );
        
        $channelTitle = $doc->createElementTextNode( 'title', $this->attribute( 'title' ) );
        $channel->appendChild( $channelTitle );
        
        $channelLink = $doc->createElementTextNode( 'link', $this->attribute( 'url' ) );
        $channel->appendChild( $channelLink );
        
        $channelDescription = $doc->createElementTextNode( 'description', $this->attribute( 'description' ) );
        $channel->appendChild( $channelDescription );
        
        $channel->appendChild( $doc->createElementTextNode( 'language', $locale->httpLocaleCode() ) );
        $channel->appendChild( $channelLanguage );
        
        $channel->appendChild( $groups );
        
        $attributeMappings = eZRSSExportItem::getAttributeMappings( $rssSources );
        foreach ( $nodeArray as $node )
        {
            $object = $node->attribute( 'object' );
            $dataMap = $object->dataMap();
            if ( $useURLAlias === true )
            {
                $nodeURL = $baseItemURL . $node->urlAlias();
            }
            else
            {
                $nodeURL = $baseItemURL . 'content/view/full/' . $object->attribute( 'id' );
            }
            
            // keep track if there's any match
            $doesMatch = false;
            // start mapping the class attribute to the respective RSS field
            foreach ( $attributeMappings as $attributeMapping )
            {
                // search for correct mapping by path
                if ( $attributeMapping[0]->attribute( 'class_id' ) == $object->attribute( 'contentclass_id' ) and in_array( $attributeMapping[0]->attribute( 'source_node_id' ), $node->attribute( 'path_array' ) ) )
                {
                    // found it
                    $doesMatch = true;
                    // now fetch the attributes
                    $title = $dataMap[$attributeMapping[0]->attribute( 'title' )];
                    $description = $dataMap[$attributeMapping[0]->attribute( 'description' )];
                    break;
                }
            }
            
            if ( ! $doesMatch )
            {
                // no match
                eZDebug::writeWarning( __CLASS__ . '::' . __FUNCTION__ . ': Cannot find matching RSS source node for content object in ' . __FILE__ . ', Line ' . __LINE__ );
                $retValue = null;
                return $retValue;
            }
            
            // title RSS element with respective class attribute content
            unset( $itemTitle );
            $itemTitle = $doc->createElementNode( 'title' );
            $titleContent = $title->attribute( 'content' );
            if ( get_class( $titleContent ) == 'ezxmltext' )
            {
                $outputHandler = $titleContent->attribute( 'output' );
                unset( $itemTitleText );
                $itemTitleText = $doc->createTextNode( $outputHandler->attribute( 'output_text' ) );
                $itemTitle->appendChild( $itemTitleText );
            }
            else
            {
                unset( $itemTitleText );
                $itemTitleText = $doc->createTextNode( $titleContent );
                $itemTitle->appendChild( $itemTitleText );
            }
            
            // title RSS element with respective class attribute content
            unset( $itemDescription );
            $itemDescription = $doc->createElementNode( 'description' );
            $descriptionContent = $description->attribute( 'content' );
            if ( get_class( $descriptionContent ) == 'ezxmltext' )
            {
                $outputHandler = $descriptionContent->attribute( 'output' );
                unset( $itemDescriptionText );
                $itemDescriptionText = $doc->createTextNode( $outputHandler->attribute( 'output_text' ) );
                $itemDescription->appendChild( $itemDescriptionText );
            }
            else
            {
                unset( $itemDescriptionText );
                $itemDescriptionText = $doc->createTextNode( $descriptionContent );
                $itemDescription->appendChild( $itemDescriptionText );
            }
            
            unset( $itemLink );
            $itemLink = $doc->createElementNode( 'link' );
            
            unset( $itemLinkUrl );
            $itemLinkUrl = $doc->createTextNode( $nodeURL );
            $itemLink->appendChild( $itemLinkUrl );
            
            unset( $item );
            $item = $doc->createElementNode( 'item' );
            
            unset( $itemPubDate );
            $itemPubDate = $doc->createElementTextNode( 'pubDate', gmdate( 'D, d M Y H:i:s', $object->attribute( 'published' ) ) . 'GMT' );
            
            $item->appendChild( $itemPubDate );
            $item->appendChild( $itemTitle );
            $item->appendChild( $itemLink );
            $item->appendChild( $itemDescription );
            
            $channel->appendChild( $item );
        }
        
        return $doc;
    }
}
?>