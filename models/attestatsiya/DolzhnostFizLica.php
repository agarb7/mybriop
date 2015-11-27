<?php
/**
 * Created by PhpStorm.
 * User: macbook22
 * Date: 05.08.15
 * Time: 14:22
 */

namespace app\models\attestatsiya;


use app\entities\Dolzhnost;
use app\entities\DolzhnostFizLicaNaRabote;
use app\entities\Organizaciya;
use app\entities\RabotaFizLica;
use app\validators\AttestaciyaDolzhnostValidator;
use app\validators\AttestaciyaOrganizaciyaValidator;
use yii\base\Model;

class DolzhnostFizLica extends Model
{
    public $fizLicoId;
    public $organizaciyaAdress;
    public $organizaciyaVedomstvo;
    public $organizaciyaNazvanie;
    public $organizaciyaId;
    public $dolzhnostId;
    public $etapObrazovaniya;

    public function attributeLabels(){
        return [
            'organizaciyaAdress'=>'Район / Город',
            'organizaciyaVedomstvo' => 'Ведомство',
            'organizaciyaNazvanie' => 'Наименование организации',
            'organizaciyaId' => 'Организация',
            'dolzhnostId' => 'Должность',
            'etapObrazovaniya' => 'Уровень образования к которому относится ваша должность'
        ];
    }

    public function rules(){
        return [
          [['organizaciyaAdress','organizaciyaVedomstvo','dolzhnostId','etapObrazovaniya'],'required'],
          [['organizaciyaId'],AttestaciyaOrganizaciyaValidator::className(),'skipOnEmpty'=>false,'message'=>'Выберите организацию из списка, либо введите название вручную'],
          [['organizaciyaNazvanie'],AttestaciyaOrganizaciyaValidator::className(),'skipOnEmpty'=>false,'message'=>'Введите название организации, либо выберите ее из списка'],
          [['dolzhnostId'],AttestaciyaDolzhnostValidator::className(),'skipOnEmpty'=>false],
          [['fizLicoId'],'safe']
        ];
    }

    public function OrganizaciyaCheck($attribute)
    {
        if (!$this->organizaciyaId && !$this->organizaciyaNazvanie)
            $this->addError($attribute, 'Выберите организацию из списка, либо введите название вручную');
    }

    public function addDolzhnost(){
        $organizaciya = $this->organizaciyaId
            ? Organizaciya::findOne($this->organizaciyaId)
            : new Organizaciya([
                'nazvanie' => $this->organizaciyaNazvanie,
                'vedomstvo' => $this->organizaciyaVedomstvo,
                'adresAdresnyjObjekt' => $this->organizaciyaVedomstvo,
                'obschij' => false
            ]);
        if (!$organizaciya->validate())
            return false;
        if ($this->organizaciyaId)
            $rabotaFizLica = RabotaFizLica::findOne(['fiz_lico'=>$this->fizLicoId,'organizaciya'=>$this->organizaciyaId]);
        else $rabotaFizLica = null;
        if (!$rabotaFizLica) {
            $rabotaFizLica = new RabotaFizLica(['fiz_lico'=>$this->fizLicoId]);
        }
        if (!$rabotaFizLica->validate())
            return false;
        $dolzhnostFizLica = new DolzhnostFizLicaNaRabote([
            'dolzhnost' => $this->dolzhnostId,
            'etapObrazovaniya' => $this->etapObrazovaniya
        ]);
        if (!$dolzhnostFizLica->validate())
            return false;
        $dolzhnost = Dolzhnost::findOne($this->dolzhnostId);
        try {
            DolzhnostFizLicaNaRabote::getDb()->transaction(
                function () use (
                    $organizaciya, $rabotaFizLica, $dolzhnostFizLica
                ) {
                    $organizaciya->save(false);
                    $rabotaFizLica->link('organizaciyaRel', $organizaciya);
                    $dolzhnostFizLica->link('rabotaFizLicaRel', $rabotaFizLica);
                }
            );
        }
        catch (Exception $e){
            return false;
        }
        return [
            'rabota_fiz_lica_id'=>$rabotaFizLica->id,
            'dolhnost'=>$dolzhnost->nazvanie.', '.$organizaciya->nazvanie
        ];
    }
}