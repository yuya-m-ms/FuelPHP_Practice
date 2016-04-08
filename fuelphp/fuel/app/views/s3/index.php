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
    <h2>Object — test.json</h2>
    <span>Body:</span>
    <pre><?= $test_json ?></pre>
    <h2>Object — test_put.json</h2>
    <span><?= Html::anchor($ObjectURL, 'ObjectURL') ?></span>
</section>
