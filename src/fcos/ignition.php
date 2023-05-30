<?php

$ignition = (object)[];
$ignition->ignition = (object)[];
$ignition->ignition->version = "3.3.0";
$ignition->ignition->config = (object)[];
$ignition->ignition->config->merge = [];

$files = glob("configs/*.ign.php");

foreach($files as $file) {
    $merge = (object)[];
    $merge->source = $_SERVER['HTTP_HOST'] . "/fcos/configs/" . $file;
    $ignition->ignition->config->merge[] = $merge;
}

print(json_encode($ignition, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
?>