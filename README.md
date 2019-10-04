# OSM Welcome Belgium

*Welcoming new OpenStreetMappers in Belgium*

This is a platform to coordinate welcoming new mappers in Belgium. The source code is hackish, extremely ugly and doesn't seem to follow *any* design principle, but it works. Mostly.

You can see a live instance for [Belgium](https://welcome.osm.be/).

## Requirements

* PHP 7.0.30 or later
* PHP-OAuth 2.0.2 or later

## Installing

* Put the contents of the repo on a webhost. I'm assuming you know how. If you don't, consider giving up now.
* Create the directories `users`, `userpics`, `contributors` and `updatelog` and make them writable for the web server process. On a typical Apache on Linux setup, this can e.g. be done by setting the group to `www-data` and setting the group write execution bit:
    ```
    dirs="users userpics contributors updatelog"; mkdir $dirs; chown $USER:www-data $dirs; chmod 0770 $dirs
    ```
* Check the PHP constant `INCLUDES_PATH` in the file `/htdocs/defines.php` to point to the folder `includes`.
* Update the OAuth `key` and `secret` in the file `includes/oauth/oauth.php` (see [wiki](https://wiki.openstreetmap.org/wiki/OAuth#Registering_your_application_as_OAuth_consumer)).
* Configure the web server to use the folder `htdocs` as document root.
* Reload the web server configuration. The platform should now be up and running, albeit empty.

## Setting up automatic jobs

To automatically load new contributors (which you'll probably want to do) and/or add data export functionality, you can set up periodic jobs, e.g. with cron jobs.

To get new contributors and to update the data about the known ones is done by executing `get_new.php` and `update_existing.php` respectively. They are to be run with PHP as the **user of the web server**. If you do it as root, the content on the web platform will be read-only.

Adding data export facilities to the server can also be achieved with periodic jobs. Just ZIP the folder `contributors` and put it on a place within the `htdocs`. Update the server configuration to enable directory indices for the folder they get put in, if you desire so. (Example config for that is commented out in the example files.)

The file crontab.sample contains an example cron setup to do automatic updates and exports.

## Testing/dev also done on nginx / phpfpm

[Glenn](https://github.com/gplv2) ran this on a Laravel 5.2 Homestead Vagrant box, with recent software versions. Since he prefers Nginx, a config is included.

* tested on PHP 7.0.3-13+deb.sury.org~trusty+1
* running Nginx version 1.9.11
