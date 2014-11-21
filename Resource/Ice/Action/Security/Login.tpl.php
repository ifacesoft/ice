<div class="row">
    <div class="col-md-4 col-md-offset-4">
        <ul class="nav nav-pills">
            <li class="active">
                <a href="#" onclick="return false;"><?= $resource->get('Login form') ?></a></li>
            <li>
                <a href="/security/register"><?= $resource->get('Register form', null, 'Ice\Action\Security_Register') ?></a>
            </li>
        </ul>
        <?= $Form[0] ?>
    </div>
</div>
