<?php

namespace li3_mixpanel\core;

/**
 * Client for sending data to Mixpanel
 *
 */
class Mixpanel extends \lithium\core\StaticObject {

	/**
	 * Hostname of remote service
	 *
	 * @var string
	 */
	public static $host = 'api.mixpanel.com';

	/**
	 * Timeout in seconds before connection attempt is closed
	 *
	 * @var integer
	 */
	public static $timeout = 1;

	/**
	 * Token needed to authenticate requests
	 *
	 * @var string
	 */
	public static $token;

	/**
	 * Tracks event in Mixpanel
	 *
	 * You can give more properties to analyze and drill down more information
	 * later on. You would be surprised what is possible on the awesome Mixpanel
	 * Webfrontend. Make sure to provide a single field wich enables Mixpanel
	 * to drill down various different events to one user, i.e. user_id
	 *
	 * @param string $event name of event to trigger
	 * @param string $properties an array with data that will be sent along with
	 *        this event in order to analyze these fields and additional data
	 * @return boolean true on succeess, false otherwise
	 */
	public static function track($event, array $properties = array()) {
		$params = compact('event', 'properties');
		return static::async_call($params);
	}

	/**
	 * This method handles the submission to the remote endpoint
	 *
	 * It does that in an asynchronous fashion to prevent time-consuming
	 * interaction. It does that with a fire-and-forget approach: It simply
	 * opens a socket connection to the remote-point and as soon as that is
	 * open it pushes through all data to be transmitted and returns right
	 * after that. It may happen, that this leads to unexpected behavior or
	 * failure of data submission. double-check your token and everything else
	 * that can fail to make sure, everything works as expected.
	 *
	 * @param array $data all data to be submitted, must be in the form
	 *        of an array, containing exactly two keys: `event` and `properties`
	 *        which are of type string (event) and array (properties). You can
	 *        submit whatever properties you like. If no token is given, it will
	 *        be automatically appended from `static::$host` which can be set in
	 *        advance like this: `Mixpanel::$token = 'foo';`
	 * @param array $options an array with additional options
	 * @return boolean true on succeess, false otherwise
	 *         actually, it just checks, if bytes sent is greater than zero. It
	 *         does _NOT_ check in any way if data is recieved sucessfully in
	 *         the endpoint and/or if given data is accepted by remote.
	 */
	public static function async_call(array $data = array(), array $options = array()) {
		if (!isset($data['properties']['token'])){
			$data['properties']['token'] = static::$token;
		}
		$url = '/track/?data=' . base64_encode(json_encode($data));
		$fp = fsockopen(static::$host, 80, $errno, $errstr, static::$timeout);
		if ($errno != 0) {
			// TODO: make something useful with error
			return false;
		}
		$out = array();
		$out[] = sprintf('GET %s HTTP/1.1', $url);
		$out[] = sprintf('Host: %s', static::$host);
		$out[] = 'Accept: */*';
		$out[] = 'Connection: close';
		$out[] = '';
		$bytes = fwrite($fp, implode("\r\n", $out));
		// $out  = "GET " . $url . " HTTP/1.1\r\n";
		// $out .= "Host: " . static::$host . "\r\n";
		// $out .= "Accept: */*\r\n";
		// $out .= "Connection: close\r\n\r\n";
		// $bytes = fwrite($fp, $out);
		fclose($fp);
		return ($bytes > 0);
	}
}

?>