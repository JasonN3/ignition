<?php

require "defaults.php";
require "customizations/customizations.php";

$ignition = (object)[];

$ignition->ignition = (object)[];
$ignition->ignition->version = "3.3.0";

$ignition->storage = (object)[];
$ignition->storage->files = [];
$ignition->storage->directories = [];

$ignition->systemd = (object)[];
$ignition->systemd->units = [];

// Add Kubernetes Repo
$file = (object)[];
$file->path = "/etc/yum.repos.d/kubernetes.repo";
$file->contents = (object)[];
$file->contents->compression = "";
$file->contents->source = "data:," . rawurlencode(file_get_contents("files/kubernetes.repo"));
$ignition->storage->files[] = $file;

// Override service entries
$systemd_unit = (object)[];
$systemd_unit->name = "containerd.service";
$systemd_unit->enabled = true;
$systemd_unit->dropins = [];
$override = (object)[];
$override->name = "override.conf";
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
$override->contents = $content;
$systemd_unit->dropins[] = $override;
$ignition->systemd->units[] = $systemd_unit;


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
net.ipv6.conf.all.forwarding        = 1
net.bridge.bridge-nf-call-ip6tables = 1

fs.inotify.max_user_instances       = 8192
fs.inotify.max_user_watches         = 524288
";
$file->contents->source = "data:," . rawurlencode($content);
$ignition->storage->files[] = $file;

// Prep kubernetes directories
$directories = ["/var/lib/etcd", "/etc/kubernetes/pki", "/etc/kubernetes/pki/etcd", "/etc/cni/net.d"];

foreach($directories as $directory) {
    $dir = (object)[];
    $dir->path = $directory;
    $ignition->storage->directories[] = $dir;
}

// Configure Containerd
$file = (object)[];
$file->path = "/etc/containerd/config.toml";
$file->contents = (object)[];
$file->contents->compresssion = "";
$content = "version = 2

imports = [\"/etc/containerd/config.d/*.toml\"]
";
$file->contents->source = "data:," . rawurlencode($content);
$ignition->storage->files[] = $file;

// Configure Containerd to use CGroups
$file = (object)[];
$file->path = "/etc/containerd/config.d/cgroups.toml";
$file->contents = (object)[];
$file->contents->compresssion = "";
$content = "version = 2

[plugins.\"io.containerd.grpc.v1.cri\".containerd.runtimes.runc.options]
  SystemdCgroup = false
";
$file->contents->source = "data:," . rawurlencode($content);
$ignition->storage->files[] = $file;

// Configure caching servers
$file = (object)[];
$file->path = "/etc/containerd/config.d/cache.toml";
$file->contents = (object)[];
$file->contents->compresssion = "";
$content = "version = 2

[plugins.\"io.containerd.grpc.v1.cri\".registry]
  config_path = \"/etc/containerd/certs.d\"
";
$file->contents->source = "data:," . rawurlencode($content);
$ignition->storage->files[] = $file;

foreach($caching_servers as $cache_srv) {
    $dir = (object)[];
    $dir->path = "/etc/containerd/certs.d/" . $cache_srv->name;
    $ignition->storage->directories[] = $dir;

    $file = (object)[];
    $file->path = "/etc/containerd/certs.d/" . $cache_srv->name . "/hosts.toml";
    $file->contents = (object)[];
    $file->contents->compresssion = "";
    $content = "server = \"" . $cache_srv->server . "\"

[host.\"" . $cache_srv->cache . "\"]
  capabilities = [\"pull\", \"resolve\"]
  override_path = true
";
    $file->contents->source = "data:," . rawurlencode($content);
    $ignition->storage->files[] = $file;
}

// Configure registry authentication
foreach($registry_auth as $auth) {
    $file = (object)[];
    $file->path = "/etc/containerd/config.d/auth_" . $auth->registry . ".toml";
    $file->contents = (object)[];
    $file->contents->compresssion = "";
    $content = "version = 2

[plugins.\"io.containerd.grpc.v1.cri\".registry.configs.\"" . $auth->registry . "\".auth]
  username = \"" . $auth->username . "\"                          
  password = \"" . $auth->password . "\"
";
    $file->contents->source = "data:," . rawurlencode($content);
    $ignition->storage->files[] = $file;
}

print(json_encode($ignition, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
?>