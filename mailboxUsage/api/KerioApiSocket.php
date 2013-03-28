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

require_once(dirname(__FILE__) . '/KerioApiSocketInterface.php');

/**
 * Kerio API Socket Class.
 *
 * This class implements basic methods used in HTTP communication.
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @license		http://www.kerio.com/developers/license/sdk-agreement
 * @version		1.3.0.62
 */
class KerioApiSocket implements KerioApiSocketInterface {

	/**
	 * Socket buffer size
	 */
	const BUFFER_SIZE = 5120;

	/**
	 * Socket handler
	 * @var resource
	 */
	private $socketHandler = '';

	/**
	 * Communication timeout
	 * @var	integer
	 */
	private $timeout = 3;

	/**
	 * Server hostname
	 * @var	string
	 */
	private $hostname = '';

	/**
	 * Server port
	 * @var	integer
	 */
	private $port = '';

	/**
	 * SSL encryption
	 * @var string
	 */
	private $cipher = 'ssl://';

	/**
	 * Headers
	 * @var	string
	 */
	private $headers = '';

	/**
	 * Body
	 * @var	string
	 */
	private $body = '';

	/**
	 * Socker error message
	 * @var	string
	 */
	private $errorMessage = '';

	/**
	 * Socker error code
	 * @var	integer
	 */
	private $errorCode = 0;

	/**
	 * Class constructor.
	 *
	 * @param	string	Hostname
	 * @param	integer	Port
	 * @param	integer	Timeout, optional
	 * @return	boolean	True on success
	 */
	public function KerioApiSocket($hostname, $port, $timeout = '') {
		/* Set host */
		$this->hostname = $hostname;
		$this->port = $port;

		/* Set timeout */
		if (is_int($timeout)) {
			$this->timeout = $timeout;
		}

		/* Open socket to server */
		$this->open();
		return ($this->socketHandler) ? TRUE : FALSE;
	}

	/**
	 * Class desctructor.
	 *
	 * @param	void
	 * @return	void
	 */
	public function __destruct() {
		$this->close();
	}

	/**
	 * Open socket to server.
	 *
	 * @param	void
	 * @return	void
	 */
	protected function open() {
		$this->socketHandler = @fsockopen($this->cipher . $this->hostname, $this->port, $errno, $errstr, $this->timeout);
		$this->errorCode = $errno;
		$this->errorMessage = $errstr;
	}

	/**
	 * Close socket to server.
	 *
	 * @param	void
	 * @return	void
	 */
	protected function close() {
		@fclose($this->socketHandler);
		unset($this->socketHandler);
	}

	/**
	 * Send data to socket.
	 *
	 * @see class/KerioApiSocketInterface::send()
	 * @param	string	Data to socket
	 * @return	string	Data from socket
	 * @throws	KerioApiException
	 */
	public function send($data) {
		if ($this->checkConnection()) {
			@fwrite($this->socketHandler, $data);
			return $this->read();
		}
		else {
			throw new KerioApiException(sprintf("Cannot connect to %s using port %d.", $this->hostname, $this->port));
		}
	}

	/**
	 * Read data from socket.
	 *
	 * @param	void
	 * @return	string	HTTP data from socket
	 * @throws	KerioApiExceptions
	 */
	protected function read() {
		if ($this->socketHandler) {
			$response = '';
			while (FALSE === feof($this->socketHandler)) {
				$response .= fgets($this->socketHandler, self::BUFFER_SIZE);
			}
			
			list($this->headers, $this->body) = explode("\r\n\r\n", $response);
			
			if (FALSE !== strpos(strtolower($this->headers), 'transfer-encoding: chunked')) {
				$this->unchunkHttp();
			}
			
			return $response;
		}
		else {
			throw new KerioApiException('Cannot read data from server, connection timeout.');
		}
	}

	/**
	 * Unchunk HTTP/1.1 body.
	 * 
	 * @param	void
	 * @return	void
	 */
	private function unchunkHttp() {
		$body = $this->body;
		for ($new = ''; !empty($body); $str = trim($body)) {
			$pos  = strpos($body, "\r\n");
			$len  = hexdec(substr($body, 0, $pos));
			$new .= substr($body, $pos + 2, $len);
			$body = substr($body, $pos + 2 + $len);
		}
		$this->body = $new;
	}

	/**
	 * Set connection encryption to ssl://
	 * 
	 * @param	boolen	True if ssl:// is used
	 * @return	void
	 */
	public function setEncryption($boolean) {
		$this->cipher = ($boolean) ? 'ssl://' : '';
	}

	/**
	 * Check connection to server.
	 *
	 * @param	void
	 * @return	boolean	True on success
	 */
	public final function checkConnection() {
		if ($this->checkHost()) {
			$socket = @fsockopen($this->hostname, $this->port, $errno, $errstr, $this->timeout);
			$this->errorCode = $errno;
			$this->errorMessage = $errstr;
			return ($socket) ? TRUE : FALSE;
		}
		else {
			return FALSE;
		}
	}

	/**
	 * Check if DNS host is valid.
	 *
	 * @param 	void
	 * @return	boolean	True on success
	 */
	public final function checkHost() {
		return gethostbyname($this->hostname) ? TRUE : FALSE;
	}

	/**
	 * Get headers.
	 * 
	 * @param	void
	 * @return	string
	 */
	public final function getHeaders() {
		return $this->headers;
	}

	/**
	 * Get body.
	 *
	 * @param	void
	 * @return	string
	 */
	 public final function getBody() {
		return $this->body;
	}

	/**
	 * Get socker error message.
	 *
	 * @param	void
	 * @return	string
	 */
	public final function getErrorMessage() {
		return $this->errorMessage;
	}

	/**
	 * Get socket error code.
	 *
	 * @param	void
	 * @return	integer
	 */
	public final function getErrorCode() {
		return $this->errorCode;
	}
}

