<?php
namespace Deployer;

require 'recipe/common.php';

// Project name
set('application', 'kwai_api');

set('ssh_multiplexing', false);

// Shared files/dirs between deploys
set('shared_files', []);
set('shared_dirs', ['files', 'config']);

// Writable dirs by web server
set('writable_dirs', []);

// Hosts
inventory('hosts.yml');

// Tasks
task('deploy:staging', function() {
    $appFiles = [
        'api',
        'src',
        'composer.json',
        'composer.lock',
        'facebook.php'
    ];
    $deployPath = get('release_path');

    foreach($appFiles as $file) {
        upload($file, "{$deployPath}");
    }
});

desc('Deploy your project');
task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:staging',
    'deploy:shared',
    'deploy:writable',
    'deploy:vendors',
    'deploy:clear_paths',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
    'success'
]);

// [Optional] If deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');
