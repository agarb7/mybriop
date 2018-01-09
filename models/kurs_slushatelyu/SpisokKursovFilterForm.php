<?php
namespace app\models\kurs_slushatelyu;

use app\entities\EntityQuery;
use app\entities\KursExtended;
use app\enums2\TipKursa;
use app\validators\ChasyObucheniyaValidator;
use app\validators\NazvanieValidator;
use app\validators\SqueezeLineFilter;
use app\widgets\DeprecatedDatePicker;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use DateTime;

class SpisokKursovFilterForm extends Model
{
    public $kategoriiSlushatelej;
    public $nazvanie;
    public $rukovoditel;
    public $chasy;
    public $nachalo;
    public $konec;

    public function attributeLabels()
    {
        return [
            'kategoriiSlushatelej' => 'Категории слушателей',
            'nazvanie' => 'Наименование программы',
            'rukovoditel' => 'Руководитель',
            'chasy' => 'Объем часов',
            'nachalo' => 'Срок проведения: с',
            'konec' => 'по'
        ];
    }

    public function rules()
    {
        return [
            ['kategoriiSlushatelej', 'each', 'rule' => ['integer']],

            ['nazvanie', SqueezeLineFilter::className()],
            ['nazvanie', NazvanieValidator::className()],

            ['rukovoditel', 'number'],

            ['chasy', ChasyObucheniyaValidator::className()],

            ['nachalo', 'date'],

            ['konec', 'date'],
        ];
    }

    public function formName()
    {
        return '';
    }

    public function search($tip, $data)
    {
        /* @var EntityQuery $query */
        $query = KursExtended::findTip($tip);

        if ($tip === TipKursa::PP || $tip === TipKursa::PO)
            $query->andWhere(['or', ['kurs.plan_prospekt_god' => '2018-01-01'], 'kurs.iup']);
        else
            $query->andWhere(['kurs.plan_prospekt_god' => '2018-01-01']);

        $query->orderBy('least([[ochnoe_nachalo]], [[zaochnoe_nachalo]])');

        if ($this->load($data) && $this->validate()) {
            if ($this->kategoriiSlushatelej) {
                $query
                    ->joinWith('kategoriiSlushatelejRel')
                    ->andWhere(['kategoriya_slushatelya.id' => $this->kategoriiSlushatelej]);
            }
            $query->andFilterWhere([
                'and',
                ['like', 'kurs.nazvanie', $this->nazvanie],
                ['kurs.rukovoditel' => $this->rukovoditel],
                ['kurs.raschitano_chasov' => $this->chasy]
            ]);

            if ($this->nachalo || $this->konec) {
                $nach = self::dateToQuotedSql($this->nachalo, true);
                $kon = self::dateToQuotedSql($this->konec);

                $query->andWhere(
                    "(coalesce(least(ochnoe_nachalo, zaochnoe_nachalo),'-infinity') <= $kon)"
                    . " and ($nach <= coalesce(greatest(ochnoe_konec, zaochnoe_konec),'infinity'))"
                );
            }
        }

        return new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
        ]);
    }

    private static function dateToQuotedSql($date, $isNachalo = false)
    {
        if (!$date)
            return $isNachalo ? "'-infinity'" : "'infinity'";

        if (is_string($date))
            $date = DeprecatedDatePicker::toDatetime($date);

        return \Yii::$app->db->quoteValue($date->format(DateTime::W3C));
    }
}