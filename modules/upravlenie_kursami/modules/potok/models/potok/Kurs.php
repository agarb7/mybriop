<?php
namespace app\upravlenie_kursami\potok\models\potok;

use Yii;

class Kurs extends \app\records\Kurs
{
    /**
     * @return KursQuery
     */
    static public function find()
    {
        return Yii::createObject(KursQuery::className(), [get_called_class()]);
    }
}