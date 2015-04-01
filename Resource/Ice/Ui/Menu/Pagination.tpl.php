<ul class="pagination <?php if (!empty($classes)) { ?><?= implode(' ', $classes) ?><?php } ?>"
    <?php if ($style) { ?>style="<?= $style ?>"<?php } ?>>
    <!--        <li>-->
    <!--            <a href="#" aria-label="Previous">-->
    <!--                <span aria-hidden="true">&laquo;</span>-->
    <!--            </a>-->
    <!--        </li>-->
    <?php if (isset($items['first'])) : ?>
        <li>
            <a href="#">
                <?= $items['first']['title'] ?> &lt;&lt;&lt;
            </a>
        </li>
    <?php endif; ?>

    <?php if (isset($items['fastPrev'])) : ?>
        <li>
            <a href="#">
                <?= $items['fastPrev']['title'] ?> &lt;&lt;
            </a>
        </li>
    <?php endif; ?>

    <!--    --><?php //if (isset($items['prev'])) : ?>
    <!--        <li>-->
    <!--            <a href="#">-->
    <!--                --><? //= $items['prev']['title'] ?><!-- &lt;-->
    <!--            </a>-->
    <!--        </li>-->
    <!--    --><?php //endif; ?>

    <?php if (isset($items['before2']) && $items['before2']['title'] > 1) : ?>
        <li class="disabled"><a style="border: none;"> &hellip; </a></li>
    <?php endif; ?>

    <?php if (isset($items['before2'])) : ?>
        <li>
            <a href="#">
                <?= $items['before2']['title'] ?>
            </a>
        </li>
    <?php endif; ?>

    <?php if (isset($items['before1'])) : ?>
        <li>
            <a href="#">
                <?= $items['before1']['title'] ?>
            </a>
        </li>
    <?php endif; ?>


    <li class="active">
        <a href="#">
            <?= $options['page'] ?> ( <?= $options['limit'] ?> / <?= $options['foundRows'] ?> )
        </a>
    </li>

    <?php if (isset($items['after1'])) : ?>
        <li>
            <a href="#">
                <?= $items['after1']['title'] ?>
            </a>
        </li>
    <?php endif; ?>

    <?php if (isset($items['after2'])) : ?>
        <li>
            <a href="#">
                <?= $items['after2']['title'] ?>
            </a>
        </li>
    <?php endif; ?>

    <?php if (isset($items['after2']) && $items['after2']['title'] < $options['pageCount'])  : ?>
        <li class="disabled"><a style="border: none;"> &hellip; </a></li>
    <?php endif; ?>

    <!--    --><?php //if (isset($items['next']))  : ?>
    <!--        <li>-->
    <!--            <a href="#">-->
    <!--                &gt; --><? //= $items['next']['title'] ?>
    <!--            </a>-->
    <!--        </li>-->
    <!--    --><?php //endif; ?>

    <?php if (isset($items['fastNext'])) : ?>
        <li>
            <a href="#">
                &gt;&gt; <?= $items['fastNext']['title'] ?>
            </a>
        </li>
    <?php endif; ?>

    <?php if (isset($items['last'])) : ?>
        <li>
            <a href="#">
                &gt;&gt;&gt; <?= $items['last']['title'] ?>
            </a>
        </li>
    <?php endif; ?>
    <!--        <li>-->
    <!--            <a href="#" aria-label="Next">-->
    <!--                <span aria-hidden="true">&raquo;</span>-->
    <!--            </a>-->
    <!--        </li>-->
</ul>
