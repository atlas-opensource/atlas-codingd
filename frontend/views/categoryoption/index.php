<?php

/* @var $this yii\web\View */

# Use the URL namespace
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

echo "<h1>".$this->params['category']->display_name."</h1>";

/* @var $this yii\web\View */
/*****************************************
* Add handle form
******************************************/
$addCategoryOptionForm = ActiveForm::begin(
[
    'action' => ['categoryoption/add'],
    'id' => 'add-category-option-form',
    'options' => ['class' => 'form-horizontal'],
]);


?>

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-6" style="width: 160px;">
            <?php echo $addCategoryOptionForm->field($this->params['categoryOption'], 'code')->textInput(['style'=>'width:150px']); ?>
        </div>
        <div class="col-sm-6" style="width: 155px;">
            <?php echo $addCategoryOptionForm->field($this->params['categoryOption'], 'name')->textInput(['style'=>'width:150px']); ?>
        </div>
        <div class="col-sm-6" style="width: 355px;">
            <?php echo $addCategoryOptionForm->field($this->params['categoryOption'], 'description')->textInput(['style'=>'width:350px']); ?>
        </div>
        <div class="col-sm-6 pull-bottom" style="width: 155px;">
            <?php //echo $addCategoryForm->submit(); ?>
            <div class="form-group field-category-name required" style="padding-top: 27px">
                <!-- <label class="control-label"></label> -->
                <?php
                    echo Html::hiddenInput('CategoryOption[category_id]', $this->params['category']->id);
                    //echo $addCategoryOptionForm->field($this->params['categoryOption'], 'category_id')->hiddenInput();
                ?>
                <?php echo Html::submitButton('Add Category Option', ['class' => 'btn btn-primary']) ?>
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
  'id' => 'category-option-grid',
  'dataProvider' => $this->params['categoryOptionDataProvider'],
  'columns' =>
  [
      'id',
      [
          'class' => \yii2mod\editable\EditableColumn::class,
          'attribute' => 'code',
          'url' => ['categoryoption/update'],
      ],
      [
          'class' => \yii2mod\editable\EditableColumn::class,
          'attribute' => 'name',
          'url' => ['categoryoption/update'],
      ],
      [
          'class' => \yii2mod\editable\EditableColumn::class,
          'attribute' => 'description',
          'url' => ['categoryoption/update'],
      ],
      # Category sub options
      [
          'label' => 'Sub Options',
          'format' => 'raw',
          'value' => function ($model)
          {
              $output = "Click here to create sub options";
              $categorySubOptions = $model->getCategorySubOptions()->all();
              if (count($categorySubOptions))
              {
                  $output = "";
                  foreach($categorySubOptions as $categorySubOption)
                  {
                      $output .= "<li><span style='display: inline-block; font-size: 90%;''>".$categorySubOption->name." (".$categorySubOption->code.")</span></li>";
                  }
              }

              # Return
              return Html::a($output, ['categorysuboption/index', 'category_option_id' => $model->id], ['data-pjax' => 0]);
              //return $output;
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
                      'delete-url' => Url::toRoute('categoryoption/delete'),
                      'pjax-container' => 'category-option-grid',
                      'title' => Yii::t('yii', 'Delete')
                  ]);
              },
          ]
      ]
  ]
]);
\yii\widgets\Pjax::end();

?>
