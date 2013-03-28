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
 * Kerio API Interface.
 *
 * This interface describes basic methods for Kerio API.
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @license		http://www.kerio.com/developers/license/sdk-agreement
 * @version		1.3.0.62
 */
interface KerioApiInterface {

	/**
	 * Class constructor.
	 *
	 * @param 	string
	 * @param	string
	 * @param	string
	 */
	public function __construct($name, $vendor, $version);

	/**
	 * Set JSON-RPC settings.
	 *
	 * @param 	string
	 * @param 	integer
	 * @param 	string
	 */
	public function setJsonRpc($version, $port, $api);

	/**
	 * Send request using method and its params.
	 *
	 * @param	string
	 * @param	array
	 */
	public function sendRequest($method, $params = '');

	/**
	 * Login method.
	 *
	 * @param	string
	 * @param	string
	 * @param	string
	 */
	public function login($hostname, $username, $password);

	/**
	 * Logout method.
	 *
	 * @param	void
	 */
	public function logout();
}
