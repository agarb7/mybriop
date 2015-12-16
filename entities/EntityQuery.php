<?php
namespace app\entities;

use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use Yii;

class EntityQuery extends ActiveQuery
{
    public $commonOnly = null;

    const DROP_DOWN = 1;
    const LIST_BOX = 1;
    const CHECKBOX_LIST = 1;

    const DEP_DROP_AJAX = 2;

//    const SELECT2_AJAX = 3;

    public function formattedAll($format, $params, $db = null)
    {
        if (is_string($params))
            $params = ['valueColumn' => $params];

        $keyColumn = ArrayHelper::getValue($params, 'keyColumn', 'id');
        $valueColumn = ArrayHelper::getValue($params, 'valueColumn');

        if (!$valueColumn)
            throw new InvalidConfigException("'valueColumn' must be set if format was choosen.");

        $rows = parent::all($db);

        if ($format === self::DROP_DOWN)
            return ArrayHelper::map($rows, $keyColumn, $valueColumn);

        if ($format === self::DEP_DROP_AJAX) {
            $output = [];
            foreach ($rows as $row)
                $output[] = ['id' => $row[$keyColumn], 'name' => $row[$valueColumn]];
            return [
                'output' => $output,
                'selected' => ArrayHelper::getValue($params, 'selected')
            ];
        }

        throw new InvalidConfigException('Invalid format was choosen');
    }

    public function commonOnly($value = true)
    {
        $this->commonOnly = $value;
        return $this;
    }

    public function createCommand($db = null)
    {
        if ($this->commonOnly) {
            $modelClass = $this->modelClass;
            $table = $modelClass::tableName();
            $this->andWhere([$table . '.obschij' => true]);
        }

        return parent::createCommand($db);
    }

    public function hasFizLico($fiz_lico = null)
    {
        if ($fiz_lico === null)
            $fiz_lico = Yii::$app->user->fizLico;

        return $this->andWhere(['fiz_lico' => $fiz_lico ? $fiz_lico->id : null]);
    }
}