<?php
namespace app\modules\documenty\models;

use yii\db\ActiveRecord;

class DokPrikazAtribut extends ActiveRecord
{
    public function rules()
    {
        return [
            [['prikaz_id','atribut_id'], 'required'],
            [['prikaz_id','atribut_id','id_znachenija'], 'integer'],
            ['znachenie', 'safe'],
        ];
    }

}