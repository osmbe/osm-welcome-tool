# OSM Welcome Belgium

*Welcoming new OpenStreetMappers in Belgium*

This is a platform to coordinate welcoming new mappers in Belgium. The source code is hackish, ugly and doesn't seem to follow *any* design principle, but it works. Mostly.

You can see live instances for [Belgium](https://welcome.osm.be/) and [Spain](http://laceci.xyz/).

# Prerequisites

* Have a web server with PHP installed and at least 50 MB of free space.

# Installing

* Put the contents of the repo on a webhost. I'm assuming you know how. If you don't, consider giving up now.
* Create the directories `users`, `userpics`, `contributors` and `updatelog` and make them writable for the web server process. On a typical Apache on Linux setup, this can e.g. be done by setting the group to `www-data` and setting the group write execution bit: (`# dirs="users userpics contributors updatelog"; mkdir $dirs; chown www-data $dirs; chmod 770 $dirs`)
* Modify the PHP constant `INCLUDES_PATH` in the file /htdocs/paths.php to point to the folder `includes`.
* Configure the web server to use the folder `htdocs` as document root.
* Reload the web server configuration. The platform should now be up and running, albeit empty.

# Setting up automatic jobs

To automatically load new contributors (which you'll probably want to do) and/or add data export functionality, you can set up periodic jobs, e.g. with cron jobs.

To get new contributors and to update the data about the known ones is done by executing `get_new.php` and `update_existing.php` respectively. They are to be run with PHP as the **user of the web server**. If you do it as root, the content on the web platform will be read-only.

Adding data export facilities to the server can also be achieved with periodic jobs. Just ZIP the folder `contributors` and put it on a place within the `htdocs`.

The file crontab.sample contains an example cron setup to do automatic updates and exports.

# Testing/dev also done on nginx / phpfpm
Basically , I(Glenn) run/tested this on a laravel 5.2 homestead vagrant box, who has recent versions.  Since I prefer nginx, a config is included

* tested on PHP 7.0.3-13+deb.sury.org~trusty+1
* running nginx version 1.9.11
