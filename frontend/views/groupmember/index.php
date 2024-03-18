<?php

/* @var $this yii\web\View */

# Use the URL namespace
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

echo "<h1>".$this->params['group']->name."</h1>";

echo "<hr />";

/*****************************************
* Add handle form
******************************************/
$addGroupMemberForm = ActiveForm::begin(
[
    'action' => ['groupmember/add'],
    'id' => 'add-group-member-form',
    'options' => ['class' => 'form-horizontal'],
]);


?>
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-6" style="width: 255px;">
            <?php echo $addGroupMemberForm->field($this->params['user'], 'id')->dropDownList([], ['style'=>'width:250px'])->label('User Email'); ?>
        </div>
        <div class="col-sm-6 pull-bottom" style="width: 155px;">
            <?php //echo $addCategoryForm->submit(); ?>
            <div class="form-group field-category-name required" style="padding-top: 27px">
                <?php echo Html::hiddenInput('GroupMember[group_id]', $this->params['group']->id);?>
                <?php echo Html::submitButton('Add Member', ['class' => 'btn btn-primary']) ?>
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>
<?php
ActiveForm::end();

\yii\widgets\Pjax::begin();
echo \yii\grid\GridView::widget(
[
    'id' => 'group-member-grid',
    'dataProvider' => $this->params['groupMemberDataProvider'],
    'columns' =>
    [
        [
            'label' => 'Email',
            'value' => function ($model)
            {
                return $model->getUser()->one()->email;
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
                        'delete-url' => Url::toRoute('groupmember/delete'),
                        'pjax-container' => 'group-member-grid',
                        'title' => Yii::t('yii', 'Delete')
                    ]);
                },
            ]
        ]
    ],
    'options' => ['height: 200px'],
]);
\yii\widgets\Pjax::end();


?>
