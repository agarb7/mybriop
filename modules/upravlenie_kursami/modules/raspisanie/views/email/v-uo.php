<p>Здравствуйте , <?= $sotrudnik->getFio(true) ?></p>

<p>
    Расписание курса "<?= $kurs->nazvanie ?>" (<?= \app\globals\ApiGlobals::dateToStr($kurs->plan_prospekt_god, 'Y') ?>), руководитель <?= $kurs->rukovoditel_rel->getFio(true)?>,  готово к проверке
</p>

<p style="text-align: center">
    <a href="<?= \yii\helpers\Url::to(['@web/upravlenie-kursami/raspisanie/zanyatie','kurs' => $kurs->id], true) ?>"
       style="display: inline-block;
              padding: 6px 12px;
              margin-bottom: 0;
              font-size: 14px;
              font-weight: normal;
              line-height: 1.42857143;
              text-align: center;
              white-space: nowrap;
              vertical-align: middle;
              -ms-touch-action: manipulation;
                  touch-action: manipulation;
              cursor: pointer;
              -webkit-user-select: none;
                 -moz-user-select: none;
                  -ms-user-select: none;
                      user-select: none;
              background-image: none;
              border: 1px solid transparent;
              border-radius: 4px;
              color: #fff;
              background-color: #337ab7;
              border-color: #2e6da4;"
    >
        Подробнее
    </a>
</p>