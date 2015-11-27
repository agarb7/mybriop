<?php
use yii\mail\MessageInterface;

/**
 * @var MessageInterface $message
 */
$message->setCharset(\Yii::$app->charset);
$message->setFrom(['my.briop@mail.ru' => 'БРИОП']);

$this->beginPage();
?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=<?= $message->getCharset() ?>" />
        <?php $this->head() ?>
    </head>
    <body>
    <?php $this->beginBody() ?>
    <?= $content ?>
    <?php /*<div class="footer">Счастья и свободы!</div>*/ ?>
    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>
