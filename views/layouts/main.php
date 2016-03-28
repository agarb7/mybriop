<?php
use yii\helpers\Html;
use app\widgets\UserControl;
use app\widgets\Nav;

$this->registerAssetBundle(\yii\web\YiiAsset::className(), \yii\web\View::POS_HEAD);
$this->registerAssetBundle(\yii\bootstrap\BootstrapAsset::className(), \yii\web\View::POS_HEAD);
$this->registerAssetBundle(\app\assets\AppAsset::className(), \yii\web\View::POS_HEAD);
$this->registerAssetBundle(\yii\bootstrap\BootstrapPluginAsset::className(), \yii\web\View::POS_HEAD);

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
    <?= UserControl::widget(['user' => Yii::$app->user]) ?>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2">
            <?= Nav::widget(['options' => ['class' =>'nav-pills nav-stacked']]) ?>
        </div>
        <div class="col-md-10">
            <?=$content?>
        </div>
    </div>
</div>

<footer class="footer myfooter">&copy; <?= date('Y'); ?> БРИОП</footer>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
