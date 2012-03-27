<?php


$Module = $Params["Module"];

if ( !isset ( $Params['RSSFeed'] ) )
    return $Module->setExitStatus( eZError::KERNEL_NOT_AVAILABLE );

$feedName = $Params['RSSFeed'];
$RSSExport = eZGEORSS::fetchByName( $feedName );

// Get and check if RSS Feed exists
if ( $RSSExport == null )
    return $Module->setExitStatus( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );

$config = eZINI::instance( 'site.ini' );
$cacheTime = intval( $config->variable( 'RSSSettings', 'CacheTime' ) );

if($cacheTime <= 0 or true )
{
    $xmlDoc = $RSSExport->fetchGEORSS2_0();
    $rssContent = $xmlDoc->toString();
}
else
{
    $cacheDir = eZSys::cacheDirectory();
    $cacheFile = $cacheDir . '/rss/' . md5( $feedName ) . '.xml';

    // If cache directory does not exist, create it. Get permissions settings from site.ini
    if ( !is_dir( $cacheDir.'/rss' ) )
    {
        $mode = $config->variable( 'FileSettings', 'TemporaryPermissions' );
        if ( !is_dir( $cacheDir ) )
        {
            mkdir( $cacheDir );
            chmod( $cacheDir, octdec( $mode ) );
        }
        mkdir( $cacheDir.'/rss' );
        chmod( $cacheDir.'/rss', octdec( $mode ) );
    }

    if ( !file_exists( $cacheFile ) or ( time() - filemtime( $cacheFile ) > $cacheTime ) )
    {
        $xmlDoc = $RSSExport->attribute( 'rss-xml' );

        $fid = @fopen( $cacheFile, 'w' );

        // If opening file for write access fails, write debug error
        if ( $fid === false )
        {
            eZDebug::writeError( 'Failed to open cache file for RSS export: '.$cacheFile );
        }
        else
        {
            // write, flush, close and change file access mode
            $mode = $config->variable( 'FileSettings', 'TemporaryPermissions' );
            $rssContent = $xmlDoc->toString();
            $length = fwrite( $fid, $rssContent );
            fflush( $fid );
            fclose( $fid );
            chmod( $cacheFile, octdec( $mode ) );

            if ( $length === false )
            {
                eZDebug::writeError( 'Failed to write to cache file for RSS export: '.$cacheFile );
            }
        }
    }
    else
    {
        $rssContent = file_get_contents( $cacheFile );
    }
}

// Set header settings
$httpCharset = eZTextCodec::httpCharset();
header( 'Content-Type: text/xml; charset=' . $httpCharset );
header( 'Content-Length: '.strlen($rssContent) );
header( 'X-Powered-By: eZ publish' );

while ( @ob_end_clean() );

echo $rssContent;

eZExecution::cleanExit();

?>