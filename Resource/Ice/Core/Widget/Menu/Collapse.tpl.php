<div class="panel panel-default">
    <div class="panel-heading" role="tab" id="heading_<?= $name ?>">
        <h4 class="panel-title">
            <a data-toggle="collapse" data-parent="#accordion_<?= $menuName ?>_<?= $token ?>" href="#collapse_<?= $name ?>" aria-expanded="true"
               aria-controls="collapse_<?= $name ?>">
                <?= $title ?>
            </a>
        </h4>
    </div>
    <div id="collapse_<?= $name ?>" class="panel-collapse collapse" role="tabpanel"
         aria-labelledby="heading_<?= $name ?>">
        <?php if (isset($options['content'])) : ?>
            <div class="panel-body">
                <?= $options['content'] ?>
            </div>
        <?php endif; ?>
    </div>
</div>