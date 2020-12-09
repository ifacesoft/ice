<div<?php if (!$component->getOption('resetFormClass')) : ?> class="form-group"<?php endif; ?>>
    <label
        for="<?= $component->getId() ?>"
        class="control-label<?php if ($component->getOption('srOnly')) : ?> sr-only<?php endif; ?><?php if ($component->getHorizontal()) : ?> col-md-<?= $component->getHorizontal() ?><?php endif; ?>"
    ><?= $component->getLabel() ?></label>
    <?php if ($component->getHorizontal()) : ?>
    <div class="col-md-<?= 12 - $component->getHorizontal() ?>"><?php endif; ?>
        <div class='input-group date' <?= $component->getIdAttribute('from') ?>>
            <input
                <?= $component->getClassAttribute($component->getOption('resetFormClass', false) ? '' : 'form-control') ?>
                type="text"
                name="<?= $component->getFromName() ?>"
                value="<?= $component->get($component->getFromName(), '') ?>"
                <?= $component->getPlaceholderAttribute() ?>
                <?= $component->getEventAttributesCode() ?>
                <?php if ($component->getOption('disabled', false)) : ?>disabled="disabled"<?php endif; ?>
                <?php if ($component->getOption('readonly', false)) : ?>readonly="readonly"<?php endif; ?>
                <?php if ($component->getOption('required', false)) : ?>required="required"<?php endif; ?>
                <?php if ($component->getOption('autofocus', false)) : ?>autofocus="autofocus"<?php endif; ?>
            >
            <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
        </div>
        <div class='input-group date' <?= $component->getIdAttribute('to') ?>>
            <input
                <?= $component->getClassAttribute($component->getOption('resetFormClass', false) ? '' : 'form-control') ?>
                type="text"
                name="<?= $component->getToName() ?>"
                value="<?= $component->get($component->getToName(), '') ?>"
                <?= $component->getPlaceholderAttribute() ?>
                <?= $component->getEventAttributesCode() ?>
                <?php if ($component->getOption('disabled', false)) : ?>disabled="disabled"<?php endif; ?>
                <?php if ($component->getOption('readonly', false)) : ?>readonly="readonly"<?php endif; ?>
                <?php if ($component->getOption('required', false)) : ?>required="required"<?php endif; ?>
                <?php if ($component->getOption('autofocus', false)) : ?>autofocus="autofocus"<?php endif; ?>
            >
            <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
        </div>
        <?php if ($component->getHorizontal()) : ?></div><?php endif; ?>
    <script>
        $(function () {
            $("#<?= $component->getId('from') ?>").datepicker({
                format: '<?= $component->getDateMomentFormat() ?>',
                language: '<?= $component->getLocale() ?>'
            });
            $("#<?= $component->getId('to') ?>").datepicker({
                useCurrent: false,
                format: '<?= $component->getDateMomentFormat() ?>',
                language: '<?= $component->getLocale() ?>'
            });
            $("#<?= $component->getId('from') ?>").on("dp.change", function (e) {
                $("#<?= $component->getId('to') ?>").data("DateTimePicker").minDate(e.date);
            });
            $("#<?= $component->getId('to') ?>").on("dp.change", function (e) {
                $("#<?= $component->getId('from') ?>").data("DateTimePicker").maxDate(e.date);
            });
        });
    </script>
</div>
