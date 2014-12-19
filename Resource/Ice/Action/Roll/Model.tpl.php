<table class="table table-striped table-bordered table-hover table-condensed">
    <tr>

    </tr>
    <?php foreach ($rows as $item) { ?>
        <tr>
            <?php foreach ($item as $value) { ?>
                <td><?= $value ?></td>
            <?php } ?>
            <td>
                <button type="button" class="btn btn-xs btn-default"
                        onclick="Tp_Action_Roll.editForm('{$message.messages_pk}');">
                    <span class="glyphicon glyphicon-edit"></span>
                </button>
            </td>
            <td>
                <button type="button" class="btn btn-xs btn-danger"
                        onclick="Tp_Action_Roll.delete('{$message.messages_pk}');">
                    <span class="glyphicon glyphicon-remove-circle"></span>
                </button>
            </td>
        </tr>
    <?php } ?>
</table>
