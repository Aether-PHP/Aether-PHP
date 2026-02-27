<?php


# - Template used to wrap the main content so we can compute its boot time
# -- to use in ndex.php

$boot_time = \benchmarks\BootTimeBench::_wrap(function (){
    # - Core init
    $app = new \Aether\Aether();
    $app->_run();
});

print_r("<br><br>" . $boot_time . " ms");