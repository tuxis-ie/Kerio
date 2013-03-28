<?php
/**
 *
 * Administration API for Kerio Connect - Sample Class.
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.3.0.62
 */
require_once(dirname(__FILE__) . '/KerioConnectApi.php');

class MyApi extends KerioConnectApi {

	/**
	 * Class constructor.
	 * Register application
	 *
	 * @param   string   Application name
	 * @param   string   Vendor of the application
	 * @param   string   Application versiopn
	 *
	 * @return  void
	 */
	public function __construct($name, $vendor, $version) {
		$this->api = parent::__construct($name, $vendor, $version);

		return $this->api;
	}

	/**
	 * Login and get list of available constants user by Kerio Connect
	 *
	 * @param   string   Hostname of remote server
	 * @param   string   Administrator username
	 * @param   string   Administrator password
	 *
	 * @return  void
	 */
	public function login($hostname, $username, $password) {
		$login = parent::login($hostname, $username, $password);

		$this->getServerConstants();

		return $login;
	}

	/**
	 * Obtain server constants
	 *
	 * @param   void
	 * @return  void
	 */
	protected function getServerConstants() {
		$this->constants = parent::getConstants();
	}

	/**
	 * Return list of available constants
	 *
	 * @param   void
	 * @return	void
	 */
	public function getConstants() {
		return $this->constants;
	}

	/**
	 * Get list of available domains
	 *
	 * @param   array   List of fields to be obtained from engine
	 * @return	array	List of available domains
	 */
	public function getDomains($fields) {
		$method = 'Domains.get';

		$params = array(
			'query' => array(
				'fields' => $fields
			)
		);

		$result = $this->sendRequest($method, $params);

		return $result['list'];
	}

	/**
	 * Get list of users from a domain
	 *
	 * @param   array   List of fields to be obtained from engine
	 * @param	string	Domain Id
	 * @param   array   Additional condition for the request
	 *
	 * @return	array	List of users
	 */
	public function getUsers($fields, $domainId, $conditions = null) {
		$method = 'Users.get';

		$params = array(
			'query' => array(
				'fields' => $fields,
				'orderBy' => array(array(
					'columnName' => 'loginName',
					'direction' => $this->constants['kerio_web_Asc']
				))
			),
			'domainId' => $domainId
		);

		if ($conditions) {
			$params['query']['conditions'] = $conditions;
		}

		$result = $this->sendRequest($method, $params);

		return $result['list'];
	}

	/**
	 * Get login name by user's Id
	 * 
	 * @param	string	User Id
	 * @param	string	Domain Id
	 * 
	 * @return	string	Login name
	 */
	public function getUserById($userId, $domainId) {
		$fields = array('id', 'loginName');
		$userList = $this->getUsers($fields, $domainId);
		
		foreach ($userList as $user) {
			if ($user['id'] == $userId) return $user['loginName']; 
		}
		
		return FALSE;
	}

	/**
	 * Get list of groups from a domain
	 *
	 * @param   array   List of fields to be obtained from engine
	 * @param	string	Domain Id
	 * @param   array   Additional condition for the request
	 *
	 * @return	array	List of groups
	 */
	public function getGroups($fields, $domainId, $conditions = null) {
		$method = 'Groups.get';

		$params = array(
			'query' => array(
				'fields' => $fields,
				'orderBy' => array(array(
					'columnName' => 'name',
					'direction' => $this->constants['kerio_web_Asc']
				))
			),
			'domainId' => $domainId
		);

		if ($conditions) {
			$params['query']['conditions'] = $conditions;
		}

		$result = $this->sendRequest($method, $params);

		return $result['list'];
	}

	/**
	 * Create new group
	 *
	 * @param	array   User defined params
	 * @return	array	Result of create action
	 */
	public function createGroup($params) {
		$method = 'Groups.create';

		$result = $this->sendRequest($method, $params);

		return $result['result'];
	}

	/**
	 * Add members to group of given ID
	 *
	 * @param	string  Group ID
	 * @param	array	List of user IDs to be added
	 *
	 * @return  void
	 */
	public function addMembersToGroup($groupId, $userList) {
		$method = 'Groups.addMemberList';

		$params = array(
			'userList' => $userList,
			'groupId'  => $groupId
		);

		$this->sendRequest($method, $params);
	}

	/**
	 * Get list of mailing lists from a domain
	 *
	 * @param   array   List of fields to be obtained from engine
	 * @param	string	Domain Id
	 * @param   array   Additional condition for the request
	 *
	 * @return	array	List of mailing lists
	 */
	public function getMailingLists($fields, $domainId, $conditions = null) {
		$method = 'MailingLists.get';

		$params = array(
			'query' => array(
				'fields' => $fields,
				'orderBy' => array(array(
					'columnName' => 'name',
					'direction' => $this->constants['kerio_web_Asc']
				))
			),
			'domainId' => $domainId
		);

		if ($conditions) {
			$params['query']['conditions'] = $conditions;
		}

		$result = $this->sendRequest($method, $params);

		return $result['list'];
	}

	/**
	 * Get list of mailing lists from a domain
	 *
	 * @param   array   List of fields to be obtained from engine
	 * @param	string  Mailing list Id
	 *
	 * @return	array	List of mailing lists
	 */
	public function getMlUserList($fields, $mlId) {
		$method = 'MailingLists.getMlUserList';

		$params = array(
			'query' => array(
				'fields' => $fields
			),
			'mlId' => $mlId
		);

		$result = $this->sendRequest($method, $params);

		return $result['list'];
	}

	/**
	 * Get list of resources from a domain
	 *
	 * @param   array   List of fields to be obtained from engine
	 * @param	string	Domain Id
	 * @param   array   Additional condition for the request
	 *
	 * @return	array	List of mailing lists
	 */
	public function getResources($fields, $domainId, $conditions = null) {
		$method = 'Resources.get';

		$params = array(
			'query' => array(
				'fields' => $fields,
				'orderBy' => array(array(
					'columnName' => 'name',
					'direction' => $this->constants['kerio_web_Asc']
				))
			),
			'domainId' => $domainId
		);

		if ($conditions) {
			$params['query']['conditions'] = $conditions;
		}

		$result = $this->sendRequest($method, $params);

		return $result['list'];
	}

	/**
	 * Get list of aliases from a domain
	 * 
	 * @param	array	List of fields to be obtained from engine
	 * @param	string	Domain Id
	 * @param	array	Additional condition for the request
	 * 
	 * @return	array	List of aliases
	 */
	public function getAliases($fields, $domainId, $conditions = null) {
		$method = 'Aliases.get';

		$params = array(
			'query' => array(
					'fields' => $fields,
					'orderBy' => array(array(
							'columnName' => 'name',
							'direction' => $this->constants['kerio_web_Asc']
					)),
					'combining' => 'Or'
			),
			'domainId' => $domainId
		);

		if ($conditions) {
			$params['query']['conditions'] = $conditions;
		}
		
		$result = $this->sendRequest($method, $params);
		
		return $result['list'];
	}

	/**
	 * Get list of all services
	 *
	 * @param   void
	 * @return	array	List of services
	 */
	function getServices() {
		$method = 'Services.get';
		$params = array();

		$result = $this->sendRequest($method, $params);

		return $result['services'];
	}

	/**
	 * Get list of all services
	 *
	 * @param   void
	 * @return	array	List of services
	 */
	function getServerStatistics() {
		$method = 'Statistics.get';
		$params = array();

		$result = $this->sendRequest($method, $params);

		return $result['statistics'];
	}

	/**
	 * Create alias.
	 *
	 * @param	string	Domain ID
	 * @param	string	Alias
	 * @param	string	Email
	 * @param	string	Description, optional
	 * @return	array	Result
	 */
	function createAlias($domain, $alias, $email, $description = '') {
		$params = array(
			'aliases' => array(array(
				'name' => $alias,
				'domainId' => $domain,
				'deliverTo' => $email,
				'description' => $description,
				'deliverToSelect' => 'TypeEmailAddress'
			))
		);
		$result = $this->sendRequest('Aliases.create', $params);
		return $result;
	}

	/**
	 * Create user.
	 *
	 * @param	string	Domain ID
	 * @param	string	Username
	 * @param	string	Password
	 * @return	array	Result
	 */
	function createUser($domain, $username, $password) {
		$params = array(
			'users' => array(array(
				'loginName' => $username,
				'password' => $password,
				'domainId' => $domain,
				'isEnabled' => TRUE
			))
		);
		$result = $this->sendRequest('Users.create', $params);
		return $result;
	}

	/**
	 * Create domain.
	 *
	 * @param	string	Domain name
	 * @return	array	Result
	 */
	function createDomain($domain) {
		$params = array(
			'domains' => array(array(
				'name' => $domain
			))
		);
		$result = $this->sendRequest('Domains.create', $params);
		return $result;
	}
	
	/**
	 * Get list of IP addresses from a file.
	 *
	 * Local function used in example spam_blacklist
	 *
	 * @param	string	Filename
	 * @return	array	List of IP addresses
	 * @throws	KerioApiException
	 */
	public function getBlacklistRecords($file) {
		$blacklist = array();
		if(file_exists($file) && is_readable($file)) {
			$data = file_get_contents($file);
			foreach (preg_split("/\n/", $data) as $record) {
				if (empty($record)) continue;
				array_push($blacklist, $record);
			}
		}
		else {
			throw new KerioApiException(sprintf('Cannot open file %s', $file));
		}
		return $blacklist;
	}

	/**
	 * Get list of IP addesses from a group
	 *
	 * Local function used in example spam_blacklist
	 *
	 * @param	string	Group name
	 * @return	array	List of IP addresses
	 */
	public function getIpGroupList($name) {
		$params = array(
			"query" => array(
				"conditions" => array(array(
					"fieldName" => "name",
					"comparator" => "Like",
					"value" => $name
				)),
				"orderBy" => array(array(
					"columnName" => "item",
					"direction" => "Asc"
				))
			)
		);
		$result = $this->sendRequest('IpAddressGroups.get', $params);
		return $result['list'];
	}

	/**
	 * Add a IP address to a IP Group
	 *
	 * Local function used in example spam_blacklist
	 *
	 * @param	string	Group name
	 * @param	string	IP address
	 * @param	string	Description, optional
	 * @return	array	Result
	 */
	public function addHostToIpGroup($group, $ip, $description = '') {
		if(empty($description)) {
			$description = sprintf('Automatically added on %s', date(DATE_RFC822));
		}
		$params = array(
			"groups" => array(array(
				"groupId" => "",
				"groupName" => $group,
				"host" => $ip,
				"type" => "Host",
				"description" => $description,
				"enabled" => TRUE
			))
		);
		$result = $this->sendRequest('IpAddressGroups.create', $params);
		return $result;
	}

	/**
	 * Remove a IP address from a IP Group
	 *
	 * Local function used in example spam_blacklist
	 *
	 * @param	string	Group name
	 * @param	string	IP address
	 * @return	array	Result
	 */
	public function removeHostFromIpGroup($group, $ip) {
		$list = $this->getIpGroupList(NULL);
		foreach ($list as $record) {
			if(($record['groupName'] != $group) || ($record['host'] != $ip)) continue;
			$hostId = $record['id'];
		}
		$params = array("groupIds" => array($hostId));
		$result = $this->sendRequest('IpAddressGroups.remove', $params);
		return $result;
	}

	/**
	 * Random password generator
	 * 
	 * Local function used in example createUser.
	 * 
	 * @param	integer	Password lenght, default 10
	 * @return	string	Random password
	 */
	function genRandomPassword($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyz';
		$string = '';

		for ($p = 0; $p < $length; $p++) {
			$string .= $characters[mt_rand(0, (strlen($characters))-1)];
		}
		
		return $string;
	}
}
