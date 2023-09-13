FROM quay.io/fedora/fedora:latest AS ipxe

RUN dnf install -y git gcc binutils make perl xz-devel mtools

RUN git clone https://github.com/ipxe/ipxe.git /ipxe; cd /ipxe/src; sed -i 's/#define DOWNLOAD_PROTO_HTTP/define DOWNLOAD_PROTO_HTTP' config/general.h; make bin/undionly.kpxe

FROM docker.io/library/php:apache

VOLUME /var/www/html/fcos/customizations

COPY src/ /var/www/html/
COPY --from=ipxe /ipxe/src/bin/undionly.kpxe /var/www/html/boot/
COPY --from=ipxe /ipxe/src/bin-x86_64-efi/ipxe.efi /var/www/html/boot/