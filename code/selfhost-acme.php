<?php

require_once __DIR__.'/common.inc.php';

// argv: SELFHOST_USER SELFHOST_PASSWORD CERTBOT_DOMAIN CERTBOT_TOKEN

$SELFHOST_USER     = $argv[1];
$SELFHOST_PASSWORD = $argv[2];
$SELFHOST_NUM      = $argv[3];
$CERTBOT_DOMAIN    = $argv[4];
$CERTBOT_TOKEN     = $argv[5];

// Step 1: Get CGISESSIONID, by just visiting the side.
$CGISESSIONID = getSessionId();
echo "CGISESSIONID: " . $CGISESSIONID . "\n";

// Step 2: Log In (Post) with that ID, so it is authenticated.
if (login($CGISESSIONID, $SELFHOST_USER, $SELFHOST_PASSWORD, $SELFHOST_NUM)) {
    echo "Success, logged in.\n";

    //Step 4, get the id for the current domain.
    $domainId = getDomainId($CERTBOT_DOMAIN, $CGISESSIONID);
    if ($domainId !== false) {
        echo "Domain-ID: " . $domainId . "\n";

        //Step 5.1: Check, if there are old acme_challenge records. We need to delete those.
        if ($CERTBOT_TOKEN == "CLEANUP")
            executeAcmeDeletions($domainId, $CERTBOT_DOMAIN, $CGISESSIONID);

        //Step 5.2: Set the new acme record.
        else
            executeAcmeInsertion($domainId, $CERTBOT_DOMAIN, $CGISESSIONID, $CERTBOT_TOKEN);
    } else {
        echo "Error, failed to obtain Domain-ID.\n";
    }
} else {
    echo "Error, login failed.\n";
}

