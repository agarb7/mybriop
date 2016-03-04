<?php
namespace app\modules\spisok_slushatelej\grid;

use yii\grid\DataColumn;
use yii\helpers\Html;
use Yii;

use app\base\Formatter;
use app\records\FizLico;

class ContactsColumn extends DataColumn
{
    /**
     * @param FizLico $model
     * @param mixed $key
     * @param int $index
     * @return string
     */
    public function renderDataCellContent($model, $key, $index)
    {
        /* @var $formatter Formatter */
        $formatter = Yii::$app->formatter;

        if ($model->telefon)
            $contacts[] = $formatter->asHtmlTelefon($model->telefon);

        if ($model->email)
            $contacts[] = $formatter->asEmail($model->email);

        return isset($contacts)
            ? Html::ul($contacts, ['encode' => false])
            : $this->grid->emptyCell;
    }
}