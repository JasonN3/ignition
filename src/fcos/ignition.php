<?php

$ignition = (object)[];
$ignition->ignition = (object)[];
$ignition->ignition->version = "3.3.0";
$ignition->ignition->config = (object)[];
$ignition->ignition->config->merge = (object)[];

$files = scandir('./configs/fcos');

foreach($files as $file) {
    $merge = (object)[];
    $merge->source = $_SERVER[HTTP_HOST] . "/configs/" . $file;
    $ignition->ignition->config->merge[] = $merge;
}

print(json_encode($ignition, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
?>