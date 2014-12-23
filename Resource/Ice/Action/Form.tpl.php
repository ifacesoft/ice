<form class="form-inline" role="form" action="#" method="post">
    <input type="hidden" name="formClass" value="<?= $formClass ?>"/>
    <input type="hidden" name="formKey" value="<?= $formKey ?>"/>
    <input type="hidden" name="filterFields" value="<?= $filterFields ?>"/>
    <input type="hidden" name="redirect" value="<?= $redirect ?>"/>
    <?php if ($groupping) { ?>
        <?php foreach ($fields as $type => $group) {
            switch ($type) {
                case 'Number':
                case 'Checkbox':
                    $cols = 2;
                    break;
                case 'Textarea':
                    $cols = 8;
                    break;
                default:
                    $cols = 4;
            } ?>
            <ul class="pull-left" style="width: 100%">
                <?php foreach ($group as $field) { ?>
                    <li class="pull-left" style="margin: 10px 20px 0 0; width: <?= $cols * 77 ?>px;">
                        <?= $field ?>
                    </li>
                <?php } ?>
            </ul>
        <?php } ?>
    <?php } else { ?>
        <ul class="pull-left" style="width: 100%">
            <?php foreach ($fields as $field) { ?>
                <li class="pull-left" style="margin: 10px 20px 0 0; width: <?= 3 * 77 ?>px;">
                    <?= $field ?>
                </li>
            <?php } ?>
        </ul>
    <?php } ?>
    <div class="btn-group">

        <input class="btn btn-primary" type="button" value="<?= $submitTitle ?>"
               onclick="Ice_Form.submit($(this), '<?= $submitActionName ?>', '<?= $reRenderClosest ?>', <?= $params ?>)"/>
    </div>
</form>