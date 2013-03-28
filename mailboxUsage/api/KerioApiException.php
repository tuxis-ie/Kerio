<?php
/**
 * This file is part of the kerio-api-php.
 *
 * Copyright (c) Kerio Technologies s.r.o.
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code
 * or visit Developer Zone. (http://www.kerio.com/developers)
 *
 * Do not modify this source code.
 * Any changes may be overwritten by a new version.
 */

/**
 * Kerio API Exception Class.
 *
 * This class extends Exception class to provide CSS-based error message formating.
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @license		http://www.kerio.com/developers/license/sdk-agreement
 * @version		1.3.0.62
 */
class KerioApiException extends Exception {

	/**
	 * Positional parameters
	 * @var	array
	 */
	private $positionalParameters = array();

	/**
	 * Error code
	 * @var	integer
	 */
	protected $code;

	/**
	 * Error message
	 * @var	string
	 */
	protected $message = '';

	/**
	 * Request message
	 * @var	string
	 */
	protected $request = '';

	/**
	 * Response message
	 * @var	string
	 */
	protected $response = '';

	/**
	 * Exception contructor.
	 *
	 * @param 	string	Message to display
	 * @param	mixed	Can be integer or string
	 * @param	array	Positional parameters in message
	 * @return	void
	 */
	public function KerioApiException($message, $code = '', $positionalParameters = '', $request = '', $response = '') {
		$this->message = $message;

		if (is_int($code) || is_string($code)) {
			$this->code = $code;
		}
		if (is_array($positionalParameters)) {
			$this->positionalParameters = $positionalParameters;
			$this->setPositionalParameterToString();
		}
		if (is_string($request)) {
			$this->request = $request;
		}
		if (is_string($response)) {
			$this->response = $response;
		}

	}

	/**
	 * Get request data.
	 *
	 * @return	string	JSON request
	 */
	public function getRequest() {
		return $this->request;
	}

	/**
	 * Get response data.
	 *
	 * @return	string	JSON response
	 */
	public function getResponse() {
		return $this->response;
	}

	/**
	 * Replace positional parameter with a string
	 *
	 * @return	void
	 */
	private function setPositionalParameterToString() {
		if (preg_match_all('/%\d/', $this->message, $matches)) {
			/* Found positional parameters */
			$index = 0;
			foreach ($matches[0] as $occurence) {
				$replaceWith = $this->positionalParameters[$index];
				$this->message = str_replace($occurence, $replaceWith, $this->message);
				$index++;
			}
		}
	}
}
