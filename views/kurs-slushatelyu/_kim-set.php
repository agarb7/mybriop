<?php
use app\entities\Kim;
use app\helpers\Val;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var $kimRecords Kim[]
 * @var $this View
 */
?>

<?php foreach ($kimRecords as $kim): ?>
    <div class="kim-block">
        <p class="opisanie"><?= Val::asText($kim, 'opisanie') ?></p>
        <?php switch ($kim->getType()) {
            case Kim::TYPE_TEXT:
                echo Html::a('показать текст', ['/kurs-slushatelyu/kim-tekst', 'kim' => $kim->hashids]);
                break;

            case Kim::TYPE_FAJL:
                echo Html::a($kim->fajlRel->vneshnee_imya_fajla, $kim->fajlRel->uri);
                break;

            case Kim::TYPE_URI:
                echo Html::a('внешняя ссылка', $kim->uri);
                break;
        } ?>
    </div>
<?php endforeach ?>