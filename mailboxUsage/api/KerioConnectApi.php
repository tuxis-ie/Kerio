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

require_once(dirname(__FILE__) . '/KerioApi.php');

/**
 * Administration API for Kerio Connect.
 *
 * This class implements product-specific methods and properties.
 *
 * Example:
 * <code>
 * <?php
 * require_once(dirname(__FILE__) . '/src/KerioConnectApi.php');
 *
 * $api = new KerioConnectApi('Sample application', 'Company Ltd.', '1.0');
 *
 * try {
 *     $api->login('mail.company.tld', 'admin', 'SecretPassword');
 *     $api->sendRequest('...');
 *     $api->logout();
 * } catch (KerioApiException $error) {
 *     print $error->getMessage();
 * }
 * ?>
 * </code>
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @license		http://www.kerio.com/developers/license/sdk-agreement
 * @version		1.3.0.62
 */
class KerioConnectApi extends KerioApi {

	/**
	 * Defines default product-specific JSON-RPC settings.
	 * @var array
	 */
	protected $jsonRpc = array(
		'version'	=> '2.0',
		'port'		=> 4040,
		'api'		=> '/admin/api/jsonrpc/'
	);

	/**
	 * Class constructor.
	 *
	 * @param	string	Application name
	 * @param	string	Application vendor
	 * @param	string	Application version
	 * @return	void
	 * @throws	KerioApiException
	 */
	public function __construct($name, $vendor, $version) {
		parent::__construct($name, $vendor, $version);
	}

	/**
	 * Set component Web Administration.
	 * 
	 * @param	void
	 * @return	void
	 */
	public function setComponentAdmin() {
		$this->setJsonRpc('2.0', 4040, '/admin/api/jsonrpc/');
	}

	/**
	 * Set component Client aka WebMail.
	 * 
	 * @param	void
	 * @return	void
	 */
	public function setComponentClient() {
		$this->setJsonRpc('2.0', 443, '/webmail/api/jsonrpc/');
	}

	/**
	 * Set component WebMail.
	 * 
	 * @param	void
	 * @return	void
	 * @deprecated
	 */
	public function setComponentWebmail() {
		trigger_error("Deprecated function setComponentMyphone(), use setComponentClient() instead", E_USER_NOTICE);
		$this->setComponentClient();
	}

	/**
	 * Get constants defined by product.
	 *
	 * @param	void
	 * @return	array	Array of constants
	 */
	public function getConstants() {
		$response = $this->sendRequest('Server.getNamedConstantList');
		$constantList = array();

		foreach ($response['constants'] as $index) {
			$constantList[$index['name']] = $index['value'];
		}

		return $constantList;
	}
}
