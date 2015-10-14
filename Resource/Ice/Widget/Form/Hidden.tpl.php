<input id="<?= $widgetClassName ?>_<?= $widgetName ?>_<?= $name ?>"
       type="hidden"
       name="<?= $name ?>"
       value="<?= isset($params[$name]) ? $params[$name] : '' ?>"
>