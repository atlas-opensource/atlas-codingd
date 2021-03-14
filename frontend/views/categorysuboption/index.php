<?php

/* @var $this yii\web\View */

# Use the URL namespace
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

echo "<h1>".$this->params['categoryOption']->name."</h1>";

/* @var $this yii\web\View */
/*****************************************
* Add handle form
******************************************/
$addCategorySubOptionForm = ActiveForm::begin(
[
    'action' => ['categorysuboption/add'],
    'id' => 'add-category-sub-option-form',
    'options' => ['class' => 'form-horizontal'],
]);


?>

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-6" style="width: 160px;">
            <?php echo $addCategorySubOptionForm->field($this->params['categorySubOption'], 'code')->textInput(['style'=>'width:150px']); ?>
        </div>
        <div class="col-sm-6" style="width: 155px;">
            <?php echo $addCategorySubOptionForm->field($this->params['categorySubOption'], 'name')->textInput(['style'=>'width:150px']); ?>
        </div>
        <div class="col-sm-6" style="width: 355px;">
            <?php echo $addCategorySubOptionForm->field($this->params['categorySubOption'], 'description')->textInput(['style'=>'width:350px']); ?>
        </div>
        <div class="col-sm-6 pull-bottom" style="width: 155px;">
            <?php //echo $addCategoryForm->submit(); ?>
            <div class="form-group field-category-name required" style="padding-top: 27px">
                <!-- <label class="control-label"></label> -->
                <?php
                    echo Html::hiddenInput('CategorySubOption[category_option_id]', $this->params['categoryOption']->id);
                    //echo $addCategoryOptionForm->field($this->params['categoryOption'], 'category_id')->hiddenInput();
                ?>
                <?php echo Html::submitButton('Add Category Sub Option', ['class' => 'btn btn-primary']) ?>
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
  'id' => 'category-sub-option-grid',
  'dataProvider' => $this->params['categorySubOptionDataProvider'],
  'columns' =>
  [
      'id',
      [
          'class' => \yii2mod\editable\EditableColumn::class,
          'attribute' => 'code',
          'url' => ['categorysuboption/update'],
      ],
      [
          'class' => \yii2mod\editable\EditableColumn::class,
          'attribute' => 'name',
          'url' => ['categorysuboption/update'],
      ],
      [
          'class' => \yii2mod\editable\EditableColumn::class,
          'attribute' => 'description',
          'url' => ['categorysuboption/update'],
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
                      'delete-url' => Url::toRoute('categorysuboption/delete'),
                      'pjax-container' => 'category-sub-option-grid',
                      'title' => Yii::t('yii', 'Delete')
                  ]);
              },
          ]
      ]
  ]
]);
\yii\widgets\Pjax::end();

?>
