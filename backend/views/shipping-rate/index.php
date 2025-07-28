<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\models\ShippingRate;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $zones common\models\ShippingZone[] */
/* @var $methods common\models\ShippingMethod[] */

$this->title = 'Shipping Rates';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shipping-rate-index">

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><?= Html::encode($this->title) ?></h3>
                    <div class="card-tools">
                        <?= Html::a('Create Shipping Rate', ['create'], ['class' => 'btn btn-success']) ?>
                        <?= Html::a('Calculate Rates', ['calculate'], ['class' => 'btn btn-info']) ?>
                        <?= Html::a('Import CSV', ['import'], ['class' => 'btn btn-warning']) ?>
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
                        <div class="col-md-2">
                            <?= Html::dropDownList('zone_id', Yii::$app->request->get('zone_id'), 
                                \yii\helpers\ArrayHelper::map($zones, 'id', 'name'), [
                                'class' => 'form-control',
                                'prompt' => 'All Zones',
                                'id' => 'filter-zone'
                            ]) ?>
                        </div>
                        <div class="col-md-2">
                            <?= Html::dropDownList('method_id', Yii::$app->request->get('method_id'), 
                                \yii\helpers\ArrayHelper::map($methods, 'id', 'name'), [
                                'class' => 'form-control',
                                'prompt' => 'All Methods',
                                'id' => 'filter-method'
                            ]) ?>
                        </div>
                        <div class="col-md-2">
                            <?= Html::dropDownList('status', Yii::$app->request->get('status'), [
                                '' => 'All Statuses',
                                ShippingRate::STATUS_ACTIVE => 'Active',
                                ShippingRate::STATUS_INACTIVE => 'Inactive'
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
                                    return $html;
                                },
                            ],
                            [
                                'attribute' => 'shippingZone.name',
                                'label' => 'Zone',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    if ($model->shippingZone) {
                                        return Html::a(
                                            Html::encode($model->shippingZone->name),
                                            ['/shipping-zone/view', 'id' => $model->shippingZone->id],
                                            ['class' => 'badge badge-info']
                                        );
                                    }
                                    return '<span class="text-muted">No zone</span>';
                                },
                            ],
                            [
                                'attribute' => 'shippingMethod.name',
                                'label' => 'Method',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    if ($model->shippingMethod) {
                                        return Html::a(
                                            Html::encode($model->shippingMethod->name),
                                            ['/shipping-method/view', 'id' => $model->shippingMethod->id],
                                            ['class' => 'badge badge-primary']
                                        );
                                    }
                                    return '<span class="text-muted">No method</span>';
                                },
                            ],
                            [
                                'label' => 'Weight Range',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    $min = $model->min_weight ? number_format($model->min_weight, 1) : '0';
                                    $max = $model->max_weight ? number_format($model->max_weight, 1) : '∞';
                                    return '<small>' . $min . ' - ' . $max . ' kg</small>';
                                },
                                'options' => ['style' => 'width: 120px;'],
                            ],
                            [
                                'label' => 'Order Value',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    if ($model->min_order_value) {
                                        return '<small>Min: $' . number_format($model->min_order_value, 2) . '</small>';
                                    }
                                    return '<small class="text-muted">No minimum</small>';
                                },
                                'options' => ['style' => 'width: 120px;'],
                            ],
                            [
                                'attribute' => 'cost',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    $html = '<strong>$' . number_format($model->cost, 2) . '</strong>';
                                    if ($model->free_shipping_threshold) {
                                        $html .= '<br><small class="text-success">Free over $' . number_format($model->free_shipping_threshold, 2) . '</small>';
                                    }
                                    return $html;
                                },
                                'options' => ['style' => 'width: 120px;'],
                            ],
                            [
                                'attribute' => 'status',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    if ($model->status == ShippingRate::STATUS_ACTIVE) {
                                        $class = 'success';
                                        $icon = 'check';
                                        $text = 'Active';
                                    } else {
                                        $class = 'danger';
                                        $icon = 'times';
                                        $text = 'Inactive';
                                    }
                                    
                                    return Html::a(
                                        '<i class="fas fa-' . $icon . '"></i> ' . $text,
                                        ['toggle-status', 'id' => $model->id],
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
                                'class' => 'yii\grid\ActionColumn',
                                'template' => '{view} {update} {duplicate} {delete}',
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
                                    'duplicate' => function ($url, $model, $key) {
                                        return Html::a('<i class="fas fa-copy"></i>', $url, [
                                            'class' => 'btn btn-sm btn-warning',
                                            'title' => 'Duplicate',
                                            'data-method' => 'post',
                                        ]);
                                    },
                                    'delete' => function ($url, $model, $key) {
                                        return Html::a('<i class="fas fa-trash"></i>', $url, [
                                            'class' => 'btn btn-sm btn-danger',
                                            'title' => 'Delete',
                                            'data-method' => 'post',
                                            'data-confirm' => 'Are you sure you want to delete this shipping rate?',
                                        ]);
                                    },
                                ],
                                'options' => ['style' => 'width: 160px;'],
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
        
        var zone = $('#filter-zone').val();
        if (zone) params.push('zone_id=' + encodeURIComponent(zone));
        
        var method = $('#filter-method').val();
        if (method) params.push('method_id=' + encodeURIComponent(method));
        
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
            alert('Please select at least one shipping rate.');
            return;
        }
        
        var status = action === 'activate' ? '{$activeStatus}' : '{$inactiveStatus}';
        var confirmMsg = action === 'activate' ? 'activate' : 'deactivate';
        
        if (confirm('Are you sure you want to ' + confirmMsg + ' ' + selected.length + ' selected rates?')) {
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
            alert('Please select at least one shipping rate.');
            return;
        }
        
        if (confirm('Are you sure you want to delete ' + selected.length + ' selected rates? This action cannot be undone.')) {
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
    '{$activeStatus}' => ShippingRate::STATUS_ACTIVE,
    '{$inactiveStatus}' => ShippingRate::STATUS_INACTIVE,
]));
?>
