<div<?php if (!$component->getOption('resetFormClass')) : ?> class="form-group"<?php endif; ?>>
    <label
        for="<?= $component->getId() ?>_many"
        class="control-label<?php if ($component->getOption('srOnly')) : ?> sr-only<?php endif; ?><?php if ($component->getHorizontal()) : ?> col-md-<?= $component->getHorizontal() ?><?php endif; ?>"
    ><?= $component->getManyLabel() ?><?php if ($component->getOption('required', false)) : ?> <sup style="color: red;">*</sup><?php endif; ?></label>
    <?php if ($component->getHorizontal()) : ?>
    <div class="col-md-<?= 12 - $component->getHorizontal() ?>"><?php endif; ?>
        <select <?= $component->getIdAttribute('many') ?>
            <?= $component->getClassAttribute($component->getOption('resetFormClass', false) ? '' : 'form-control') ?>
            name="<?= $component->getName() ?>_many<?php if ($component->getOption('multiple', false)) : ?>[]<?php endif; ?>"
            <?php if ($component->getOption('multiple', false)) : ?>multiple="multiple"<?php endif; ?>
            <?php if (!empty($options['size'])) : ?>size="<?= $options['size'] ?>"<?php endif; ?>
            <?= $component->getPlaceholderManyAttribute('data-placeholder') ?>
            <?php if ($component->getOption('disabled', false)) : ?>disabled="disabled"<?php endif; ?>
            <?php if ($component->getOption('readonly', false)) : ?>readonly="readonly"<?php endif; ?>
            <?php if ($component->getOption('required', false)) : ?>required="required"<?php endif; ?>
            <?php if ($component->getOption('autofocus', false)) : ?>autofocus="autofocus"<?php endif; ?>
        >
            <?php foreach ($component->getManyItems() as $item) : ?>
                <option value="<?= htmlentities($item[$component->getManyItemKey()]) ?>"
                        <?php if ($item[$component->getManyItemKey()] == $component->getManyValue()) : ?>selected="selected"<?php endif; ?>
                ><?= $item[$component->getManyItemTitle()] ?></option>
            <?php endforeach; ?>
        </select>
        <?php if ($component->getHorizontal()) : ?></div><?php endif; ?>
</div>
<div <?php if (!$component->getOption('resetFormClass')) : ?> class="form-group"<?php endif; ?>>
    <label
        for="<?= $component->getId() ?>"
        class="control-label<?php if ($component->getOption('srOnly')) : ?> sr-only<?php endif; ?><?php if ($component->getHorizontal()) : ?> col-md-<?= $component->getHorizontal() ?><?php endif; ?>"
    ><?= $component->getLabel() ?><?php if ($component->getOption('required', false)) : ?> <sup style="color: red;">*</sup><?php endif; ?></label>
    <?php if ($component->getHorizontal()) : ?>
    <div class="col-md-<?= 12 - $component->getHorizontal() ?>"><?php endif; ?>
        <select <?= $component->getIdAttribute() ?>
            <?= $component->getClassAttribute($component->getOption('resetFormClass', false) ? '' : 'form-control') ?>
            name="<?= $component->getName() ?><?php if ($component->getOption('multiple', false)) : ?>[]<?php endif; ?>"
            <?php if ($component->getOption('multiple', false)) : ?>multiple="multiple"<?php endif; ?>
            <?php if (!empty($options['size'])) : ?>size="<?= $options['size'] ?>"<?php endif; ?>
            <?= $component->getEventAttributesCode() ?>
            <?= $component->getPlaceholderAttribute('data-placeholder') ?>
            <?php if ($component->getOption('disabled', false)) : ?>disabled="disabled"<?php endif; ?>
            <?php if ($component->getOption('readonly', false)) : ?>readonly="readonly"<?php endif; ?>
            <?php if ($component->getOption('required', false)) : ?>required="required"<?php endif; ?>
            <?php if ($component->getOption('autofocus', false)) : ?>autofocus="autofocus"<?php endif; ?>
        >
        </select>
        <?php if ($component->getHorizontal()) : ?></div><?php endif; ?>
</div>
<script>
    $(function () {
        $("#<?= $component->getId('many') ?>").chosen({
            <?php if ($component->getOption('required', false) === false) : ?>allow_single_deselect: true,<?php endif; ?>
            max_selected_options: 5


        }).change(function () {
            if ($("#<?= $component->getId('many') ?>").val()) {
                $("#<?= $component->getId() ?> option").remove()
                <?= $component->getId() ?>_items[$("#<?= $component->getId('many') ?>").val()].forEach(function (item, key) {
                    $("#<?= $component->getId() ?>").append('<option>' + Ice.replaceRender('<?= $component->getItemTitle() ?>', item) + '</option>');
                });
                $("#<?= $component->getId() ?>").trigger("chosen:updated");
                $("#<?= $component->getId() ?>").closest('div.form-group').show();
            } else {
                $("#<?= $component->getId() ?>").closest('div.form-group').hide();

                $("#<?= $component->getId() ?>").val(null);
            }
        });

        $("#<?= $component->getId() ?>").chosen({
            max_selected_options: 5<?php if ($component->getOption('required', false) === false) : ?>,
            allow_single_deselect: true<?php endif; ?>
        });

        var <?= $component->getId() ?>_items = Ice.jsonToObject('<?= $component->getItemsGroupJson() ?>');

        if ($("#<?= $component->getId('many') ?>").val()) {
            $("#<?= $component->getId() ?> option").remove()
            <?= $component->getId() ?>_items[$("#<?= $component->getId('many') ?>").val()].forEach(function (item, key) {
                $("#<?= $component->getId() ?>").append('<option>' + Ice.replaceRender('<?= $component->getItemTitle() ?>', item) + '</option>');
            });
            $("#<?= $component->getId() ?>").trigger("chosen:updated");
            $("#<?= $component->getId() ?>").closest('div.form-group').show();
        } else {
            $("#<?= $component->getId() ?>").closest('div.form-group').hide();

            $("#<?= $component->getId() ?>").val(null);
        }
    });
</script>