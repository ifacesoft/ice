<tbody>
<tr>
    <?php foreach ($component->getOption('widget')->getParts() as $name => $part) { ?>
        <td><?= $component->getName() ?></td>
    <?php } ?>
</tr>
</tbody>
