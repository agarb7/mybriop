<?php
/**
 * Created by PhpStorm.
 * User: tsyrya
 * Date: 25.01.16
 * Time: 23:29
 */

namespace app\components;


use yii\base\Exception;

class Months
{
    const PAD_IMINITELNIJ = 1;
    const PAD_RODITELNIJ = 2;
    const PAD_DATELNIJ = 3;
    const PAD_VINITELNIJ = 4;
    const PAD_TVORITELNIJ = 5;
    const PAD_PREDLOGNIJ = 6;

    const MO_YANVAR = 1;
    const MO_FEVRAL = 2;
    const MO_MART = 3;
    const MO_APREL = 4;
    const MO_MAJ = 5;
    const MO_IYUNJ = 6;
    const MO_IYULJ = 7;
    const MO_AVGUST = 8;
    const MO_SENTYABR = 9;
    const MO_OKTYABR = 10;
    const MO_NOYABR = 11;
    const MO_DEKABR = 12;

    public static $months = [
        self::MO_YANVAR => [
            self::PAD_IMINITELNIJ => 'январь',
            self::PAD_RODITELNIJ => 'января',
            self::PAD_DATELNIJ => 'январю',
            self::PAD_VINITELNIJ => 'января',
            self::PAD_TVORITELNIJ => 'январем',
            self::PAD_PREDLOGNIJ => 'январе'
        ],
        self::MO_FEVRAL => [
            self::PAD_IMINITELNIJ => 'февраль',
            self::PAD_RODITELNIJ => 'февраля',
            self::PAD_DATELNIJ => 'февралю',
            self::PAD_VINITELNIJ => 'февраля',
            self::PAD_TVORITELNIJ => 'февралем',
            self::PAD_PREDLOGNIJ => 'феврале'
        ],
        self::MO_MART => [
            self::PAD_IMINITELNIJ => 'март',
            self::PAD_RODITELNIJ => 'марта',
            self::PAD_DATELNIJ => 'марту',
            self::PAD_VINITELNIJ => 'марта',
            self::PAD_TVORITELNIJ => 'мартом',
            self::PAD_PREDLOGNIJ => 'марте'
        ],
        self::MO_APREL => [
            self::PAD_IMINITELNIJ => 'апрель',
            self::PAD_RODITELNIJ => 'апреля',
            self::PAD_DATELNIJ => 'апрелю',
            self::PAD_VINITELNIJ => 'апреля',
            self::PAD_TVORITELNIJ => 'апрелем',
            self::PAD_PREDLOGNIJ => 'апреле'
        ],
        self::MO_MAJ => [
            self::PAD_IMINITELNIJ => 'май',
            self::PAD_RODITELNIJ => 'мая',
            self::PAD_DATELNIJ => 'маю',
            self::PAD_VINITELNIJ => 'мая',
            self::PAD_TVORITELNIJ => 'маем',
            self::PAD_PREDLOGNIJ => 'мае'
        ],
        self::MO_IYUNJ => [
            self::PAD_IMINITELNIJ => 'июнь',
            self::PAD_RODITELNIJ => 'июня',
            self::PAD_DATELNIJ => 'июню',
            self::PAD_VINITELNIJ => 'июня',
            self::PAD_TVORITELNIJ => 'июнем',
            self::PAD_PREDLOGNIJ => 'июне'
        ],
        self::MO_IYULJ => [
            self::PAD_IMINITELNIJ => 'июль',
            self::PAD_RODITELNIJ => 'июля',
            self::PAD_DATELNIJ => 'июлю',
            self::PAD_VINITELNIJ => 'июля',
            self::PAD_TVORITELNIJ => 'июлем',
            self::PAD_PREDLOGNIJ => 'июле'
        ],
        self::MO_AVGUST => [
            self::PAD_IMINITELNIJ => 'август',
            self::PAD_RODITELNIJ => 'августа',
            self::PAD_DATELNIJ => 'августу',
            self::PAD_VINITELNIJ => 'августа',
            self::PAD_TVORITELNIJ => 'августом',
            self::PAD_PREDLOGNIJ => 'августе'
        ],
        self::MO_SENTYABR => [
            self::PAD_IMINITELNIJ => 'сентябрь',
            self::PAD_RODITELNIJ => 'сентября',
            self::PAD_DATELNIJ => 'сентябрю',
            self::PAD_VINITELNIJ => 'сентября',
            self::PAD_TVORITELNIJ => 'сентябрем',
            self::PAD_PREDLOGNIJ => 'сентябре'
        ],
        self::MO_OKTYABR => [
            self::PAD_IMINITELNIJ => 'октябрь',
            self::PAD_RODITELNIJ => 'октября',
            self::PAD_DATELNIJ => 'октябрю',
            self::PAD_VINITELNIJ => 'октября',
            self::PAD_TVORITELNIJ => 'октябрем',
            self::PAD_PREDLOGNIJ => 'октябре'
        ],
        self::MO_NOYABR => [
            self::PAD_IMINITELNIJ => 'ноябрь',
            self::PAD_RODITELNIJ => 'ноября',
            self::PAD_DATELNIJ => 'ноябрю',
            self::PAD_VINITELNIJ => 'ноября',
            self::PAD_TVORITELNIJ => 'ноябрем',
            self::PAD_PREDLOGNIJ => 'ноябре'
        ],
        self::MO_DEKABR => [
            self::PAD_IMINITELNIJ => 'декабрь',
            self::PAD_RODITELNIJ => 'декабря',
            self::PAD_DATELNIJ => 'декабрю',
            self::PAD_VINITELNIJ => 'декабря',
            self::PAD_TVORITELNIJ => 'декабрем',
            self::PAD_PREDLOGNIJ => 'декабре'
        ],
    ];

    /**
     * @param int $month
     * @param int $padeg
     * @return string
     */
    public static function getMonthName($month, $padeg){
        $months = self::$months;
        if (isset($months[$month][$padeg])) return $months[$month][$padeg];
        else throw new Exception('Wrong parameters');
    }
}