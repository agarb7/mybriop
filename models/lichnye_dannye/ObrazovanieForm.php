<?php
namespace app\models\lichnye_dannye;

use app\entities\ObrazovanieFizLica;
use app\enums\TipDokumentaObObrazovanii;
use app\enums\TipKursa;
use app\widgets\DatePicker;
use yii\base\Model;

//todo dokument_ob_obrazovanii_kopiya;

class ObrazovanieForm extends Model
{
    public $dokumentTip;
    public $dokumentSeriya;
    public $dokumentNomer;
    public $dokumentData;

    public $kvalifikaciya;
    public $organizaciya;

    public $kursTip;
    public $kursNazvanie;
    public $kursChasy;

    public function rules()
    {
        return [
            ['dokumentTip', 'safe'],
            ['dokumentSeriya', 'safe'],
            ['dokumentNomer', 'safe'],
            ['dokumentData', 'safe'],

            ['kvalifikaciya', 'safe'],
            ['organizaciya', 'safe'],

            ['kursTip', 'safe'],
            ['kursNazvanie', 'safe'],
            ['kursChasy', 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'dokumentTip' => 'Тип',
            'dokumentSeriya' => 'Серия',
            'dokumentNomer' => 'Номер',
            'dokumentData' => 'Дата выдачи',

            'kvalifikaciya' => 'Квалификация',
            'organizaciya' => 'Образовательное учреждение',

            'kursTip' => 'Тип курса',
            'kursNazvanie' => 'Название курса',
            'kursChasy' => 'Объём часов'
        ];
    }

    public function populate()
    {
        /**
         * @var $obr ObrazovanieFizLica
         */
        $obr = ObrazovanieFizLica::findOne($this->_id);

        $this->dokumentTip = TipDokumentaObObrazovanii::asValue($obr->dokument_ob_obrazovanii_tip);
        $this->dokumentSeriya = $obr->dokument_ob_obrazovanii_seriya;
        $this->dokumentNomer = $obr->dokument_ob_obrazovanii_nomer;
        $this->dokumentData = DatePicker::fromSql($obr->dokument_ob_obrazovanii_data);

        //todo populate directories data

        $this->kursTip = TipKursa::asValue($obr->kurs_tip);
        $this->kursNazvanie = $obr->kurs_nazvanie;
        $this->kursChasy = $obr->kurs_chasy;
    }

    public function save()
    {
        if (!$this->validate())
            return false;

        $obr = new ObrazovanieFizLica;

        $obr->id = $this->_id;
        $obr->isNewRecord = false;

        $obr->dokument_ob_obrazovanii_tip = TipDokumentaObObrazovanii::asSql($this->dokumentTip);
        $obr->dokument_ob_obrazovanii_seriya = $this->dokumentSeriya;
        $obr->dokument_ob_obrazovanii_nomer = $this->dokumentNomer;
        $obr->dokument_ob_obrazovanii_data = DatePicker::toSql($this->dokumentData);

        //todo save directories data

        $obr->kurs_tip = TipKursa::asSql($this->kursTip);
        $obr->kurs_nazvanie = $this->kursNazvanie;
        $obr->kurs_chasy = $this->kursChasy;

        return $obr->save();
    }

    public function delete()
    {
        return ObrazovanieFizLica::deleteAll(['id' => $this->_id]);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->_id = $id;
    }

    public function belongsToUser()
    {
        return ObrazovanieFizLica::find()
            ->whichFizLicoHas()
            ->andWhere(['id' => $this->_id])
            ->exists();
    }

    /**
     * @var integer ID of ObrazovanieFizLica record
     */
    private $_id;
}