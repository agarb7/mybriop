<?php
namespace app\modules\spisok_slushatelej\grid;

use yii\grid\DataColumn;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use Yii;

use app\records\FizLico;
use app\records\RabotaFizLica;

class WorkColumn extends DataColumn
{
    /**
     * @param FizLico $model
     * @param mixed $key
     * @param int $index
     * @return string
     */
    public function renderDataCellContent($model, $key, $index)
    {
        foreach ($model->getRaboty_fiz_lica_rel()->each() as $rabota)
            $raboty[] = $this->renderRabota($rabota);

        return isset($raboty)
            ? Html::ul($raboty, ['class' => 'raboty', 'encode' => false])
            : $this->grid->emptyCell;
    }

    /**
     * @param RabotaFizLica $rabota
     * @return string
     */
    private function renderRabota($rabota)
    {
        $result[] = Html::tag(
            'span',
            ArrayHelper::getValue($rabota, 'organizaciya_rel.nazvanie'),
            ['class' => 'organizaciya']
        );

        if ($dolzhnosti = $this->renderDolzhnosti($rabota))
            $result[] = $dolzhnosti;

        if ($tel = $rabota->telefon)
            $result[] = Yii::$app->formatter->asHtmlTelefon($tel);

        return implode("\n", $result);
    }

    /**
     * @param RabotaFizLica $rabota
     * @return string
     */
    private function renderDolzhnosti($rabota)
    {
        foreach ($rabota->getDolzhnosti_fiz_lica_na_rabote_rel()->each() as $dolzhnost)
            $dolzhnosti[] = ArrayHelper::getValue($dolzhnost, 'dolzhnost_rel.nazvanie');

        return isset($dolzhnosti)
            ? Html::ul($dolzhnosti, ['class' => 'dolzhnosti', 'encode' => false])
            : '';
    }
}