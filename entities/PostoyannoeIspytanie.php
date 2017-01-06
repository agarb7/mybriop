<?php
/**
 * Created by PhpStorm.
 * User: tsyrya
 * Date: 28.02.16
 * Time: 16:54
 */

namespace app\entities;

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

}