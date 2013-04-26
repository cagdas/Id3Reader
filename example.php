<?
/**
 * ID3Reader - The easiest way of fetching metadata from shoutcast streams 
 * 
 * Example One
 * Fetch 1.FM CHILLOUT LOUNGE radio stations current song a.k.a nowplaying title.
 */
require_once 'class/Id3Reader.php';
$url = "http://205.164.35.5:80/";
$nowplaying = Id3Reader::getStreamMetadata($url);
echo "Now playing : " . $nowplaying;
