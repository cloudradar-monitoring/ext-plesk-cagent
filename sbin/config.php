#!/usr/bin/php
<?php
$hub_url = $argv[1];
$hub_user = $argv[2];
$hub_password = $argv[3];

$file = '/etc/cagent/cagent.conf';

if(!is_file($file)){
    fwrite(STDERR, sprintf('Configuration file %s does not exist',$file));
    exit(1);
}
if(!is_readable($file)){
    fwrite(STDERR, sprintf('Configuration file %s is not readable',$file));
    exit(1);
}
if(!is_writable($file)){
    fwrite(STDERR, sprintf('Configuration file %s is not writable',$file));
    exit(1);
}
$config = file_get_contents($file);
$config = preg_replace("/^hub_user = .*?$/im","hub_user = \"$hub_user\"",$config);
$config = preg_replace("/^hub_url = .*?$/im","hub_url = \"$hub_url\"",$config);
$config = preg_replace("/^hub_password = .*?$/im","hub_password = \"$hub_password\"",$config);
file_put_contents($file,$config);

echo "Configuration updated".PHP_EOL;