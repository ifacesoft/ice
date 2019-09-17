<li id="<?= $menuName ?>_<?= $name ?>"
    <?php if (!empty($options['classes'])) { ?>class="<?= implode(' ', $options['classes']) ?>"<?php } ?>>
    <a href="<?= $href ?>" onclick='<?= $onclick ?>'
       data-url='<?= $dataUrl ?>'
       data-json='<?= $dataJson ?>'
       data-action='<?= $dataAction ?>'
       data-block='<?= $dataBlock ?>'
       <?php if (isset($options['style'])) : ?>style="<?= $options['style'] ?>"<?php endif; ?>>
        <?= $title ?>
    </a>
</li>