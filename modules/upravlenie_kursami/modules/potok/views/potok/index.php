<?php

use app\upravlenie_kursami\potok\PotokAsset;

/**
 * @var $this \yii\web\View
 */

//todo clear on ajax-error
//todo clear on ajax-indicator
//todo local storage
$js = <<<'JS'
    (function(){       
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
        
        var baseUrl = '/upravlenie-kursami/potok/potok/';
        var kursListUrl = baseUrl + 'kurs-list';
        var temaListUrl = baseUrl + 'tema-list?kurs=';
        var applyPotokUrl = baseUrl + 'apply-potok';
        
        var kursList = new KursList;
        
        var $temaModal = $('#tema-modal');
        var $applyModal = $('#apply-modal');        
        var $kursTable = $('#kurs-table');       
        var $applyBtnBlock = $('#apply-btn-block');
        
        $.templates({
            kursListTmpl: '#kurs-list-tmpl',
            temaListTmpl: '#tema-list-tmpl',
            applyModalTmpl: '#apply-modal-tmpl',
            applyBtnBlockTmpl: '#apply-btn-block-tmpl'
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
        
        $kursTable.on("click", ".kurs-table__row", function (e) {
            if (e.isDefaultPrevented()) 
                return;
            
            var kurs = $.view(e.currentTarget).data;
            var url = temaListUrl + kurs.id;
            
            $.link.temaListTmpl("#tema-modal .modal-body", kurs);
            $temaModal.modal("show");            
            
            $.getJSON(url, function (data) {
                kurs.loadTemy(data);
            });
             
            e.preventDefault();
        });        
        
        $temaModal.on('click', '.tema-modal__tema', function (e) {            
            var tema = $.view(e.currentTarget).data;
            var kurs = tema.podrazdel.razdel.kurs;
            
            if (kurs.tema === tema)
                tema = null;
            
            $.observable(kurs).setProperty('tema', tema);
            
            if (tema !== null)
                $temaModal.modal('hide');            
        });
        
        $applyBtnBlock.on('click', '.apply-btn', function (e) {
            $applyModal.modal('show');
            
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
  <tr class="kurs-table__row">
    <td>
        <article class="kurs-table__kurs">
            <header>
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
                class{merge:isInSchedule() toggle='tema-modal__tema_in-schedule'}
                class{merge:isInStreamOnly() toggle='tema-modal__tema_in-stream-only'}
            ">
                <h4>{{:~baseNomer}}.{{:nomer}}. {{:nazvanie}}</h4>

                <div class="tema-modal__prepodavatel">
                    {{if prepodavatel}}
                        <p class="tema-modal__fio">{{:prepodavatel.fio}}</p>
                        <p class="tema-modal__podrazdeleniya">{{:prepodavatel.podrazdeleniya}}</p>
                    {{/if}}
                </div>

                <p class="tema-modal__tip_raboty">{{:tip_raboty}}</p>

                <div class="tema-modal__info">
                    {{if isInSchedule()}}
                        <p class="tema-modal__in-schedule">В расписании</p>
                    {{/if}}

                    {{if isInStreamOnly()}}
                        <p class="tema-modal__in-stream-only">
                            Уже в потоке
                            <a href="#" class="tema-modal__remove-from-stream">Убрать</a>
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
                        <td>{^{:tema^prepodavatel}}</td>
                    </tr>
                {{/if}}
            {{/for}}
        </tbody>
    </table>

    <input>
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
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<div class="tema-modal modal fade" id="tema-modal" tabindex="-1">
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
                <button type="button" class="btn btn-primary">Запоточить</button>
            </div>
        </div>
    </div>
</div>

<div id="apply-btn-block">
</div>

</div>