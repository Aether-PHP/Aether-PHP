<?php

echo "Homepage demo for automated Router/Controller<br>";



# - Data extracted from view dispatcher
/** @var string $loggedin */
echo "<pre>";
var_dump($loggedin);
/** @var string $sessid */
var_dump($sessid);
echo "</pre>";

echo __("welcome");