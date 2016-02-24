<?php
use app\helpers\Html;
use kartik\date\DatePickerAsset;
use kartik\select2\Select2;
use kartik\touchspin\TouchSpinAsset;
use yii\web\View;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;

/**
 * @var $this View
 * @var $createParams array
 * @var $updateParams array
 * @var $indexParams array
 */

// workaround for kratik-select2 pjax loading bug
echo Html::tag('div', Select2::widget(['name' => 'stub']), ['class' => 'hidden']);

TouchSpinAsset::register($this);
DatePickerAsset::register($this);
?>

<?php Modal::begin([
    'id' => 'modal-create',
    'header' => '<h4>Создание записи</h4>'
]) ?>

    <?php Pjax::begin(['id' => 'pjax-create']) ?>

        <?php if (isset($createParams)): ?>

            <?= $this->render('_form', $createParams) ?>

        <?php endif ?>

    <?php Pjax::end() ?>

<?php Modal::end() ?>


<?php Modal::begin([
    'id' => 'modal-update',
    'header' => '<h4>Редактирование записи</h4>'
]) ?>

    <?php Pjax::begin(['id' => 'pjax-update']) ?>

        <?php if (isset($updateParams)): ?>

            <?= $this->render('_form', $updateParams) ?>

        <?php endif ?>

    <?php Pjax::end() ?>

<?php Modal::end() ?>


<?php Modal::begin([
    'id' => 'modal-delete',
    'header' => '<h4>Удаление записи</h4>'
]) ?>

    <?php Pjax::begin(['id' => 'pjax-delete']) ?>

        <?php if (isset($deleteParams)): ?>

            <?= $this->render('_delete-form', $deleteParams) ?>

        <?php endif ?>

    <?php Pjax::end() ?>

<?php Modal::end() ?>


<?php Modal::begin([
    'id' => 'modal-iup',
    'header' => '<h4>Индивидуальный учебный план</h4>'
]) ?>

    <?php Pjax::begin(['id' => 'pjax-iup']) ?>

        <?php if (isset($iupParams)): ?>

            <?= $this->render('_iup-form', $iupParams) ?>

        <?php endif ?>

    <?php Pjax::end() ?>

<?php Modal::end() ?>


<?php Pjax::begin(['id' => 'pjax-index']) ?>

    <?php if (isset($indexParams)): ?>

        <?= $this->render('_index', $indexParams) ?>

    <?php endif ?>

<?php Pjax::end() ?>


<?php $this->registerJs("
    var setupGridClickHandlers = function () {
        var bind = function (btn, modal, container) {
            $(btn).click(function (event) {
                $(modal).modal('show');
                var url = $(event.target).prop('href');
                $.pjax({url:url, container: container, scrollTo: false});
                event.preventDefault();
            });
        }

        bind('.btn-update', $('#modal-update'), $('#pjax-update'));
        bind('.btn-create', $('#modal-create'), $('#pjax-create'));
        bind('.btn-delete', $('#modal-delete'), $('#pjax-delete'));
        bind('.btn-iup', $('#modal-iup'), $('#pjax-iup'));
    };

    setupGridClickHandlers();

    $('#pjax-index').on('pjax:end', setupGridClickHandlers);

    $('#pjax-update, #pjax-create, #pjax-iup, #pjax-delete').on('pjax:end', function () {
        \$this = $(this);
        if (\$this.find('.data').data('tohide'))
            \$this.closest('.modal').modal('hide');
    });

    $('#modal-update, #modal-create, #modal-iup, #modal-delete').on('hidden.bs.modal', function () {
        $.pjax({
            url: $(this).find('.data').data('backurl'),
            container: '#pjax-index',
            scrollTo: false
        });
    });
") ?>

<!-- todo
    ensure modal shown
    urlCreator in proper place
    reduce modal structure
    asset
    clear docblock
    hashids
-->
