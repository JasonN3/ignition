<?php

$ignition = (object)[];

$ignition->ignition = (object)[];
$ignition->ignition->version = "3.3.0";

$ignition->storage = (object)[];
$ignition->storage->files = [];
$ignition->storage->directories = [];
$ignition->storage->links = [];

// Add Kubernetes Repo
$file = (object)[];
$file->path = "/etc/yum.repos.d/kubernetes.repo";
$file->contents = (object)[];
$file->contents->compression = "";
$file->contents->source = "data:," . rawurlencode(file_get_contents("files/kubernetes.repo"));
$ignition->storage->files[] = $file;

// Override service entries
$directory = (object)[];
$directory->path = "/etc/systemd/system/containerd.service.d";
$ignition->storage->directories[] = $directory;

$file = (object)[];
$file->path = "/etc/systemd/system/containerd.service.d/override.conf";
$file->contents = (object)[];
$file->contents->compression = "";
$content = "[Service]
Restart=always
RestartSec=5
OOMScoreAdjust=-999
LimitNOFILE=infinity
# Having non-zero Limit*s causes performance problems due to accounting overhead
# in the kernel. We recommend using cgroups to do container-local accounting.
LimitNPROC=infinity
LimitCORE=infinity
";
$file->contents->source = "data:," . rawurlencode($content);
$ignition->storage->files[] = $file;

// Enable containerd
$link = (object)[];
$link->path = "/etc/systemd/system/multi-user.target.wants/containerd.service";
$link->target = "/usr/lib/systemd/system/containerd.service";
$ignition->storage->links[] = $link;

// Enable modules
$file = (object)[];
$file->path = "/etc/modules-load.d/containerd.conf";
$file->contents = (object)[];
$file->contents->compression = "";
$content = "overlay
br_netfilter
";
$file->contents->source = "data:," . rawurlencode($content);
$ignition->storage->files[] = $file;

// Blacklist ip_tables
$file = (object)[];
$file->path = "/etc/modprobe.d/blacklist_iptables.conf";
$file->contents = (object)[];
$file->contents->compression = "";
$content = "blacklist ip_tables
";
$file->contents->source = "data:," . rawurlencode($content);
$ignition->storage->files[] = $file;

// Configure for CRI
$file = (object)[];
$file->path = "/etc/sysctl.d/99-kubernetes-cri.conf";
$file->contents = (object)[];
$file->contents->compression = "";
$content = "net.bridge.bridge-nf-call-iptables  = 1
net.ipv4.ip_forward                 = 1
net.bridge.bridge-nf-call-ip6tables = 1

fs.inotify.max_user_instances       = 8192
fs.inotify.max_user_watches         = 524288
";
$file->contents->source = "data:," . rawurlencode($content);
$ignition->storage->files[] = $file;



print(json_encode($ignition, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
?>