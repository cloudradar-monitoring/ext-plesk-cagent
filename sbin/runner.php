#!/usr/bin/php
<?php

use Symfony\Component\Process\Process;

require_once __DIR__."/../../../plib/modules/cagent/vendor/autoload.php";
$args = ['/usr/bin/cagent', $argv[1]];

$process = new Process($args);
$process->run();
if (!$process->isSuccessful()) {
    fwrite(STDERR, $process->getErrorOutput());
    exit(1);
}
echo $process->getOutput();
exit();