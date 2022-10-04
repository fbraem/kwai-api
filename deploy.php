<?php
namespace Deployer;

require 'recipe/common.php';

// Project name
set('application', 'kwai_api');
if (!has('public_path')) {
    set('public_path', '/www');
}

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
        'autoload.php',
        'composer.json',
        'composer.lock',
        'facebook.php'
    ];
    $releasePath = get('release_path');

    foreach($appFiles as $file) {
        upload($file, "{$releasePath}");
    }
});

task('generate:autoload', function() {
    // Generate a simple autoload file, which make it possible to use a PSR-4
    // autoloader for classes that are located in a private folder.
    run("mkdir -p {{public_path}}");
    $realpath = run("readlink -f {{deploy_path}}");
    run(
        "echo \"<?php require('${realpath}/current/vendor/autoload.php');\" > {{public_path}}/autoload.php"
    );
});

task('copy:api', function() {
   run('cp -R {{release_path}}/api {{public_path}}');
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
    'generate:autoload',
    'copy:api',
    'deploy:unlock',
    'cleanup',
    'success'
]);

// [Optional] If deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');
