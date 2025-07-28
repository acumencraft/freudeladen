<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $categories array */

$this->title = 'FAQs';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="faq-index">

    <div class="row">
        <div class="col-md-6">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
        <div class="col-md-6 text-right">
            <?= Html::a('Create FAQ', ['create'], ['class' => 'btn btn-success']) ?>
            <?= Html::a('Manage Categories', ['faq-category/index'], ['class' => 'btn btn-info']) ?>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Search & Filter</h5>
                </div>
                <div class="card-body">
                    <?php echo Html::beginForm(['index'], 'get', ['class' => 'form-inline']); ?>
                    
                    <div class="form-group mr-3">
                        <?= Html::label('Question', 'question', ['class' => 'mr-2']) ?>
                        <?= Html::textInput('question', Yii::$app->request->get('question'), [
                            'class' => 'form-control',
                            'placeholder' => 'Search by question...'
                        ]) ?>
                    </div>
                    
                    <div class="form-group mr-3">
                        <?= Html::label('Category', 'category_id', ['class' => 'mr-2']) ?>
                        <?= Html::dropDownList('category_id', Yii::$app->request->get('category_id'), 
                            ['' => 'All Categories'] + $categories, 
                            ['class' => 'form-control']
                        ) ?>
                    </div>
                    
                    <div class="form-group mr-3">
                        <?= Html::label('Status', 'status', ['class' => 'mr-2']) ?>
                        <?= Html::dropDownList('status', Yii::$app->request->get('status'), 
                            ['' => 'All Status', '0' => 'Inactive', '1' => 'Active'], 
                            ['class' => 'form-control']
                        ) ?>
                    </div>
                    
                    <div class="form-group">
                        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
                        <?= Html::a('Reset', ['index'], ['class' => 'btn btn-secondary ml-2']) ?>
                    </div>
                    
                    <?php echo Html::endForm(); ?>
                </div>
            </div>
        </div>
    </div>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['id' => 'faq-grid'],
        'columns' => [
            ['class' => 'yii\grid\CheckboxColumn'],
            
            [
                'attribute' => 'sort_order',
                'headerOptions' => ['style' => 'width: 80px;'],
                'contentOptions' => ['class' => 'text-center'],
            ],
            
            [
                'attribute' => 'question',
                'format' => 'raw',
                'value' => function ($model) {
                    $question = Html::encode($model->question);
                    if (strlen($question) > 100) {
                        $question = substr($question, 0, 100) . '...';
                    }
                    return Html::a($question, ['view', 'id' => $model->id], [
                        'class' => 'fw-bold text-decoration-none',
                        'title' => Html::encode($model->question)
                    ]);
                },
            ],
            
            [
                'attribute' => 'category_id',
                'label' => 'Category',
                'value' => function ($model) {
                    return $model->category ? $model->category->name : 'No Category';
                },
                'headerOptions' => ['style' => 'width: 150px;'],
            ],
            
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function ($model) {
                    $class = $model->status == 1 ? 'success' : 'secondary';
                    return '<span class="badge badge-' . $class . '">' . $model->getStatusLabel() . '</span>';
                },
                'headerOptions' => ['style' => 'width: 100px;'],
            ],
            
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
                'headerOptions' => ['style' => 'width: 150px;'],
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {toggle} {delete}',
                'buttons' => [
                    'toggle' => function ($url, $model, $key) {
                        $icon = $model->status == 1 ? 'eye-slash' : 'eye';
                        $title = $model->status == 1 ? 'Deactivate' : 'Activate';
                        $class = $model->status == 1 ? 'warning' : 'success';
                        return Html::a('<i class="fas fa-' . $icon . '"></i>', ['toggle-status', 'id' => $model->id], [
                            'title' => $title,
                            'class' => 'btn btn-sm btn-outline-' . $class,
                            'data-pjax' => '0',
                        ]);
                    },
                    'view' => function ($url, $model, $key) {
                        return Html::a('<i class="fas fa-eye"></i>', ['view', 'id' => $model->id], [
                            'title' => 'View',
                            'class' => 'btn btn-sm btn-outline-primary',
                        ]);
                    },
                    'update' => function ($url, $model, $key) {
                        return Html::a('<i class="fas fa-edit"></i>', ['update', 'id' => $model->id], [
                            'title' => 'Update',
                            'class' => 'btn btn-sm btn-outline-info',
                        ]);
                    },
                    'delete' => function ($url, $model, $key) {
                        return Html::a('<i class="fas fa-trash"></i>', ['delete', 'id' => $model->id], [
                            'title' => 'Delete',
                            'class' => 'btn btn-sm btn-outline-danger',
                            'data-confirm' => 'Are you sure you want to delete this FAQ?',
                            'data-method' => 'post',
                        ]);
                    },
                ],
                'headerOptions' => ['style' => 'width: 200px;'],
            ],
        ],
    ]); ?>

    <div class="row mt-3">
        <div class="col-md-12">
            <div class="btn-group">
                <?= Html::beginForm(['bulk-delete'], 'post', ['id' => 'bulk-delete-form']) ?>
                <?= Html::button('Delete Selected', [
                    'class' => 'btn btn-danger',
                    'onclick' => 'bulkAction("delete")',
                ]) ?>
                <?= Html::endForm() ?>
                
                <?= Html::beginForm(['bulk-status'], 'post', ['id' => 'bulk-status-form']) ?>
                <?= Html::dropDownList('bulk_status', '', [
                    '1' => 'Activate Selected',
                    '0' => 'Deactivate Selected'
                ], [
                    'class' => 'form-control d-inline-block mr-2',
                    'style' => 'width: auto;',
                    'prompt' => 'Bulk Status...'
                ]) ?>
                <?= Html::button('Apply', [
                    'class' => 'btn btn-info',
                    'onclick' => 'bulkAction("status")',
                ]) ?>
                <?= Html::endForm() ?>
            </div>
        </div>
    </div>

    <?php Pjax::end(); ?>

</div>

<script>
function bulkAction(action) {
    var keys = $('#faq-grid').yiiGridView('getSelectedRows');
    if (keys.length === 0) {
        alert('Please select at least one item.');
        return;
    }
    
    var form, confirmMessage;
    if (action === 'delete') {
        form = $('#bulk-delete-form');
        confirmMessage = 'Are you sure you want to delete the selected FAQs?';
    } else if (action === 'status') {
        var status = $('select[name="bulk_status"]').val();
        if (!status) {
            alert('Please select a status action.');
            return;
        }
        form = $('#bulk-status-form');
        var statusText = status == '1' ? 'activate' : 'deactivate';
        confirmMessage = 'Are you sure you want to ' + statusText + ' the selected FAQs?';
    }
    
    if (confirm(confirmMessage)) {
        // Add selected IDs as hidden inputs
        $.each(keys, function(index, key) {
            form.append('<input type="hidden" name="selection[]" value="' + key + '">');
        });
        
        form.submit();
    }
}

// Make rows sortable
$(function() {
    $('#faq-grid tbody').sortable({
        handle: '.sort-handle',
        update: function(event, ui) {
            var ids = [];
            $('#faq-grid tbody tr').each(function() {
                var id = $(this).data('key');
                if (id) ids.push(id);
            });
            
            $.post('<?= Url::to(['update-sort']) ?>', {ids: ids}, function(data) {
                if (data.success) {
                    $.pjax.reload({container: '#faq-grid'});
                }
            });
        }
    });
});
</script>

<style>
.sortable-row {
    cursor: move;
}
.sort-handle {
    cursor: move;
    color: #666;
}
</style>
