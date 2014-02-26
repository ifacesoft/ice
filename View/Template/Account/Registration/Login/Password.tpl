<form role="form" class="Account Account_form" action="/registration/"
      onsubmit="Account.register($(this)); return false;">
    <input type="hidden" name="accountType" value="{$accountType}"/>

    <div class="form-group">
        <label for="login">Логин</label>
        <input type="login" class="form-control" id="login" name="login"
               placeholder="Введите логин">
    </div>
    <div class="form-group">
        <label for="password">Пароль</label>
        <input type="password" class="form-control" id="password" name="password"
               placeholder="Введите пароль">
    </div>
    <input type="submit" class="btn btn-primary pull-right" value="Зарегистрировать"/>

    <div class="clearfix"></div>
</form>