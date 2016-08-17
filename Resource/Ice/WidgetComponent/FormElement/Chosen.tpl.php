<?php require __DIR__ . '/../Form/ListBox.tpl.php' ?>

<script>
    $(function () {
        $("#<?= $component->getId() ?>").chosen({
            <?php if ($component->getOption('required', false) === false) : ?>allow_single_deselect: true,<?php endif; ?>
            <?php if ($component->getOption('multiple', false)) : ?>placeholder_text_multiple: '<?= $component->getPlaceholder() ?>',
            <?php else : ?>placeholder_text_single: '<?= $component->getPlaceholder() ?>',<?php endif; ?>
            max_selected_options: 5,
            no_results_text: 'Oops, nothing found!'
        });
    });
</script>