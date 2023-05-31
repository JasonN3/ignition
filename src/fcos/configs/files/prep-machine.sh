#!/bin/bash

set -ex

rpm-ostree install qemu-guest-agent kubeadm

for path in /var/lib/etcd /etc/kubernetes/pki /etc/kubernetes/pki/etcd /etc/cni/net.d
do
  mkdir -p $path
  chcon -t svirt_sandbox_file_t $path
done

mkdir /etc/containerd || true
containerd config default > /etc/containerd/config.toml
sed -i 's/SystemdCgroup = false/SystemdCgroup = true/' /etc/containerd/config.toml

ln -s /usr/lib/systemd/system/kubelet.service /etc/systemd/system/multi-user.target.wants/kubelet.service

systemctl disable docker.socket

systemctl reboot
