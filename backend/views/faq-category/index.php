<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'FAQ Categories';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="faq-category-index">

    <div class="row">
        <div class="col-md-6">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
        <div class="col-md-6 text-right">
            <?= Html::a('Create FAQ Category', ['create'], ['class' => 'btn btn-success']) ?>
            <?= Html::a('Manage FAQs', ['faq/index'], ['class' => 'btn btn-info']) ?>
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
                        <?= Html::label('Name', 'name', ['class' => 'mr-2']) ?>
                        <?= Html::textInput('name', Yii::$app->request->get('name'), [
                            'class' => 'form-control',
                            'placeholder' => 'Search by name...'
                        ]) ?>
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
        'options' => ['id' => 'faq-category-grid'],
        'columns' => [
            ['class' => 'yii\grid\CheckboxColumn'],
            
            [
                'attribute' => 'sort_order',
                'headerOptions' => ['style' => 'width: 80px;'],
                'contentOptions' => ['class' => 'text-center'],
            ],
            
            [
                'attribute' => 'name',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a(Html::encode($model->name), ['view', 'id' => $model->id], [
                        'class' => 'fw-bold text-decoration-none'
                    ]);
                },
            ],
            
            'slug',
            
            [
                'attribute' => 'description',
                'value' => function ($model) {
                    if ($model->description) {
                        return strlen($model->description) > 50 ? 
                            substr($model->description, 0, 50) . '...' : 
                            $model->description;
                    }
                    return 'No description';
                },
                'contentOptions' => ['class' => 'text-muted'],
            ],
            
            [
                'label' => 'FAQs Count',
                'value' => function ($model) {
                    return $model->getFaqCount() . ' (' . $model->getActiveFaqCount() . ' active)';
                },
                'headerOptions' => ['style' => 'width: 120px;'],
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
                        $disabled = $model->getFaqCount() > 0 ? 'disabled' : '';
                        $title = $model->getFaqCount() > 0 ? 'Cannot delete - has FAQs' : 'Delete';
                        return Html::a('<i class="fas fa-trash"></i>', ['delete', 'id' => $model->id], [
                            'title' => $title,
                            'class' => 'btn btn-sm btn-outline-danger ' . $disabled,
                            'data-confirm' => 'Are you sure you want to delete this FAQ category?',
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
            <?= Html::beginForm(['bulk-delete'], 'post', ['id' => 'bulk-form']) ?>
            <?= Html::button('Delete Selected', [
                'class' => 'btn btn-danger',
                'onclick' => 'bulkDelete()',
            ]) ?>
            <?= Html::endForm() ?>
        </div>
    </div>

    <?php Pjax::end(); ?>

</div>

<script>
function bulkDelete() {
    var keys = $('#faq-category-grid').yiiGridView('getSelectedRows');
    if (keys.length === 0) {
        alert('Please select at least one item to delete.');
        return;
    }
    
    if (confirm('Are you sure you want to delete the selected FAQ categories? Categories with FAQs will be skipped.')) {
        var form = $('#bulk-form');
        
        // Add selected IDs as hidden inputs
        $.each(keys, function(index, key) {
            form.append('<input type="hidden" name="selection[]" value="' + key + '">');
        });
        
        form.submit();
    }
}

// Make rows sortable
$(function() {
    $('#faq-category-grid tbody').sortable({
        handle: '.sort-handle',
        update: function(event, ui) {
            var ids = [];
            $('#faq-category-grid tbody tr').each(function() {
                var id = $(this).data('key');
                if (id) ids.push(id);
            });
            
            $.post('<?= Url::to(['update-sort']) ?>', {ids: ids}, function(data) {
                if (data.success) {
                    $.pjax.reload({container: '#faq-category-grid'});
                }
            });
        }
    });
});
</script>
