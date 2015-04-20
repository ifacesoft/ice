<div>
    <button class="btn btn-primary"
            onclick="Ice_Widget_Form.modal($(this), '{$modelClassName}', {$pk}, '{$submitActionName}', {$formFilterFields}, {$grouping}, '{$submitTitle}', '{$template}', {$params}, '{$reRenderClosest}', {$reRenderActionNames});">{$submitTitle}</button>
</div>
<div class="modal fade">
    <div class="container">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                            class="sr-only">Close</span></button>
                <h4 class="modal-title">{$modelClassName}</h4>
            </div>
            <div class="modal-body">
                {$Form[0]}
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div><!-- /.modal -->