#!/usr/bin/env php
<?php
/**
 * Administration API for Kerio Connect - Sample Application.
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * 
 * Changed by: Mark Schouten <mark@tuxis.nl>
 * March 2013, Ede, NL
 * © Mark Schouten
 * Released as GPL
 */

 */
require_once(dirname(__FILE__) . '/config.php');
require_once(dirname(__FILE__) . '/api/MyApi.php');

$scripted = FALSE;

if (isset($argv[1]) && $argv[1] == "-s")
	$scripted = TRUE;

$api = new MyApi($name, $vendor, $version);

/* Main application */
try {
	/* Login */
	$session = $api->login($hostname, $username, $password);

	/* Get domain list */
	$fields = array('id', 'name');
	$domainList = $api->getDomains($fields);

	foreach ($domainList as $domain) {
		if ($scripted == FALSE) 
			printf(" - Domain %s\n", $domain['name']);

		/* Get user list */
		$query_params = array(
			'query' => array(
				'fields' => array(
					'loginName',
					'fullName',
					'consumedSize'
				),
				'orderBy' => array(array(
					'columnName' => 'consumedSize',
					'direction' => 'Desc'
				)),
				'start' => 0,
				'limit' => 10
			),
			'domainId' => $domain['id']
		);
		$userList = $api->sendRequest('Users.get', $query_params);

		if ($userList['totalItems'] > 0) {
			foreach ($userList['list'] as $user) {
				$username = sprintf('%s@%s', $user['loginName'], $domain['name']);
				$fullname = $user['fullName'];
				$usage = $user['consumedSize']['value'];
				$units = $user['consumedSize']['units'];
				if ($scripted == FALSE) {
					printf("   * %s (%s) consumes %d %s.\n", $username, $fullname, $usage, $units);
				} else {
					$mtp = array("Bytes" => 1, "MegaBytes" => 1024*1024);
					$bytes = $usage * $mtp[$units];
					printf("%s,%d\n", $username, $bytes);
				}
			}
		}
		else {
			if ($scripted == FALSE)
				print "   ! No users in this domain.\n\n";
		}
	}

}
catch (KerioApiException $error) {

	/* Catch possible errors */
	print "There has been some errors\n";
}

/* Logout */
if (isset($session)) {
	$api->logout();
}
