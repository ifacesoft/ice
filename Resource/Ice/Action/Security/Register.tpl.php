<div class="row">
    <div class="col-md-4 col-md-offset-4">
        <ul class="nav nav-pills">
            <li>
                <a href="/security/login">
                    <?= $container->getResource()->get('Login form') ?>
                </a>
            </li>
            <li class="active">
                <a href="#" onclick="return false;">
                    <?= $container->getResource()->get('Register form') ?>
                </a>
            </li>
        </ul>
        <?= $Form[0] ?>
    </div>
</div>
