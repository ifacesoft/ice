<a href="<?php if (isset($options['href'])) { ?><?= $options['href'] ?><?php } else { ?>#<?php } ?>"
   <?php if (isset($options['onclick'])) { ?>onclick="<?= $options['onclick'] ?>"<?php } ?>
   <?php if (!empty($options['classes'])) { ?>class="<?= implode(' ', $options['classes']) ?>"<?php } ?>
   <?php if (isset($options['style'])) { ?>style="<?= $options['style'] ?>"<?php } ?>><?= $value ?></a>
