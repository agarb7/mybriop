<?php
namespace app\modules\spisok_slushatelej\grid;

use app\enums2\StatusKursaFizLica;
use app\modules\spisok_slushatelej\models\Slushatel;
use yii\grid\Column;
use yii\helpers\Html;

class ActionColumn extends Column
{
    /**
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        /* @var $model Slushatel */

        if ($model->iup && $model->status === StatusKursaFizLica::OZHIDAET_PODTVERZHDENIYA)
            return $this->renderIupButtons($model);

        if ($model->status === StatusKursaFizLica::ZAPISAN){
            $buttons = $this->renderCancelButton($model);
            $buttons .= $this->renderChangeDataSlushatelyaButton($model);
            return $buttons;
        }


        if ($model->status === StatusKursaFizLica::OTMENEN_BRIOP)
            return $this->renderSignUpAgainButton($model);

        return StatusKursaFizLica::getName($model->status);
    }

    /**
     * @param Slushatel $model
     * @return string
     */
    private function renderIupButtons($model)
    {
        return
            $this->renderButton('Принять ИУП', 'accept-iup', $model)
            . $this->renderButton('Отклонить ИУП', 'cancel-iup', $model);
    }

    /**
     * @param Slushatel $model
     * @return string
     */
    private function renderCancelButton($model)
    {
        return $this->renderButton('Отменить запись', 'cancel', $model);
    }

    /**
     * @param Slushatel $model
     * @return string
     */
    private function renderSignUpAgainButton($model)
    {
        return $this->renderButton('Записать снова', 'sign-up-again', $model);
    }

    /**
     * @param Slushatel $model
     * @return string
     */
    private function renderChangeDataSlushatelyaButton($model)
    {
        return $this->renderButton('Редактировать', 'edit-dannye-slushatelja', $model);
    }

    /**
     * @param string $text
     * @param string $action
     * @param Slushatel $model
     * @return string
     */
    private function renderButton($text, $action, $model)
    {
        return Html::a($text, [
            $action,
            'kurs' => $model->kurs,
            'fizLico' => $model->id
        ], [
            'class' => 'btn btn-default',
            'data-method' => 'post'
        ]);
    }
}