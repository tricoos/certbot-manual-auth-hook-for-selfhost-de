<?php

require_once __DIR__ . '/common.inc.php';

echo "Cleaning up all leftover ACME TXT records...\n";

// argv: SELFHOST_USER SELFHOST_PASSWORD CERTBOT_DOMAIN CERTBOT_TOKEN

$SELFHOST_USER     = getenv('SELFHOST_USER');
$SELFHOST_PASSWORD = getenv('SELFHOST_PASSWORD');
$SELFHOST_NUM      = getenv('SELFHOST_CUSTOMERNUMBER');
$CERTBOT_DOMAIN    = getenv('CERTBOT_DOMAIN');

// Step 1: Get CGISESSIONID, by just visiting the side.
$CGISESSIONID = getSessionId();
echo "CGISESSIONID: " . $CGISESSIONID . "\n";

// Step 2: Log In (Post) with that ID, so it is authenticated.
if (login($CGISESSIONID, $SELFHOST_USER, $SELFHOST_PASSWORD, $SELFHOST_NUM)) {
    echo "Success, logged in.\n";

    //Step 4, get the id for the current domain.
    $domainId = getDomainId($CERTBOT_DOMAIN, $CGISESSIONID);
    if ($domainId != false) {
        echo "Domain-ID: " . $domainId . "\n";

        //Step 5.1: Check, if there are old acme_challenge records. We need to delete those.
        executeAcmeDeletions($domainId, $CERTBOT_DOMAIN, $CGISESSIONID);
    } else {
        echo "Error, failed to obtain Domain ID.\n";
    }
} else {
    echo "Error, login failed.\n";
}

