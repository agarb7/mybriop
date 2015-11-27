<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets;

$this->registerAssetBundle(\yii\web\YiiAsset::className(), \yii\web\View::POS_HEAD);
$this->registerAssetBundle(\yii\bootstrap\BootstrapAsset::className(), \yii\web\View::POS_HEAD);
$this->registerAssetBundle(\app\assets\AppAsset::className(), \yii\web\View::POS_HEAD);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <link rel="icon" type="image/png" href="/img/fav.png" />
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <?= Html::csrfMetaTags() ?>
</head>
<body>
<?php $this->beginBody() ?>
<div class="navbar navbar-inverse mybar navbar-static-top">
    <a class="heading" href="/"><?=Html::img('@web/img/logo.png')?></a>
    <span class="inline-block logo-text" ><a style="color: #fff" href="/">Бурятский республиканский<br>институт образовательной политики</a></span>

    <div class="usercontrol">
        <ul class="usercontrol-actions">
            <li><a href="<?= Url::to(['polzovatel/vhod']) ?>">Вход</a></li>
        </ul>
    </div>
</div>

<div class="container">
    <div class="row vertical-center-row">
        <div class="col">
            <?=$content?>
        </div>
    </div>
</div>

<footer class="footer myfooter">&copy; <?= date('Y'); ?> БРИОП</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
