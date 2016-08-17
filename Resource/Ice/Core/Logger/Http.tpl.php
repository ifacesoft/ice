<?php

?>
<meta charset="UTF-8"/>
<div style="font-size: 12px;font-family: Tahoma, Geneva, sans-serif; background-color: #ffffff; color: #000000;">
    <div class="alert alert-<?= $type ?>">
        <?php if (isset($previous)) { ?>
            <div style="color: #000000;">
                <em><?= $time ?></em> -
                host: <strong><?= $host ?></strong>
                | uri: <strong><?= $uri ?></strong>
                <?php if (!empty($referer)) { ?> | referer: <strong><?= $referer ?></strong><?php } ?>
                <?php if (!empty($lastTemplate)) { ?> | lastTemplate: <strong><?= $lastTemplate ?></strong><?php } ?>
            </div>
        <?php } ?>
        <pre><span style="color: red;"><?= $message ?></span><?= "\n\t" ?><span
                style="color: blue;"><?= $errPoint ?></span></pre>
        <?php if ($errcontext) { ?>
            <span style="font-size: 12px;"><?= highlight_string($errcontext, true) ?></span>
        <?php } ?>
        <pre
            style="margin: 0;"><?= str_replace('#', '</span>#', str_replace('):', '):<span style="color: grey;">', str_replace(dirname(MODULE_DIR), '', $stackTrace))) ?></pre>
        <?php if (isset($previous)) { ?>
            <?= $previous ?>
        <?php } ?>
    </div>
</div>
