<?php
/* @var $this yii\web\View */

# Use the URL namespace
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>
<h1>category/add</h1>

<?php
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
<div class="row">
    <div class="col-sm-6" style="width: 160px;">
        <?php echo $addCategoryForm->field($this->params['category'], 'name')->textInput(['style'=>'width:150px']); ?>
    </div>
    <div class="col-sm-6" style="width: 155px;">
        <?php echo $addCategoryForm->field($this->params['addHandleFormModel'], 'label')->textInput(['style'=>'width:150px']); ?>
    </div>
</div>



<div class="form-group">
      <!-- //Html::submitButton(Yii::t('app', 'add_handle'), ['class' => 'btn btn-primary']) -->
      <?= Html::submitButton('Add Handle', ['class' => 'btn btn-primary']) ?>
</div>
<?php
ActiveForm::end();
