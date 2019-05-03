#!/usr/bin/php
<?php
$args = false;
if('rpm' == $argv[1]){
    $args = ['yum', '-y','localinstall',$argv[2]];
}
if('deb' == $argv[1]){
    $args = ['dpkg','-i',$argv[2]];
    $output = ['DPKG'];
    $code = 0;
    //use built-in exec() here cause command line php version can be different from used in web UI
    exec(join(" ",$args),$output,$code);
    if(0 != $code) {
        fwrite(STDERR,join(PHP_EOL, $output));
        exit($code);
    }

    $args = ['apt-get','update'];
    $output = ['APT_GET UPDATE'];
    $code = 0;
    exec(join(" ",$args),$output,$code);
    if(0 != $code) {
        fwrite(STDERR,join(PHP_EOL, $output));
        exit($code);
    }

    $args = ['apt-get','install','cagent','-y'];
}
if(!empty($args)){
    $output = ['LAST STEP'];
    $code = 0;
    //use built-in exec() here cause command line php version can be different from used in web UI
    exec(join(" ",$args),$output,$code);
    if(0 != $code) {
        fwrite(STDERR,join(PHP_EOL, $output));
        exit($code);
    }else {
        echo join(PHP_EOL, $output);
    }
    exit($code);
}
exit();