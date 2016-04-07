<?php
namespace app\modules\spisok_slushatelej\grid;

use app\modules\spisok_slushatelej\models\Slushatel;
use yii\grid\DataColumn;

use Yii;

use app\components\Formatter;
use yii\helpers\Html;

class FioColumn extends DataColumn
{
    /**
     * @param Slushatel $model
     * @param mixed $key
     * @param int $index
     * @return string
     */
    public function renderDataCellContent($model, $key, $index)
    {
        $result = Yii::$app->formatter->asFizLico($model, Formatter::FIZ_LICO_FORMAT_FULL);

        if ($model->iup)
            $result .= '<br>' . Html::tag('span', 'ИУП', ['class' => 'label label-warning']);

        return $result;
    }
}