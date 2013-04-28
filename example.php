<?
/**
 * ID3Reader - The easiest way of fetching metadata from shoutcast streams 
 * 
 * Example for How to fetch radio stations current song a.k.a nowplaying title.
 * 
 * Example One
 *	 1.FM CHILLOUT LOUNGE - http://205.164.35.5:80
 * 
 * Example Two
 *	181.FM - 90's Alternative - http://205.164.35.5:80/
 */
require_once 'class/Id3Reader.php';


$url = "http://205.164.35.5:80/"; //1.FM CHILLOUT LOUNGE
$data = Id3Reader::getStreamMetadata($url);
echo "Now playing : " . $data['title']."\n";

$url = "http://108.61.73.119:8052/"; //181.FM - 90's Alternative
$data = Id3Reader::getStreamMetadata($url);
echo "Now playing : " . $data['title']."\n";


