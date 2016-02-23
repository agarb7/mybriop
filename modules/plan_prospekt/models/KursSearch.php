<?php
namespace app\modules\plan_prospekt\models;

use app\enums2\TipKursa;
use app\validators\ChasyObucheniyaValidator;
use app\validators\NazvanieValidator;
use app\validators\SqueezeLineFilter;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class KursSearch extends \app\records\Kurs
{
    public $kategorii_slushatelej;
    public $nachalo;
    public $konec;

    public function rules()
    {
        return [
            ['tip', 'in', 'range' => TipKursa::items()],

            ['kategorii_slushatelej', 'each', 'rule' => ['integer']],

            ['plan_prospekt_god', 'date'],

            ['nazvanie', SqueezeLineFilter::className()], //todo include in Nazvanie
            ['nazvanie', NazvanieValidator::className()],

            ['rukovoditel', 'integer'],

            ['raschitano_chasov', ChasyObucheniyaValidator::className()],

            ['nachalo', 'date'],

            ['konec', 'date'],
        ];
    }

    public function search($params)
    {
        $query = KursForm::find()
            ->with('kategorii_slushatelej_rel')
            ->orderBy('id')
            ->filterWhere(['extract(year from [[plan_prospekt_god]])' => ArrayHelper::getValue($params, 'year')]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);

        return $dataProvider;
    }
}