<?php
namespace app\controllers;

use app\components\Controller;
use app\components\FuncResponse;
use app\components\JsResponse;
use app\entities\KimKursa;
use app\entities\KimPodrazdelaKursa;
use app\entities\KimTemy;
use app\entities\KontroliruyuschijKursa;
use app\entities\KontroliruyuschijPodrazdelaKursa;
use app\entities\Kurs;
use app\entities\KursExtended;
use app\entities\RazdelKursa;
use app\entities\Tema;
use app\entities\TemaDiplomnojRabotyKursa;
use app\entities\UmkKursa;
use app\entities\UmkPodrazdelaKursa;
use app\entities\UmkTemy;
use app\enums\Rol;
use app\globals\KursGlobals;
use app\models\podrazdel_kursa\PodrazdelKursa;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use Yii;
use yii\web\Response;

class KursyRukovoditelyaController extends Controller
{
    public function actionSpisok($god = null)
    {
        $employees = KursGlobals::get_sotrudniki();

        $data = new ActiveDataProvider([
            'query' => KursExtended::findMyAsRukovoditel()
                ->andFilterWhere(['plan_prospekt_god' => $god])
                ->orderBy('id'),
            'key' => 'hashids',
            'sort' => false
        ]);

        return $this->render('spisok', compact('data', 'employees'));
    }

    public function actionGetKursyByYear(){
        Yii::$app->response->format = Response::FORMAT_JSON;

        $year = Yii::$app->request->post('year');
        $response = new JsResponse();

        $response->data = Kurs::find()
            ->where(['EXTRACT(YEAR FROM plan_prospekt_god)'=>$year])
            ->andWhere(['rukovoditel'=>Yii::$app->user->fizLico->id])
            ->orderBy('id')
            ->all();

        return $response;
    }

    public function actionGetKursyAnother(){
        Yii::$app->response->format = Response::FORMAT_JSON;

        $year = Yii::$app->request->post('year');
        $employee = Yii::$app->request->post('employee');
        $response = new JsResponse();

        $response->data = Kurs::find()
            ->where(['EXTRACT(YEAR FROM plan_prospekt_god)'=>$year])
            ->andWhere(['rukovoditel'=>$employee])
            ->orderBy('id')
            ->all();

        return $response;
    }

    public function actionCheckProgramExistence(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = new JsResponse();
        $kurs_id = Yii::$app->request->post('kurs_id');
        if (Kurs::doesHaveProgram($kurs_id)){
            $response->data = true;
        }
        else{
            $response->data = false;
            $response->type = JsResponse::ERROR;
        }
        return $response;
    }

    public function actionCopyProgram(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $from = Yii::$app->request->post('from');
        $to = Yii::$app->request->post('to');
        $response = new JsResponse();

        $transaction = \Yii::$app->db->beginTransaction();
        try{

            if (Kurs::doesHaveProgram($to)){
                Kurs::deleteProgram($to);
            }
            /**
             * @var Kurs $from_kurs
             * @var Kurs $to_kurs
             */
            //$from_kurs = Kurs::findOne($from);
            $from_kurs = Kurs::find()
                ->joinWith('kimRel')
                ->joinWith('umkRel')
                ->joinWith('kontroliruyushihKursaRel')
                ->joinWith('temyDiplomnihRabotRel')
                ->joinWith('razdelyKursaRel.podrazdelyKursaRel.temyRel')
                ->joinWith('razdelyKursaRel.podrazdelyKursaRel.kimRel')
                ->joinWith('razdelyKursaRel.podrazdelyKursaRel.umkRel')
                ->joinWith('razdelyKursaRel.podrazdelyKursaRel.kontroliruyushihPodrazdelaKursaRel')
                ->joinWith('razdelyKursaRel.podrazdelyKursaRel.temyRel.kimRel')
                ->joinWith('razdelyKursaRel.podrazdelyKursaRel.temyRel.umkRel')
                ->where(['kurs.id'=>$from])
                ->one();
            //kurs
            $to_kurs = Kurs::findOne($to);

            $to_kurs->annotaciya = $from_kurs->annotaciya;
            $to_kurs->aktualnost = $from_kurs->aktualnost;
            $to_kurs->cel = $from_kurs->cel;
            $to_kurs->zadachi = $from_kurs->zadachi;
            $to_kurs->zadachi = $from_kurs->zadachi;
            $to_kurs->planiruemyeRezultaty = $from_kurs->planiruemyeRezultaty;
            $to_kurs->formaItogovojAttestacii = $from_kurs->formaItogovojAttestacii;
            $to_kurs->harakteristikaNovojKvalifikacii = $from_kurs->harakteristikaNovojKvalifikacii;
            $to_kurs->trebovaniya_k_urovnyu_podgotovki = $from_kurs->trebovaniya_k_urovnyu_podgotovki;
            $to_kurs->formaObucheniya = $from_kurs->formaObucheniya;
            $to_kurs->informacionnyeUsloviya = $from_kurs->informacionnyeUsloviya;
            $to_kurs->kadrovyeUsloviya = $from_kurs->kadrovyeUsloviya;
            $to_kurs->uchebnometodicheskieUsloviya = $from_kurs->uchebnometodicheskieUsloviya;
            $to_kurs->tehnicheskieUsloviya = $from_kurs->tehnicheskieUsloviya;
            $to_kurs->itogovayaAttestaciya = $from_kurs->itogovayaAttestaciya;
            $to_kurs->rezhimZanyatij = $from_kurs->rezhimZanyatij;
            $to_kurs->spisokLiteratury = $from_kurs->spisokLiteratury;
            $to_kurs->chasyItogovojAttestacii = $from_kurs->chasyItogovojAttestacii;
            $to_kurs->opisanieItogovojAttestacii = $from_kurs->opisanieItogovojAttestacii;
            $to_kurs->nedelyaItogovojAttestacii = $from_kurs->nedelyaItogovojAttestacii;
            $to_kurs->harakteristikaNovojKvalifikacii = $from_kurs->harakteristikaNovojKvalifikacii;
            $to_kurs->sostaviteli = $from_kurs->sostaviteli;
            $to_kurs->recenzenti = $from_kurs->recenzenti;
            $to_kurs->itogovayaAttestaciyaTekst = $from_kurs->itogovayaAttestaciyaTekst;
            $to_kurs->save();
            //temy_diplomnih_rabot
            foreach ($from_kurs->temyDiplomnihRabotRel as $item) {
                $new_tema_diplomnoj_raboty = new TemaDiplomnojRabotyKursa();
                $new_tema_diplomnoj_raboty->kurs = $to_kurs->id;
                $new_tema_diplomnoj_raboty->nazvanie = $item->nazvanie;
                $new_tema_diplomnoj_raboty->save();
            }
            //kim_kurs
            foreach ($from_kurs->kimRel as $item) {
                $new_kim_kurs = new KimKursa();
                $new_kim_kurs->kim = $item->id;
                $new_kim_kurs->kurs = $to_kurs->id;
                $new_kim_kurs->save();
            }
            //umk_kurs
            foreach ($from_kurs->umkRel as $item) {
                $new_umk_kurs = new UmkKursa();
                $new_umk_kurs->umk = $item->id;
                $new_umk_kurs->kurs = $to_kurs->id;
                $new_umk_kurs->save();
            }
            //kontroliruyushie_kursa
            foreach ($from_kurs->kontroliruyushihKursaRel as $item) {
                $new_kontroliruyshij_kursa = new KontroliruyuschijKursa();
                $new_kontroliruyshij_kursa->kontroliruyuschijFizLico = $item->kontroliruyuschijFizLico;
                $new_kontroliruyshij_kursa->kurs = $to_kurs->id;
                $new_kontroliruyshij_kursa->kontroliruyuschijVakansiya = $item->kontroliruyuschijVakansiya;
                $new_kontroliruyshij_kursa->save();
            }
            //razdels
            foreach ($from_kurs->razdelyKursaRel as $razdel) {
                /**
                 * @var RazdelKursa $razdel
                 */
                $new_razdel = new RazdelKursa();
                $new_razdel->kurs = $to_kurs->id;
                $new_razdel->nazvanie = $razdel->nazvanie;
                $new_razdel->nomer = $razdel->nomer;
                $new_razdel->tip = $razdel->tip;
                $new_razdel->save();
                //podrazdels
                foreach ($razdel->podrazdelyKursaRel as $podrazdel) {
                    /**
                     * @var PodrazdelKursa $podrazdel
                     */
                    $new_podrazdel = new PodrazdelKursa();
                    $new_podrazdel->razdel = $new_razdel->id;
                    $new_podrazdel->forma_kontrolya = $podrazdel->forma_kontrolya;
                    $new_podrazdel->rukovoditel = $podrazdel->rukovoditel;
                    $new_podrazdel->nomer = $podrazdel->nomer;
                    $new_podrazdel->nazvanie = $podrazdel->nazvanie;
                    $new_podrazdel->raschitano_chasov_lekcyj = $podrazdel->raschitano_chasov_lekcyj;
                    $new_podrazdel->raschitano_chasov_praktik = $podrazdel->raschitano_chasov_praktik;
                    $new_podrazdel->raschitano_chasov_srs = $podrazdel->raschitano_chasov_srs;
                    $new_podrazdel->chasy_kontrolya = $podrazdel->chasy_kontrolya;
                    $new_podrazdel->aktualnost = $podrazdel->aktualnost;
                    $new_podrazdel->cel = $podrazdel->cel;
                    $new_podrazdel->zadachi = $podrazdel->zadachi;
                    $new_podrazdel->planiruemye_rezultaty = $podrazdel->planiruemye_rezultaty;
                    $new_podrazdel->mesto_discipliny_v_strukture_programmy = $podrazdel->mesto_discipliny_v_strukture_programmy;
                    $new_podrazdel->informacionnye_usloviya = $podrazdel->informacionnye_usloviya;
                    $new_podrazdel->uchebnometodicheskie_usloviya = $podrazdel->uchebnometodicheskie_usloviya;
                    $new_podrazdel->kadrovye_usloviya = $podrazdel->kadrovye_usloviya;
                    $new_podrazdel->materialnotehnicheskie_usloviya = $podrazdel->materialnotehnicheskie_usloviya;
                    $new_podrazdel->literatura = $podrazdel->literatura;
                    $new_podrazdel->status = $podrazdel->status;
                    $new_podrazdel->nedelya_nachalo = $podrazdel->nedelya_nachalo;
                    $new_podrazdel->nedelya_konec = $podrazdel->nedelya_konec;
                    $new_podrazdel->rukovoditel_vakansiya = $podrazdel->rukovoditel_vakansiya;
                    $new_podrazdel->save();
                    //podrazdel_kim
                    foreach ($podrazdel->kimRel as $kim) {
                        $new_podrazdel_kim = new KimPodrazdelaKursa();
                        $new_podrazdel_kim->podrazdelKursa = $new_podrazdel->id;
                        $new_podrazdel_kim->kim = $kim->id;
                        $new_podrazdel_kim->save();
                    }
                    //podrazdel_umk
                    foreach ($podrazdel->umkRel as $umk) {
                        $new_podrazdel_umk = new UmkPodrazdelaKursa();
                        $new_podrazdel_umk->podrazdelKursa = $new_podrazdel->id;
                        $new_podrazdel_umk->umk = $umk->id;
                        $new_podrazdel_umk->save();
                    }
                    //kontroliruyushie_podrazdela_kursa
                    foreach ($podrazdel->kontroliruyushihPodrazdelaKursaRel as $item) {
                        $new_kontroliruyshij_podrazdela = new KontroliruyuschijPodrazdelaKursa();
                        $new_kontroliruyshij_podrazdela->kontroliruyuschijFizLico = $item->kontroliruyuschijFizLico;
                        $new_kontroliruyshij_podrazdela->podrazdelKursa = $new_podrazdel->id;
                        $new_kontroliruyshij_podrazdela->kontroliruyuschijVakansiya = $item->kontroliruyuschijVakansiya;
                        $new_kontroliruyshij_podrazdela->save();
                    }
                    //podrazdel_temy
                    foreach ($podrazdel->temyRel as $tema) {
                        /**
                         * @var Tema $tema
                         */
                        $new_tema = new Tema();
                        $new_tema->podrazdel = $new_podrazdel->id;
                        $new_tema->tip_raboty = $tema->tip_raboty;
                        $new_tema->forma_kontrolya = $tema->forma_kontrolya;
                        $new_tema->prepodavatel_fiz_lico = $tema->prepodavatel_fiz_lico;
                        $new_tema->nomer = $tema->nomer;
                        $new_tema->nazvanie = $tema->nazvanie;
                        $new_tema->soderzhanie = $tema->soderzhanie;
                        $new_tema->chasy = $tema->chasy;
                        $new_tema->nedelya = $tema->nedelya;
                        $new_tema->prepodavatel_vakansiya = $tema->prepodavatel_vakansiya;
                        $new_tema->save();
                        //tema_kim
                        foreach ($tema->kimRel as $kim) {
                            $new_tema_kim = new KimTemy();
                            $new_tema_kim->tema = $new_tema->id;
                            $new_tema_kim->kim = $kim->id;
                            $new_tema_kim->save();
                        }
                        //tema_umk
                        foreach ($tema->umkRel as $umk) {
                            $new_tema_umk = new UmkTemy();
                            $new_tema_umk->tema = $new_tema->id;
                            $new_tema_umk->umk = $umk->id;
                            $new_tema_umk->save();
                        }
                    }
                }
            }
            $transaction->commit();
        }
        catch(Exception $e){
            $transaction->rollBack();
            $response->type = JsResponse::ERROR;
            $response->msg = $e->getMessage();
        }
        return $response;
    }

    public function actionDeleteProgram(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $kurs_id = Yii::$app->request->post('kurs_id');
        $response = new JsResponse();
        /**
         * @var FuncResponse $deleting
         */
        $deleting = Kurs::deleteProgram($kurs_id);

        $response->type = $deleting->type;
        $response->msg = $deleting->msg;

        return $response;
    }

    /**
     * @inheritdoc
     */
    public function accessRules()
    {
        return [
            '*' => Rol::RUKOVODITEL_KURSOV
        ];
    }
}