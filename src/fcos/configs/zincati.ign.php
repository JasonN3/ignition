<?php

require "customizations/customizations.php";

$ignition = (object)[];
$ignition->ignition = (object)[];
$ignition->ignition->version = "3.3.0";
$ignition->storage = (object)[];
$ignition->storage->files = [];

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

print(json_encode($ignition, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
?>