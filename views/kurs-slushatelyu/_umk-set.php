<?php
use app\entities\Umk;
use app\helpers\Val;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var $umkRecords Umk[]
 * @var $this View
 */
?>

<?php foreach ($umkRecords as $umk): ?>
    <div class="umk-block">
        <p class="opisanie"><?= Val::asText($umk, 'opisanie') ?></p>
        <?php switch ($umk->getType()) {
            case Umk::TYPE_FAJL:
                echo Html::a($umk->fajlRel->vneshnee_imya_fajla, $umk->fajlRel->uri);
                break;

            case Umk::TYPE_URI:
                echo Html::a('внешняя ссылка', $umk->uri);
                break;
        } ?>
    </div>
<?php endforeach ?>