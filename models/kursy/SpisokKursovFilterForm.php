<?php
namespace app\models\kursy;

use app\entities\EntityQuery;
use app\entities\KursExtended;
use app\validators\ChasyObucheniyaValidator;
use app\validators\NazvanieValidator;
use app\validators\SqueezeLineFilter;
use app\widgets\DeprecatedDatePicker;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use DateTime;
use Yii;

class SpisokKursovFilterForm extends Model
{
    public $kategoriiSlushatelej;
    public $nazvanie;
    public $rukovoditel;
    public $chasy;
    public $nachalo;
    public $konec;
    public $planProspektGod;

    public function attributeLabels()
    {
        return [
            'kategoriiSlushatelej' => 'Категории слушателей',
            'nazvanie' => 'Наименование программы',
            'rukovoditel' => 'Руководитель',
            'chasy' => 'Объем часов',
            'nachalo' => 'Срок проведения: с',
            'konec' => 'по',
            'planProspektGod' => 'План проспект'
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

            ['planProspektGod', 'date'],
        ];
    }

    public function formName()
    {
        return '';
    }

    public function search($tip, $data)
    {
        /**
         * @var EntityQuery $query
         */
        $query = KursExtended::findTip($tip);
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
                ['kurs.raschitano_chasov' => $this->chasy],
                ['kurs.plan_prospekt_god' => $this->planProspektGod]
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

    public static function planProspektGodItems()
    {
        $formatter = Yii::$app->formatter;
        return [
            '' => '',
            $formatter->asDate('2015-01-01') => '2015',
            $formatter->asDate('2016-01-01') => '2016',
            $formatter->asDate('2017-01-01') => '2017'
        ];
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