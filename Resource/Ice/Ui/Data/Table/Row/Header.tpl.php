<tr>
    <th>#</th>
    <?php foreach ($columns as $column) : ?>
        <th><?= $column['title'] ?><a href="#"> &darr;&uarr; </a></th>
    <?php endforeach; ?>
</tr>