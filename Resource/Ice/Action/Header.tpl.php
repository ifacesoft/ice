<div class="container">
    <div class="row" style="background: url(/resource/img/logo/ice62.jpg) no-repeat left; height: 62px;">
        <div class="col-md-4" onclick="location.href='/'" style="cursor: pointer;padding-left: 70px;">
            <div>
                <span style="font-family: DaxlineMedium;font-size: 20px;">Open Source</span>
                <span style="font-family: DaxlineThin;font-size: 20px;">PHP Framework</span>
            </div>
            <div>
                <span style="font-size: 19px;">Best Practices Inside</span>
            </div>

        </div>
        <div class="col-md-6">
            <?= $Header_Menu[0] ?>
        </div>
        <div class="col-md-2">
<!--            --><?php //if (isset($user) && $user) : ?>
<!--                <br>-->
<!--                <button onclick="location.href='/ice/security/logout'" class="btn btn-default">-->
<!--                    <span class="glyphicon glyphicon-log-out"></span>-->
<!--                    Выйти-->
<!--                </button>-->
<!--            --><?php //else : ?>
<!--                <br>-->
<!--                <button onclick="location.href='/ice/security/login';" class="btn btn-default">-->
<!--                    <span class="glyphicon glyphicon-log-in"></span>-->
<!--                    Войти-->
<!--                </button>-->
<!--            --><?php //endif; ?>
        </div>
    </div>
</div>
<div
    style="background-color: #6f5499; background-image: linear-gradient(to bottom, #563d7c 0px, #6f5499 100%)">
    <div class="container" style="padding: 5px;">
        <?php foreach ($flags as $flag) : ?>
            <img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                 class="flag flag-<?= $flag['country'] ?>" style="cursor: pointer;"
                 alt="<?= $flag['lang'] ?>" onclick="location.href='/ice/locale/<?= $flag['locale']?>';"
                 data-toggle="tooltip" data-placement="top" title="<?= $flag['lang'] ?>"/>
        <?php endforeach; ?>
    </div>
</div>