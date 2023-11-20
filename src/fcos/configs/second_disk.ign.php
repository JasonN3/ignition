<?php

require "defaults.php";
require "customizations/customizations.php";

$ignition = (object)[];
$ignition->ignition = (object)[];
$ignition->ignition->version = "3.3.0";

$ignition->storage = (object)[];
$ignition->storage->disks = [];
$ignition->storage->filesystems = [];

$ignition->systemd = (object)[];
$ignition->systemd->units = [];

$disk = (object)[];
$disk->device = $secondary_disk;
$disk->wipeTable = true;
$disk->partitions = [];
$partition = (object)[];
$partition->label = "containerd";
$partition->number = 0;
$partition->sizeMiB = 0;
$disk->partitions[] = $partition;
$ignition->storage->disks[] = $disk;

$filesystem = (object)[];
$filesystem->device = "/dev/disk/by-partlabel/containerd";
$filesystem->format = "xfs";
$filesystem->path = "/var/lib/containerd";
$filesystem->label = "containerd";
$filesystem->wipeFilesystem = true;
$ignition->storage->filesystems[] = $filesystem;

$systemd_unit = (object)[];
$systemd_unit->name = "var-lib-containerd.mount";
$systemd_unit->enabled = true;
$systemd_unit->contents = file_get_contents("files/var-lib-containerd.mount");
$ignition->systemd->units[] = $systemd_unit;

print(json_encode($ignition, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
?>
