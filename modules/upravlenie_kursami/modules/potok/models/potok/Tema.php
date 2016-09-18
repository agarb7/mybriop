<?php
namespace app\upravlenie_kursami\potok\models\potok;

use Yii;

class Tema extends \app\records\Tema
{
    /**
     * @return TemaQuery
     */
    static public function find()
    {
        return Yii::createObject(TemaQuery::className(), [get_called_class()]);
    }
}