<?php

# TODO: Send mail or other stuff here, use PHPMailer and specify SMTP data in .env
$dir     = '/etc/letsencrypt/live/' . getenv('MY_DOMAIN');
$files   = scandir($dir);
$success = 0;
foreach ($files as $file) {
    if (preg_match("/\.pem$/", basename($file))) {
        if (copy($dir.'/'.$file, '/etc/certoutput/' . $file)) {
            $success++;
        }
    }
}

if ($success) {
    exit(0);
}
exit(1);
