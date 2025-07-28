<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\models\ShippingMethod;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Shipping Methods';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shipping-method-index">

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><?= Html::encode($this->title) ?></h3>
                    <div class="card-tools">
                        <?= Html::a('Create Shipping Method', ['create'], ['class' => 'btn btn-success']) ?>
                    </div>
                </div>
                <div class="card-body">
                    
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <?= Html::textInput('name', Yii::$app->request->get('name'), [
                                'class' => 'form-control',
                                'placeholder' => 'Search by name...',
                                'id' => 'filter-name'
                            ]) ?>
                        </div>
                        <div class="col-md-3">
                            <?= Html::dropDownList('provider', Yii::$app->request->get('provider'), 
                                array_merge(['' => 'All Providers'], ShippingMethod::getProviderOptions()), [
                                'class' => 'form-control',
                                'id' => 'filter-provider'
                            ]) ?>
                        </div>
                        <div class="col-md-3">
                            <?= Html::dropDownList('status', Yii::$app->request->get('status'), [
                                '' => 'All Statuses',
                                ShippingMethod::STATUS_ACTIVE => 'Active',
                                ShippingMethod::STATUS_INACTIVE => 'Inactive'
                            ], [
                                'class' => 'form-control',
                                'id' => 'filter-status'
                            ]) ?>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-primary" id="apply-filters">Apply Filters</button>
                            <a href="<?= \yii\helpers\Url::to(['index']) ?>" class="btn btn-secondary">Clear</a>
                        </div>
                    </div>

                    <?php Pjax::begin(); ?>
                    
                    <!-- Bulk Actions -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="btn-group">
                                <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
                                    Bulk Actions
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="#" onclick="bulkAction('activate')">
                                        <i class="fas fa-check"></i> Activate Selected
                                    </a>
                                    <a class="dropdown-item" href="#" onclick="bulkAction('deactivate')">
                                        <i class="fas fa-times"></i> Deactivate Selected
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item text-danger" href="#" onclick="bulkDelete()">
                                        <i class="fas fa-trash"></i> Delete Selected
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'layout' => '{items}{pager}',
                        'tableOptions' => ['class' => 'table table-striped table-bordered'],
                        'columns' => [
                            [
                                'class' => 'yii\grid\CheckboxColumn',
                                'checkboxOptions' => function ($model, $key, $index, $column) {
                                    return ['value' => $model->id];
                                },
                            ],
                            [
                                'attribute' => 'sort_order',
                                'label' => 'Order',
                                'value' => function ($model) {
                                    return '<span class="badge badge-secondary">' . $model->sort_order . '</span>';
                                },
                                'format' => 'raw',
                                'options' => ['style' => 'width: 80px;'],
                            ],
                            [
                                'attribute' => 'name',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    $html = Html::a(Html::encode($model->name), ['view', 'id' => $model->id], [
                                        'class' => 'text-primary font-weight-bold'
                                    ]);
                                    if ($model->description) {
                                        $html .= '<br><small class="text-muted">' . Html::encode($model->description) . '</small>';
                                    }
                                    return $html;
                                },
                            ],
                            [
                                'attribute' => 'provider',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    $providerLabels = [
                                        ShippingMethod::PROVIDER_CUSTOM => 'warning',
                                        ShippingMethod::PROVIDER_DHL => 'danger',
                                        ShippingMethod::PROVIDER_DPD => 'info',
                                        ShippingMethod::PROVIDER_UPS => 'dark',
                                        ShippingMethod::PROVIDER_FEDEX => 'purple',
                                        ShippingMethod::PROVIDER_HERMES => 'success',
                                    ];
                                    $class = $providerLabels[$model->provider] ?? 'secondary';
                                    return '<span class="badge badge-' . $class . '">' . Html::encode($model->getProviderLabel()) . '</span>';
                                },
                            ],
                            [
                                'attribute' => 'status',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    if ($model->status == ShippingMethod::STATUS_ACTIVE) {
                                        $class = 'success';
                                        $icon = 'check';
                                        $text = 'Active';
                                        $action = 'toggle-status';
                                    } else {
                                        $class = 'danger';
                                        $icon = 'times';
                                        $text = 'Inactive';
                                        $action = 'toggle-status';
                                    }
                                    
                                    return Html::a(
                                        '<i class="fas fa-' . $icon . '"></i> ' . $text,
                                        [$action, 'id' => $model->id],
                                        [
                                            'class' => 'btn btn-sm btn-' . $class,
                                            'data-method' => 'post',
                                            'data-confirm' => 'Are you sure you want to change the status?'
                                        ]
                                    );
                                },
                                'options' => ['style' => 'width: 120px;'],
                            ],
                            [
                                'label' => 'Rates',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    $count = $model->getRatesCount();
                                    if ($count > 0) {
                                        return Html::a(
                                            '<span class="badge badge-info">' . $count . ' rates</span>',
                                            ['/shipping-rate/index', 'method_id' => $model->id],
                                            ['title' => 'View shipping rates for this method']
                                        );
                                    }
                                    return '<span class="text-muted">No rates</span>';
                                },
                                'options' => ['style' => 'width: 100px;'],
                            ],
                            [
                                'attribute' => 'created_at',
                                'format' => 'datetime',
                                'options' => ['style' => 'width: 150px;'],
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'template' => '{view} {update} {delete}',
                                'buttons' => [
                                    'view' => function ($url, $model, $key) {
                                        return Html::a('<i class="fas fa-eye"></i>', $url, [
                                            'class' => 'btn btn-sm btn-info',
                                            'title' => 'View',
                                        ]);
                                    },
                                    'update' => function ($url, $model, $key) {
                                        return Html::a('<i class="fas fa-edit"></i>', $url, [
                                            'class' => 'btn btn-sm btn-primary',
                                            'title' => 'Update',
                                        ]);
                                    },
                                    'delete' => function ($url, $model, $key) {
                                        return Html::a('<i class="fas fa-trash"></i>', $url, [
                                            'class' => 'btn btn-sm btn-danger',
                                            'title' => 'Delete',
                                            'data-method' => 'post',
                                            'data-confirm' => 'Are you sure you want to delete this shipping method?',
                                        ]);
                                    },
                                ],
                                'options' => ['style' => 'width: 120px;'],
                            ],
                        ],
                    ]); ?>
                    
                    <?php Pjax::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bulk action forms -->
<?= Html::beginForm(['bulk-status'], 'post', ['id' => 'bulk-status-form', 'style' => 'display: none;']) ?>
    <?= Html::hiddenInput('bulk_status', '', ['id' => 'bulk-status-value']) ?>
    <div id="bulk-selection"></div>
<?= Html::endForm() ?>

<?= Html::beginForm(['bulk-delete'], 'post', ['id' => 'bulk-delete-form', 'style' => 'display: none;']) ?>
    <div id="bulk-delete-selection"></div>
<?= Html::endForm() ?>

<?php
$js = <<<JS
    // Filter functionality
    $('#apply-filters').click(function() {
        var url = '{$indexUrl}';
        var params = [];
        
        var name = $('#filter-name').val();
        if (name) params.push('name=' + encodeURIComponent(name));
        
        var provider = $('#filter-provider').val();
        if (provider) params.push('provider=' + encodeURIComponent(provider));
        
        var status = $('#filter-status').val();
        if (status !== '') params.push('status=' + encodeURIComponent(status));
        
        if (params.length > 0) {
            url += '?' + params.join('&');
        }
        
        window.location.href = url;
    });
    
    // Enter key support for filters
    $('.form-control').keypress(function(e) {
        if (e.which == 13) {
            $('#apply-filters').click();
        }
    });

    window.bulkAction = function(action) {
        var selected = [];
        $('input[name="selection[]"]:checked').each(function() {
            selected.push($(this).val());
        });
        
        if (selected.length === 0) {
            alert('Please select at least one shipping method.');
            return;
        }
        
        var status = action === 'activate' ? '{$activeStatus}' : '{$inactiveStatus}';
        var confirmMsg = action === 'activate' ? 'activate' : 'deactivate';
        
        if (confirm('Are you sure you want to ' + confirmMsg + ' ' + selected.length + ' selected methods?')) {
            $('#bulk-status-value').val(status);
            $('#bulk-selection').html('');
            for (var i = 0; i < selected.length; i++) {
                $('#bulk-selection').append('<input type="hidden" name="selection[]" value="' + selected[i] + '">');
            }
            $('#bulk-status-form').submit();
        }
    };
    
    window.bulkDelete = function() {
        var selected = [];
        $('input[name="selection[]"]:checked').each(function() {
            selected.push($(this).val());
        });
        
        if (selected.length === 0) {
            alert('Please select at least one shipping method.');
            return;
        }
        
        if (confirm('Are you sure you want to delete ' + selected.length + ' selected methods? This action cannot be undone.')) {
            $('#bulk-delete-selection').html('');
            for (var i = 0; i < selected.length; i++) {
                $('#bulk-delete-selection').append('<input type="hidden" name="selection[]" value="' + selected[i] + '">');
            }
            $('#bulk-delete-form').submit();
        }
    };
JS;

$this->registerJs(strtr($js, [
    '{$indexUrl}' => \yii\helpers\Url::to(['index']),
    '{$activeStatus}' => ShippingMethod::STATUS_ACTIVE,
    '{$inactiveStatus}' => ShippingMethod::STATUS_INACTIVE,
]));
?>
