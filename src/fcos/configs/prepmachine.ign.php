<?php

$ignition = (object)[];

$ignition->ignition = (object)[];
$ignition->ignition->version = "3.3.0";

$ignition->storage = (object)[];
$ignition->storage->files = [];

$ignition->systemd = (object)[];
$ignition->systemd->units = [];

// Add prep-machine.sh script
$file = (object)[];
$file->path = "/usr/local/src/prep-machine.sh";
$file->contents = (object)[];
$file->contents->compression = "";
$file->contents->source = "data:," . rawurlencode(file_get_contents("files/prep-machine.sh"));
$ignition->storage->files[] = $file;

// Add prep-machine service
$systemd_unit = (object)[];
$systemd_unit->name = "prep-machine.service";
$systemd_unit->enabled = true;
$systemd_unit->contents = file_get_contents("files/prep-machine.service");
$ignition->systemd->units[] = $systemd_unit;


print(json_encode($ignition, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
?>