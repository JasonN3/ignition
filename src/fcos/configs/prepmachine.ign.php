<?php

$ignition = (object)[];

$ignition->ignition = (object)[];
$ignition->ignition->version = "3.3.0";

$ignition->storage = (object)[];
$ignition->storage->files = [];
$ignition->storage->links = [];

// Add prep-machine.sh script
$file = (object)[];
$file->path = "/usr/local/src/prep-machine.sh";
$file->contents = (object)[];
$file->contents->compression = "";
$file->contents->source = "data:," . rawurlencode(file_get_contents("files/prep-machine.sh"));
$ignition->storage->files[] = $file;

// Add prep-machine service
$file = (object)[];
$file->path = "/etc/systemd/system/prep-machine.service";
$file->contents = (object)[];
$file->contents->compression = "";
$file->contents->source = "data:," . rawurlencode(file_get_contents("files/prep-machine.service"));
$ignition->storage->files[] = $file;

// Enable pre-machine service
$link = (object)[];
$link->path = "/etc/systemd/system/multi-user.target.wants/prep-machine.service";
$link->target = "/etc/systemd/system/prep-machine.service";
$ignition->storage->links[] = $link;



print(json_encode($ignition, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
?>