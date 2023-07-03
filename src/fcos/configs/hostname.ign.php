<?php

require "defaults.php";
require "customizations/customizations.php";

$ignition = (object)[];
$ignition->ignition = (object)[];
$ignition->ignition->version = "3.3.0";
$ignition->storage = (object)[];
$ignition->storage->files = [];

$file = (object)[];
$file->path = "/etc/hostname";
$file->contents = (object)[];
$file->contents->compression = "";
$file->contents->source = "data:," . rawurlencode($name_format);
$ignition->storage->files[] = $file;

print(json_encode($ignition, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
?>