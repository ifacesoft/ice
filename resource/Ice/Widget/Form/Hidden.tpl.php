<input <?= $component->getIdAttribute() ?>
    type="hidden"
    name="<?= $component->getName() ?>"
    value="<?= htmlentities($component->getValue(), ENT_QUOTES) ?>"
>