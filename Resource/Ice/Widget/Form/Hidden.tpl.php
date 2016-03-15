<input id="<?= $partId ?>"
       type="hidden"
       name="<?= $name ?>"
       value="<?= isset($params[$value]) ? htmlentities($params[$value], ENT_QUOTES) : '' ?>"
>