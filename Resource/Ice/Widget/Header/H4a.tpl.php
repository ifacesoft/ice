<h4 <?= $component->getIdAttribute() ?>
    <?= $component->getClassAttribute() ?>
><a href="<?= $component->getHref() ?>#<?= $component->getComponentName() ?>"
        <?= $component->getEventAttributesCode() ?>
    ><?php if (isset($params[$label])) : ?><?= $params[$label] ?><?php else : ?><?= $component->getLabel() ?><?php endif; ?></a>
</h4>