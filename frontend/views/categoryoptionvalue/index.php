<?php

/* @var $this yii\web\View */

# Use the URL namespace
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use \common\models\CategoryOption;

$tweet = $this->params['tweet'];
$tweetUrl = "https://twitter.com/statuses/".$this->params['tweet']->tweet_id;

echo "<a href='".Url::toRoute(['categoryoptionvalue/index', 'tweet_id' => $this->params['tweet']->getPrevTweet()->id])."'><button class='btn btn-primary' style='float: left; margin-bottom: 10px; margin-right: 10px;'><< Previous Tweet</button></a>";
echo "<a href='".Url::toRoute(['categoryoptionvalue/index', 'tweet_id' => $this->params['tweet']->getNextTweet()->id])."'><button class='btn btn-primary' style='float: right; margin-bottom: 10px; margin-right: 10px;'>Next Tweet >></button></a>";
echo "<br /><br />";

echo "<h1>".$this->params['tweet']->getHandle()->one()['handle']." / ".$this->params['tweet']->getHandle()->one()['name']."</h1>";
echo "<h3 style='font-style: italic;'>\"".$this->params['tweet']->tweet_text."\"</h3>";
echo "<hr />";

?>
<!-- <div id="tweet" tweetID="<?php //echo $this->params['tweet']->tweet_id ?>"</div> -->

<!-- Container -->
<div class="container-fluid">

    <!-- The whole kit and kaboodle -->
    <div class="row">

        <!-- Left hand side -->
        <div class="col-sm-5" style="text-align: left">
            <div id="tweet" tweetID="<?php echo $this->params['tweet']->tweet_id; ?>"></div>
        </div>

        <!-- Right hand side -->
        <div class="col-sm-7" id="category-rows-container">
            <?php
                $addMultipleCategoryOptionValueForm = ActiveForm::begin(
                [
                    'action' => ['categoryoptionvalue/addmultiple'],
                    'id' => 'add-multiple-category-option-value-form',
                    'options' => ['class' => 'form-horizontal'],
                ]);

                # Loop through categories
                foreach ($this->params['categories'] as $category)
                {
                    # Start first row on the left
                    echo "<div class='row'>";

                        # Show category
                        echo "<div class='col-sm-3 category-label-container'>".Html::label($category->display_name, "category-".$category->id."-options", ["id" => "category-".$category->id."-label-0", "data-count" => 0])."</div>";

                        # Start the select box
                        echo "<div class='col-sm-3 category-select-container'>";
                            $allowMultiple = ($category->allow_multiple) ? "allowMultiple" : "";
                            echo Html::dropDownList('category_options[category-'.$category->id.']', null, $this->params['categoryOptions'][$category->id], ['options' => ['notSelected' => ['disabled' => true, 'selected' => 'selected']], 'class' => "optionSelect form-control $allowMultiple", 'id' => "category-".$category->id."-options-0", 'data-count' => 0, 'data-category' => $category->id]);
                            echo Html::hiddenInput('category_ids[category-'.$category->id.']', $category->id);
                            echo Html::hiddenInput('category_allow_multiple[category-'.$category->id.']', $category->allow_multiple, ['id' => "category-".$category->id."-allowMultiple"]);
                        echo "</div>";

                        # Check for sub options and display select if there is any
                        echo "<div class='col-sm-5 category-suboption-container'>";
                            echo Html::dropDownList('category_suboptions['.'category-'.$category->id.']', null, [], ['class' => 'form-control hidden', 'id' => "category-".$category->id."-subOptions-0", "data-count" => 0]);
                            // echo Html::hiddenInput('category_ids['."category-".$category->id."-options".']', $category->id);
                        echo "</div>";

                    # End row
                    echo "</div>";
                }

                echo "<div class='row button-row'>";
                    echo "<div class='col-sm-3' style='text-align: right;'>";
                        echo Html::hiddenInput('tweet_id', $this->params['tweet']->id, ["id" => "tweet-id"]);
                        echo Html::hiddenInput('', $this->params['tweet']->id);
                        echo Html::submitButton('Add Coding(s)', ['class' => 'btn btn-primary', 'id' => 'addAllCodingBtn']);
                        ActiveForm::end();
                    echo "</div>";
                echo "</div>";

                /*****************************************
                * Add handle form
                ******************************************/
                // $addCategoryOptionValueForm = ActiveForm::begin(
                // [
                //     'action' => ['categoryoptionvalue/add'],
                //     'id' => 'add-category-option-value-form',
                //     'options' => ['class' => 'form-horizontal'],
                // ]);
            ?>

        <!-- Last row on the right (add single coding) -->
        <!-- <div class="row"> -->

            <!-- Second row on the right, left column -->
            <!--
            <div class="col-sm-3" style="width: 255px;">
                <?php // echo $addCategoryOptionValueForm->field($this->params['category'], 'display_name')->dropDownList([], ['style'=>'width:250px'])->label('Coding Category'); ?>
            </div>
            -->

            <!-- Second row on the right, middle column -->
            <!-- <div class="col-sm-3" style="width: 255px;"> -->
                <?php // echo $addCategoryOptionValueForm->field($this->params['categoryOption'], 'id')->dropDownList([], ['style'=>'width:250px'])->label('Code'); ?>
            <!-- </div> -->

            <!-- Second row on the right, second middle column -->
            <!-- <div class="col-sm-3 hidden" style="width: 255px;" id="category_sub_option_container"> -->
                <?php // echo $addCategoryOptionValueForm->field($this->params['categorySubOptionValue'], 'category_sub_option_id')->dropDownList([], ['style'=>'width:250px;'])->label('Sub Dimension'); ?>
            <!-- </div> -->

            <!-- Second row on the right, far right column -->
            <!-- <div class="col-sm-3 pull-bottom" style="width: 155px;"> -->
                <?php //echo $addCategoryForm->submit(); ?>
                <!-- <div class="form-group field-category-name required" style="padding-top: 27px"> -->
                    <?php
                        // echo Html::hiddenInput('CategoryOptionValue[tweet_id]', $this->params['tweet']->id);
                        // echo Html::hiddenInput('CategorySubOptionValue[tweet_id]', $this->params['tweet']->id);
                        //echo $addCategoryOptionForm->field($this->params['categoryOption'], 'category_id')->hiddenInput();
                    ?>
                    <?php // echo Html::submitButton('Add Coding', ['class' => 'btn btn-primary']) ?>
                    <!-- <div class="help-block"></div> -->
                <!-- </div> -->
            <!-- </div> -->
        <!-- </div> -->
        <?php // ActiveForm::end(); ?>
        <!-- </div> -->
        <!-- </div> -->
        </div>
    </div>
</div>
<?php


//echo "<iframe src='$tweetUrl' height='500px' width: '40%'>Sorry, something went wrong when trying to laod the tweet. Click here to open the tweet in a new window</iframe>";
\yii\widgets\Pjax::begin();
echo \yii\grid\GridView::widget(
[
    'id' => 'category-option-value-grid',
    'dataProvider' => $this->params['categoryOptionValueDataProvider'],
    'columns' =>
    [
        [
            'label' => 'Category',
            'value' => function ($model)
            {
                return $model->getCategoryOption()->one()->getCategory()->one()->display_name;
            }
        ],
        [
            'label' => 'Coding',
            'value' => function ($model)
            {
                return $model->getCategoryOption()->one()->code." - ".$model->getCategoryOption()->one()->name;
            }
        ],
        [
            'label' => 'Sub Coding',
            'format' => 'raw',
            'value' => function ($model) use ($tweet)
            {
                $output = "";

                foreach ($model->getCategorySubOptionValues()->all() as $categorySubOptionValue)
                {
                    $output .= "{$categorySubOptionValue->getCategorySubOption()->one()->code} - {$categorySubOptionValue->getCategorySubOption()->one()->name}";
                }

                return $output;
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
                        'delete-url' => Url::toRoute('categoryoptionvalue/delete'),
                        'pjax-container' => 'category-option-value-grid',
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
