<?php
namespace app\models\kursy_rukovoditelya;

use app\entities\KursFizLica;
use app\enums2\StatusKursaFizLica;
use app\validators\HashidsValidator;
use yii\base\Model;

class ZapisModel extends Model
{
    public $kursHashids;
    public $fizLicoHashids;

    public $kursId;
    public $fizLicoId;
    public $status;

    public function rules()
    {
        return [
            ['fizLicoHashids', HashidsValidator::className(), 'targetAttribute' => 'fizLicoId'],

            ['kursId', 'required'],
            ['kursId', 'integer'],

            ['fizLicoId', 'required'],
            ['fizLicoId', 'integer'],

            ['status', 'required'],
            ['status', 'in', 'range' => [StatusKursaFizLica::ZAPISAN, StatusKursaFizLica::OTMENEN_BRIOP]],

            ['kursId', 'validateExistance'],
        ];
    }

    public function applyStatus()
    {
        if (!$this->validate())
            return false;

        $this->_kursFizLica->status = $this->status;

        return $this->_kursFizLica->save();
    }

    public function validateExistance($attribute)
    {
        $this->_kursFizLica = KursFizLica::find()
            ->where([
                'fiz_lico' => $this->fizLicoId,
                'kurs' => $this->kursId
            ])
            ->one();

        if (!$this->_kursFizLica)
            $this->addError($attribute, 'Такой записи на курс не существует');
    }

    /**
     * @var KursFizLica
     */
    private $_kursFizLica;
}