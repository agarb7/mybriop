<?php
namespace app\modules\spisok_slushatelej\controllers;


use app\modules\spisok_slushatelej\models\DannyeSlushatelja;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use Yii;

use app\enums2\Rol;
use app\enums2\StatusKursaFizLica;
use app\records\Kurs;

use app\modules\spisok_slushatelej\models\Slushatel;
use app\modules\spisok_slushatelej\models\KursSlushatelya;


class SlushatelController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [
                            Rol::SOTRUDNIK_UCHEBNOGO_OTDELA,
                            Rol::RUKOVODITEL_KURSOV
                        ]
                    ]
                ],
            ],

            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'cancel' => ['post'],
                    'sign-up-again' => ['post'],
                    'accept-iup' => ['post'],
                    'cancel-iup' => ['post'],
                    'change-data-slushatelya' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex($kurs)
    {
        // todo can rule

        $kursRecord = Kurs::findOne($kurs);
        if (!$kursRecord)
            throw new NotFoundHttpException;

        $query = Slushatel::findForKurs($kurs)->orderBy([
            'kurs_fiz_lica.status' => SORT_ASC,
            'fiz_lico.familiya' => SORT_ASC,
            'fiz_lico.imya' => SORT_ASC,
            'fiz_lico.otchestvo' => SORT_ASC,
        ]);

        $provider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false
        ]);

        return $this->render('index', compact('provider', 'kursRecord'));
    }

    public function actionCancel($kurs, $fizLico)
    {
        return $this->actionChangeStatusSlushatelya($kurs, $fizLico, StatusKursaFizLica::OTMENEN_BRIOP);
    }

    public function actionSignUpAgain($kurs, $fizLico)
    {
        return $this->actionChangeStatusSlushatelya($kurs, $fizLico, StatusKursaFizLica::ZAPISAN);
    }

    public function actionAcceptIup($kurs, $fizLico)
    {
        return $this->actionChangeStatusSlushatelya($kurs, $fizLico, StatusKursaFizLica::ZAPISAN);
    }

    public function actionCancelIup($kurs, $fizLico)
    {
        return $this->actionChangeStatusSlushatelya($kurs, $fizLico, StatusKursaFizLica::OTMENEN_BRIOP);
    }

    private function actionChangeStatusSlushatelya($kurs, $fizLico, $status)
    {
        // todo can rule

        /* @var $model KursSlushatelya */
        $model = KursSlushatelya::findOne(['kurs' => $kurs, 'fiz_lico' => $fizLico]);
        if (!$model)
            throw new NotFoundHttpException;

        $model->setStatusAndVremya($status);
        $model->save();

        return $this->redirect(['index', 'kurs' => $kurs]);
    }
    
    public function actionEditDannyeSlushatelja($kurs, $fizLico)
    {
        $user = \Yii::$app->user;
        if ($user->isGuest) return $this->redirect('/polzovatel/vhod');

        if (Yii::$app->request->post('submit')==='edit') {
            $post = Yii::$app->request->post();
            $kurs = $post['kurs'];
            $newDannyeSlushatelja = new DannyeSlushatelja();
            $newDannyeSlushatelja->fizLicoId = $post['DannyeSlushatelja']['fizLicoId'];
            $newDannyeSlushatelja->familiya = $post['DannyeSlushatelja']['familiya'];
            $newDannyeSlushatelja->imya = $post['DannyeSlushatelja']['imya'];
            $newDannyeSlushatelja->otchestvo = $post['DannyeSlushatelja']['otchestvo'];
            $newDannyeSlushatelja->organizacii = $post['DannyeSlushatelja']['organizacii'];
            $newDannyeSlushatelja->rajony = $post['DannyeSlushatelja']['rajony'];
            if ($newDannyeSlushatelja->save()) {
                \Yii::$app->session->setFlash('success','Данные успешно обновлены!',false);
                $this->redirect('index?kurs='.$kurs);
            } else {
                \Yii::$app->session->setFlash('danger','Данные не обновлены!',false);
                $this->redirect('index?kurs='.$kurs);
            }
        } else {
            $model = new DannyeSlushatelja($fizLico);
            if (!$model)
                throw new NotFoundHttpException;
            return $this->render('edit', compact('model','kurs'));
        }
    }
}