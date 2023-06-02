<?php

require "customizations/customizations.php";

$ignition = (object)[];
$ignition->ignition = (object)[];
$ignition->ignition->version = "3.3.0";

$ignition->storage = (object)[];
$ignition->storage->files = [];

$ignition->systemd = (object)[];
$ignition->systemd->units = [];

if($fleet_lock_server != "") {
    // Create Trusted User CA Key
    $file = (object)[];
    $file->path = "/etc/zincati/config.d/reboot-manager.toml";
    $file->append = [];
    $file_content = (object)[];
    $file_content->compression = "";
    $content = "[updates]
    strategy = \"fleet_lock\"
    fleet_lock.base_url = \"" . $fleet_lock_server . "\"";
    $file_content->source = "data:," . rawurlencode($content);
    $file->append[] = $file_content;
    $ignition->storage->files[] = $file;
}

// Add prep-machine service
$systemd_unit = (object)[];
$systemd_unit->name = "bootupd-auto.service";
$systemd_unit->enabled = true;
$systemd_unit->contents = file_get_contents("files/bootupd-auto.service");
$ignition->systemd->units[] = $systemd_unit;

print(json_encode($ignition, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
?>