<?php
namespace app\widgets;

use yii\base\Widget;
use Yii;
use yii\bootstrap\ButtonGroup;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

class PlanProspektGodPanel extends Widget
{
    public $years = [null, '2015-01-01', '2016-01-01', '2017-01-01'];
    public $yearLabels = [
        null => 'все',
        '2015-01-01' => '2015',
        '2016-01-01' => '2016',
        '2017-01-01' => '2017',
    ];

    public function run()
    {
        $header = Html::tag('span', 'План проспект');

        $btnGroup = ButtonGroup::widget([
            'buttons' => array_map( function ($year) {
                return [
                    'label' => ArrayHelper::getValue($this->yearLabels, $year),
                    'tagName' => 'a',
                    'options' => [
                        'href' => Url::current(['god' => $year]),
                        'class' => ['active' => ($year === $this->getGod()) ? 'active' : null ]
                    ]
                ];
            }, $this->years)
        ]);

        return Html::tag('div', $header . $btnGroup);
    }

    private function getGod()
    {
        $params = Yii::$app->request->getQueryParams();
        return ArrayHelper::getValue($params, 'god');
    }
}