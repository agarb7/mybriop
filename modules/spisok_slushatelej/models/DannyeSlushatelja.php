<?php
namespace app\modules\spisok_slushatelej\models;

use app\entities\FizLico;
use app\entities\RabotaFizLica;
use yii\base\Model;

/**
 * Class DannyeSlushatelja
 * @package app\modules\spisok_slushatelej\models
 * @property int $fizLicoId
 * @property string $familiya
 * @property string $imya
 * @property string $otchestvo
 * @property array $organizacii
 * @property array $rajony
 */
class DannyeSlushatelja extends Model
{
    public $fizLicoId;
    public $familiya;
    public $imya;
    public $otchestvo;
    public $organizacii;
    public $rajony;

    public function __construct($fizLicoId)
    {
        parent::__construct();
        if ($fizLicoId == null) return;
        else {
            $organizacii = [];
            $rajony = [];
            $fizLico = FizLico::findOne(['id'=>$fizLicoId]);
            $this->fizLicoId = $fizLicoId;
            $this->familiya = $fizLico->familiya;
            $this->imya = $fizLico->imya;
            $this->otchestvo = $fizLico->otchestvo;
            $rabotaFizLica = RabotaFizLica::find()->with('organizaciyaRel')->where(['fiz_lico' => $fizLicoId])->asArray()->all();
            foreach ($rabotaFizLica as $value){//var_dump($value['organizaciyaRel']['adres_adresnyj_objekt']);
                $organizacii[$value['id']]['orgId']=$value['organizaciyaRel']['id'];
                $rajony[$value['organizaciya']]['adrId']=$value['organizaciyaRel']['adres_adresnyj_objekt'];
            }
            $this->organizacii = $organizacii;
            $this->rajony = $rajony;
        }
    }

    public function attributeLabels()
    {
        return[
            'familiya' => 'Фамилия',
            'imya' => 'Имя',
            'otchestvo' => 'Отчество',
            'organizacii' => 'Организация',
            'rajony' => 'Город/Район',
        ];
    }
}