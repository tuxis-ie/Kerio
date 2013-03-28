<?php
/**
 * Kerio APIs Client Library for PHP - Config Example.
 * @copyright Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 *
 * Changed by: Mark Schouten <mark@tuxis.nl>
 * March 2013, Ede, NL
 * © Mark Schouten
 * Released as GPL
 */
/* Application details */
$name = 'Kerioindecloud.nl usage monitor';
$vendor = 'Tuxis Internet Engineering';
$version = '1.0';


function getPassword($stars = false) {
    // Get current style

    $oldStyle = shell_exec('stty -g');

    if ($stars === false) {
        shell_exec('stty -echo');
        $password = rtrim(fgets(STDIN), "\n");
    } else {
        shell_exec('stty -icanon -echo min 1 time 0');

        $password = '';
        while (true) {
            $char = fgetc(STDIN);

            if ($char === "\n") {
                break;
            } else if (ord($char) === 127) {
                if (strlen($password) > 0) {
                    fwrite(STDOUT, "\x08 \x08");
                    $password = substr($password, 0, -1);
                }
            } else {
                fwrite(STDOUT, "*");
                $password .= $char;
            }
        }
    }

    // Reset old style

    shell_exec('stty ' . $oldStyle);

    // Return the password

    return $password;
}

try {
	/* Optionally configure your hostname and username here */
	$hostname = '';
	$username = '';

	/* Check if config file is empty */
	if (empty($hostname))
		$hostname = readline("Hostname: ");
	if (empty($username))
		$username = readline("Username: ");

	fwrite(STDOUT, "Password: ");
	$password = getPassword(true);
	fwrite(STDOUT, "\n");
}
catch (Exception $e) {
	die($e->getMessage());
}



