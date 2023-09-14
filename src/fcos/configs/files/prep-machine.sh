#!/bin/bash

set -ex

rpm-ostree install qemu-guest-agent kubeadm

for path in /var/lib/etcd /etc/kubernetes/pki /etc/kubernetes/pki/etcd /etc/cni/net.d
do
  chcon -vt svirt_sandbox_file_t $path
done

containerd config default > /etc/containerd/config.toml
sed -i 's/imports = .*/imports = ["\/etc\/containerd\/config.d\/*.toml"]/' /etc/containerd/config.toml

# This only work in the main config. It does not work as an import
sed -i 's/SystemdCgroup = .*/SystemdCgroup = true/g' /etc/containerd/config.toml

ln -s /usr/lib/systemd/system/kubelet.service /etc/systemd/system/multi-user.target.wants/kubelet.service

systemctl disable docker.socket

sed -i 's/SELINUX=.*/SELINUX=permissive/' /etc/selinux/config

systemctl reboot
