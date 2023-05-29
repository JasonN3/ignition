<?php

require "customizations/customizations.php";

$ignition = (object)[];
$ignition->ignition = (object)[];
$ignition->ignition->version = "3.3.0";
$ignition->passwd = (object)[];
$ignition->passwd->users = [];

// Core User
$user = (object)[];
$user->name = "core";
$user->sshAuthorizedKeys = [];
$user->sshAuthorizedKeys[] = $ssh_key;

$ignition->passwd->users[] = $user;
$ignition->storage = (object)[];
$ignition->storage->files = [];

// Create Trusted User CA Key
$file = (object)[];
$file->path = "/etc/ssh/trusted-user-ca-keys.pem";
$file->contents = (object)[];
$file->contents->compression = "";
$file->contents->source = "data:," . rawurlencode($ssh_ca_key);
$ignition->storage->files[] = $file;

// Enable Trusted User CA Key
$file = (object)[];
$file->path = "/etc/ssh/sshd_config";
$file->append = [];
$file_content = (object)[];
$file_content->compression = "";
$content = "TrustedUserCAKeys /etc/ssh/trusted-user-ca-keys.pem";
$file_content->source = "data:," . rawurlencode($content);
$file->append[] = $file_content;
$ignition->storage->files[] = $file;

// Configure time sync
$file = (object)[];
$file->path = "/etc/chrony.conf";
$file->contents = (object)[];
$file->contents->compression = "";
$content = "driftfile /var/lib/ntp/drift
";
foreach($timeservers as $timeserver) {
    $content .= $timeserver . "\n";
}
$content .= "makestep 1.0 3
rtcsync
logdir /var/log/chrony";
$file->contents->source = "data:," . rawurlencode($content);
$ignition->storage->files[] = $file;

print(json_encode($ignition, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
?>