<?php

use app\upravlenie_kursami\potok\PotokAsset;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * @var $this \yii\web\View
 * @var $prepodavateli array
 */

//todo clear on ajax-error
//todo clear on ajax-indicator
//todo local storage
$js = <<<'JS'
    (function(){
        var baseUrl = '/upravlenie-kursami/potok/potok/';
        var kursListUrl = baseUrl + 'kurs-list';
        var temaListUrl = baseUrl + 'tema-list?kurs=';
        var temaZanyatiyaListUrl = baseUrl + 'temy-zanyatiya-list?id=';
        var createZanyatieUrl = baseUrl + 'create-zanyatie';
        var deleteZanyatieUrl = baseUrl + 'delete-zanyatie?id=';
        var allowRaspisanieUrl = baseUrl + 'allow-raspisanie?';        
        
        function Tema(other) {
            $.extend(this, other);
        }
        
        Tema.prototype.isSelected = function () {
            return this === this.podrazdel.razdel.kurs.tema;
        };
        
        Tema.prototype.isSelected.depends = 'podrazdel.razdel.kurs.tema';
        
        Tema.prototype.isInSchedule = function () {
            return this.zanyatie && this.zanyatie.data;
        };        
        
        Tema.prototype.isInStreamOnly = function () {
            return this.zanyatie && this.zanyatie.id && !this.zanyatie.data;
        };        
        
        function Kurs(other) {
            $.extend(this, other);
            
            this.razdely = [];
        }
        
        Kurs.prototype.loadTemy = function (razdely) {
            var self = this;
            
            $.each(razdely, function (i, razdel) {
                razdel.kurs = self;
                $.each(razdel.podrazdely, function (i, podrazdel) {
                    podrazdel.razdel = razdel; 
                    $.each(podrazdel.temy, function (i, oldTema) {                        
                        var tema = new Tema(oldTema);
                        podrazdel.temy[i] = tema;
                        
                        tema.podrazdel = podrazdel;                        
                                                 
                        var wasSelected = self.tema 
                            && tema.id 
                            && self.tema.id === tema.id
                             
                        if (wasSelected)
                            self.tema = tema;
                    });
                });
            });
                        
            $.observable(self).setProperty('razdely', razdely);
        };
        
        Kurs.prototype.canRazreshit = function () {
            return this.status_programmy === 'zavershena'
                && this.status_raspisaniya === null;
        };
        
        Kurs.prototype.canRazreshit.depends = ['status_programmy','status_raspisaniya'];
        
        Kurs.prototype.canZapretit = function () {
            return this.status_programmy === 'zavershena'
                && this.status_raspisaniya === 'redaktiruetsya';            
        };
        
        Kurs.prototype.canZapretit.depends = ['status_programmy','status_raspisaniya'];
        
        function KursList() {
            var self = this;
            
            self.data = [];
        }        
        
        KursList.prototype.loadData = function (ajaxData) {
            var self = this;            
            var hash = {};
            
            $.each(self.data, function (i, kurs) {
                hash[kurs.id] = kurs;
            });
            
            $.each(ajaxData, function (i, oldKurs) {                
                var kurs = new Kurs(oldKurs);
                ajaxData[i] = kurs;
                
                var hashedKurs = hash[kurs.id];
                if (hashedKurs)
                    kurs.tema = hashedKurs.tema;                    
            });
            
            $.observable(self).setProperty('data',ajaxData);            
        };
        
        //todo refactor to use hash for store selected
        KursList.prototype.selectedCount = function () {
            var self = this;
            
            var result = 0;
            
            for (var i=0; i<self.data.length; ++i)                
                if (self.data[i].tema)                     
                    ++result;
            
            return result;
        };
        
        KursList.prototype.selectedCount.depends = 'data^**';
        
        KursList.prototype.zanyatieCreationData = function (formData) {
            var self = this;
            var chastiTem = {
                temy: [],
                chasti_tem: []
            };
            
            $.each(self.data, function (i, kurs) {
                if (kurs.tema) {
                    chastiTem.temy.push(kurs.tema.id);
                    chastiTem.chasti_tem.push(kurs.tema.chast);
                }
            });
            
            return $.extend({}, formData, chastiTem);
        };        
        
        var kursList = new KursList;
        
        var $temaModal = $('#tema-modal');
        var $applyModal = $('#apply-modal');        
        var $kursTable = $('#kurs-table');       
        var $applyBtnBlock = $('#apply-btn-block');
        var $finalApplyBtn = $('#final-apply-btn');        
        var $deleteModal = $('#delete-modal');
        var $finalDeleteBtn = $('#final-delete-btn');
        var $ajaxLoader = $('#ajax-loader');
        
        $(document).ajaxStart(function() {
            console.log($ajaxLoader.get(0));
            $ajaxLoader.addClass('ajax-loader_shown');
            $ajaxLoader.removeClass('ajax-loader_hidden');            
        });
        
        $(document).ajaxStop(function() {
            console.log('stop');
            $ajaxLoader.removeClass('ajax-loader_shown');
            $ajaxLoader.addClass('ajax-loader_hidden');
        });
        
        $(document).ajaxError(function(e, req, settings) {
            var msgs = {
                'GET': 'Произошла ошибка: данные не загружены',
                'POST': 'Произошла ошибка: данные могут быть НЕ изменены'                
            };
            
            var msg = msgs[settings.type] || 'Произошла ошибка';
            alert(msg);
        });
        
        $.templates({
            kursListTmpl: '#kurs-list-tmpl',
            temaListTmpl: '#tema-list-tmpl',
            applyModalTmpl: '#apply-modal-tmpl',
            applyBtnBlockTmpl: '#apply-btn-block-tmpl',
            deleteModalTmpl: '#delete-modal-tmpl'
        });
        
        $.views.tags({
            date: {template: '#date-tmpl'},
            daterange: {template: '#daterange-tmpl'}
        });
                
        $.link.kursListTmpl('#kurs-table tbody', kursList);        
        $.link.applyModalTmpl('#apply-modal .modal-body', kursList);
        $.link.applyBtnBlockTmpl('#apply-btn-block', kursList);        
                
        $.getJSON(kursListUrl, function (data) {
            kursList.loadData(data);
        });
        
        $kursTable.on('click', '.kurs-table__annotaciya-kursa-shower', function (e) {
            $(e.target).closest('.kurs-table__kurs').addClass('kurs-table__kurs_annotaciya');
            e.preventDefault();
        });
                
        $kursTable.on('click', '.kurs-table__annotaciya-kursa-hider', function (e) {
            $(e.target).closest('.kurs-table__kurs').removeClass('kurs-table__kurs_annotaciya');
            e.preventDefault();
        });        
        
        $kursTable.on("click", ".kurs-table__row_est_programma", function (e) {
            if (e.isDefaultPrevented()) 
                return;
            
            var kurs = $.view(e.currentTarget).data;
            var url = temaListUrl + kurs.id;
            
            $.link.temaListTmpl("#tema-modal .modal-body", kurs);
            
            $.getJSON(url, function (data) {
                kurs.loadTemy(data);
                $temaModal.modal("show");
            }).fail()
             
            e.preventDefault();
        });
        
        function allowRaspisanie (allow) {
            return function (e) {
                var kurs = $.view(e.currentTarget).data;
                
                var url = allowRaspisanieUrl + $.param({
                    kurs: kurs.id,
                    allow: allow
                });
                
                $.post(url, function () {
                    alert(allow ? 'Расписание было разрешено' : 'Расписание было запрещено');
                
                    location.reload(true);
                });
                
                e.preventDefault();
            };
        }
                
        $kursTable.on("click", ".allow-raspisanie-btn", allowRaspisanie(true));
        $kursTable.on("click", ".disallow-raspisanie-btn", allowRaspisanie(false));
        
        $temaModal.on('click', '.tema-modal__tema_selectable', function (e) {            
            var tema = $.view(e.currentTarget).data;
            var kurs = tema.podrazdel.razdel.kurs;
            
            if (kurs.tema === tema)
                tema = null;
            
            $.observable(kurs).setProperty('tema', tema);
            
            if (tema !== null)
                $temaModal.modal('hide');         
                   
            e.preventDefault();
        });
        
        $temaModal.on('click', '.tema-modal__remove-from-stream', function (e) {            
            var tema = $.view(e.currentTarget).data;
            var zanId = tema.zanyatie.id;
            var url = temaZanyatiyaListUrl + zanId; 
            
            $.get(url, function (data) {
                $temaModal.modal('hide');
                
                $.link.deleteModalTmpl('#delete-modal .modal-body', [data]);
                
                $deleteModal.data('id', zanId);
                $deleteModal.modal('show');                
            });
           
            e.preventDefault();
        });
        
        $finalDeleteBtn.on('click', function () {
            var url = deleteZanyatieUrl + $deleteModal.data('id');
            
            $.post(url, function () {
                $deleteModal.hide();
                
                alert('Поток был улалён');
                
                location.reload(true);
            });
            
            e.preventDefault();
        });
        
        $applyBtnBlock.on('click', '.apply-btn', function (e) {
            $applyModal.modal('show');
            
            e.preventDefault();            
        });
        
        $finalApplyBtn.on('click', function (e) {
            var $applyForm = $('#apply-form');
            var formData = {};

            $.each($applyForm.serializeArray(), function (i, item) {                
                formData[item.name] = item.value;
            });

            var data = kursList.zanyatieCreationData(formData);

            $.post(createZanyatieUrl, data, function () {
                $applyModal.modal('hide');
               
                alert('Поток был создан');
                
                location.reload(true);
            });
            
            e.preventDefault();
        });
        
    })();
JS;

$this->registerJs($js);
PotokAsset::register($this);

$this->title = "БРИОП - Потоки";
?>

<script id="date-tmpl" type="text/x-jsrender">
    {{if ~tag.tagCtx.args[1]}}
        <p class="kurs-table__daterange-date">{{:~tag.tagCtx.args[0] + " " + ~tag.tagCtx.args[1]}}</p>
    {{/if}}
</script>

<script id="daterange-tmpl" type="text/x-jsrender">
    {{if ~tag.tagCtx.args[1].nachalo || ~tag.tagCtx.args[1].konec}}
        <dt class="kurs-table__daterange-term">{{:~tag.tagCtx.args[0]}}</dt>
        <dd class="kurs-table__daterange-desc">
            {{date "с"  ~tag.tagCtx.args[1].nachalo /}}
            {{date "по" ~tag.tagCtx.args[1].konec   /}}
        </dd>
    {{/if}}
</script>

<script id="kurs-list-tmpl" type="text/x-jsrender">
  {^{for data}}
    <tr class="kurs-table__row {{if est_programma link=false}}kurs-table__row_est_programma{{/if}}">
    <td>
        <article class="kurs-table__kurs">
            <header>
                {{if !est_programma}}
                    <em class="hasnt-programm">нет программы!</em>
                {{else status_programmy !== 'zavershena'}}
                    <em class="not-completed">не подписана!</em>
                {{/if}}

                <h4>{{:nazvanie}}</h4>
            </header>
            {{if annotaciya}}
                <section class="kurs-table__annotaciya-kursa">
                    {{:annotaciya}}
                </section>
                <footer>
                    <a href="#" class="kurs-table__annotaciya-kursa-shower">показать описание...</a>
                    <a href="#" class="kurs-table__annotaciya-kursa-hider">скрыть описание</a>
                </footer>
            {{/if}}
        </article>
    </td>
    <td>
        {{:rukovoditel}}
    </td>
    <td>
        <dl>
            {{daterange "очно" ochnoe /}}
            {{daterange "заочно" zaochnoe /}}
        </dl>
    </td>
    <td>
        <p>{{:raschitano_chasov}}</p>
    </td>
    <td>
        {^{if tema}}
            <p>{^{:tema^nazvanie}}</p>
        {{/if}}
    </td>
    <td>
    {{if canRazreshit()}}
        <a class="allow-raspisanie-btn btn btn-default">Разрешить<br>расписание</a>
    {{else canZapretit()}}
        <a class="disallow-raspisanie-btn btn btn-default">Запретить<br>расписание</a>
    {{/if}}
     </td>
  </tr>
  {{/for}}
</script>

<script id="tema-list-tmpl" type="text/x-jsrender">
    {^{for razdely}}
    <section class="tema-modal__razdel">
        <header>
            <h2>{{:nomer}}. {{:nazvanie}}</h2>
        </header>

        {{for podrazdely ~baseNomer=nomer}}
        <section class="tema-modal__podrazdel">
            <header>
                <h3>{{:~baseNomer}}.{{:nomer}}. {{:nazvanie}}</h3>
            </header>

            {{for temy ~baseNomer = ~baseNomer + '.' + nomer}}
            <section class="tema-modal__tema" data-link="
                class{merge:isSelected() toggle='tema-modal__tema_selected'}
                class{merge:!isInSchedule()
                    && !isInStreamOnly()
                    && podrazdel.razdel.kurs.status_programmy === 'zavershena'
                toggle='tema-modal__tema_selectable'}
            ">
                <h4>{{:~baseNomer}}.{{:nomer}}. {{:nazvanie}}</h4>

                <div class="tema-modal__prepodavatel">
                    {{if prepodavatel}}
                        <p class="tema-modal__fio">{{:prepodavatel.fio}}</p>
                        <p {{if prepodavatel.podrazdeleniya}}class="tema-modal__podrazdeleniya"{{/if}}>
                            {{:prepodavatel.podrazdeleniya}}
                        </p>
                    {{/if}}
                </div>

                <p class="tema-modal__tip_raboty">{{:tip_raboty}}</p>

                <div class="tema-modal__info">
                    {{if isInSchedule()}}
                        <p class="tema-modal__in-schedule">В расписании</p>
                    {{/if}}

                    {{if isInStreamOnly()}}
                        <p class="tema-modal__in-stream-only">
                            Уже в потоке:
                            <a href="#" class="tema-modal__remove-from-stream">убрать?</a>
                        </p>
                    {{/if}}
                </div>
            </section>
            {{/for}}

        </section>
        {{/for}}

    </section>
    {{/for}}
</script>

<script id="apply-modal-tmpl" type="text/x-jsrender">
    <table>
        <thead>
            <tr>
                <th>Курс</th>
                <th>Тема</th>
                <th>Преподаватель</th>
            </tr>
        </thead>
        <tbody>
            {^{for data}}
                {^{if tema}}
                    <tr>
                        <td>{^{:nazvanie}}</td>
                        <td>{^{:tema^nazvanie}}</td>
                        <td>{^{:tema^prepodavatel.fio}}</td>
                    </tr>
                {{/if}}
            {{/for}}
        </tbody>
    </table>

    <form id="apply-form" class="form-horizontal">
        <div class="form-group">
            <label class="col-sm-2 control-label">Тема потока</label>
            <div class="col-sm-10">
                <input class="form-control" name="nazvanie">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">Преподаватель</label>
            <div class="col-sm-10">
                <?= Html::dropDownList(
                    'prepodavatel',
                    null,
                    ArrayHelper::merge(['' => ''], $prepodavateli),
                    [
                        'class' => 'form-control'
                    ]
                );?>
            </div>
        </div>
    </form>
</script>

<script id="delete-modal-tmpl" type="text/x-jsrender">
    <p>
        Поток состоящий из следующих тем будет удалён. Продолжить?
    </p>

    <table>
        <thead>
            <tr>
                <th>Курс</th>
                <th>Тема</th>
                <th>Преподаватель</th>
            </tr>
        </thead>
        <tbody>
            {^{for}}
                <tr>
                    <td>{^{:kurs_nazvanie}}</td>
                    <td>{^{:podrazdely[0].temy[0].nazvanie}}</td>
                    <td>{^{:podrazdely[0].temy[0].prepodavatel.fio}}</td>
                </tr>
            {{/for}}
        </tbody>
    </table>
</script>

<script id="apply-btn-block-tmpl" type="text/x-jsrender">
    {{!--Todo help-block if no ability to stream --}}
    {^{if selectedCount()>1}}
        <a href="#" class="apply-btn btn btn-primary">Запоточить...</a>
    {{/if}}
</script>

<div class="upravlenie-kursami-potok">

<table id="kurs-table" class="kurs-table">
    <thead>
    <tr>
        <th>Курс</th>
        <th>Руководитель</th>
        <th class="kurs-table__daterange-header">Проведение</th>
        <th>Часы</th>
        <th class="kurs-table__tema-header">Выбранная тема</th>
        <th class="kurs-table__action-header"></th>
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<div class="modal fade" id="tema-modal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Выбор темы потока</h4>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="apply-modal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Параметры потока</h4>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" id="final-apply-btn" class="btn btn-primary">Запоточить</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="delete-modal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Удалить поток</h4>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" id="final-delete-btn" class="btn btn-primary">Удалить</button>
            </div>
        </div>
    </div>
</div>

<div id="apply-btn-block">
</div>

</div>

<div id="ajax-loader" class="ajax-loader ajax-loader_hidden"></div>
