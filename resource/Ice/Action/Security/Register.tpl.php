<div class="row">
    <div class="col-md-4 col-md-offset-4">
        <ul class="nav nav-tabs">
            <li>
                <a href="<?= Ice\Core\Route::getInstance('ice_security_login')->getUrl() ?>"><?= $resource['Security_Login']->get('Login form', null, 'Ice\Action\Security_Login') ?></a>
            </li>
            <li class="active">
                <a href="#" onclick="return false;"><?= $resource['Security_Login']->get('Register form') ?></a>
            </li>
        </ul>
        <div class="panel panel-default">
            <div class="panel-body">
                <h2 class="form-signin-heading"><?= $resource['Security_Login']->get('sign out') ?></h2>
                <?= $form ?>
            </div>
        </div>
    </div>
</div>
