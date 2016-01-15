<?php
namespace app\base;

use app\enums\EnumBase;
use app\enums\EtapObrazovaniya;
use app\enums\OrgTipDolzhnosti;
use app\enums\OrgTipRaboty;
use app\enums\TipDokumentaObObrazovanii;
use yii\helpers\Html;

class Formatter extends \yii\i18n\Formatter
{
    public $dateFormat = 'dd.MM.yyyy';

    /**
     * @var string "9" is substituted by digits of formatted value
     */
    public $telefonFormat = '+79999999999';

    /**
     * @var string "9" is substituted by digits of formatted value
     */
    public $innFormat = '999999999999';

    /**
     * @var string "9" is substituted by digits of formatted value
     */
    public $snilsFormat = '999-999-999-99';

    /**
     * @var string "9" is substituted by digits of formatted value
     */
    public $pasportNomerFormat = '99 99 999999';

    /**
     * @var string "9" is substituted by digits of formatted value
     */
    public $pasportKodPodrazdeleniyaFormat = '999-999';

    public function asHtmlTelefon($value, $defaultChar = null)
    {
        return Html::tag('span', $this->asTelefon($value, $defaultChar), ['class' => 'telefon']);
    }

    public function asTelefon($value, $defaultChar = null, $format = null)
    {
        if ($value === null)
            return $this->nullDisplay;

        if ($format === null)
            $format = $this->telefonFormat;

        return self::formatByMask($value, $defaultChar, $format);
    }

    public function asInn($value, $format = null)
    {
        if ($value === null)
            return $this->nullDisplay;

        if ($format === null)
            $format = $this->innFormat;

        return self::formatByMask($value, null, $format);
    }

    public function asSnils($value, $format = null)
    {
        if ($value === null)
            return $this->nullDisplay;

        if ($format === null)
            $format = $this->snilsFormat;

        return self::formatByMask($value, null, $format);
    }

    public function asPasportNomer($value, $format = null)
    {
        if ($value === null)
            return $this->nullDisplay;

        if ($format === null)
            $format = $this->pasportNomerFormat;

        return self::formatByMask($value, null, $format);
    }

    public function asPasportKodPodrazdeleniya($value, $format = null)
    {
        if ($value === null)
            return $this->nullDisplay;

        if ($format === null)
            $format = $this->pasportKodPodrazdeleniyaFormat;

        return self::formatByMask($value, null, $format);
    }

    public function asEnum($value, $class)
    {
        if ($value === null)
            return $this->nullDisplay;

        /** @var $class EnumBase */
        return $class::getNameBySql($value);
    }

    public function asTipDokumentaObObrazovanii($value)
    {
        return static::asEnum($value, TipDokumentaObObrazovanii::className());
    }

    public function asOrgTipRaboty($value)
    {
        return static::asEnum($value, OrgTipRaboty::className());
    }

    public function asOrgTipDolzhnosti($value)
    {
        return static::asEnum($value, OrgTipDolzhnosti::className());
    }

    public function asEtapObrazovaniya($value)
    {
        return static::asEnum($value, EtapObrazovaniya::className());
    }

    private static function formatByMask($value, $defaultChar, $format)
    {
        $res = '';

        $valSz = strlen($value);
        $valPos = 0;

        $maskSz = strlen($format);
        $maskPos = 0;

        while ($maskPos < $maskSz) {
            $maskCh = $format[$maskPos++];

            if ($maskCh !== '9') {
                $res .= $maskCh;

                continue;
            }

            for (;;) {
                if ($valPos < $valSz) {
                    $valueCh = $value[$valPos++];
                    if (!ctype_digit($valueCh))
                        continue;

                    $res .= $valueCh;

                    break;
                } else {
                    $res .= $defaultChar;

                    break;
                }
            }
        }

        return $res;
    }
}