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

?>