<?php
namespace app\modules\plan_prospekt\widgets;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class ActionColumn extends \yii\grid\ActionColumn
{
    public $template = '{update} {delete} {iup}';

    public function init()
    {
        parent::init();

        if (!isset($this->buttonOptions['class']))
            $this->buttonOptions['class'] = ['btn', 'btn-action'];

        if (!isset($this->header)) {
            $url = $this->createUrl('create', null, null, null);
            $this->header = $this->createButton($url, 'Создать', ['btn-create']);
        }
    }

    /**
     * Initializes the default button rendering callbacks.
     */
    protected function initDefaultButtons()
    {
        if (!isset($this->buttons['update'])) {
            $this->buttons['update'] = function ($url) {
                return $this->createButton($url, 'Редактировать', ['btn-update']);
            };
        }
        if (!isset($this->buttons['delete'])) {
            $this->buttons['delete'] = function ($url) {
                return $this->createButton($url, 'Удалить', ['btn-delete']);
            };
        }
        if (!isset($this->buttons['iup'])) {
            $this->buttons['iup'] = function ($url, $model) {
                return $this->createButton($url, 'ИУП', ['btn-iup']);
            };
        }
    }

    private function createButton($url, $text, $class)
    {
        $options = ArrayHelper::merge([
            'title' => $text,
            'aria-label' => $text,
            'class' => $class,
            'data-pjax' => 0,
        ], $this->buttonOptions);

        return Html::a($text, $url, $options);
    }
}