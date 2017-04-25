<?php
/**
 * Created by PhpStorm.
 * User: tsyrya
 * Date: 28.02.16
 * Time: 16:54
 */

namespace app\entities;
use app\enums\KategoriyaPedRabotnika;
use app\helpers\ArrayHelper;

/**
 * Class PostoyannoeIspytanie
 * @package app\entities
 *
 * @property int id
 * @property string nazvanie
 * @property bool pervayaKategoriya
 * @property bool vysshayaKategoriya
 */
class PostoyannoeIspytanie extends EntityBase
{

    CONST PORTFOLIO_ID = 1;
    CONST SPD_ID = 2;

    public static function getPortfolioId(){
        return 1;
    }

    public static function getSpdId(){
        return 2;
    }

    public static function getIspytaniyaByKategoriya($kategoriyaId, $otraslevoeSoglashenie = false){
        $result = [];
        $db = static::getDb();
        if ($kategoriyaId == KategoriyaPedRabotnika::PERVAYA_KATEGORIYA){
            $sql = 'SELECT id FROM postoyannoe_ispytanie WHERE pervaya_kategoriya = true';
            $result = $db->createCommand($sql)->queryAll();
        }
        else{
            $sql = 'SELECT id FROM postoyannoe_ispytanie WHERE vysshaya_kategoriya = true';
            if ($otraslevoeSoglashenie) {
                $sql .= ' and id != 2';
            }
            $result = $db->createCommand($sql)->queryAll();
        }
        return ArrayHelper::getColumn($result,'id');
    }

}