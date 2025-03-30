FROM php:8.4.5-cli

COPY code /etc/selfhosthook
RUN apt update && apt install certbot -y
RUN chmod +x /etc/selfhosthook/selfhost-acme.sh /etc/selfhosthook/selfhost-acme-cleanup.sh /etc/selfhosthook/selfhost-acme-deploy.sh
RUN mkdir /etc/certoutput
WORKDIR /etc/selfhosthook

#ENTRYPOINT ["tail", "-f", "/dev/null"]
CMD [ "/etc/selfhosthook/run.sh" ]
