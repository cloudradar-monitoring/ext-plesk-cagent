#!/usr/bin/php
<?php

use Symfony\Component\Process\Process;

require_once __DIR__."/../plib/vendor/autoload.php";
$args = false;
if('rpm' == $argv[1]){
    $args = ['rpm','-i',$argv[2]];
}
if('deb' == $argv[1]){
    $args = ['dpkg','-i',$argv[2]];
}
if(!empty($args)){
    $process = new Process($args);
    $process->run();
    if (!$process->isSuccessful()) {
        fwrite(STDERR,$process->getErrorOutput());
        exit(1);
    }
    echo $process->getOutput();
    exit();
}
exit();