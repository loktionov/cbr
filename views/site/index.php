<style>
    .col-20 {
        width: 80%;
        margin: 10px;
        float: none;
    }
</style>
<?php
/* @var $this yii\web\View */
use app\models\Valutes;
use kartik\select2\Select2;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\daterange\DateRangePicker;


$this->title = 'Курс валют';
$form = ActiveForm::begin([
    'id' => 'valute-form',
    'options' => ['class' => 'form-horizontal'],
]);
$data = ArrayHelper::map(Valutes::find()->all(), 'VnumCode', 'Vname');
?>
<div class="col-20">
    <? echo Select2::widget([
        'name' => 'v1',
        'value' => @$_POST['v1'],
        'data' => $data,
        'options' => ['placeholder' => 'Первая валюта']
    ]); ?>
</div>
<div class="col-20">
    <? echo Select2::widget([
        'name' => 'v2',
        'value' => @$_POST['v2'],
        'data' => $data,
        'options' => ['placeholder' => 'Вторая валюта']
    ]);
    ?>
</div>
<div class="col-20">
    <?
    echo Html::input('date', 'date', (!empty($_POST['date']) ? $_POST['date'] : date('Y-m-d')));
    ?>
</div>
<div class="col-20">
    <?
    echo Html::input('number', 'sum', (!empty($_POST['sum']) ? $_POST['sum'] : 1));
    ?>
</div>
<div class="col-20" style="font-size: 40px">
    <? echo $res; ?>
</div>
<div class="form-group">

    <?= Html::submitButton('Посчитать', ['class' => 'btn btn-primary']) ?>

</div>
<? ActiveForm::end(); ?>

<?
$form = ActiveForm::begin([
    'id' => 'period-form',
    'options' => ['class' => 'form-horizontal'],
]); ?>
<div class="col-20">
    <? echo Select2::widget([
        'name' => 'v3',
        'value' => @$_POST['v3'],
        'data' => $data,
        'options' => ['placeholder' => 'Динамика']
    ]); ?>
</div>
<div class="col-20">
    <? echo '<label class="control-label">Date Range</label>';
    echo '<div class="drp-container">';
    echo DateRangePicker::widget([
        'name' => 'date_range',
        'presetDropdown' => true,
        'hideInput' => true
    ]);
    echo '</div>';
    ?>
</div>
<?=
\dosamigos\highcharts\HighCharts::widget([
    'clientOptions' => [
        'title' => [
            'text' => 'Динамика курса валюты'
        ],
        'xAxis' => [
            'type' => 'datetime'
        ],
        'yAxis' => [
            'title' => [
                'text' => 'Курс'
            ]
        ],
        'series' => [
            ['name'=>'Курс', 'data' => $range],
        ]
    ]
]);
?>
<div class="form-group">

    <?= Html::submitButton('Показать динамику', ['class' => 'btn btn-primary']) ?>

</div>
<? ActiveForm::end(); ?>