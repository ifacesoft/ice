<input <?= $component->getIdAttribute() ?>
       type="hidden"
       name="<?= $component->getName() ?>"
       value="<?= isset($params[$value]) ? htmlentities($params[$value], ENT_QUOTES) : '' ?>"
>