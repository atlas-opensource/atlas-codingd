<?php

# Use the URL namespace
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\grid\GridView;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */

echo "<h1>".$this->params['handle']->handle." / ".$this->params['handle']->name."</h1>";

echo "<a href='".Url::toRoute(['tweet/export', 'handleId' => $this->params['handle']->id])."'><button id='exportTweetBtn' data-refreshUrl='".Url::toRoute(['tweet/export', 'handleId' => $this->params['handle']->id])."' class='btn btn-primary' style='float: right; margin-bottom: 10px; margin-right: 10px;'>Export</button></a>";
echo "<button id='refreshTweetBtn' data-refreshUrl='".Url::toRoute(['handle/getandstorenewtweets', 'handleId' => $this->params['handle']->id])."' class='btn btn-primary' style='float: right; margin-bottom: 10px; margin-right: 10px;'>Refresh</button>";
echo "<a href='".Url::toRoute(['tweet/index', 'handleId' => $this->params['handle']->id])."'><button id='clearFilterBtn' data-refreshUrl='".Url::toRoute(['tweet/index', 'handleId' => $this->params['handle']->id])."' class='btn btn-primary' style='float: right; margin-bottom: 10px; margin-right: 10px;'>Clear Filters</button></a>";
echo "<br />";
echo "<br />";
echo "<br />";
echo "<br />";

# Define default width for tweet text column (relatively large)
$tweetColumnWidth = "65%";

# Instantiate container for additional columns
$additionalColumns = array();

# This is terrible! Find a better way to do this. Use role access or something.
if (in_array(\Yii::$app->user->id, [1,2]))
{
    # Define tweet column width
    $tweetColumnWidth = "400px";

    # Loop through users and build column to display their coding
    foreach ($this->params['users'] as $user)
    {
        if (\Yii::$app->user->id != $user->id)
        {
            $column =
            [
                'format' => 'raw',
                'headerOptions' => ['style' => 'width:400px'],
                'label' => $user->email,
                'value' => function ($model) use ($user)
                {
                    $output = "Click here to code tweet";
                    $categoryOptionValues = $model->getCategoryOptionValues()->where(['user_id' => $user->id])->orderBy("category_option_id ASC")->all();
                    if (count($categoryOptionValues))
                    {
                        $output = "";
                        foreach($categoryOptionValues as $categoryOptionValue)
                        {
                            $category = $categoryOptionValue->getCategoryOption()->one()->getCategory()->one()->display_name;
                            $categoryOption = $categoryOptionValue->getCategoryOption()->one();
                            $categorySubOptionCode = "";
                            $categorySubOptionName = "";
                            $categorySubOptionValues = $categoryOptionValue->getCategorySubOptionValues()->all();
                            if (count($categorySubOptionValues) == 1)
                            {
                                foreach ($categorySubOptionValues as $categorySubOptionValue)
                                {
                                    $categorySubOptionCode = $categorySubOptionValue->getCategorySubOption()->one()->code;
                                    $categorySubOptionName = $categorySubOptionValue->getCategorySubOption()->one()->name;
                                }
                            }

                            $output .= "<li><span style='display: inline-block; text-align: justify; padding-right: 5px; font-size: 90%;'>".$category.":</span><span style='display: inline-block; font-size: 90%;''>".$categoryOption->name." (".$categoryOption->code.$categorySubOptionCode.")</span></li>";
                        }
                    }

                    # Return
                    // return Html::a($output, ['categoryoptionvalue/index', 'tweet_id' => $model->id], ['data-pjax' => 0]);
                    return $output;
                }
            ];

            # Add column to columns
            $additionalColumns[] = $column;
        }
    }
}

$columns =
[
    //'id',
    [
        'attribute' => 'id',
        'label' => 'ID',
        'format' => 'html',
        'value' => function($model)
        {
            # Return
            return Html::a($model->id, 'https://twitter.com/statuses/'.$model->tweet_id, ['target' => '_blank', 'data-pjax' => 0]);
        }
    ],
    // 'date',
    [
        'attribute'=>'date',
        'value'=>'date',
        'filterType' => \kartik\grid\GridView::FILTER_DATE_RANGE,
        // 'filter' => \kartik\daterange\DateRangePicker::widget
        // ([
        //     'attribute'=>'date_filter',
        //     'convertFormat'=>true,
        //     'name'=>'date_filter',
        //     'presetDropdown'=>TRUE,
        //     'hideInput'=>true,
        //     'model'=>$this->params['tweetSearch'],
        //     'pluginOptions'=>
        //     [
        //         'format'=>'Y-m-d',
        //         'opens'=>'left',
        //         'pjaxContainerId' => 'tweet-grid',
        //     ],
        //     'pjaxContainerId' => 'tweet-grid',
        // ]),
    ],
    [
        'attribute' => 'tweet_text',
        'label' => 'Tweet Text',
        //'headerOptions' => ['style' => 'width:'.$tweetColumnWidth],
    ],
    'favorites',
    'retweets',
    [
        'label' => 'My Coding',
        'format' => 'raw',
        'headerOptions' => ['style' => 'width:400px'],
        'value' => function ($model)
        {
            $output = "Click here to code tweet";
            $categoryOptionValues = $model->getCategoryOptionValues()->where(['user_id' => \Yii::$app->user->id])->orderBy("category_option_id ASC")->all();
            if (count($categoryOptionValues))
            {
                $output = "";
                foreach($categoryOptionValues as $categoryOptionValue)
                {
                    $category = $categoryOptionValue->getCategoryOption()->one()->getCategory()->one()->display_name;
                    $categoryOption = $categoryOptionValue->getCategoryOption()->one();

                    $categorySubOptionCode = "";
                    $categorySubOptionName = "";
                    $categorySubOptionValues = $categoryOptionValue->getCategorySubOptionValues()->all();
                    if (count($categorySubOptionValues) == 1)
                    {
                        foreach ($categorySubOptionValues as $categorySubOptionValue)
                        {
                            $categorySubOptionCode = $categorySubOptionValue->getCategorySubOption()->one()->code;
                            $categorySubOptionName = $categorySubOptionValue->getCategorySubOption()->one()->name;
                        }
                    }

                    $output .= "<li><span style='display: inline-block; text-align: justify; padding-right: 5px; font-size: 90%;'>".$category.":</span><span style='display: inline-block; font-size: 90%;''>".$categoryOption->name." (".$categoryOption->code.$categorySubOptionCode.")</span></li>";
                }
            }

            # Return
            return Html::a($output, ['categoryoptionvalue/index', 'tweet_id' => $model->id], ['data-pjax' => 0]);
        }
    ]
];

# Merge additional columns (defined above columns) into columns
$columns = array_merge($columns, $additionalColumns);

# Grid
\yii\widgets\Pjax::begin();
//echo \yii\grid\GridView::widget(
echo \kartik\grid\GridView::widget(
[

	# Set dataprovider
    'id' => 'tweet-grid',
    'filterModel' => $this->params['tweetSearch'],
    'dataProvider' => $this->params['tweetDataProvider'],
    'columns' => $columns,
    'options' => ['overflow: wrap', 'height: 200px', 'width: 100%',],
    'pager' => [
        'firstPageLabel' => 'First',
        'lastPageLabel'  => 'Last'
    ],
]);
\yii\widgets\Pjax::end();

?>
