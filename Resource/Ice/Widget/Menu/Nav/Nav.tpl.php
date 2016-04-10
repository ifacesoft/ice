<li>
    <a id="<?= $component->getPartId() ?><?php if (isset($offset)) : ?>_<?= $offset ?><?php endif; ?>"
       href="<?= $component->getHref() ?>#<?= $component->getComponentName() ?>"
    ><?= $component->getLabel() ?></a>
    <?= $options['nav'] ?>
</li>