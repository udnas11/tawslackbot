<?php
namespace Deployer;

require 'recipe/common.php';

// Configuration

set('repository', 'git@github.com:udnas11/tawslackbot.git');
set('git_tty', false); // [Optional] Allocate tty for git on first deployment
set('shared_files', ['public/config.json']);
set('shared_dirs', ['public/logs']);
set('writable_dirs', []);
set('allow_anonymous_stats', false);

set('ssh_multiplexing', false);

// Hosts

host('tawdcs.org')->user('dev')
    ->stage('production')
	->set('deploy_path', '/home/dev/projects-www/api.tawdcs.org');
//    ->set('deploy_path', '/home/dev/projects-www/tawslackbot');
    
//host('beta.project.com')
//    ->stage('beta')
//    ->set('deploy_path', '/home/dev/projects-www/tawslackbot');    


// Tasks

desc('Restart PHP-FPM service');
task('php-fpm:restart', function () {
    // The user must have rights for restart service
    // /etc/sudoers: username ALL=NOPASSWD:/bin/systemctl restart php-fpm.service
    run('sudo systemctl restart php7.0-fpm.service');
});
after('deploy:symlink', 'php-fpm:restart');

desc('Deploy your project');
task('deploy', [
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:writable',
    'deploy:vendors',
    'deploy:clear_paths',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
    'success'
]);

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');
