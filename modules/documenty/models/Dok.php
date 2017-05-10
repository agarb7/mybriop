<?php
/**
 * Created by PhpStorm.
 * User: asv
 * Date: 24.03.2017
 * Time: 22:20
 */

namespace app\modules\documenty\models;

use yii\db\ActiveRecord;

class Dok extends ActiveRecord
{
    public function rules()
    {
        return [
            [['prikaz_id'], 'required'],
            [['prikaz_id'], 'integer'],
        ];
    }

}