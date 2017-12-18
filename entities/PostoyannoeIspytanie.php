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
    
    public static function getIkId(){
        return array(3,4,5,6,7);
    } 

    public static function getIspytaniya($kategoriyaId,$otraslevoeSoglashenie = false,$isFgos,$isUchitel,$nachaloAttestacii){
        $result = [];
        $db = static::getDb();
        if ($nachaloAttestacii < '2017-04-01') {
            /**
             * 1. Все типы должностей прикрепили ИК вместо Портфолио
             * 2. Учителя аттестуются по ИК
             * 3. Не учителя могут аттестоваться на выбор по портфолио или ИК
             */
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
        } else {
            /**
             * 1. Для всех типов должностей Портфолио заменить на ИК
             * 2. Для педработников занимающих должности типа "учитель" возможен выбор "постоянного испытания" по ФГОС в каждой из категорий
             * 3. Для педработников остальных должностей "постоянное испытание" == ИК
             * P.S.: Должности с ДОУ должны подчиняться п.2
             */
            if ($isUchitel) {
                if ($isFgos) {
                    if ($kategoriyaId == KategoriyaPedRabotnika::PERVAYA_KATEGORIYA) {
                        $result[] = 3;
                    } else {
                        $result[] = 5;
                        if (!$otraslevoeSoglashenie) $result[] = 2;
                    }
                } else {
                    if ($kategoriyaId == KategoriyaPedRabotnika::PERVAYA_KATEGORIYA) {
                        $result[] = 4;
                    } else{
                        $result[] = 6;
                        if (!$otraslevoeSoglashenie) $result[] = 2;
                    }
                }
            } else {
                $result[] = 7;
                if ($kategoriyaId == KategoriyaPedRabotnika::VYSSHAYA_KATEGORIYA and !$otraslevoeSoglashenie) $result[] = 2;
            }
            return $result;
        }
    }
}