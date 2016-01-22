<?php
namespace app\controllers;

use app\helpers\ArrayHelper;
use app\helpers\StringHelper;
use app\models\plan_prospekt\Kurs;
use yii\base\InvalidParamException;
use yii\console\Controller;
use Yii;
use yii\db\Query;
use yii\filters\AccessControl;

class PlanProspektController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [['allow' => true, 'ips' => ['127.0.0.1']]],
            ]
        ];
    }

    public function actionLoad($csv)
    {
        $handle = fopen($csv, 'r');
        if ($handle === false)
            throw new InvalidParamException($csv);

        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();

        $cnt = 1;

        try {
            while (($row = fgetcsv($handle)) !== false) {
                echo $cnt++ . "\n";
                $this->createKursRecord($row);
            }

            $transaction->commit();
        } catch(\Exception $e) {
            $transaction->rollBack();

            throw $e;
        }

        return self::EXIT_CODE_NORMAL;
    }

    private function createKursRecord($row)
    {
        $kurs = new Kurs;

        $kurs->plan_prospekt_god = '2016-01-01';

        $kurs->nazvanie = StringHelper::squeezeLine($row[1]);
        $kurs->annotaciya = StringHelper::squeezeText($row[2]) ?: null;
        $kurs->raschitano_chasov = $this->trimNull($row[3]);
        $kurs->ochnoe_nachalo = $this->trimNull($row[4]);
        $kurs->ochnoe_konec = $this->trimNull($row[5]);
        $kurs->zaochnoe_nachalo = $this->trimNull($row[6]);
        $kurs->zaochnoe_konec = $this->trimNull($row[7]);
        $kurs->raschitano_slushatelej = $this->trimNull($row[8]);

        $kurs->rukovoditel = $this->findRukovoditel($row[9]);

        $kurs->finansirovanie = $this->trimNull($row[10]);
        $kurs->tip = $this->trimNull($row[11]);
        $kurs->formy_obucheniya = $this->trimNull($row[12]);
        $kurs->strukturnoe_podrazdelenie = $this->trimNull($row[13]);

        $kurs->save();

        $kats = $this->parseKategorii($row[0]);

        foreach ($kats as $kat) {
            Yii::$app->db->createCommand()->insert('kategoriya_slushatelya_kursa', [
                'kurs' => $kurs->id,
                'kategoriya_slushatelya' => $this->ensureKategoriya($kat)
            ])->execute();
        }
    }

    private function trimNull($str)
    {
        return trim($str) ?: null;
    }

    private function findRukovoditel($fio)
    {
        $familiya = ArrayHelper::getValue(preg_split('/[\s.]/u', trim($fio)), 0);

        return (new Query)
            ->select('f.id')
            ->from(['f' => 'fiz_lico'])
            ->leftJoin(['r' => 'rabota_fiz_lica'], 'f.id = r.fiz_lico')
            ->where(['lower(f.familiya)' => mb_strtolower($familiya)])
            ->groupBy('f.id')
            ->having('bool_or(r.organizaciya = 1)')
            ->scalar() ?: null;
    }

    private function parseKategorii($str)
    {
        $kats = preg_split('/,/u', $str);
        return array_map('app\helpers\StringHelper::squeezeLine', $kats);
    }

    private function ensureKategoriya($kat)
    {
        $id = (new Query)
            ->select('id')
            ->from('kategoriya_slushatelya')
            ->where(['lower(nazvanie)' => mb_strtolower($kat)])
            ->scalar();

        if ($id)
            return $id;

        return Yii::$app->db->schema->insert('kategoriya_slushatelya', [
            'nazvanie' => $kat
        ])['id'];
    }
}