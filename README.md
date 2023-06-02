# ignition
This container hosts an http server that will generate ignition files for a Fedora CoreOS deployment.

## Features
- Hostname is generated based on PHP code written in customizations file
- SSH access is granted to the SSH key provided. User is `core`
- SSH access can also be granted using CA server to sign the SSH key
- A second drive is configured to store the container images and ephemeral storage
- Zincati can be configured to point to a fleet lock server so nodes can be drained before rebooting and only 1 node can reboot at a time. https://github.com/JasonN3/kubernetes_reboot_manager is available as a fleet lock server that will run within kubernetes
- Some customizations to improve Kubernetes runtime
- Automatic installation of `kubeadm` and `kubelet`

## Usage
1. Launch the container on a system
```bash
sudo podman -d -p 8080:80 -v ignition:/var/www/html/fcos/customizations ghcr.io/jasonn3/ignition:latest
```
1. Edit the cusomizations
```bash
vi $(podman volume mount ignition)/customizations.php
```

There is an iPXE script available at `http://_your_server_/fcos/fcos.ipxe.php`. You can configure DHCP to boot in to iPXE and chain to the script or you can build a custom iPXE ISO with an embedded script. To build a custom iPXE ISO, please follow iPXE's instructions available [here](https://ipxe.org/download). Whether you are building your own image or using a pre-built image, make sure that HTTPS is an enabled protocol. HTTPS is used when downloading the images from Fedora Project's website.

Enabling HTTPS before building
This command can be run after `cd ipxe/src` and before running `make`
```bash
sed -i 's/#undef DOWNLOAD_PROTO_HTTPS/#define DOWNLOAD_PROTO_HTTPS/' config/general.h
```

Embedding an iPXE script
```bash
cat << EOF > script.ipxe
#!ipxe

dhcp
chain http://_your_server_/fcos/fcos.ipxe.php
EOF

make bin/ipxe.iso EMBED=script.ipxe
```

Once the OS is deployed. Kubernetes can be installed by following the instructions available [here](https://kubernetes.io/docs/setup/production-environment/tools/kubeadm/create-cluster-kubeadm/)