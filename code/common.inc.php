<?php

function getSessionId()
{
    $content = file_get_contents("https://selfhost.de/cgi-bin/");
    preg_match("@CGISESSID=([^\"]+)\"@is", $content, $matches);
    return $matches[1];
}

function login($sessionId, $user, $pass, $num)
{
    $params               = [];
    $params["CGISESSID"]  = $sessionId;
    $params["p"]          = "cms";
    $params["login_user"] = $user;
    $params["login_pass"] = $pass;

    $result = doPost("https://selfhost.de/cgi-bin/selfhost", $params);

    //success or not?
    if (strpos($result, $num) != 0) {
        return true;
    }
    return false;
}

function getDomainId($domain, $sessionId)
{
    $content = file_get_contents("https://selfhost.de/cgi-bin/selfhost?p=account&cat=domain&CGISESSID=" . $sessionId);
    if (preg_match("@<tr>.*?" . preg_quote($domain) . ".*?\?p=account&cat=domconfig&subcat=records&id=(\d+).*?</tr>@is", $content, $matches)) {
        return $matches[1];
    }
    return false;
}

function executeAcmeDeletions($domainId, $domain, $CGISESSIONID)
{
    $content = file_get_contents("https://selfhost.de/cgi-bin/selfhost?p=account&cat=domconfig&subcat=records&id=" . $domainId . "&CGISESSID=" . $CGISESSIONID);
    preg_match_all("@_acme-challenge\." . preg_quote($domain) . ".*?<a href=\"([^\"]+do=delete[^\"]+)\">@is", $content, $matches, PREG_SET_ORDER);
    echo "Challenges to delete:\n";
    foreach ($matches as $match) {
        echo " -> " . $match[1] . "\n";
        file_get_contents("https://selfhost.de/cgi-bin/selfhost" . $match[1]);
    }
    echo "Challenges deleted, if any.\n";
}

function executeAcmeInsertion($domainId, $domain, $CGISESSIONID, $challenge)
{
    $params              = [];
    $params["CGISESSID"] = $CGISESSIONID;
    $params["p"]         = "account";
    $params["cat"]       = "domconfig";
    $params["subcat"]    = "records";
    $params["do"]        = "new";
    $params["id"]        = $domainId;
    $params["typ"]       = "txt";
    $params["formular"]  = "18";
    $params["record"]    = "_acme-challenge";
    $params["ttl"]       = 60;
    $params["content"]   = $challenge;

    doPost("https://selfhost.de/cgi-bin/selfhost", $params);
    echo "Acme-Insertions done: $challenge";
}

function doPost($url, $paramArray)
{
    return file_get_contents($url, false, stream_context_create(
        [
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-type: application/x-www-form-urlencoded",
                'content' => http_build_query($paramArray)
            ]
        ]
    ));
}