<?php
foreach (reset($result) as $part) {
    $part->render($render);
}