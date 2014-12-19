<h3>Default template</h3>
<?php if (isset($errors)) {
    print_r($errors);
} ?> <br><?php if (isset($main)) {
    echo $main;
} ?>