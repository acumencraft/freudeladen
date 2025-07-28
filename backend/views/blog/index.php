<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\BlogPost */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $categories array */

$this->title = 'Blog Posts';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="blog-post-index">

    <div class="row">
        <div class="col-md-6">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
        <div class="col-md-6 text-right">
            <?= Html::a('Create Blog Post', ['create'], ['class' => 'btn btn-success']) ?>
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
                        <?= Html::label('Title', 'title', ['class' => 'mr-2']) ?>
                        <?= Html::textInput('title', Yii::$app->request->get('title'), [
                            'class' => 'form-control',
                            'placeholder' => 'Search by title...'
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
                            ['' => 'All Status', '0' => 'Draft', '1' => 'Published'], 
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
        'columns' => [
            ['class' => 'yii\grid\CheckboxColumn'],
            
            [
                'attribute' => 'featured_image',
                'label' => 'Image',
                'format' => 'raw',
                'value' => function ($model) {
                    if ($model->featured_image) {
                        return Html::img($model->getFeaturedImageUrl(), [
                            'style' => 'width: 60px; height: 40px; object-fit: cover;'
                        ]);
                    }
                    return '<span class="text-muted">No image</span>';
                },
                'headerOptions' => ['style' => 'width: 80px;'],
            ],
            
            [
                'attribute' => 'title',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a(Html::encode($model->title), ['view', 'id' => $model->id], [
                        'class' => 'fw-bold text-decoration-none'
                    ]);
                },
            ],
            
            [
                'attribute' => 'category_id',
                'label' => 'Category',
                'value' => function ($model) {
                    return $model->category ? $model->category->name : 'No Category';
                },
            ],
            
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function ($model) {
                    $class = $model->status == 1 ? 'success' : 'warning';
                    return '<span class="badge badge-' . $class . '">' . $model->getStatusLabel() . '</span>';
                },
                'headerOptions' => ['style' => 'width: 100px;'],
            ],
            
            [
                'attribute' => 'published_at',
                'format' => 'datetime',
                'headerOptions' => ['style' => 'width: 150px;'],
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
                        $title = $model->status == 1 ? 'Unpublish' : 'Publish';
                        return Html::a('<i class="fas fa-' . $icon . '"></i>', ['toggle-status', 'id' => $model->id], [
                            'title' => $title,
                            'class' => 'btn btn-sm btn-outline-info',
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
                            'class' => 'btn btn-sm btn-outline-warning',
                        ]);
                    },
                    'delete' => function ($url, $model, $key) {
                        return Html::a('<i class="fas fa-trash"></i>', ['delete', 'id' => $model->id], [
                            'title' => 'Delete',
                            'class' => 'btn btn-sm btn-outline-danger',
                            'data-confirm' => 'Are you sure you want to delete this blog post?',
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
    var keys = $('#grid').yiiGridView('getSelectedRows');
    if (keys.length === 0) {
        alert('Please select at least one item to delete.');
        return;
    }
    
    if (confirm('Are you sure you want to delete the selected blog posts?')) {
        var form = $('#bulk-form');
        
        // Add selected IDs as hidden inputs
        $.each(keys, function(index, key) {
            form.append('<input type="hidden" name="selection[]" value="' + key + '">');
        });
        
        form.submit();
    }
}
</script>
