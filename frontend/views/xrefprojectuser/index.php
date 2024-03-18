<?php

/* @var $this yii\web\View */

# Use the URL namespace
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

echo "<h1>".$this->params['project']->name."</h1>";

/* @var $this yii\web\View */
/*****************************************
* Add handle form
******************************************/
$addxrefProjectUserForm = ActiveForm::begin(
[
    'action' => ['xrefprojectuser/add'],
    'id' => 'add-xref-project-user-form',
    'options' => ['class' => 'form-horizontal'],
]);


?>

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-6" style="width: 160px;">
            <?php echo $addxrefProjectUserForm->field($this->params['user'], 'email')->textInput(['style'=>'width:150px']); ?>
        </div>
        <div class="col-sm-6 pull-bottom" style="width: 155px;">
            <?php //echo $addCategoryForm->submit(); ?>
            <div class="form-group field-category-name required" style="padding-top: 27px">
                <!-- <label class="control-label"></label> -->
                <?php
                    echo Html::hiddenInput('XrefProjectUser[project_id]', $this->params['project']->id);
                    //echo $addCategoryOptionForm->field($this->params['categoryOption'], 'category_id')->hiddenInput();
                ?>
                <?php echo Html::submitButton('Add User', ['class' => 'btn btn-primary']) ?>
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
  'id' => 'xref-project-user-grid',
  'dataProvider' => $this->params['xrefProjectUserDataProvider'],
  'columns' =>
  [
      'id',
      # Category sub options
      [
          'label' => 'User',
          'format' => 'raw',
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
                      'delete-url' => Url::toRoute('xrefprojectuser/delete'),
                      'pjax-container' => 'xref-project-user-grid',
                      'title' => Yii::t('yii', 'Delete')
                  ]);
              },
          ]
      ]
  ]
]);
\yii\widgets\Pjax::end();

?>
