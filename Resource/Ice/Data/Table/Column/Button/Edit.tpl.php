<button class="btn btn-info btn-xs"
        onclick="Ice_Form.modal(
            $(this),
            '<?= $scheme['option']['modelClassName'] ?>',
        <?= $value ?>,
            '<?= $scheme['option']['submitActionName'] ?>',
        <?= $scheme['option']['formFilterFields'] ?>,
        <?= $scheme['option']['grouping'] ?>,
            '<?= $scheme['option']['submitTitle'] ?>',
            '<?= $scheme['option']['template'] ?>',
        <?= $scheme['option']['params'] ?>,
            '<?= $scheme['option']['reRenderClosest'] ?>',
        <?= $scheme['option']['reRenderActionNames'] ?>
            ); return false;">
    <span aria-hidden="true" class="glyphicon glyphicon-edit"> </span>
</button>
