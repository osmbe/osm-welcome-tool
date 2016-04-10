# OSM Welcome Belgium

*Welcoming new OpenStreetMappers in Belgium*

This is a platform to coordinate welcoming new mappers in Belgium. The source code is hackish, ugly and doesn't seem to follow *any* design principle, but it works. Mostly.

# Prerequisites

* Have a web server with PHP installed and at least 50 MB of free space.

# Installing

* Put the contents of the repo on a webhost. I'm assuming you know how. If you don't, consider giving up now.
* Create the directories `users`, `userpics`, `contributors` and `updatelog` and make them writable for the web server process. On a typical Apache on Linux setup, this can e.g. be done by setting the group to `www-data` and setting the group write execution bit: (`# dirs="users userpics contributors updatelog"; mkdir $dirs; chown www-data $dirs; chmod 770 $dirs`)
* Modify the PHP constant `INCLUDES_PATH` in the file /htdocs/paths.php to point to the folder `includes`.
* Configure the web server to use the folder `htdocs` as document root.
* Reload the web server configuration. The platform should now be up and running, albeit empty.

# Loading new contributors

Add a periodic job (e.g. with cron) that executes the files `get_new.php` and `update_existing.php` as the **user of the web server**. If you do it as root, the content on the web platform will be read-only.

An example crontab:

    # m h  dom mon dow   command
    # Take a backup of the contributors at 4:50
    50 4 * * * sudo -u www-data tar -zcf "/var/backups/osmwelcome/`date +\%Y-\%m-\%d`.tgz" /var/www/osmwelcome/contributors/ && sudo -u www-data rm -rf "/var/backups/osmwelcome/`date --date '15 days ago' +\%Y-\%m-\%d`.tgz"
    # Update existing contributors at 5:00
    0 5 * * * sudo -u www-data php /var/www/osmwelcome/update_existing.php >"/var/www/osmwelcome/updatelog/`date +\%Y-\%m-\%d_\%H-\%M-\%S`_update.log" 2>&1
    # Get new contributors at 6:00
    0 6 * * * sudo -u www-data php /var/www/osmwelcome/get_new.php >"/var/www/osmwelcome/updatelog/`date +\%Y-\%m-\%d_\%H-\%M-\%S`_new.log" 2>&1
