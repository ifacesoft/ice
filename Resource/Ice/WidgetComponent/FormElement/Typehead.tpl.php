<div<?php if (!$component->getOption('resetFormClass')) : ?> class="form-group"<?php endif; ?>>
    <label
        for="<?= $component->getId() ?>"
        class="control-label<?php if ($component->getOption('srOnly')) : ?> sr-only<?php endif; ?><?php if ($component->getHorizontal()) : ?> col-md-<?= $component->getHorizontal() ?><?php endif; ?>"
    ><?= $component->getLabel() ?> <?php if ($component->getOption('required', false)) : ?><sup
            style="color: red;">*</sup><?php endif; ?></label>

    <?php if ($component->getHorizontal()) : ?>
    <div class="col-md-<?= 12 - $component->getHorizontal() ?>"><?php endif; ?>
        <input id="<?= $component->getId() ?>_typeahead"
            <?= $component->getClassAttribute($component->getOption('resetFormClass', false) ? '' : 'form-control') ?>
            type="text"
               name="<?= $component->getName() ?>_typeahead"
               value="<?= $component->getItemValue() ?>"
            <?= $component->getPlaceholderAttribute() ?>
            <?= $component->getEventAttributesCode() ?>
            <?php if ($component->getOption('disabled', false)) : ?>disabled="disabled"<?php endif; ?>
            <?php if ($component->getOption('readonly', false)) : ?>readonly="readonly"<?php endif; ?>
            <?php if ($component->getOption('required', false)) : ?>required="required"<?php endif; ?>
            <?php if ($component->getOption('autofocus', false)) : ?>autofocus="autofocus"<?php endif; ?>
        >
        <input <?= $component->getIdAttribute() ?> type="hidden"
               name="<?= $component->getName() ?>"
               value="<?= $component->getValue() ?>">
        <?php if ($component->getHorizontal()) : ?></div><?php endif; ?>
    <script>
        $(function () {
            var states = new Bloodhound({
                datumTokenizer: function (d) {
                    return Bloodhound.tokenizers.whitespace(d.<?= $component->getItemTitle() ?>);
                },
                queryTokenizer: Bloodhound.tokenizers.whitespace,
                local: <?= $component->getItemsJson() ?>
            });

            $('#<?= $component->getId() ?>_typeahead').typeahead({
                    hint: true,
                    highlight: true,
                    minLength: 1
                },
                {
                    name: 'states',
                    displayKey: '<?= $component->getItemTitle() ?>',
                    source: states,

                }).on('typeahead:select', function(ev, suggestion) {
                $('#<?= $component->getId() ?>').val(suggestion.<?= $component->getItemId() ?>)
            });
        });
    </script>
</div>
