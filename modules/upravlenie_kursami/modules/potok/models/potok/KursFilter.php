<?php
namespace app\upravlenie_kursami\potok\models\potok;

use app\enums2\TipKursa;
use app\validators\Enum2Validator;
use app\behaviors\TransformationBehavior;

use yii\base\Model;

/**
 * Class KursFilter
 *
 * @property string $dateStartSql
 * @property string $dateEndSql
 */
class KursFilter extends Model
{
    public $god;
    public $tip;
    public $nazvanie;
    public $rukovoditelId;
    public $dateStartSql;
    public $dateEndSql;
    public $chasyStart;
    public $chasyEnd;

    public function behaviors()
    {
        return [TransformationBehavior::className()];
    }

    public function rules()
    {
        return [
            ['god', 'integer', 'min' => 2015, 'max' => 2020],
            ['tip', Enum2Validator::className(), 'enum' => TipKursa::className()],
            ['nazvanie', 'string', 'max' => '400'],
            ['rukovoditelId', 'integer'],
            [['dateStart', 'dateEnd'], 'date'],
            [['chasyStart', 'chasyEnd'], 'integer']
        ];
    }

    public function transformations()
    {
        return [
            [['dateStartSql' => 'dateStart', 'dateEndSql' => 'dateEnd'], 'date']
        ];
    }
}