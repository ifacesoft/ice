<li<?php if (!empty($options['active'])) : ?> class="active"<?php endif; ?>>
    <a href="<?php if (isset($options['href'])) : ?><?= $options['href'] ?><?php endif; ?>#<?= $name ?>"><?= $title ?></a>
</li>