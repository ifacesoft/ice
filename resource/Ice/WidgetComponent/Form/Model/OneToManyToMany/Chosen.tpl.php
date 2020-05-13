<div<?php if (!$component->getOption('resetFormClass')) : ?> class="form-group"<?php endif; ?>>
    <label
            for="<?= $component->getId() ?>_many"
            class="control-label<?php if ($component->getOption('srOnly')) : ?> sr-only<?php endif; ?><?php if ($component->getHorizontal()) : ?> col-md-<?= $component->getHorizontal() ?><?php endif; ?>"
    ><?= $component->getManyLabel() ?><?php if ($component->getOption('required', false)) : ?> <sup style="color: red;">*</sup><?php endif; ?>
    </label>
    <?php if ($component->getHorizontal()) : ?>
    <div class="col-md-<?= 12 - $component->getHorizontal() ?>"><?php endif; ?>
        <select <?= $component->getIdAttribute('many') ?>
            <?php
            $manyItemKey = $component->getManyItemKey();
            $manyItemValue = $component->getManyValue();
            $manyItemTitle = $component->getManyItemTitle();

            if (is_array($manyItemTitle)) {
                $manyItemTitle = reset($manyItemTitle);
            }
            ?>
            <?= $component->getClassAttribute($component->getOption('resetFormClass', false) ? '' : 'form-control') ?>
                name="<?= $manyItemKey ?>_many<?php if ($component->getOption('multiple', false)) : ?>[]<?php endif; ?>"
                <?php if ($component->getOption('multiple', false)) : ?>multiple="multiple"<?php endif; ?>
                <?php if (!empty($component->getOption('size'))) : ?>size="<?= $component->getOption('size') ?>"<?php endif; ?>
            <?= $component->getPlaceholderManyAttribute('data-placeholder') ?>
                <?php if ($component->getOption('disabled', false)) : ?>disabled="disabled"<?php endif; ?>
                <?php if ($component->getOption('readonly', false)) : ?>readonly="readonly"<?php endif; ?>
                <?php if ($component->getOption('required', false)) : ?>required="required"<?php endif; ?>
                <?php if ($component->getOption('autofocus', false)) : ?>autofocus="autofocus"<?php endif; ?>
        >
            <?php
            foreach ($component->getManyItems() as $item) : ?>
                <?php
                $selected = (string)$item[$manyItemKey] === html_entity_decode($manyItemValue);
                $disabled = isset($item['disabled']) && $item['disabled'] === true;
                ?>
                <option value="<?= htmlentities($item[$manyItemKey]) ?>"
                        <?php if ($selected) : ?>selected="selected"<?php endif; ?>
                        <?php if ($disabled) : ?>disabled="disabled"<?php endif; ?>
                ><?= $item[$manyItemTitle] ?></option>
            <?php endforeach; ?>
        </select>
        <?php if ($component->getHorizontal()) : ?></div><?php endif; ?>
</div>

<?php require __DIR__ . '/../../../FormElement/Chosen.tpl.php' ?>

<?php
$itemKey = $component->getItemKey();
$itemTitle = $component->getItemTitle();

if (is_array($itemKey)) {
    $itemKey = reset($itemKey);
}
if (is_array($itemTitle)) {
    $itemTitle = reset($itemTitle);
}
?>

<script>
    $(function () {
        $("#<?= $component->getId('many') ?>").chosen({
            <?php if ($component->getOption('required', false) === false) : ?>allow_single_deselect: true,<?php endif; ?>
            max_selected_options: 5,
            no_results_text: 'Oops, nothing found!',
            placeholder_text_single: 'Select an Option',
            search_contains: true
        }).change(function () {
            if ($("#<?= $component->getId('many') ?>").val()) {
                $("#<?= $component->getId() ?> option").remove()
                $.each(<?= $component->getId() ?>_items[$("#<?= $component->getId('many') ?>").val()], function (key, item) {
                    if (item['<?= $itemKey ?>'] == '<?= html_entity_decode($component->getValue()) ?>') {
                        $("#<?= $component->getId() ?>").append('<option value="' + item['<?= $itemKey ?>'] + '" selected="selected">' + Ice.replaceRender('<?= $itemTitle ?>', item) + '</option>');
                    } else {
                        $("#<?= $component->getId() ?>").append('<option value="' + item['<?= $itemKey ?>'] + '">' + Ice.replaceRender('<?= $itemTitle ?>', item) + '</option>');
                    }
                });
                $("#<?= $component->getId() ?>").trigger("chosen:updated");
                $("#<?= $component->getId() ?>").closest('div.form-group').show();
            } else {
                $("#<?= $component->getId() ?>").closest('div.form-group').hide();

                $("#<?= $component->getId() ?>").val(null);
            }
        });

        var <?= $component->getId() ?>_items = Ice.jsonToObject('<?= $component->getItemsGroupJson() ?>');

        if ($("#<?= $component->getId('many') ?>").val()) {
            $("#<?= $component->getId() ?> option").remove();
            $.each(<?= $component->getId() ?>_items[$("#<?= $component->getId('many') ?>").val()], function (key, item) {
                if (item['<?= $itemKey ?>'] == '<?= html_entity_decode($component->getValue()) ?>') {
                    $("#<?= $component->getId() ?>").append('<option value="' + item['<?= $itemKey ?>'] + '" selected="selected">' + Ice.replaceRender('<?= $itemTitle ?>', item) + '</option>');
                } else {
                    $("#<?= $component->getId() ?>").append('<option value="' + item['<?= $itemKey ?>'] + '">' + Ice.replaceRender('<?= $itemTitle ?>', item) + '</option>');
                }
            });
            $("#<?= $component->getId() ?>").trigger("chosen:updated");
            $("#<?= $component->getId() ?>").closest('div.form-group').show();
        } else {
            $("#<?= $component->getId() ?>").closest('div.form-group').hide();

            $("#<?= $component->getId() ?>").val(null);
        }
    });
</script>