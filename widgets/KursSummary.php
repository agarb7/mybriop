<?php
namespace app\widgets;

use app\entities\Kurs;
use yii\base\NotSupportedException;
use yii\bootstrap\Widget;

class KursSummary extends Widget
{
    /**
     * @var Kurs
     */
    public $model;

    const SCENARIO_DETAIL = 1;
    const SCENARIO_BRIEF =2;

    public $scenario = self::SCENARIO_DETAIL;

    public function run()
    {
        if ($this->scenario === self::SCENARIO_DETAIL)
            return $this->render('kurs_summary_detail', ['model' => $this->model]);

        throw new NotSupportedException('Scenario is not implemented yet');
    }
}