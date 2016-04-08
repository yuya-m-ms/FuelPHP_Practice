<section>
    <ul class="nav nav-pills">
        <li class='<?php echo Arr::get($subnav, "index" ); ?>'>
            <?php echo Html::anchor('s3/index','Index');?>
        </li>
    </ul>
    <p>
        Index
    </p>
</section>
<section>
    <h2>Buckets</h2>
    <pre><?= $buckets ?></pre>
    <br>
    <h2>Objects</h2>
    <pre><?= $objects ?></pre>
    <br>
    <h2>Object — test.json</h2>
    <pre><?= $test_json ?></pre>
</section>