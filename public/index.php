<?php

echo __("welcome") . "<br>";

# - Data extracted from view dispatcher
/** @var string $loggedin */
echo "<pre>";
var_dump($loggedin);
/** @var string $sessid */
var_dump($sessid);
echo "</pre>";
