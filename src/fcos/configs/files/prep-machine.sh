#!/bin/bash

set -ex

rpm-ostree install qemu-guest-agent kubeadm

for path in /var/lib/etcd /etc/kubernetes/pki /etc/kubernetes/pki/etcd /etc/cni/net.d
do
  chcon -vt svirt_sandbox_file_t $path
done

ln -s /usr/lib/systemd/system/kubelet.service /etc/systemd/system/multi-user.target.wants/kubelet.service

systemctl disable docker.socket

systemctl reboot
