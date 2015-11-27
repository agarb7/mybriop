<?php
namespace app\entities;

use app\enums\StatusZapisiNaKurs;
use app\enums\TipKursa;
use app\helpers\ArrayHelper;
use app\helpers\Val;
use Yii;
use DateTime;

class KursExtended extends Kurs
{
    public $zapisanoSlushatelej;
    public $isUserZapisan;
    public $userStatusKursa;

    public function isCanceledByRukovoditel()
    {
        return $this->userStatusKursa === StatusZapisiNaKurs::asSql(StatusZapisiNaKurs::OTMENENO_RUKOVODITELEM);
    }

    public function isStarted()
    {
        return (new DateTime) >= $this->konecAsDate;
    }

    public function isInDuration()
    {
        $now = new DateTime;
        return $this->nachaloAsDate <= $now && $now <= $this->konecAsDate;
    }

    public function isEnded()
    {
        return (new DateTime) > $this->konecAsDate;
    }

    //todo refactor as rule
    public function userCanNotChangeZapisReason($change)
    {
        if ($change === StatusZapisiNaKurs::OTMENA_ZAPISI) {
            if ($this->isEnded())
                return 'Отменить нельзя: уже закончился';

            if ($this->isStarted())
                return 'Отменить нельзя: уже начался';

        } elseif ($change === StatusZapisiNaKurs::ZAPIS) {
            if ($this->isCanceledByRukovoditel())
                return 'Запись отменена руководителем';
        }

        return null;
    }

    public static function tableName()
    {
        return 'kurs';
    }

    public function getNazvaniyaKategorijSlushatelej()
    {
        return ArrayHelper::getColumn($this->kategoriiSlushatelejRel, 'nazvanie', false);
    }

    /**
     * @inheritdoc
     */
    public static function find()
    {
        $cols = [
            '{{kurs}}.*',
            'zapisanoSlushatelej' => 'coalesce(count({{slushatel}}.[[id]]),0)',
            'isUserZapisan' => self::userZapisanCond(),
            'userStatusKursa' => 'kurs_tek_polzovatelya.status'
        ];

        return parent::find()
            ->select($cols)
            ->joinWith([
                'slushateliRel' => function ($q) {
                    $q->from(['slushatel' => 'fiz_lico']);
                }
            ])
            ->joinWith([
                'kursyFizLicaRel' => function ($q) {
                    $q
                        ->from(['kurs_tek_polzovatelya' => 'kurs_fiz_lica'])
                        ->onCondition([
                            'kurs_tek_polzovatelya.fiz_lico' => Val::of(Yii::$app->user->fizLico, 'id')
                        ]);
                }
            ])
            ->groupBy(['kurs.id', 'kurs_tek_polzovatelya.id'])
            ->with('rukovoditelRel', 'kategoriiSlushatelejRel');
    }

    public static function findTip($tip)
    {
        return static::find()->where(['tip' => TipKursa::asSql($tip)]);
    }

    public static function findMyAsSlushatel()
    {
        return static::find()->where(self::userZapisanCond());
    }

    public static function findMyAsRukovoditel()
    {
        $fiz_lico = Yii::$app->user->fizLico;

        if (!$fiz_lico)
            return null;

        return static::find()->where(['rukovoditel' => $fiz_lico->id]);
    }

    private static function userZapisanCond()
    {
        $q_status = Yii::$app->db->quoteValue(StatusZapisiNaKurs::asSql(StatusZapisiNaKurs::ZAPIS));
        return "{{kurs_tek_polzovatelya}}.[[status]] = $q_status";
    }

}
