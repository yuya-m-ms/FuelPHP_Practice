<section>
    <ul class="nav nav-pills">
        <li class='<?= Arr::get($subnav, "index" ); ?>'>
            <?= Html::anchor('s3/index','Index');?>
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
<br>
<br>
<br>