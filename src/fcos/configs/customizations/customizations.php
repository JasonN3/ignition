<?php

// Public key for "core" user
$ssh_key = "";

// Public key of CA that can sign SSH keys
$ssh_ca_key = "";

// List of time servers. Each entry is an entire line in /etc/chrony.conf
$timeservers = [
    "pool pool.ntp.org prefer iburst"
];

// URL of fleet lock server for reboot management
$fleet_lock_server = "";

// PHP code to generate hostname
$name_format = "";

//Primary disk device path
$primary_disk = "/dev/sda";

// Second disk device path
$secondary_disk = "/dev/sdb";

// Caching servers
$caching_servers = [];
/*
$caching_servers[] = [
    "name"   => "docker.io",
    "server" => "registry-1.docker.io",
    "cache"  => "quay/v2/docker_io"
];
*/

$registry_auth = [];
/*
$registry_auth[] = [
    "registry" => "quay",
    "username" => "user",
    "password" => "your password"
];
*/

?>