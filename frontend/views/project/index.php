<?php
/* @var $this yii\web\View */

# Use the URL namespace
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\spinner\Spinner;
use kartik\date\DatePicker;
use kartik\grid\GridView;

/*****************************************
* Add project form
******************************************/
$addProjectForm = ActiveForm::begin([
    'action' => ['project/add'],
    'id' => 'add-project-form',
    'options' => ['class' => 'form-horizontal'],
]);

?>
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-6" style="width: 160px;">
            <?php echo $addProjectForm->field($this->params['project'], 'name')->textInput(['style'=>'width:150px']); ?>
        </div>
        <!--
        <div class="col-sm-6" style="width: 155px;">
            <?php //echo $addHandleForm->field($this->params['addHandleFormModel'], 'label')->textInput(['style'=>'width:150px']); ?>
        </div>
        -->
        <div class="col-sm-6 pull-bottom" style="width: 155px;">
            <?php //echo $addCategoryForm->submit(); ?>
            <div class="form-group field-category-name required" style="padding-top: 27px">
                <!-- <label class="control-label"></label> -->
                <?php echo Html::submitButton('Add Project', ['class' => 'btn btn-primary', 'id' => 'addProjectBtnNoSpinner']) ?>
                <?php

                    echo '<button class="btn btn-primary hidden" id="addProjectBtnWithSpinner">';
                        echo Spinner::widget(['preset' => 'tiny', 'align' => 'right', 'caption' => 'Add Project']);
                    echo '</button>';

                ?>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>
<?php
ActiveForm::end();

\yii\widgets\Pjax::begin();
echo kartik\grid\GridView::widget(
//echo \yii\grid\GridView::widget(
[

  # Set ID
  'id' => 'project-grid',

   # Set dataprovider
  'dataProvider' => $this->params['projectDataProvider'],
  'filterModel' => $this->params['projectSearch'],

	# Define columns
	'columns' =>
	[
        # ID
        'id',
        [
            'attribute' => 'id',
            'format' => 'raw',
            'value' => function($model)
            {
                return Html::a('Go to Project', ['handle/index', 'project_id' => $model->id], ['data-pjax' => "0"]);
            }
        ],
        # User
        [
            'label' => 'Creator',
            'format' => 'raw',
            'value' => function ($model)
            {
                return $model->getUser()->one()->email;
            }
        ],

		# Editable Name
		[
			'class' => \yii2mod\editable\EditableColumn::class,
			'attribute' => 'name',
			'url' => ['project/update'],
		],
        [
            'label' => 'Options',
            'format' => 'raw',
            'value' => function ($model)
            {
                # Define output
                $output = "";

                # Pull options
                $xrefProjectUsers = $model->getXrefProjectUsers()->all();

                # Check to make sure we have some
                if (count($xrefProjectUsers))
                {
                    # Loop through options and add to output
                    foreach ($xrefProjectUsers as $ndx => $xrefProjectUser)
                    {
                        $output .= "<li>".$xrefProjectUser->getUser()->one()->email."</li>";
                    }
                }
                else
                {
                    $output = "Add Users";
                }

                # Finish output tag
                //$output = "</ol>";
                //return $output;
                return Html::a($output, ['xrefprojectuser/index', 'project_id' => $model->id], ['data-pjax' => 0]);
            }
        ],

        # Delete
        [
            //'data-key' => function($data) {return $data->id;},
            'class' => 'yii\grid\ActionColumn',
            'template' => '{delete}',
            'buttons' =>
            [
                'delete' => function ()
                {
                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', true,
                    [
                        'class' => 'pjax-delete-link',
                        //'handle-id' => $data['id'],
                        'delete-url' => Url::toRoute('project/delete'),
                        'pjax-container' => 'project-grid',
                        'title' => Yii::t('yii', 'Delete')
                    ]);
                },
            ]
        ]
	]
]);
\yii\widgets\Pjax::end();

?>
