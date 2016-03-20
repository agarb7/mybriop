<?php
/**
 * Created by PhpStorm.
 * User: tsyrya
 * Date: 25.10.15
 * Time: 17:12
 */

namespace app\models\attestatsiya;


use app\entities\ZayavlenieNaAttestaciyu;
use app\enums\StatusZayavleniyaNaAttestaciyu;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class AttestaciyaSpisokFilter extends Model
{
    public $vreamyaProvedeniya;
    public $dolzhnost;
    public $kategoriya;
    public $podtverzhdenieRegistracii = false;
    public $varIspytanie2;
    public $varIspytanie3;
    public $fio;

    public function attributeLabels()
    {
        return [
            'fio' => 'ФИО',
            'vreamyaProvedeniya' => 'Период прохождения',
            'dolzhnost' => 'Должность',
            'kategoriya' => 'Категория',
            'podtverzhdenieRegistracii' => 'Подтвержденные',
            'varIspytanie2' => 'Вариативное испытание 2',
            'varIspytanie3' => 'Вариативное испытание 3'
        ];
    }

    public function rules(){
        return [
          [['fio','podtverzhdenieRegistracii'],'safe'],
          [['vreamyaProvedeniya','dolzhnost','varIspytanie2','varIspytanie3'],'each','rule' => ['integer']],
          [['kategoriya'],'each','rule' => ['string']]
        ];
    }

    public function search($request)
    {
        $query = ZayavlenieNaAttestaciyu::find()
                                ->joinWith('dolzhnostRel')
                                ->joinWith('attestacionnoeVariativnoeIspytanie2Rel')
                                ->joinWith('attestacionnoeVariativnoeIspytanie3Rel')
                                ->joinWith('vremyaProvedeniyaAttestaciiRel')
                                ->joinWith('organizaciyaRel')
                                ->joinWith('varIspytanie2FajlRel')
                                ->joinWith('varIspytanie3FajlRel')
                                ->joinWith('varIspytanie3FajlRel')
                                ->joinWith('portfolioFajlRel')
                                ->joinWith('prezentatsiyaFajlRel')
                                ->where(['!=','zayavlenie_na_attestaciyu.status',StatusZayavleniyaNaAttestaciyu::REDAKTIRUETSYA_PED_RABOTNIKOM])
                                ->orderBy('zayavlenie_na_attestaciyu.id');
        if ($this->load($request) && $this->validate()){
            if ($this->fio){
                $query->andWhere(['like','"familiya"||\' \'||"imya"||\' \'||"otchestvo"',$this->fio]);
            }
            if ($this->podtverzhdenieRegistracii){
                $query->andWhere(['zayavlenie_na_attestaciyu.status' => StatusZayavleniyaNaAttestaciyu::PODPISANO_PED_RABOTNIKOM]);
            }
            if ($this->vreamyaProvedeniya){
                $query->andWhere(['in','vremya_provedeniya_attestacii.id',$this->vreamyaProvedeniya]);
            }
            if ($this->dolzhnost){
                $query->andWhere(['in','dolzhnost.id',$this->dolzhnost]);
            }
            if ($this->kategoriya){
                $query->andWhere(['in','na_kategoriyu',$this->kategoriya]);
            }
            if ($this->varIspytanie2){
                $query->andWhere(['in','var_ispytanie_2',$this->varIspytanie2]);
            }
            if ($this->varIspytanie3){
                $query->andWhere(['in','var_ispytanie_3',$this->varIspytanie3]);
            }
        }
        return new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
            'pagination' => [
                'pageSize' => 20,
            ]
        ]);
    }
}