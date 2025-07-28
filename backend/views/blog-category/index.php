<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Blog Categories';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="blog-category-index">

    <div class="row">
        <div class="col-md-6">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
        <div class="col-md-6 text-right">
            <?= Html::a('Create Blog Category', ['create'], ['class' => 'btn btn-success']) ?>
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
        'columns' => [
            ['class' => 'yii\grid\CheckboxColumn'],
            
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
                'label' => 'Posts Count',
                'value' => function ($model) {
                    return $model->getPostCount();
                },
                'headerOptions' => ['style' => 'width: 120px;'],
            ],
            
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
                'headerOptions' => ['style' => 'width: 150px;'],
            ],
            
            [
                'attribute' => 'updated_at',
                'format' => 'datetime',
                'headerOptions' => ['style' => 'width: 150px;'],
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        return Html::a('<i class="fas fa-eye"></i>', ['view', 'id' => $model->id], [
                            'title' => 'View',
                            'class' => 'btn btn-sm btn-outline-primary',
                        ]);
                    },
                    'update' => function ($url, $model, $key) {
                        return Html::a('<i class="fas fa-edit"></i>', ['update', 'id' => $model->id], [
                            'title' => 'Update',
                            'class' => 'btn btn-sm btn-outline-warning',
                        ]);
                    },
                    'delete' => function ($url, $model, $key) {
                        $disabled = $model->getPostCount() > 0 ? 'disabled' : '';
                        $title = $model->getPostCount() > 0 ? 'Cannot delete - has blog posts' : 'Delete';
                        return Html::a('<i class="fas fa-trash"></i>', ['delete', 'id' => $model->id], [
                            'title' => $title,
                            'class' => 'btn btn-sm btn-outline-danger ' . $disabled,
                            'data-confirm' => 'Are you sure you want to delete this blog category?',
                            'data-method' => 'post',
                        ]);
                    },
                ],
                'headerOptions' => ['style' => 'width: 150px;'],
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
    var keys = $('#grid').yiiGridView('getSelectedRows');
    if (keys.length === 0) {
        alert('Please select at least one item to delete.');
        return;
    }
    
    if (confirm('Are you sure you want to delete the selected blog categories? Categories with posts will be skipped.')) {
        var form = $('#bulk-form');
        
        // Add selected IDs as hidden inputs
        $.each(keys, function(index, key) {
            form.append('<input type="hidden" name="selection[]" value="' + key + '">');
        });
        
        form.submit();
    }
}
</script>
