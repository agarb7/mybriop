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
    public $dolzhnostNazvanie;
    public $etapObrazovaniya;

    public function attributeLabels(){
        return [
            'organizaciyaAdress'=>'Район / Город',
            'organizaciyaVedomstvo' => 'Ведомство',
            'organizaciyaNazvanie' => 'Наименование организации',
            'organizaciyaId' => 'Организация',
            'dolzhnostId' => 'Должность',
            'dolzhnostNazvanie' => 'Наименование должности',
            'etapObrazovaniya' => 'Уровень образования к которому относится ваша должность'
        ];
    }

    public function rules(){
        return [
          [['organizaciyaAdress','organizaciyaVedomstvo','etapObrazovaniya'],'required'],
          [['organizaciyaId'],AttestaciyaOrganizaciyaValidator::className(),'skipOnEmpty'=>false,'message'=>'Выберите организацию из списка, либо введите название вручную'],
          [['organizaciyaNazvanie'],AttestaciyaOrganizaciyaValidator::className(),'skipOnEmpty'=>false,'message'=>'Введите название организации, либо выберите ее из списка'],
          [['dolzhnostId'],AttestaciyaDolzhnostValidator::className(),'skipOnEmpty'=>false, 'message' => 'Выберите должность из списка, либо введите название вручную'],
          [['dolzhnostNazvanie'], AttestaciyaDolzhnostValidator::className(),'skipOnEmpty'=>false,'message'=>'Введите название должности, либо выберите ее из списка'],
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

        $dolzhnost = $this->dolzhnostId
                ? Dolzhnost::findOne($this->dolzhnostId)
                : new Dolzhnost([
                    'nazvanie' => $this->dolzhnostNazvanie,
                    'tip' => 'inaya',
                    'obschij' => false
                ]);

        try {
            DolzhnostFizLicaNaRabote::getDb()->transaction(
                function () use (
                    $organizaciya, $rabotaFizLica, $dolzhnost
                ) {
                    $organizaciya->save(false);
                    $dolzhnost->save(false);
                    $rabotaFizLica->link('organizaciyaRel', $organizaciya);
                    $dolzhnostFizLica = new DolzhnostFizLicaNaRabote([
                        'dolzhnost' => $dolzhnost->id,
                        'etapObrazovaniya' => $this->etapObrazovaniya
                    ]);
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