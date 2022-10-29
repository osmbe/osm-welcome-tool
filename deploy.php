<?php

namespace Deployer;

require 'recipe/symfony.php';

set('git_tty', true);

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
