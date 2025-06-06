#!ipxe
<?php

require "configs/defaults.php";
require "configs/customizations/customizations.php";

if (isset($_SERVER['HTTPS']) &&
    ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
    isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
    $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
  $protocol = 'https://';
}
else {
  $protocol = 'http://';
}

echo "set base " . $protocol . $_SERVER['HTTP_HOST'];
?>

menu Select a channel
<?php
$streams = ['stable', 'testing', 'next'];
$channel = (object)[];

foreach($streams as $channel) {
  $channel_info = json_decode(file_get_contents("https://builds.coreos.fedoraproject.org/streams/" . $channel . ".json"));
  $channel_versions[$channel] = $channel_info->architectures->x86_64->artifacts->metal->release;
  echo "item " . $channel . " " . ucfirst($channel) . " (" . $channel_versions[$channel] . ")\n";
}
?>
choose channel

goto ${channel}

<?php
foreach($streams as $channel) {
  echo ":" . $channel . "\n";
  echo "set version " . $channel_versions[$channel] . "\n";
  echo "goto os_menu\n";
}
?>


:os_menu
menu Please choose an operating system to deploy
item fcos Fedora CoreOS
item fcos-live Fedora CoreOS (Live)
choose os && goto ${os}

:fcos
kernel https://builds.coreos.fedoraproject.org/prod/streams/${channel}/builds/${version}/x86_64/fedora-coreos-${version}-live-kernel.x86_64 rd.neednet=1 console=tty0 initrd=main coreos.live.rootfs_url=https://builds.coreos.fedoraproject.org/prod/streams/${channel}/builds/${version}/x86_64/fedora-coreos-${version}-live-rootfs.x86_64.img coreos.inst.install_dev=<?php echo $primary_disk; ?> coreos.inst.ignition_url=${base}/fcos/ignition.php coreos.inst.image_url=https://builds.coreos.fedoraproject.org/prod/streams/${channel}/builds/${version}/x86_64/fedora-coreos-${version}-metal.x86_64.raw.xz coreos.inst.copy-network
initrd https://builds.coreos.fedoraproject.org/prod/streams/${channel}/builds/${version}/x86_64/fedora-coreos-${version}-live-initramfs.x86_64.img
boot

:fcos-live
kernel https://builds.coreos.fedoraproject.org/prod/streams/${channel}/builds/${version}/x86_64/fedora-coreos-${version}-live-kernel-x86_64 ip=dhcp rd.neednet=1 console=tty0 initrd=main coreos.live.rootfs_url=https://builds.coreos.fedoraproject.org/prod/streams/${channel}/builds/${version}/x86_64/fedora-coreos-${version}-live-rootfs.x86_64.img ignition.firstboot ignition.platform.id=metal ignition.config.url=${base}/fcos/ignition.php
#kernel https://builds.coreos.fedoraproject.org/prod/streams/${channel}/builds/${version}/x86_64/fedora-coreos-${version}-live-kernel-x86_64 ip=dhcp rd.neednet=1 console=tty0 ignition.firstboot ignition.platform.id=metal ignition.config.url=${base}/fcos/ignition.php
initrd https://builds.coreos.fedoraproject.org/prod/streams/${channel}/builds/${version}/x86_64/fedora-coreos-${version}-live-initramfs.x86_64.img
boot
