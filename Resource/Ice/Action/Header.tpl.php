<div class="container">
    <div class="row" style="background: url(/resource/img/logo/ice62.jpg) no-repeat left; height: 62px;">
        <div class="col-md-4 col-md-offset-1" onclick="location.href='/'" style="cursor: pointer;">
            <div>
                <span style="font-family: DaxlineMedium;font-size: 22px;">Open Source</span>
                <span style="font-family: DaxlineThin;font-size: 22px;">PHP Framework</span>
            </div>
            <div>
                <span style="font-size: 20px;">Best Practices Inside</span>
            </div>

        </div>
        <div class="col-md-5">
            <ul class="nav nav-pills">
                <li role="presentation">
                    <a href="/handbook"><?=$resource['Header']->get('Руководство')?></a>
                </li>
                <li role="presentation">
                    <a href="/resource/api/Ice/0.0">API</a>
                </li>
                <li role="presentation">
                    <a href="https://github.com/ifacesoft/Ice/tree/master">GitHub</a>
                </li>
                <li role="presentation">
                    <a href="https://bitbucket.org/dp_ifacesoft/ice">Bitbucket</a>
                </li>
                <li role="presentation" class="active">
                    <a href="/cookbook"><?=$resource['Header']->get('Полезные статьи')?></a>
                </li>
                <li role="presentation">
                    <a href="/faq">F.A.Q.</a>
                </li>
                <li role="presentation">
                    <a href="/blog"><?=$resource['Header']->get('Блог')?></a>
                </li>
                <li role="presentation">
                    <a href="/forum"><?=$resource['Header']->get('Форум')?></a>
                </li>
            </ul>
        </div>
        <div class="col-md-2">
            <?php if (isset($user) && $user) : ?>
            <br>
            <button onclick="location.href='/ice/security/logout'" class="btn btn-default">
                <span class="glyphicon glyphicon-log-out"></span>
                Выйти
            </button>
            <?php else : ?>
            <br>
            <button onclick="location.href='/ice/security/login';" class="btn btn-default">
                <span class="glyphicon glyphicon-log-in"></span>
                Войти
            </button>
            <?php endif; ?>
        </div>
    </div>
</div>