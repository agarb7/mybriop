<?php
/**
 * Created by PhpStorm.
 * User: asv
 * Date: 22.10.2017
 * Time: 19:45
 */

namespace app\modules\upravlenie_kadrami\controllers;

use app\records\DolzhnostFizLicaNaRabote;
use Yii;
use app\enums2\Rol;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\data\ArrayDataProvider;
use app\modules\upravlenie_kadrami\models\Sotrudnik;

class SostavPodrazdelenijaController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        // all the action are accessible to ADMINISTRATOR, SOTRUDNIK_UCHEBNOGO_OTDELA and SOTRUDNIK_OTDELA_KADROV
                        'allow' => true,
                        'roles' => [
                            Rol::ADMINISTRATOR,
                            Rol::SOTRUDNIK_UCHEBNOGO_OTDELA,
                            Rol::SOTRUDNIK_OTDELA_KADROV,
                        ]
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }
    
    public function actionSostav()
    {
        if (Yii::$app->request->isAjax && $pid = Yii::$app->request->get('pid')){
            $query = Yii::$app->db->createCommand('select * from rabotajushhie_sotrudniki_briop where podrazdelenie_id = '.$pid)->queryAll();
            $data = new ArrayDataProvider([
                'allModels' => $query,
                'sort' => false
            ]);
            echo $this->renderAjax('sostav.php', ['data' => $data]);
        }
    }
    
    public function actionEdit()
    {
        $post = \Yii::$app->request->post();
        $model = new Sotrudnik();
        if($model->load($post) && $model->editImpl()) {
            \Yii::$app->session->setFlash('success','Данные успешно сохранены!',false);
            return $this->render('index');
        } elseif ($fl = Yii::$app->request->get('fl') and $dflnr = Yii::$app->request->get('dflnr')){
            $sotrudnik = new Sotrudnik($fl,$dflnr);
            return $this->render('edit.php', ['sotrudnik'=>$sotrudnik]);
        }
    }

    public function actionArhiv()
    {
        if ($dflnr = Yii::$app->request->get('dflnr')){
            $dnr = DolzhnostFizLicaNaRabote::findOne(['id' => $dflnr]);
            $dnr->actual = false;
            if ($dnr->save()) {
                \Yii::$app->session->setFlash('success','Сотрудник перемещен в архив!',false);
            } else {
                \Yii::$app->session->setFlash('danger','Произошла ошибка!',false);
            }
            return $this->render('index');
        } else return false;
    }

    public function actionSovmeshhenie ()
    {
        $post = \Yii::$app->request->post();
        $model = new Sotrudnik();
        if($model->load($post) && $model->sovmeshenieImpl()) {
            \Yii::$app->session->setFlash('success', 'Совмещение успешно добавлено!', false);
            return $this->render('index');
        } elseif ($fl = Yii::$app->request->get('fl') and $dflnr = Yii::$app->request->get('dflnr')){
            $sotrudnik = new Sotrudnik($fl,$dflnr);
            return $this->render('sovmeshhenie.php', ['sotrudnik'=>$sotrudnik]);
        }
    }
    
    public function actionPerevod()
    {
        $post = \Yii::$app->request->post();
        $model = new Sotrudnik();
        if($model->load($post) && $model->perevodImpl()) {
            \Yii::$app->session->setFlash('success', 'Сотрудник переведен!', false);
            return $this->render('index');
        } elseif ($fl = Yii::$app->request->get('fl') and $dflnr = Yii::$app->request->get('dflnr')){
            $sotrudnik = new Sotrudnik($fl,$dflnr);
            return $this->render('perevod.php', ['sotrudnik'=>$sotrudnik]);
        }
    }
    
}