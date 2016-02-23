<?php
use yii\web\View;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;

/**
 * @var $this View
 * @var $createParams array
 * @var $updateParams array
 * @var $indexParams array
 */
?>

<?php Modal::begin([
    'id' => 'modal-create',
    'header' => '<h4>Создание записи</h4>'
]) ?>

    <?php Pjax::begin(['id' => 'pjax-create']) ?>

        <?php if (isset($createParams)) echo $this->render('create', $createParams) ?>

    <?php Pjax::end() ?>

<?php Modal::end() ?>


<?php Modal::begin([
    'id' => 'modal-update',
    'header' => '<h4>Редактирование записи</h4>'
]) ?>

    <?php Pjax::begin(['id' => 'pjax-update']) ?>

        <?php if (isset($updateParams)) echo $this->render('update', $updateParams) ?>

    <?php Pjax::end() ?>

<?php Modal::end() ?>


<?php Modal::begin([
    'id' => 'modal-delete',
    'header' => '<h4>Удаление записи</h4>'
]) ?>

    <?php Pjax::begin(['id' => 'pjax-delete']) ?>

        <?php if (isset($deleteParams)) echo $this->render('delete', $deleteParams) ?>

    <?php Pjax::end() ?>

<?php Modal::end() ?>


<?php Modal::begin([
    'id' => 'modal-iup',
    'header' => '<h4>Индивидуальный учебный план</h4>'
]) ?>

    <?php Pjax::begin(['id' => 'pjax-iup']) ?>

        <?php if (isset($iupParams)) echo $this->render('iup', $iupParams) ?>

    <?php Pjax::end() ?>

<?php Modal::end() ?>


<?php Pjax::begin(['id' => 'pjax-index']) ?>

    <?= $this->render('index', $indexParams) ?>

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

<!-- todo ensure modal shown -->