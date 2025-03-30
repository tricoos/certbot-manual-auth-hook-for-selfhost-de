#!/bin/bash
php selfhost-acme-cleanup.php
certbot certonly --keep-until-expiring -vvv --no-eff-email --email $CERTBOT_EMAIL --agree-tos --manual --preferred-challenges dns -d $MY_DOMAIN -d *.$MY_DOMAIN --manual-public-ip-logging-ok --manual-auth-hook /etc/selfhosthook/selfhost-acme.sh --manual-cleanup-hook /etc/selfhosthook/selfhost-acme-cleanup.sh
php selfhost-acme-complete.php