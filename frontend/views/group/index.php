<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\spinner\Spinner;


echo "<h1>My Groups</h1>";

$addGroupForm = ActiveForm::begin(
[
    'action' => ['group/add'],
    'id' => 'add-group-form',
    'options' => ['class' => 'form-horizontal'],
]);


?>
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-6" style="width: 160px;">
            <?php echo $addGroupForm->field($this->params['group'], 'name')->textInput(['style'=>'width:150px']); ?>
        </div>
        <div class="col-sm-6" style="width: 155px;">
            <?php echo $addGroupForm->field($this->params['group'], 'description')->textInput(['style'=>'width:150px']); ?>
        </div>
        <div class="col-sm-6 pull-bottom" style="width: 155px;">
            <?php //echo $addCategoryForm->submit(); ?>
            <div class="form-group field-category-name required" style="padding-top: 27px">
                <!-- <label class="control-label"></label> -->
                <?php echo Html::submitButton('Add Group', ['class' => 'btn btn-primary', 'id' => 'addGroupBtnNoSpinner']) ?>
                <?php

                    echo '<button class="btn btn-primary hidden" id="addGroupBtnWithSpinner">';
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


\yii\widgets\Pjax::begin();
//\yii\widgets\Pjax::widget(['id' => 'formsection']);
echo \yii\grid\GridView::widget(
[

  # Set ID
  'id' => 'group-grid',

	# Set dataprovider
  'dataProvider' => $this->params['groupDataProvider'],

	# Define columns
	'columns' =>
	[
        # Label
        [
            'class' => \yii2mod\editable\EditableColumn::class,
            'attribute' => 'name',
            'url' => ['group/update'],
        ],

		# Editable Handle
		[
			'class' => \yii2mod\editable\EditableColumn::class,
			'attribute' => 'description',
			'url' => ['group/update'],
		],

        # Group members
        [
            'label' => 'Members',
            'format' => 'raw',
            'value' => function ($model)
            {
                $output = "Click here to edit members";
                $members = $model->getGroupMembers()->all();
                if (count($members))
                {
                    $output = "";
                    foreach($members as $member)
                    {
                        $output .= "<li>".$member->getUser()->one()->email."</li>";
                    }
                }

                # Return
                return Html::a($output, ['groupmember/index', 'group_id' => $model->id], []);
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
                        'delete-url' => Url::toRoute('group/delete'),
                        'pjax-container' => 'group-grid',
                        'title' => Yii::t('yii', 'Delete')
                    ]);
                },
            ]
        ]
	]
]);
\yii\widgets\Pjax::end();
?>
