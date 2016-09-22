<?php
namespace app\upravlenie_kursami\potok\models\potok;

use yii\base\Model;

class TemaFilter extends Model
{
    public $nazvanie;
    public $prepodavatelId;

    public function rules()
    {
        return [
            ['nazvanie', 'string', 'max' => '400'],
            ['prepodavatelId', 'integer']
        ];
    }
}