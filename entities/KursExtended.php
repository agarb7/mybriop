<?php
namespace app\entities;

use app\enums\StatusProgrammyKursa;
use app\enums2\StatusKursaFizLica;
use app\enums2\TipFinansirovaniya;
use app\enums2\TipKursa;
use app\helpers\ArrayHelper;
use app\helpers\Val;
use Yii;
use DateTime;
use app\enums\Rol;

class KursExtended extends Kurs
{
    const AVAILABLE_ACTION_OTMENIT = 10;
    const AVAILABLE_ACTION_PROGRAMMA = 20;
    const AVAILABLE_ACTION_INFO_O_PODACHE = 30;
    const AVAILABLE_ACTION_BYUDZHET = 40;
    const AVAILABLE_ACTION_VNEBYUDZHET = 50;
    const AVAILABLE_ACTION_IUP = 60;
    
    public $zapisanoSlushatelej;
    public $isUserZapisan;
    public $userStatusKursa;

    public static function tableName()
    {
        return 'kurs';
    }

    public function getIsCanceledByBriop()
    {
        return $this->userStatusKursa === StatusKursaFizLica::OTMENEN_BRIOP;
    }

    public function getIsStarted()
    {
        if (!$this->nachaloAsDate)
            return false;

        return (new DateTime) >= $this->nachaloAsDate;
    }

    public function getIsInDuration()
    {
        if (!$this->nachaloAsDate || !$this->konecAsDate)
            return false;

        $now = new DateTime;
        return $this->nachaloAsDate <= $now && $now <= $this->konecAsDate;
    }

    public function getIsEnded()
    {
        if (!$this->konecAsDate)
            return false;

        return (new DateTime) > $this->konecAsDate;
    }

    public function getIsNabor()
    {
        if ($this->raschitanoSlushatelej > $this->zapisanoSlushatelej)
            return true;

        return false;
    }

    public function getAvailableAction()
    {
        if (Yii::$app->user->can(Rol::RUKOVODITEL_KURSOV)) {
            return [self::AVAILABLE_ACTION_PROGRAMMA, null];
        }

        if ($this->isUserZapisan) {
            return $this->getIsStarted()
                ? [self::AVAILABLE_ACTION_PROGRAMMA, null]
                : [self::AVAILABLE_ACTION_OTMENIT, null];
        }

        if ($this->isCanceledByBriop)
            return [null, 'Запись отменена БРИОП'];

        if ($this->isEnded)
            return [null, 'Закончился'];

        if (!$this->nachaloAsDate)
            return [self::AVAILABLE_ACTION_INFO_O_PODACHE, null];

        if (!$this->isStarted || $this->tip === TipKursa::PK) {
            if ($this->finansirovanie === TipFinansirovaniya::BYUDZHET && $this->isNabor)
                return [self::AVAILABLE_ACTION_BYUDZHET, null];
            elseif ($this->finansirovanie === TipFinansirovaniya::VNEBYUDZHET)
                return [self::AVAILABLE_ACTION_VNEBYUDZHET, null];
           
            return [null, 'Набор завершен'];
        }

        if ($this->iup)
            return [self::AVAILABLE_ACTION_IUP, null];
        
        return [null, 'Уже идёт'];
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
        return static::find()->where(['tip' => $tip]);
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
        $q_status = Yii::$app->db->quoteValue(StatusKursaFizLica::ZAPISAN);
        return "{{kurs_tek_polzovatelya}}.[[status]] = $q_status";
    }

}
