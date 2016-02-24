<?php
namespace app\modules\plan_prospekt\models;

use app\enums2\TipKursa;
use app\validators\ChasyObucheniyaValidator;
use app\validators\DateValidator;
use app\validators\NazvanieValidator;
use app\validators\SqueezeLineFilter;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class KursSearch extends \app\records\Kurs
{
    public $kategorii_slushatelej;
    public $nachnutsya_posle;
    public $zakonchatsya_do;

    public function rules()
    {
        return [
            ['tip', 'in', 'range' => TipKursa::items()],
            ['tip', 'default'],

            ['kategorii_slushatelej', 'each', 'rule' => ['integer']],
            ['kategorii_slushatelej', 'default'],

            ['nazvanie', 'filter', 'filter' => 'mb_strtolower'],
            ['nazvanie', SqueezeLineFilter::className()], //todo include in NazvanieValidator
            ['nazvanie', NazvanieValidator::className()],
            ['nazvanie', 'default'],

            ['rukovoditel', 'integer'],
            ['rukovoditel', 'default'],

            ['raschitano_chasov', ChasyObucheniyaValidator::className()],
            ['raschitano_chasov', 'default'],

            ['nachnutsya_posle', DateValidator::className(), 'timestampAttribute' => 'nachnutsya_posle'],
            ['nachnutsya_posle', 'default'],

            ['zakonchatsya_do', DateValidator::className(), 'timestampAttribute' => 'zakonchatsya_do'],
            ['zakonchatsya_do', 'default'],
        ];
    }

    public function search($params)
    {
        $query = KursForm::find()
            ->joinWith('kategorii_slushatelej_rel')
            ->groupBy('kurs.id')
            ->orderBy('kurs.id')
            ->filterWhere(['extract(year from {{kurs}}.[[plan_prospekt_god]])' => ArrayHelper::getValue($params, 'year')]);

        if ($this->load($params) && $this->validate()) {
            $query
                ->andFilterWhere(['like', 'lower(kurs.nazvanie)', $this->nazvanie])
                ->andFilterWhere(['kategoriya_slushatelya.id' => $this->kategorii_slushatelej])
                ->andFilterWhere(['tip' => $this->tip])
                ->andFilterWhere(['rukovoditel' => $this->rukovoditel])
                ->andFilterWhere(['raschitano_chasov' => $this->raschitano_chasov])
                ->andFilterWhere(['>=', 'least([[ochnoe_nachalo]], [[zaochnoe_nachalo]])', $this->nachnutsya_posle])
                ->andFilterWhere(['<=', 'greatest([[ochnoe_konec]], [[zaochnoe_konec]])', $this->zakonchatsya_do]);
        }

        return new ActiveDataProvider([
            'query' => $query
        ]);
    }
}