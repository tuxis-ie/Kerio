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

require_once(dirname(__FILE__) . '/KerioApiInterface.php');
require_once(dirname(__FILE__) . '/KerioApiSocket.php');
require_once(dirname(__FILE__) . '/KerioApiException.php');

/**
 * Kerio API Class.
 *
 * This is main class.
 *
 * Example:
 * <code>
 * <?php
 * require_once(dirname(__FILE__) . '/src/KerioApi.php');
 *
 * class MyApi extents KerioApi {
 *
 *     public function __contruct($name, $vendor, $version) {
 *         parent::__construct($name, $vendor, $version);
 *     }
 *
 *     public function getFoo() {
 *         return $this->sendRequest('...');
 *     }
 * }
 * ?>
 * </code>
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @license		http://www.kerio.com/developers/license/sdk-agreement
 * @version		1.3.0.62
 */
class KerioApi implements KerioApiInterface {

	/**
	 * End-Line format
	 */
	const CRLF = "\r\n";

	/**
	 * HTTP server status
	 */
	const HTTP_SERVER_OK = 200;

	/**
	 * Library name
	 * @var	string
	 */
	public $name = 'Kerio APIs Client Library for PHP';

	/**
	 * Library version
	 * @var string
	 */
	public $version = '1.3.0.62';

	/**
	 * Debug mode
	 * @var	boolean
	 */
	private $debug = FALSE;

	/**
	 * Unique id used in request
	 * @var	integer
	 */
	private $requestId = 0;

	/**
	 * Hostname
	 * @var string
	 */
	protected $hostname = '';

	/**
	 * X-Token
	 * @var	string
	 */
	protected $token = '';

	/**
	 * Cookies
	 * @var	string
	 */
	protected $cookies = '';

	/**
	 * Application details
	 * @var	array
	 */
	protected $application = array('name' => '', 'vendor' => '', 'version' => '');

	/**
	 * JSON-RPC settings
	 * @var	array
	 */
	protected $jsonRpc = array('version' => '', 'port' => '', 'api' => '');

	/**
	 * HTTP headers
	 * @var	array
	 */
	protected $headers = array();

	/**
	 * Socket handler
	 * @var	resource
	 */
	private $socketHandler = '';

	/**
	 * Socket timeout
	 * @var	integer
	 */
	private $timeout = '';

	/**
	 * Class contructor.
	 *
	 * @param	string	Application name
	 * @param	string	Application vendor
	 * @param	string	Application version
	 * @return	void
	 * @throws	KerioApiException
	 */
	public function __construct($name, $vendor, $version) {
		$this->checkPhpEnvironment();
		$this->setApplication($name, $vendor, $version);
		$this->setJsonRpc($this->jsonRpc['version'], $this->jsonRpc['port'], $this->jsonRpc['api']);
	}

	/**
	 * Check PHP environment.
	 *
	 *  @param	void
	 *  @return	void
	 */
	private function checkPhpEnvironment() {
		if (version_compare(PHP_VERSION, '5.1.0', '<')) {
			die(sprintf('<h1>kerio-api-php error</h1>Minimum PHP version required is 5.1.0. Your installation is %s.<br>Please, upgrade your PHP installation.', phpversion()));
		}
		if (FALSE === function_exists('openssl_open')) {
			die('<h1>kerio-api-php error</h1>Your PHP installation does not have OpenSSL enabled.<br>To configure OpenSSL support in PHP, please edit your php.ini config file and enable row with php_openssl module, e.g. extension=php_openssl.dll<br>For more information see <a href="http://www.php.net/manual/en/openssl.installation.php">http://www.php.net/manual/en/openssl.installation.php</a>.');
		}
		if (FALSE === function_exists('json_decode')) {
			die('<h1>kerio-api-php error</h1>Your PHP installation does not have JSON enabled.<br>To configure JSON support in PHP, please edit your php.ini config file and enable row with php_json module, e.g. extension=php_json.dll<br>For more information see <a href="http://www.php.net/manual/en/json.installation.php">http://www.php.net/manual/en/json.installation.php</a>.');
		}
	}

	/**
	 * Set application to identify on server.
	 *
	 * @param	string	Application name
	 * @param	string	Vendor
	 * @param	string	Version
	 * @return	void
	 * @throws	KerioApiException
	 */
	private function setApplication($name, $vendor, $version) {
		if (empty($name) && empty($vendor) && empty($version)) {
			throw new KerioApiException('Application not defined.');
		}
		else {
			$this->debug(sprintf("Registering application '%s' by '%s' version '%s'<br>", $name, $vendor, $version));
			$this->application = array(
				'name'		=> $name,
				'vendor'	=> $vendor,
				'version'	=> $version
			);
		}
	}

	/**
	 * Get application detail.
	 *
	 * @param	void
	 * @return	array	Application details
	 */
	public final function getApplication() {
		return $this->application;
	}

	/**
	 * Set JSON-RPC settings.
	 *
	 * @see class/KerioApiInterface::setJsonRpc()
	 * @param	string	JSON-RPC version
	 * @param	integer	JSON-RPC port
	 * @param	string	JSON-RPC URI
	 * @return	void
	 * @throws	KerioApiException
	 */
	public final function setJsonRpc($version, $port, $api) {
		if (empty($version) && empty($port) && empty($api)) {
			throw new KerioApiException('JSON-RPC not defined.');
		}
		else {
			$this->debug(sprintf("Registering JSON-RPC %s on %s using port %d", $version, $api, $port));
			$this->jsonRpc = array(
				'version'	=> $version,
				'port'		=> $port,
				'api'		=> $api
			);
		}
	}

	/**
	 * Get JSON-RPC settings.
	 *
	 * @param	void
	 * @return	array	JSON-RPC settings
	 */
	public final function getJsonRpc() {
		return $this->jsonRpc;
	}

	/**
	 * Enable or disable of displaying debug messages.
	 *
	 * @param	boolean
	 * @return	void
	 */
	public final function setDebug($boolean) {
		$this->debug = (bool) $boolean;
	}

	/**
	 * Get debug settings.
	 *
	 * @param	void
	 * @return	boolean
	 */
	public final function getDebug() {
		return $this->debug;
	}

	/**
	 * Display a message if debug is TRUE.
	 *
	 * @param 	string	Message
	 * @param	string	CSS class
	 * @return	string	Message in &lt;div&gt; tags
	 */
	public function debug($message, $css = 'debug') {
		if ($this->debug) {
			printf('<div class="%s">%s</div>%s', $css, $message, "\n");
		}
	}

	/**
	 * Get product API version.
	 *
	 * @param	void
	 * @return	integer	API version
	 */
	public function getApiVersion() {
		$method = 'Version.getApiVersion';
		$response = $this->sendRequest($method);
		return $response['apiVersion'];		
	}

	/**
	 * Login method.
	 *
	 * @see class/KerioApiInterface::login()
	 * @param	string	Hostname
	 * @param	string	Username
	 * @param	string	Password
	 * @return	array	Result
	 * @throws	KerioApiException
	 */
	public function login($hostname, $username, $password) {
		$this->clean();

		if (empty($hostname)) {
			throw new KerioApiException('Cannot login. Hostname not set.');
		}
		elseif (empty($username)) {
			throw new KerioApiException('Cannot login. Username not set.');
		}
		elseif (empty($this->application)) {
			throw new KerioApiException('Cannot login. Application not defined.');
		}

		$this->setHostname($hostname);

		$method = 'Session.login';
		$params = array(
			'userName'		=> $username,
			'password'		=> $password,
			'application'	=> $this->application
		);

		$response = $this->sendRequest($method, $params);
		return $response;
	}

	/**
	 * Logout method.
	 *
	 * @see class/KerioApiInterface::logout()
	 * @param	void
	 * @return	array	Result
	 */
	public function logout() {
		$method = 'Session.logout';
		$response = $this->sendRequest($method);
		$this->clean();
		return $response;
	}

	/**
	 * Clean data.
	 *
	 * @param	void
	 * @return	void
	 */
	public function clean() {
		if ($this->token) {
			$this->debug('Removing X-Token.');
			$this->token = '';
		}
		if ($this->cookies) {
			$this->debug('Removing Cookies.');
			$this->cookies = '';
		}
		$this->hostname = '';
		$this->socketHandler = '';
	}

	/**
	 * Get full HTTP request.
	 *
	 * @param	string	HTTP method [POST,GET,PUT]
	 * @param	string	HTTP body
	 * @return	string	HTTP request
	 * @throws	KerioApiException
	 */
	protected function getHttpRequest($method, $body) {
		/* Clean data */
		$this->headers = array();
		$bodyRequest = '';
		$fullRequest = '';

		/* Prepare headers and get request body*/
		switch ($method) {
			case 'POST': // common requests
				$bodyRequest = $this->getHttpPostRequest($body);
				break;
			case 'GET': // download
				$bodyRequest = $this->getHttpGetRequest($body);
				break;
			case 'PUT': // upload
				$bodyRequest = $this->getHttpPutRequest($body);
				break;
			default:
				throw new KerioApiException('Cannot send request, unknown method.');
		}

		/* Add port to headers if non-default is used */
		$port = ($this->jsonRpc['port'] == 443)
			? ''
			: sprintf(':%d', $this->jsonRpc['port']);

		/* Set common headers */
		$this->headers['Host:']				= sprintf('%s%s', $this->hostname, $port);
		$this->headers['Content-Length:']	= strlen($bodyRequest);
		$this->headers['Connection:']		= 'close';

		/* Set X-Token and Cookies */
		if ($this->token) {
			$this->headers['Cookie:']	= $this->cookies;
			$this->headers['X-Token:']	= $this->token;
		}

		/* Build request */
		foreach ($this->headers as $item => $value){
			$fullRequest .= $item . ' ' . $value . self::CRLF;
		}
		$fullRequest .= self::CRLF;
		$fullRequest .= $bodyRequest;

		/* Return */
		return $fullRequest;
	}

	/**
	 * Get headers for POST request.
	 *
	 * @param	string	Request body
	 * @return	string	Request body
	 */
	protected function getHttpPostRequest($data) {
		$this->headers['POST']			= sprintf('%s HTTP/1.1', $this->jsonRpc['api']);
		$this->headers['Accept:']		= 'application/json-rpc';
		$this->headers['Content-Type:']	= 'application/json-rpc; charset=UTF-8';
		$this->headers['User-Agent:']	= sprintf('%s/%s', $this->name, $this->version);

		return str_replace(array("\r", "\r\n", "\n", "\t"), '', $data) . self::CRLF;
	}

	/**
	 * Get headers for GET request.
	 *
	 * @param	string	Request body
	 * @return	string	Request body
	 */
	protected function getHttpGetRequest($data) {
		$this->headers['GET']				= sprintf('%s HTTP/1.1', $data);
		$this->headers['Accept:']			= '*/*';

		return $data . self::CRLF;
	}

	/**
	 * Get headers for PUT request.
	 *
	 * @param	string	Request body
	 * @return	string	Request body
	 */
	protected function getHttpPutRequest($data) {
		$boundary = sprintf('---------------------%s', substr(md5(rand(0,32000)), 0, 10));

		$this->headers['POST']			= sprintf('%s%s HTTP/1.1', $this->jsonRpc['api'], 'upload/');
		$this->headers['Accept:']		= '*/*';
		$this->headers['Content-Type:']	= sprintf('multipart/form-data; boundary=%s', $boundary);

		$body = '--' . $boundary . self::CRLF;
		$body .= 'Content-Disposition: form-data; name="unknown"; filename="newFile.bin"' . self::CRLF;
		$body .= self::CRLF;
		$body .= $data . self::CRLF;
		$body .= '--' . $boundary . '--' . self::CRLF;

		return $body;
	}

	/**
	 * Send request using method and its params.
	 *
	 * @see class/KerioApiInterface::sendRequest()
	 * @param	string	Interface.method
	 * @param	array	Params of 'Interface.method'.
	 * @return	array	Returns same type as param is, e.g. JSON if method is also JSON
	 */
	public function sendRequest($method, $params = '') {
		$request = array(
			'jsonrpc'	=>	$this->jsonRpc['version'],
			'id'		=>	$this->getRequestId(),
			'token'		=>	$this->token,
			'method'	=>	$method,
			'params'	=>	$params
		);

		if (empty($this->token)) {
			unset($request['token']);
		}
		if (empty($params)) {
			unset($request['params']);
		}

		$json_request = json_encode($request);

		/* Send data to server */
		$json_response = $this->send('POST', $json_request);

		/* Return */
		$response = json_decode($json_response, TRUE);
		return $response['result'];
	}

	/**
	 * Send JSON request.
	 *
	 * @param	string	JSON request
	 * @return	string	JSON response
	 */
	public function sendRequestJson($json) {
		return $this->send('POST', $json);
	}

	/**
	 * Send data to server.
	 *
	 * @param	string	Request method [POST,GET,PUT]
	 * @param	string	Request body
	 * @return	string	Server response
	 * @throws	KerioApiException
	 */
	protected function send($method, $data) {
		if (empty($this->hostname)) {
			throw new KerioApiException('Cannot send data before login.');
		}

		/* Get full HTTP request */
		$request = $this->getHttpRequest($method, $data);
		$this->debug(sprintf("&rarr; Raw request:\n<pre>%s</pre>", $request));

		/* Open socket */
		$this->socketHandler = new KerioApiSocket($this->hostname, $this->jsonRpc['port'], $this->timeout);

		/* Send data */
		$rawResponse = $this->socketHandler->send($request);
		$this->debug(sprintf("&larr; Raw response:\n<pre>%s</pre>", $rawResponse));

		/* Parse response */
		$headers	= $this->socketHandler->getHeaders();
		$body		= $this->socketHandler->getBody();
		$this->checkHttpResponse(self::HTTP_SERVER_OK, $headers);

		/* Decode JSON response */
		$response = stripslashes($body);
		$response = json_decode($body, TRUE);
		if (($method == 'POST') && empty($response)) {
			throw new KerioApiException('Invalid JSON data, cannot parse response.');
		}

		/* Set CSRF token */
		if (empty($this->token)) {
			if (isset($response['result']['token'])) {
				$this->setToken($response['result']['token']);
			}
		}

		/* Handle errors */
		if (isset($response['error'])) {
			if (FALSE === empty($response['error'])) {
				$message	= $response['error']['message'];
				$code		= $response['error']['code'];
				$params = (isset($response['error']['data']))
					? $response['error']['data']['messageParameters']['positionalParameters']
					: '';
				throw new KerioApiException($message, $code, $params, $data, $body);
			}
		}
		elseif (isset($response['result']['errors'])) {
			if (FALSE === empty($response['result']['errors'])) {
				$message	= $response['result']['errors'][0]['message'];
				$code		= $response['result']['errors'][0]['code'];
				$params		= $response['result']['errors'][0]['messageParameters']['positionalParameters'];
				throw new KerioApiException($message, $code, $params, $data, $body);
			}
		}

		/* Handle Cookies */
		if (empty($this->cookies)) {
			$this->setCookieFromHeaders($headers);
		}

		/* Return */
		return $body;
	}

	/**
	 * Get a file from server.
	 *
	 * @param	string	File url
	 * @param	string	Save directory
	 * @param	string	Save as, optional. Default is file.bin
	 * @return	boolean	True on success
	 * @throws	KerioApiException
	 */
	public function downloadFile($url, $directory, $filename = '') {
		$saveAs = (empty($filename)) ? 'file.bin' : $filename;
		$saveAs = sprintf('%s/%s', $directory, $filename);

		$data = $this->send('GET', $url);

		$this->debug(sprintf('Saving file %s', $saveAs));
		if (FALSE === @file_put_contents($saveAs, $data)) {
			throw new KerioApiException(sprintf('Unable to save file %s', $saveAs));
		}
		return TRUE;
	}

	/**
	 * Put a file to server.
	 *
	 * @param	string	Absolute path to file
	 * @param	integer	Reference ID where uploaded file belongs to, optional
	 * @return	array	Result
	 * @throws	KerioApiException
	 */
	public function uploadFile($filename, $id = null) {
		$data = @file_get_contents($filename);

		if ($data) {
			$this->debug(sprintf('Uploading file %s', $filename));
			$json_response = $this->send('PUT', $data);
		}
		else {
			throw new KerioApiException(sprintf('Unable to open file %s', $filename));
		}

		$response = json_decode($json_response, TRUE);
		return $response['result'];
	}

	/**
	 * Check HTTP/1.1 reponse header.
	 *
	 * @param	integer	Requested HTTP code
	 * @param	string	HTTP headers
	 * @return	boolean	True if match
	 * @throws	KerioApiException
	 */
	protected function checkHttpResponse($code, $headers) {
		preg_match('#HTTP/\d+\.\d+ (\d+) (.+)#', $headers, $result);
		switch ($result[1]) {
			case $code:
				return TRUE;
			default:
				$remote = sprintf('https://%s:%d%s', $this->hostname, $this->jsonRpc['port'], $this->jsonRpc['api']);
				throw new KerioApiException(sprintf('%d - %s on remote server %s', $result[1], $result[2], $remote));
		}
	}

	/**
	 * Set hostname.
	 *
	 * @param	string	Hostname
	 * @return	void
	 */
	public function setHostname($hostname) {
		$hostname = preg_split('/:/', $hostname);
		$this->hostname = $hostname[0];
		if (isset($hostname[1])) {
			$this->setJsonRpc($this->jsonRpc['version'], $hostname[1], $this->jsonRpc['api']);
		}
	}

	/**
	 * Get request ID.
	 *
	 * @param	void
	 * @return	integer
	 */
	private function getRequestId() {
		$this->requestId++;
		return $this->requestId;
	}

	/**
	 * Set security Cross-Site Request Forgery X-Token.
	 *
	 * @param	string	X-Token value
	 * @return	void
	 */
	protected function setToken($token) {
		$this->debug(sprintf('Setting X-Token %s.', $token));
		$this->token = $token;
	}

	/**
	 * Get security Cross-Site Request Forgery X-Token.
	 *
	 * @param	void
	 * @return	string	X-Token value
	 */
	public function getToken() {
		return $this->token;
	}

	/**
	 * Set Cookies.
	 *
	 * @param	string	Cookies
	 * @return	void
	 */
	protected function setCookie($cookies) {
		$this->cookies = $cookies;
	}

	/**
	 * Get Cookies.
	 *
	 * @param	void
	 * @return	string	Cookies
	 */
	public function getCookie() {
		return $this->cookies;
	}

	/**
	 * Set Cookie from response.
	 *
	 * @param	string	HTTP headers
	 * @return	void
	 */
	private function setCookieFromHeaders($headers) {
		foreach (explode("\n", $headers) as $line) {
			if (preg_match_all('/Set-Cookie:\s(\w*)=(\w*)/', $line, $result)) {
				foreach ($result[1] as $index => $cookie) {
					$this->debug(sprintf('Setting %s=%s.', $cookie, $result[2][$index]));
					$this->setCookie(sprintf('%s %s=%s;', $this->getCookie(), $cookie, $result[2][$index]));
				}
			}
		}
	}

	/**
	 * Set connection timeout.
	 *
	 * @param	integer	Timeout in seconds
	 * @return	void
	 */
	public function setTimeout($timeout) {
		$this->timeout = (integer) $timeout;
	}
}
