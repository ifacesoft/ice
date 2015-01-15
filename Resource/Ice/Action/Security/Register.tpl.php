<div class="row">
    <div class="col-md-4 col-md-offset-4">
        <ul class="nav nav-pills">
            <li>
                <a href="/ice/security/login"><?= $resource->get('Login form', null, 'Ice\Action\Security_Login') ?></a>
            </li>
            <li class="active">
                <a href="#" onclick="return false;"><?= $resource->get('Register form') ?></a>
            </li>
        </ul>
        <?= $Form[0] ?>
    </div>
</div>
