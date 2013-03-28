About
=====

This script `mailboxUsage.php` should be run on a machine with PHP installed.
It queries the Kerio Connect server about all users and all domains and shows
how much diskspace each mailbox uses.

How to use
==========

The script is quite easy to use:
* Download all the files and place them locally
* Make sure you can connect to your Kerio Connect Adminconsole from this host

Run the script

    $ ./mailboxUsage.php 
    Hostname: kerio.tuxis.nl
    Username: admin
    Password: ********
     - Domain tuxis.nl
       * XYZ1@tuxis.nl (User 1) consumes 1154 MegaBytes.
       * XYZ2@tuxis.nl (User 2) consumes 1008924 Bytes.
       * XYZ3@tuxis.nl (User 3) consumes 5682 Bytes.
     - Domain tuxis.net
       ! No users in this domain.

Hostname, Username and Password will be asked for if you run the script.
Optionally, you can configure the hostname and username in config.php

If you want the output parseable, use the `-s` switch.

    $ ./mailboxUsage.php -s
    Hostname: kerio.tuxis.nl
    Username: admin
    Password: ********
    XYZ1@tuxis.nl,1210056704
    XYZ2@tuxis.nl,1008924
    XYZ3@tuxis.nl,5682

This allows you, for example, to use the data to generate averages and graphs.
