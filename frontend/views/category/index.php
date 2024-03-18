<?php

/* @var $this yii\web\View */

# Use the URL namespace
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;


# Display a title
echo "<h1>Coding Categories</h1>";

/*****************************************
* Add handle form
******************************************/
$addCategoryForm = ActiveForm::begin(
[
    'action' => ['category/add'],
    'id' => 'add-category-form',
    'options' => ['class' => 'form-horizontal'],
]);


?>

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-6" style="width: 160px;">
            <?php echo $addCategoryForm->field($this->params['category'], 'name')->textInput(['style'=>'width:150px']); ?>
        </div>
        <div class="col-sm-6" style="width: 155px;">
            <?php echo $addCategoryForm->field($this->params['category'], 'display_name')->textInput(['style'=>'width:150px']); ?>
        </div>
        <div class="col-sm-6 pull-bottom" style="width: 155px;">
            <?php //echo $addCategoryForm->submit(); ?>
            <div class="form-group field-category-name required" style="padding-top: 27px">
                <!-- <label class="control-label"></label> -->
                <?php echo Html::hiddenInput('Category[project_id]', $this->params['project_id']); ?>
                <?php echo Html::submitButton('Add category', ['class' => 'btn btn-primary']) ?>
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
  'id' => 'category-grid',
  'dataProvider' => $this->params['categoryDataProvider'],
  'columns' =>
  [
      'id',
      'user_id',
      //'name',
      [
          'class' => \yii2mod\editable\EditableColumn::class,
          'attribute' => 'name',
          'url' => ['category/update'],
      ],
      [
          'class' => \yii2mod\editable\EditableColumn::class,
          'attribute' => 'display_name',
          'url' => ['category/update'],
      ],
      //'display_name',
      [
          'label' => 'Options',
          'format' => 'raw',
          'value' => function ($model)
          {
              # Define output
              $output = "";

              # Pull options
              $categoryOptions = $model->getCategoryOptions()->all();

              # Check to make sure we have some
              if (count($categoryOptions))
              {
                  # Loop through options and add to output
                  foreach ($categoryOptions as $ndx => $categoryOption)
                  {

                      //$output .= "<li>".$categoryOption->name."</li>";
                      $output .= "<li>".$categoryOption->code." - ".$categoryOption->name."</li>";
                  }
              }
              else
              {
                  $output = "Add Options";
              }

              # Finish output tag
              //$output = "</ol>";
              //return $output;
              return Html::a($output, ['categoryoption/index', 'categoryId' => $model->id], ['data-pjax' => 0]);
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
                      'delete-url' => Url::toRoute('category/delete'),
                      'pjax-container' => 'category-grid',
                      'title' => Yii::t('yii', 'Delete')
                  ]);
              },
          ]
      ]

  ]
]);
\yii\widgets\Pjax::end();

?>
