<?php
namespace app\upravlenie_kursami\potok\models\potok;

use app\records\ZanyatieChastiTemy;
use app\validators\ChastTemyValidator;
use app\validators\NazvanieValidator;

use Yii;

class Zanyatie extends \app\records\Zanyatie
{
    public $temy;
    public $chasti_tem;

    public function rules()
    {
        return [
            ['nazvanie', NazvanieValidator::className()],
            ['temy', 'validateTemyCount'],
            ['temy', 'each', 'rule' => ['integer']], //todo existence validate
            ['chasti_tem', 'each', 'rule' => [ChastTemyValidator::className()]] //todo existence validate
        ];
    }

    public function validateTemyCount($attribute)
    {
        if (count($this->temy) !== count($this->chasti_tem))
            $this->addError($attribute, 'temy and chasti counts mismatch');
    }

    public function save($runValidation = true, $attributeNames = null)
    {
        return Yii::$app->db->transaction(function() use ($runValidation, $attributeNames) {
            return $this->internalSave($runValidation, $attributeNames);
        });
    }

    private function internalSave($runValidation, $attributeNames)
    {
        if (!parent::save($runValidation, $attributeNames))
            return false;

        for ($i=0,
             $cnt=count($this->temy);
             $i<$cnt;
             ++$i)
        {
            $zct = new ZanyatieChastiTemy;

            $zct->zanyatie = $this->id;
            $zct->tema = $this->temy[$i];
            $zct->chast_temy = $this->chasti_tem[$i];

            if (!$zct->save())
                return false;
        }

        return true;
    }
}
