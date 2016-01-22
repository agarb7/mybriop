<?php
namespace app\models\dolzhnost;

use app\entities\Dolzhnost;
use app\validators\NazvanieValidator;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use Yii;

class DolzhnostModel extends Model
{
    public $ids;
    public $name;

    const SCENARIO_MERGE = 'merge';

    public function rules()
    {
        return [
            ['ids', 'required'],
            ['ids', function($attribute) {
                if (!is_array($this->$attribute))
                    $this->addError($attribute, 'Ids must be array.');
            }],

            ['name', 'required', 'on' => self::SCENARIO_MERGE],
            ['name', NazvanieValidator::className(), 'on' => self::SCENARIO_MERGE],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Новое наименование'
        ];
    }

    public function merge()
    {
        if ($this->scenario !== self::SCENARIO_MERGE)
            return false;

        if (!$this->validate())
            return false;

        return Yii::$app->db->transaction(function () {
            return $this->mergeHelper();
        });
    }

    public function loadDolzhnosti($runValidation = true)
    {
        if ($runValidation && !$this->validate(['ids']))
            return false;

        if ($this->_dolzhnosti === null) {
            foreach ($this->ids as $id) {
                $record = Dolzhnost::findOneByHashids($id);
                if (!$record)
                    return false;

                $this->_dolzhnosti[$id] = $record;
            }
        }

        return true;
    }

    /**
     * @return Dolzhnost[]
     */
    public function getDolzhnosti()
    {
        return $this->_dolzhnosti ?: [];
    }

    public function guessName()
    {
        $nameCounts = [];

        foreach ($this->getDolzhnosti() as $rec) {
            if (!isset($nameCounts[$rec->nazvanie]))
                $nameCounts[$rec->nazvanie] = 1;
            else
                ++$nameCounts[$rec->nazvanie];
        }

        $this->name = ArrayHelper::getValue(
            array_flip($nameCounts),
            max($nameCounts)
        );

        return true;
    }

    private function mergeHelper()
    {
        $newRecord = new Dolzhnost;
        $newRecord->nazvanie = $this->name;
        $newRecord->obschij = true;
        $newRecord->save();

        if (!$this->loadDolzhnosti(false))
            throw new Exception('Dolzhnosti didn\'t loaded.');

        foreach ($this->getDolzhnosti() as $rec) {
            $this->relinkRelations($rec, $newRecord);
            $rec->delete();
        }

        return true;
    }

    /**
     * @param $dolzh Dolzhnost
     * @param $newDolzh Dolzhnost
     */
    private function relinkRelations($dolzh, $newDolzh)
    {
        foreach (Dolzhnost::relations() as $relation) {
            $relationQuery = $dolzh->getRelation($relation);
            foreach ($relationQuery->each() as $rec)
                $rec->link($relationQuery->inverseOf, $newDolzh);
        }
    }

    private $_dolzhnosti = null;
}