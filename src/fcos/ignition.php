<?php

if (isset($_SERVER['HTTPS']) &&
    ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
    isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
    $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
  $protocol = 'https://';
}
else {
  $protocol = 'http://';
}

$ignition = (object)[];
$ignition->ignition = (object)[];
$ignition->ignition->version = "3.3.0";
$ignition->ignition->config = (object)[];
$ignition->ignition->config->merge = [];

$files = glob("configs/*.ign.php");

foreach($files as $file) {
    $merge = (object)[];
    $merge->source = $protocol . $_SERVER['HTTP_HOST'] . "/fcos/" . $file;
    $ignition->ignition->config->merge[] = $merge;
}

print(json_encode($ignition, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
?>