<?php
use Ice\Helper\Console;

?>

<?= Console::getText(' Please modify your hosts file (\'/etc/hosts\' or \'%SystemRoot%\system32\drivers\etc\host\') ', Console::C_RED_B, Console::BG_GREEN) ?>


127.0.0.1       <?= strtolower($moduleName) . '.global' ?> <?= strtolower($moduleName) . '.test' ?> <?= strtolower($moduleName) . '.local' ?>



<?= Console::getText(' For nginx web-server add new \'server\' section ', Console::C_RED_B, Console::BG_GREEN) ?>


upstream <?= strtolower($moduleName) ?>_phpfcgi {
#    server 127.0.0.1:9000;
server unix:/var/run/php5-fpm.sock;
}

server {
listen 80;

server_name <?= strtolower($moduleName) . '.global' ?> <?= strtolower($moduleName) . '.test' ?> <?= strtolower($moduleName) . '.local' ?>;
root <?= MODULE_DIR ?>Web;

error_log <?= $logDir ?>error.log;
access_log <?= $logDir ?>access.log combined;

client_max_body_size 8m;

rewrite ^/index\.php/?(.*)$ /$1 permanent;

location / {
index index.php;
try_files $uri @rewriteapp;
}

location @rewriteapp {
rewrite ^(.*)$ /index.php/$1 last;
}

location ~ ^/index\.php(/|$) {
fastcgi_pass <?= strtolower($moduleName) ?>_phpfcgi;
fastcgi_split_path_info ^(.+\.php)(/.*)$;
fastcgi_read_timeout 300;

include fastcgi_params;

fastcgi_param  SCRIPT_FILENAME $document_root$fastcgi_script_name;
fastcgi_param  HTTPS off;

fastcgi_buffers 64 128k;
fastcgi_buffer_size 4096k;
fastcgi_busy_buffers_size 4096k;
}
}


<?= Console::getText(' For apache web-server add new \'vhost\' directive ', Console::C_RED_B, Console::BG_GREEN) ?>


<VirtualHost *:80>
    ServerName <?= strtolower($moduleName) . '.local' ?>
    ServerAlias <?= strtolower($moduleName) . '.global' ?> <?= strtolower($moduleName) . '.test' ?>

    DocumentRoot <?= MODULE_DIR ?>Web
    DirectoryIndex index.php

    CustomLog <?= $logDir ?>access.log combined
    ErrorLog <?= $logDir ?>error.log

    <Directory <?= dirname(MODULE_DIR) ?>>
        AllowOverride All
        Order allow,deny
        Allow from All
    </Directory>
</VirtualHost>

<?= Console::getText('ATTENTION!!! FIRST READ UPPER FOR VALID INSTALL! ', Console::C_RED_B) ?>


<?= Console::getText('Congratulations!' . "\n" . 'After web-server setting and restart open in browser: http://' . strtolower($moduleName) . '.local', Console::C_GREEN_B) ?>

