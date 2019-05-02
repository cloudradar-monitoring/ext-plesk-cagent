#!/usr/bin/php
<?php
$args = false;
if('rpm' == $argv[1]){
    $args = ['rpm','-i',$argv[2]];
}
if('deb' == $argv[1]){
    $args = ['dpkg','-i',$argv[2]];
}
if(!empty($args)){
    $output = [];
    $code = 0;
    //use built-in exec() here cause command line php version can be different from used in web UI
    exec(join(" ",$args),$output,$code);
    echo join(PHP_EOL,$output);
    exit($code);
}
exit();