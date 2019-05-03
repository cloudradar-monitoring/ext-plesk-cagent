#!/usr/bin/php
<?php
$args = ['/usr/bin/cagent', $argv[1]];
$output = [];
$code = 0;
//use built-in exec() here cause command line php version can be different from used in web UI
exec(join(" ",$args),$output,$code);
if(0 != $code) {
    fwrite(STDERR,join(PHP_EOL, $output));
    exit($code);
}else {
    echo join(PHP_EOL, $output);
}