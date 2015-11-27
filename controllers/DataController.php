<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 27.03.15
 * Time: 5:27
 */

namespace app\controllers;

use app\entities\Polzovatel;
use app\entities\PolzovatelEntity;
use app\enums\Rol;
use app\enums\RolEnum;
use app\repositories\FizLicoRepo;
use app\repositories\PolzovatelRepo;
use yii\base\UserException;
use yii\db\Query;
use yii\web\Controller;
use app\entities\FizLico;

class DataController extends Controller
{
    public function actionSozdatPolzovatelejRukovoditelyamKursov()
    {
        $query = new Query;
        $fiz_lico_ids = $query
            ->select('rukovoditel')->distinct()
            ->from('kurs')
            ->all();

        $result = '';

        foreach($fiz_lico_ids as $id) {
            if ($id['rukovoditel']) {
                $polzovatel_data = self::makePolzovatel($id['rukovoditel']);
                $result .= implode("\t", $polzovatel_data) . "\n";
            }
        }

        return $result;
    }

    private static function makePolzovatel($fiz_lico_id)
    {
        $fiz_lico = FizLico::find()->where(['id'=>$fiz_lico_id])->one();//  (new FizLicoRepo)->select(['id' => $fiz_lico_id]);
        if (!$fiz_lico)
            throw new UserException("Физ.лицо с id=$fiz_lico_id не найдено");

        //$fiz_lico = $fiz_lica[0];

        $login = self::makeLogin($fiz_lico->familiya, $fiz_lico->imya, $fiz_lico->otchestvo);
        $parol = strtolower(\Yii::$app->security->generateRandomString(4));
        $klyuch_autentifikacii = \Yii::$app->security->generateRandomString(255);
        $roli = [Rol::RUKOVODITEL_KURSOV];

        $polzovatel_config = [
            'fizLico' => $fiz_lico,
            'login' => $login,
            'parol' => $parol,
            'klyuchAutentifikacii' => $klyuch_autentifikacii,
            'roli' => $roli,
            'aktiven' => true
        ];

        $polzovatel = new Polzovatel();// new PolzovatelEntity($polzovatel_config);
        $polzovatel->fizLico = $fiz_lico_id;
        $polzovatel->login = $login;
        $polzovatel->parol = $parol;
        $polzovatel->klyuchAutentifikacii = $klyuch_autentifikacii;
        $polzovatel->roli = $roli;
        $polzovatel->aktiven = true;


        if(!$polzovatel->save())
            throw new UserException("Ошибка сохранения пользователя $polzovatel->login.");

        return [
            $fiz_lico->familiya,
            $fiz_lico->imya,
            $fiz_lico->otchestvo,
            $login,
            $parol
        ];
    }

    private static function makeLogin($familiya, $imya, $otchestvo)
    {
        $part1 = self::toLowerAndTransliterate($familiya);
        $part2 = self::toLowerAndTransliterate(mb_substr($imya,0,1));
        $part3 = self::toLowerAndTransliterate(mb_substr($otchestvo,0,1));
        return "{$part1}{$part2}{$part3}";
    }

    private static function toLowerAndTransliterate($str)
    {
        $lower = mb_strtolower($str);
        $len = mb_strlen($lower);
        $res = '';
        for ($i=0; $i<$len; ++$i) {
            $ch = mb_substr($lower, $i, 1);
            $trans_ch = array_key_exists($ch, self::$translitTable)
                ? self::$translitTable[$ch]
                : '';
            $res .= $trans_ch;
        }
        return $res;
    }

    private static $translitTable = [
        'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'e', 'ж' => 'zh',
        'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o',
        'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c',
        'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch', 'ъ' => 'j', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu',
        'я' => 'ya'
    ];
}
