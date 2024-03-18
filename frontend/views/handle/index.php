<?php

# Use the URL namespace
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\spinner\Spinner;
use kartik\date\DatePicker;
use kartik\grid\GridView;



/* @var $this yii\web\View */
# Extract project for easy access
$project = $this->params['project'];

# Show what project user is working in
echo "<h1>$project->name</h1>";

/*****************************************
* Add handle form
******************************************/
$addHandleForm = ActiveForm::begin([
    'action' => ['handle/add'],
    'id' => 'add-handle-form',
    'options' => ['class' => 'form-horizontal'],
]);

?>
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-6" style="width: 160px;">
            <?php echo $addHandleForm->field($this->params['addHandleFormModel'], 'handle')->textInput(['style'=>'width:150px']); ?>
        </div>
        <div class="col-sm-6" style="width: 155px;">
            <?php echo $addHandleForm->field($this->params['addHandleFormModel'], 'label')->textInput(['style'=>'width:150px']); ?>
        </div>
        <div class="col-sm-6 pull-bottom" style="width: 155px;">
            <?php //echo $addCategoryForm->submit(); ?>
            <div class="form-group field-category-name required" style="padding-top: 27px">
                <!-- <label class="control-label"></label> -->
                <?php echo Html::hiddenInput('XrefProjectHandle[project_id]', $this->params['project']->id); ?>
                <?php echo Html::submitButton('Add Handle', ['class' => 'btn btn-primary', 'id' => 'addHandleBtnNoSpinner']) ?>
                <?php

                    echo '<button class="btn btn-primary hidden" id="addHandleBtnWithSpinner">';
                        echo Spinner::widget(['preset' => 'tiny', 'align' => 'right', 'caption' => 'Add Handle']);
                    echo '</button>';

                ?>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>
<?php
ActiveForm::end();

echo "<a href='".Url::toRoute(['handle/index'])."' style='margin-bottom: 20px;'><button id='clearFilterBtn' data-refreshUrl='".Url::toRoute(['handle/index'])."' class='btn btn-primary' style='float: right; margin-bottom: 10px; margin-right: 10px;'>Clear Filters</button></a>";
echo "<br />";
echo "<br />";
echo "<br />";




\yii\widgets\Pjax::begin();
echo kartik\grid\GridView::widget(
//echo \yii\grid\GridView::widget(
[

  # Set ID
  'id' => 'handle-grid',

	# Set dataprovider
  'dataProvider' => $this->params['handleDataProvider'],
  'filterModel' => $this->params['handleSearch'],

	# Define columns
	'columns' =>
	[
		# Editable Handle
		[
			'class' => \yii2mod\editable\EditableColumn::class,
			'attribute' => 'handle',
			'url' => ['handle/update'],
		],

        # Label
        [
            'class' => \yii2mod\editable\EditableColumn::class,
            'attribute' => 'label',
            'url' => ['handle/update'],
        ],

		# Name
        'name',
        'user_since',
        'numTweets',

        # View Tweets
        [
            'label' => 'View Tweets',
            'format' => 'raw',
            'value' => function ($model)
            {
                // return Html::a('View Tweets', ['tweet/index', 'handleId' => $model->id], ['data-pjax' => 0, 'target' => '_blank']);
                return Html::a('View Tweets', ['tweet/index', 'handleId' => $model->id], ['data-pjax' => "0"]);

            }
        ],

        # Delete
        [
            //'data-key' => function($data) {return $data->id;},
            'class' => 'yii\grid\ActionColumn',
            'template' => '{delete}',
            'buttons' =>
            [
                'delete' => function () use ($project)
                {
                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', true,
                    [
                        'class' => 'pjax-delete-link',
                        //'handle-id' => $data['id'],
                        //'delete-url' => Url::toRoute('handle/delete'),
                        'delete-url' => Url::toRoute(['handle/delete', 'project_id' => $project->id]),
                        'pjax-container' => 'handle-grid',
                        'title' => Yii::t('yii', 'Delete')
                    ]);
                },
            ]
        ]
	]
]);
\yii\widgets\Pjax::end();

?>
