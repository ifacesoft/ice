<div <?php if (!$component->getOption('resetFormClass')) : ?> class="form-group"<?php endif; ?>>
    <label for="<?= $component->getId() ?>"
           class="control-label<?php if ($component->getOption('srOnly')) : ?> sr-only<?php endif; ?><?php if ($component->getHorizontal()) : ?> col-md-<?= $component->getHorizontal() ?><?php endif; ?>"
    ><?= $component->getLabel() ?><?php if ($component->getOption('required', false)) : ?> <sup
            style="color: red;">*</sup><?php endif; ?></label>
    <?php if ($component->getHorizontal()) : ?>
    <div class="col-md-<?= 12 - $component->getHorizontal() ?>"><?php endif; ?>
        <select <?= $component->getIdAttribute() ?>
            <?= $component->getClassAttribute($component->getOption('resetFormClass', false) ? '' : 'form-control') ?>
            name="<?= $component->getName() ?><?php if ($component->getOption('multiple', false)) : ?>[]<?php endif; ?>"
            <?php if ($component->getOption('multiple', false)) : ?>multiple="multiple"<?php endif; ?>
            size="<?= $component->getOption('size', 1) ?>"
            <?= $component->getEventAttributesCode() ?>
            <?= $component->getPlaceholderAttribute('data-placeholder') ?>
            <?php if ($component->getOption('disabled', false)) : ?>disabled="disabled"<?php endif; ?>
            <?php if ($component->getOption('readonly', false)) : ?>readonly="readonly"<?php endif; ?>
            <?php if ($component->getOption('required', false)) : ?>required="required"<?php endif; ?>
            <?php if ($component->getOption('autofocus', false)) : ?>autofocus="autofocus"<?php endif; ?>
        >
            <?php foreach ($component->getItems() as $item) : ?>
                <option value="<?= htmlentities($item[$component->getItemKey()]) ?>"
                        <?php if ((is_array($component->getValue()) && in_array($item[$component->getItemKey()], $component->getValue())) || $item[$component->getItemKey()] == $component->getValue()) : ?>selected="selected"<?php endif; ?>
                ><?= $component->getTitle($item) ?></option>
            <?php endforeach; ?>
        </select>
        <?php if ($component->getHorizontal()) : ?></div><?php endif; ?>
    <script>
        $(function () {
            $("#<?= $component->getId() ?>").chosen({
                <?php if ($component->getOption('required', false) === false) : ?>allow_single_deselect: true,<?php endif; ?>
                <?php if ($component->getOption('multiple', false)) : ?>placeholder_text_multiple: '<?= $component->getPlaceholder() ?>',<?php else : ?>placeholder_text_single: '<?= $component->getPlaceholder() ?>',<?php endif; ?>
                max_selected_options: 5,
                no_results_text: 'Oops, nothing found!'
            });
        });
    </script>
</div>