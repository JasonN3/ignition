FROM quay.io/fedora/fedora:latest AS ipxe

RUN dnf install -y git gcc binutils make perl xz-devel mtools genisoimage syslinux

RUN git clone https://github.com/ipxe/ipxe.git /ipxe; cd /ipxe/src; sed -i 's/\#undef\sDOWNLOAD_PROTO_HTTPS/\#define DOWNLOAD_PROTO_HTTPS/' config/general.h; make bin/undionly.kpxe; make bin/ipxe.iso

FROM docker.io/library/php:apache

VOLUME /var/www/html/fcos/customizations

COPY src/ /var/www/html/
RUN mkdir /var/www/html/boot
COPY --from=ipxe /ipxe/src/bin/undionly.kpxe /var/www/html/boot/
COPY --from=ipxe /ipxe/src/bin/ipxe.iso /var/www/html/boot/
