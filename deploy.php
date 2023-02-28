<?php

namespace Deployer;

require 'recipe/symfony.php';
require 'contrib/php-fpm.php'; // See https://deployer.org/docs/7.x/contrib/php-fpm + https://deployer.org/docs/7.x/avoid-php-fpm-reloading

set('git_tty', true);
set('php_fpm_version', '8.1');

// Config

set('repository', 'https://github.com/osmbe/osm-welcome-tool.git');
set('branch', '2.x');

add('shared_files', ['var/data.db']);
add('shared_dirs', []);
add('writable_dirs', []);

// Hosts

host('welcome.osm.be')
    ->set('remote_user', 'root')
    ->set('deploy_path', '/var/www/osm-welcome-tool');

// Tasks

task('npm:build', function () {
    runLocally('npm install');
    runLocally('npm run build');
});
task('npm:rsync', function () {
    runLocally('rsync -e ssh -az public/build/ {{remote_user}}@{{hostname}}:{{release_path}}/public/build/');
});
task('npm', ['npm:build', 'npm:rsync']);

// Hooks

after('deploy:update_code', 'npm');
after('deploy:failed', 'deploy:unlock');
after('deploy:success', 'php-fpm:reload');

set('bin/composer', function () {
    return '/usr/bin/php{{php_fpm_version}} /usr/local/bin/composer';
});
