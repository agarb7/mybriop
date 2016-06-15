<?php
namespace app\base;

use Yii;

/**
 * Class Module
 */
class Module extends \yii\base\Module
{
    /**
     * @var string[]
     */
    private $_activeRecordMap = [];

    /**
     * @return string[]
     */
    public function getActiveRecordMap()
    {
        return $this->_activeRecordMap;
    }

    /**
     * @param string[] $activeRecords
     */
    public function setActiveRecordMap($activeRecords)
    {
        $this->_activeRecordMap = $activeRecords;

        $queryClass = ActiveQuery::className();

        if (!Yii::$container->has($queryClass)) {
            Yii::$container->set($queryClass, function ($container, $params, $config) use ($queryClass) {
                $modelClass = self::findActiveRecordClass($params[0]);
                return new $queryClass($modelClass, $config);
            });
        }
    }

    private static function findActiveRecordClass($class)
    {
        for ($current = Yii::$app->controller->module; $current !== null; $current = $current->module) {
            if (!$current instanceof Module) 
                continue;
            
            $activeRecordMap = $current->getActiveRecordMap(); 
            
            if (isset($activeRecordMap[$class]))
                return $activeRecordMap[$class];
        }
        
        return $class;
    }
}