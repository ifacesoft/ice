<?php

?>[<?= $time ?>] - host: <?= $host ?> | uri: <?= $uri ?><?php if (!empty($referer)) { ?> | referer: <?= $referer ?><?php } ?><?php if (!empty($lastTemplate)) { ?> | lastTemplate: <?= $lastTemplate ?><?php } ?>

<?= $message ?> (<?= $errPoint ?>)
