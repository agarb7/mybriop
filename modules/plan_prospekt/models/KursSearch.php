<?php
namespace app\modules\plan_prospekt\models;

use app\enums2\TipKursa;
use app\records\Kurs;
use app\validators\ChasyObucheniyaValidator;
use app\validators\DateValidator;
use app\validators\NazvanieValidator;
use app\validators\SqueezeLineFilter;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\Sort;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class KursSearch extends Kurs
{
    public $kategorii_slushatelej;
    public $nachnutsya_posle;
    public $zakonchatsya_do;

    public function attributeLabels()
    {
        return [
            'kategorii_slushatelej' => 'Категории слушателей',
            'nazvanie' => 'Название',
            'raschitano_chasov' => 'Количество часов',
            'rukovoditel' => 'Руководитель',
            'tip' => 'Тип',
            'nachnutsya_posle' => 'Начнутся после',
            'zakonchatsya_do' => 'Закончатся до',
        ];
    }

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
        $query = Kurs::find()
            ->joinWith('kategorii_slushatelej_rel')
            ->joinWith('rukovoditel_rel')
            ->groupBy(['kurs.id', 'fiz_lico.id'])
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

        $sort = new Sort([
            'attributes' => [
                'nazvanie',
                'formy_obucheniya',
                'raschitano_slushatelej',
                'finansirovanie',
                'tip',
                'raschitano_chasov',
                'rukovoditel_rel' => [
                    'asc' => ['fiz_lico.familiya' => SORT_ASC, 'fiz_lico.imya' => SORT_ASC, 'fiz_lico.otchestvo' => SORT_ASC],
                    'desc' => ['fiz_lico.familiya' => SORT_DESC, 'fiz_lico.imya' => SORT_DESC, 'fiz_lico.otchestvo' => SORT_DESC],
                ],
                'vremya_provedeniya' => [
                    'asc' => ['least([[ochnoe_nachalo]], [[zaochnoe_nachalo]])' => SORT_ASC],
                    'desc' => ['least([[ochnoe_nachalo]], [[zaochnoe_nachalo]])' => SORT_DESC],
                ]
            ],
        ]);

        return new ActiveDataProvider([
            'sort' => $sort,
            'query' => $query
        ]);
    }

    public function attributes()
    {
        return ArrayHelper::merge(
            ActiveRecord::attributes(),
            Model::attributes()
        );
    }

    public function hasValues()
    {
        foreach ($this->attributes as $attribute) {
            if ($attribute !== null)
                return true;
        }

        return false;
    }
}