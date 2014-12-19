<?php
use Ice\Core\Logger;
use Ice\Helper\Console;
use Ice\Helper\Php;

?><?php if (isset($previous)) { ?>[<?= $time ?>] - host: <?= $host ?><?php if (!empty($uri)) { ?> | uri: <?= $uri ?><?php } ?><?php if (!empty($referer)) { ?> | referer: <?= $referer ?><?php } ?><?php if (!empty($lastTemplate)) { ?> | lastTemplate: <?= $lastTemplate ?><?php } ?>

<?php } ?>
<?= Console::getText(' ' . $message . ' ', Console::C_BLACK_B, Logger::$consoleColors[$type]) ?>

<?= Console::getText($errPoint, Console::C_BLUE) ?>

<?php if (!empty($errcontext)) { ?><?= Console::getText(Php::varToPhpString($errcontext), Console::C_GREEN) ?><?php } ?>

<?= str_replace("):", '):' . Console::RESET, str_replace("#", Console::C_GRAY_B . "#", str_replace(ROOT_DIR, '/', $stackTrace))) . Console::RESET ?>

<?php if (isset($previous)) { ?>
    <?= str_repeat("\t", $level) . str_replace("\n", "\n" . str_repeat("\t", $level), $previous) ?>
<?php } ?>

