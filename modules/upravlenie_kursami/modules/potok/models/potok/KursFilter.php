<?php
namespace app\upravlenie_kursami\potok\models\potok;

use yii\base\Model;
use app\behaviors\TransformationBehavior;

/**
 * Class KursFilter
 *
 * @property string $dateStartSql
 * @property string $dateEndSql
 */
class KursFilter extends Model
{
    public $nazvanie;
    public $rukovoditelId;
    public $dateStart;
    public $dateEnd;
    public $chasyStart;
    public $chasyEnd;

    public function behaviors()
    {
        return [TransformationBehavior::className()];
    }

    public function rules()
    {
        return [
            ['nazvanie', 'string', 'max' => '2048'],
            ['rukovoditelId', 'integer'],
            [['dateStart', 'dateEnd'], 'date'],
            ['dateStartSql', 'compareAttribute' => 'dateEndSql', 'operator' => '<'],
            [['chasyStart', 'chasyEnd'], 'integer'],
            ['chasyStart', 'compareAttribute' => 'chasyEnd', 'operator' => '<']
        ];
    }

    public function transformations()
    {
        return [
            [['dateStart' => 'dateStartSql', 'dateEnd' => 'dateEndSql'], 'date']
        ];
    }
}