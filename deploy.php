<?php

namespace Deployer;

require 'recipe/symfony.php';
require 'contrib/php-fpm.php'; // See https://deployer.org/docs/7.x/contrib/php-fpm + https://deployer.org/docs/7.x/avoid-php-fpm-reloading

set('git_tty', true);
set('php_fpm_version', '8.1');

// Config

set('repository', 'https://github.com/osmbe/osm-welcome-tool.git');

add('shared_files', [
    'var/data.db',
]);
add('shared_dirs', []);
add('writable_dirs', []);

// Hosts

host('welcome.osm.be')
    ->set('remote_user', 'root')
    ->set('deploy_path', '/var/www/osm-welcome-tool');

// Hooks

after('deploy:failed', 'deploy:unlock');

after('deploy:success', 'php-fpm:reload');
