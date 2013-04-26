<?php

/**
 * Id3Reader
 * The easiest way of fetching metadata from shoutcast streams
 * 
 * @author Cagdas Emek <cagdas.emek@gmail.com>
 * @version 1.0
 * @copyright (c) 2013, Cagdas EMEK
 * 
 */
class Id3Reader {

	public static function getStreamMetadata($url) 
	{

		$URI = parse_url($url);

		if (!isset($URI["port"])) $URI["port"] = 80;

		$sock = fsockopen($URI["host"], $URI["port"]);
		
		if (!isset($URI["path"])) {
			// Hacking Shoutcast
			// Shoutcast suppose to be requested mp3 file by using .mp3 file extention
			$URI["path"] = ";*.mp3"; 
		}

		$path = $URI["path"];
		$put = "GET $path HTTP/1.0 " . "\r\n";
		$put.= "Icy-MetaData:1 " . " \r\n";
		$put.= "\r\n\r\n";

		fputs($sock, $put, strlen($put) + 1);
		$data = "";
		$i = 0;

		while ($header = stream_get_line($sock, 4096, "\r\n")) {
			$data.= $header . "\n";
		}

		$data = strtolower($data);

		//Check redirect
		if (preg_match('/location: ([^\"]*)/i', $data, $matches)) {
			$newUrl = trim($matches[1]); //find redirected url
			fclose($sock); // close current connection
			//echo "HTTP_REDIRECT_FOUND:" . $newUrl . "\n";
			unset($URI);
			unset($sock);
			unset($data);
			unset($matches);
			unset($put);

			return self::getStreamMetadata($newUrl);
		}elseif (preg_match('/http\/1.0\ 403/i', $data, $matches)) {
			fclose($sock); // close current connection
			unset($URI);
			unset($sock);
			unset($data);
			unset($matches);
			unset($put);

			return "HTTP_MAX_LISTENERS_REACHED";
		} elseif (preg_match('/icy 404/i', $data, $matches)) {
			fclose($sock); // close current connection
			unset($URI);
			unset($sock);
			unset($data);
			unset($matches);
			unset($put);

			return "HTTP_404_NOT_FOUND";
		}


		$pointLength = 1;
		if (preg_match('/icy-metaint:([^\"]*)/', $data, $matches)) {
			$pointLength = $matches[1];
		}

		$i = 1;
		do {
			fread($sock, 1); // junk data
		} while (++$i <= $pointLength);

		// Now let's read stream meta data
		$metadata = "";
		$ch = "";
		// Every metadata seperated with semicolon. We know stream title is finishing after second semicolon
		$stopwords = 0;
		while ($stopwords != 2) {
			$ch = fread($sock, 1);
			$metadata.=$ch;
			if ($ch == ';') $stopwords++;
		}

		$nowplaying = "NO_STREAM_TITLE";
		if (preg_match("/StreamTitle=\'(.*)\';/siU", $metadata, $songTitle))
			$nowplaying = $songTitle[1];

		return $nowplaying;
	}

}