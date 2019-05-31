#!/usr/bin/php
<?php

function cagent_remove_dir($dir) {
    foreach (glob($dir) as $entry) {
        if (is_dir($entry)) {
            cagent_remove_dir("$entry/*");
            rmdir($entry);
        } else {
            unlink($entry);
        }
    }
}
$args = false;
if('rpm' == $argv[1]){
    $args = ['rpm','-e','cagent'];
}
if('deb' == $argv[1]){
    $args = ['apt-get','remove','--purge','cagent','-y'];
}
if(!empty($args)){
    $output = [];
    $code = 0;
    //use built-in exec() here cause command line php version can be different from used in web UI
    exec(join(" ",$args),$output,$code);
    cagent_remove_dir("/etc/cagent/");
    cagent_remove_dir("/var/log/cagent/");
    if(0 != $code) {
        fwrite(STDERR,join(PHP_EOL, $output));
        exit($code);
    }else {
        echo join(PHP_EOL, $output);
    }
}
exit();