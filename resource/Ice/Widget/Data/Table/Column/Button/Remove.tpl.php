<button class="btn btn-danger btn-xs"
        onclick="Ice_Widget_Form.remove(
            $(this),
            '<?= $scheme['option']['modelClassName'] ?>',
        <?= $value ?>, <?= $scheme['option']['params'] ?>,
            '<?= $scheme['option']['reRenderClosest'] ?>',
        <?= $scheme['option']['reRenderActionNames'] ?>
            ); return false;">
    <span aria-hidden="true" class="glyphicon glyphicon-remove"> </span>
</button>
