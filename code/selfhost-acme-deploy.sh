#!/bin/bash

# ##########################################################################
# ONLY Change stuff below this line, if you know what you are doing :-)
# ##########################################################################
CERTBOT_DOMAIN=$1

#resulting certificate name
filename=/etc/letsencrypt/live/$CERTBOT_DOMAIN/cert.pem

#check, if the certificate was renewed successfully or not.
fileage=$(($(date +%s) - $(date +%s -r "$filename")))

# younger than an hour, so it worked.
if [[ $fileage -ge 3600 ]]
then
	echo "Renewal was successful"
else
	echo "Renewal failed."
fi