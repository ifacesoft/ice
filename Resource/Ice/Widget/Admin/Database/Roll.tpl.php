<?php $parts = reset($result) ?>
<div class="row">
    <div class="col-md-9"><?= $widget->renderPart($parts['table']) ;?></div>
    <div class="col-md-3" style="background-color: #f5f5f5;"><?= $widget->renderPart($parts['filter']) ;?></div>
</div>