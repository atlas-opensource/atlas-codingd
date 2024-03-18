<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tweet".
 *
 * @property int $id
 * @property int $handle_id
 * @property string $date
 * @property int $tweet_id
 * @property string $tweet_text
 * @property string $app
 * @property int $followers
 * @property int $follows
 * @property int $retweets
 * @property int $favorites
 * @property string $location
 *
 * @property CategoryOptionValue[] $categoryOptionValues
 * @property Handle $handle
 */
class Tweet extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tweet';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return
        [
            [['handle_id'], 'required'],
            [['handle_id', 'tweet_id', 'followers', 'follows', 'retweets', 'favorites'], 'integer'],
            [['date'], 'safe'],
            [['tweet_text', 'retweet_text'], 'string', 'max' => 800],
            [['app', 'location'], 'string', 'max' => 255],
            [['handle_id'], 'exist', 'skipOnError' => true, 'targetClass' => Handle::className(), 'targetAttribute' => ['handle_id' => 'id']],
        ];
    }


    /**
     * Makes sure the twitter id does not alerady exist for this user before inserting
     * @return \yii\db\ActiveRecord::beforeSave()
     */
    public function beforeSave($insert)
    {
        # Only continue if we are inserting
        if ($insert)
        {
            # Test value to see if it is actually a tweet (I don't know what find() returns when it doens't find anything)
            if (\common\models\Tweet::find()->where(['tweet_id' => $this->tweet_id, 'handle_id' => $this->handle_id])->exists())
            {
                # Should not insert this because we already stored this tweet.
                return false;
            }
        }

        # Return ActiveRecord::beforeSave();
        return parent::beforeSave($insert);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return
        [
            'id' => 'ID',
            'handle_id' => 'Handle ID',
            'date' => 'Date',
            'tweet_id' => 'Tweet ID',
            'tweet_text' => 'Tweet Text',
            'app' => 'App',
            'followers' => 'Followers',
            'follows' => 'Follows',
            'retweets' => 'Retweets',
            'favorites' => 'Favorites',
            'location' => 'Location',
            'retweet_text' => 'Retweet Text',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategoryOptionValues()
    {
        return $this->hasMany(CategoryOptionValue::className(), ['tweet_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHandle()
    {
        return $this->hasOne(Handle::className(), ['id' => 'handle_id']);
    }

    public static function getExportHeader()
    {
        # Build header row (this should be rewritten to use the actual fields and attribute labels)
        $headerRow = array('"ID"', '"Date"', '"Handle"', '"Name"', '"Label"', '"Followers"', '"Location"', '"Tweet Text"', '"Tweet ID"');

        # Loop through content categories
        $categories = Category::find()->orderBy("name ASC")->all();
        foreach ($categories as $category)
        {
            # Loop through users
            $users = MnemosyneUser::find()->orderBy("email ASC")->all();
            foreach ($users as $user)
            {
                # Add category to the
                $headerRow[] = '"'.$category->display_name . " " . $user->email.'"';

                # Check for sub categories and set flag if there is
                $subCoding = false;
                $categoryOptions = $category->getCategoryOptions()->all();
                if (!empty($categoryOptions) && is_array($categoryOptions) && count($categoryOptions))
                {
                    # Loop through $categoryOptions
                    foreach($categoryOptions as $categoryOption)
                    {
                        $subOptions = $categoryOption->getCategorySubOptions()->all();
                        if (!empty($subOptions) && is_array($subOptions) && count($subOptions))
                        {
                            $subCoding = true;
                        }
                    }
                }

                if ($subCoding)
                {
                    $headerRow[] = '"'.$category->display_name . " Sub Coding " . $user->email.'"';
                }

                # Check if multiple are allowed
                if ($category->allow_multiple)
                {
                    # Define subquery to get category id
                    $subQuery = CategoryOption::find()->select('id')->where(['category_id' => $category->id]);

                    # Get a count of how many we will need (maximum that any user has applied)
                    $count = (new \yii\db\Query())
                        ->select(['user_id', 'tweet_id', 'count(*) AS multiCount'])
                        ->from('category_option_value')
                        ->where(['in', 'category_option_id', $subQuery])
                        ->groupBy("tweet_id, user_id")
                        ->orderBy("multiCount DESC")
                        ->one();

                    # Loop through the count (-1 because we have one above already) and add columns
                    for ($i = 0; $i < $count['multiCount']-2; $i++)
                    {
                        # Set user visible counter
                        $displayIndex = $i+2;
                        $displayIndex = (string)$displayIndex;
                        $headerRow[] = '"'.$category->display_name . " ($displayIndex) " . $user->email.'"';
                    }
                }
            }
        }

        # Return
        return $headerRow;
    }

    // public function getExportTweetData()
    // {
    //     # Setup var for return
    //     $dataRow = array();
    //
    //     # Pull all the coding for this tweet (if this doesnt work try looping through users like the indexcontroller)
    //     $categoryOptionValues = $this->getCategoryOptionValues()->where(['tweet_id' => $this->id])->all();
    //
    //     // # Check to make sure we have some coding values
    //     if (!empty($categoryOptionValues) && is_array($categoryOptionValues) && count($categoryOptionValues))
    //     {
    //         # Build row (TODO: this should be rewritten to use the actual fields and attribute labels)
    //         $dataRow = array();
    //         $dataRow['"ID"'] = '"'.$this->id.'"';
    //         $dataRow['"Date"'] = '"'.$this->date.'"';
    //         $dataRow['"Name"'] = '"'.$this->getHandle()->one()->name.'"';
    //         $dataRow['"Handle"'] = '"'.$this->getHandle()->one()->handle.'"';
    //         $dataRow['"Label"'] = '"'.$this->getHandle()->one()->label.'"';
    //         $dataRow['"Followers"'] = '"'.$this->followers.'"';
    //         $dataRow['"Location"'] = '"'.$this->location.'"';
    //         // $dataRow['"Tweet Text"'] = '"'.addslashes($this->tweet_text).'"';
    //         $dataRow['"Tweet Text"'] = '"'.str_replace('"', '""', $this->tweet_text).'"';
    //         $dataRow['"Tweet ID"'] = '"'.(string)$this->tweet_id.'"';
    //
    //         # Set multiple counter
    //         $allowMultipleCounter = 2;
    //
    //         # Loop through coding
    //         foreach ($categoryOptionValues as $categoryOptionValue)
    //         {
    //             # Get the category
    //             $category = $categoryOptionValue->getCategoryOption()->one()->getCategory()->one();
    //
    //             # Get the value the user selected for this category
    //             $categoryOption = $categoryOptionValue->getCategoryOption()->one();
    //
    //             # Get the user object
    //             $user=NULL;
    //             unset($user);
    //             $user = $categoryOptionValue->getUser()->one();
    //
    //             # Check if multiple are allowed
    //             if ($category->allow_multiple && (!empty($dataRow["\"".$category->display_name . " " . $user->email."\""]) || !empty($dataRow["\"".$category->display_name . " ($allowMultipleCounter) " . $user->email."\""])))
    //             {
    //                 # Append data to the row with the new header
    //                 $dataRow["\"".$category->display_name . " ($allowMultipleCounter) " . $user->email."\""] = '"'.$categoryOption->code . " - " . $categoryOption->name.'"';
    //
    //                 # Increment repeated coding counter
    //                 $allowMultipleCounter++;
    //
    //                 if ($user->email == "juliajuarez098@gmail.com" && $this->id == 43358);
    //                 {
    //                     print_r($dataRow);
    //                 }
    //             }
    //             # Otherwise add the column to the row as if it were the first
    //             else
    //             {
    //                 # Append the value the user chose for this category to the array representing the excel row at the position defined by category and user
    //                 $dataRow["\"".$category->display_name . " " . $user->email."\""] = '"'.$categoryOption->code . " - " . $categoryOption->name.'"';
    //             }
    //
    //
    //             # Define sub option code as null
    //             $categorySubOptionCode = "";
    //
    //             # Define sub option name as null
    //             $categorySubOptionName = "";
    //
    //             # Pull all the sub codings
    //             $categorySubOptionValues = $categoryOptionValue->getCategorySubOptionValues()->all();
    //
    //             # Check to make sure there is exactly one for this coding (TODO: refactor this at the model level or use a different query above)
    //             if (count($categorySubOptionValues) == 1)
    //             {
    //                 # Loop through the array (should only have one value)
    //                 foreach ($categorySubOptionValues as $categorySubOptionValue)
    //                 {
    //                     # Define the sub code
    //                     $categorySubOptionCode = $categorySubOptionValue->getCategorySubOption()->one()->code;
    //
    //                     # Define the sub code name
    //                     $categorySubOptionName = $categorySubOptionValue->getCategorySubOption()->one()->name;
    //                 }
    //             }
    //
    //             $dataRow['"'.$category->display_name . " Sub Coding " . $user->email.'"'] = '"'.$categorySubOptionCode . " - " . $categorySubOptionName.'"';
    //         }
    //     }
    //     # Check to make sure we have some coding values
    //     // if (!empty($categoryOptionValues) && is_array($categoryOptionValues) && count($categoryOptionValues))
    //     // {
    //     //     # Build row (TODO: this should be rewritten to use the actual fields and attribute labels)
    //     //     $dataRow = array();
    //     //     $dataRow['"ID"'] = '"'.$this->id.'"';
    //     //     $dataRow['"Date"'] = '"'.$this->date.'"';
    //     //     $dataRow['"Name"'] = '"'.$this->getHandle()->one()->name.'"';
    //     //     $dataRow['"Handle"'] = '"'.$this->getHandle()->one()->handle.'"';
    //     //     $dataRow['"Label"'] = '"'.$this->getHandle()->one()->label.'"';
    //     //     $dataRow['"Followers"'] = '"'.$this->followers.'"';
    //     //     $dataRow['"Location"'] = '"'.$this->location.'"';
    //     //     $dataRow['"Tweet Text"'] = '"'.addslashes($this->tweet_text).'"';
    //     //     $dataRow['"Tweet ID"'] = '"'.(string)$this->tweet_id.'"';
    //     //
    //     //     # Set multiple counter (this counter keeps increasing past the maximum number of multi coded tweets. It needs to be reset.)
    //     //     $allowMultipleCounter = 2;
    //     //
    //     //     # Loop through coding
    //     //     foreach ($categoryOptionValues as $categoryOptionValue)
    //     //     {
    //     //         # Get the category (TODO: this variable name is misleading and it makes me think it is an object, its not. It is a string representing the name).
    //     //         $category = $categoryOptionValue->getCategoryOption()->one()->getCategory()->one();
    //     //
    //     //         # Get the value the user selected for this category
    //     //         $categoryOption = $categoryOptionValue->getCategoryOption()->one();
    //     //
    //     //         # Get the user object
    //     //         $user=NULL;
    //     //         unset($user);
    //     //         $user = $categoryOptionValue->getUser()->one();
    //     //
    //     //         # Check if multiple are allowed
    //     //         if ($category->allow_multiple)
    //     //         {
    //     //             # Case 0: None of the codes have been entered into the row for this user and this category.
    //     //             if (empty($dataRow["\"".$category->display_name . " " . $user->email."\""]))
    //     //             {
    //     //                 # Append the value the user chose for this category to the array representing the excel row at the position defined by category and user
    //     //                 $dataRow["\"".$category->display_name . " " . $user->email."\""] = '"'.$categoryOption->code . " - " . $categoryOption->name.'"';
    //     //             }
    //     //             # Case 1: The first code has been entered for this user and this category
    //     //             elseif (!empty($dataRow["\"".$category->display_name . " " . $user->email."\""]))
    //     //             {
    //     //                 # Loop through the possible indexes and check to see if they have been populated starting at the lowest
    //     //                 # Maybe the allowMultipleCounter should start at 1 or 0 to avoid some confusion
    //     //                 while ($ndx = 2; $ndx <= $multiCount; $ndx++)
    //     //                 {
    //     //                     # Check to see if this index is empty, only populate the next empty one
    //     //                     if (empty($dataRow["\"".$category->display_name . " ($ndx) " . $user->email."\""]))
    //     //                     {
    //     //                         # Append data to the row with the new header
    //     //                         $dataRow["\"".$category->display_name . " ($ndx) " . $user->email."\""] = '"'.$categoryOption->code . " - " . $categoryOption->name.'"';
    //     //                         break;
    //     //                     }
    //     //
    //     //                     // $dataRow["\"".$category->display_name . " ($allowMultipleCounter) " . $user->email."\""] = '"'.$categoryOption->code . " - " . $categoryOption->name.'"';
    //     //                 }
    //     //             }
    //     //             // # Case 2: More than one code has been entered into the row for this user and this category
    //     //             // elseif (!empty($dataRow["\"".$category->display_name . " ($allowMultipleCounter) " . $user->email."\""]))
    //     //             // {
    //     //             //
    //     //             // }
    //     //
    //     //             // # Increment repeated coding counter
    //     //             // $allowMultipleCounter++;
    //     //         }
    //     //         # Otherwise add the column to the row as if it were the first
    //     //         else
    //     //         {
    //     //             # Append the value the user chose for this category to the array representing the excel row at the position defined by category and user
    //     //             $dataRow["\"".$category->display_name . " " . $user->email."\""] = '"'.$categoryOption->code . " - " . $categoryOption->name.'"';
    //     //         }
    //     //
    //     //
    //     //         # Define sub option code as null
    //     //         $categorySubOptionCode = "";
    //     //
    //     //         # Define sub option name as null
    //     //         $categorySubOptionName = "";
    //     //
    //     //         # Pull all the sub codings
    //     //         $categorySubOptionValues = $categoryOptionValue->getCategorySubOptionValues()->all();
    //     //
    //     //         # Check to make sure there is exactly one for this coding (TODO: refactor this at the model level or use a different query above)
    //     //         if (count($categorySubOptionValues) == 1)
    //     //         {
    //     //             # Loop through the array (should only have one value)
    //     //             foreach ($categorySubOptionValues as $categorySubOptionValue)
    //     //             {
    //     //                 # Define the sub code
    //     //                 $categorySubOptionCode = $categorySubOptionValue->getCategorySubOption()->one()->code;
    //     //
    //     //                 # Define the sub code name
    //     //                 $categorySubOptionName = $categorySubOptionValue->getCategorySubOption()->one()->name;
    //     //             }
    //     //         }
    //     //
    //     //         $dataRow['"'.$category->display_name . " Sub Coding " . $user->email.'"'] = '"'.$categorySubOptionCode . " - " . $categorySubOptionName.'"';
    //     //
    //     //         if ($user->email == "testuser0" && $this->id == "")
    //     //         {
    //     //             print_r($dataRow);exit();
    //     //         }
    //     //
    //     //     }
    //     // }
    //
    //     # Return
    //     return $dataRow;
    // }

    public function getExportTweetData()
    {
        # Setup var for return
        $dataRow = array();

        # Pull all the coding for this tweet (if this doesnt work try looping through users like the indexcontroller)
        $categoryOptionValues = $this->getCategoryOptionValues()->where(['tweet_id' => $this->id])->all();

        # Check to make sure we have some coding values
        if (!empty($categoryOptionValues) && is_array($categoryOptionValues) && count($categoryOptionValues))
        {
            # Build row (TODO: this should be rewritten to use the actual fields and attribute labels)
            $dataRow = array();
            $dataRow['"ID"'] = '"'.$this->id.'"';
            $dataRow['"Date"'] = '"'.$this->date.'"';
            $dataRow['"Name"'] = '"'.$this->getHandle()->one()->name.'"';
            $dataRow['"Handle"'] = '"'.$this->getHandle()->one()->handle.'"';
            $dataRow['"Label"'] = '"'.$this->getHandle()->one()->label.'"';
            $dataRow['"Followers"'] = '"'.$this->followers.'"';
            $dataRow['"Location"'] = '"'.$this->location.'"';
            $dataRow['"Tweet Text"'] = '"'.str_replace('"', '""', $this->tweet_text).'"';
            $dataRow['"Tweet ID"'] = '"'.(string)$this->tweet_id.'"';

            # Set multiple counter (this counter keeps increasing past the maximum number of multi coded tweets. It needs to be reset.)
            $allowMultipleCounter = 2;

            # Loop through coding
            foreach ($categoryOptionValues as $categoryOptionValue)
            {
                # Get the category (TODO: this variable name is misleading and it makes me think it is an object, its not. It is a string representing the name).
                $category = $categoryOptionValue->getCategoryOption()->one()->getCategory()->one();

                # Define subquery to get category id
                $subQuery = CategoryOption::find()->select('id')->where(['category_id' => $category->id]);

                # Get a count of how many we will need (maximum that any user has applied)
                $multiCount = (new \yii\db\Query())
                    ->select(['user_id', 'tweet_id', 'count(*) AS multiCount'])
                    ->from('category_option_value')
                    ->where(['in', 'category_option_id', $subQuery])
                    ->groupBy("tweet_id, user_id")
                    ->orderBy("multiCount DESC")
                    ->one();

                $multiCount = $multiCount['multiCount'];

                # Get the value the user selected for this category
                $categoryOption = $categoryOptionValue->getCategoryOption()->one();

                # Get the user object
                $user=NULL;
                unset($user);
                $user = $categoryOptionValue->getUser()->one();

                # Check if multiple are allowed
                if ($category->allow_multiple)
                {
                    # Case 0: None of the codes have been entered into the row for this user and this category.
                    if (empty($dataRow["\"".$category->display_name . " " . $user->email."\""]))
                    {
                        # Append the value the user chose for this category to the array representing the excel row at the position defined by category and user
                        $dataRow["\"".$category->display_name . " " . $user->email."\""] = '"'.$categoryOption->code . " - " . $categoryOption->name.'"';
                    }
                    # Case 1: The first code has been entered for this user and this category
                    elseif (!empty($dataRow["\"".$category->display_name . " " . $user->email."\""]))
                    {
                        # Loop through the possible indexes and check to see if they have been populated starting at the lowest
                        # Maybe the allowMultipleCounter should start at 1 or 0 to avoid some confusion
                        for ($ndx = 2; $ndx <= $multiCount; $ndx++)
                        {
                            # Check to see if this index is empty, only populate the next empty one
                            if (empty($dataRow["\"".$category->display_name . " ($ndx) " . $user->email."\""]))
                            {
                                # Append data to the row with the new header
                                $dataRow["\"".$category->display_name . " ($ndx) " . $user->email."\""] = '"'.$categoryOption->code . " - " . $categoryOption->name.'"';
                                break;
                            }

                            // $dataRow["\"".$category->display_name . " ($allowMultipleCounter) " . $user->email."\""] = '"'.$categoryOption->code . " - " . $categoryOption->name.'"';
                        }
                    }
                    // # Case 2: More than one code has been entered into the row for this user and this category
                    // elseif (!empty($dataRow["\"".$category->display_name . " ($allowMultipleCounter) " . $user->email."\""]))
                    // {
                    //
                    // }

                    // # Increment repeated coding counter
                    // $allowMultipleCounter++;
                }
                # Otherwise add the column to the row as if it were the first
                else
                {
                    # Append the value the user chose for this category to the array representing the excel row at the position defined by category and user
                    $dataRow["\"".$category->display_name . " " . $user->email."\""] = '"'.$categoryOption->code . " - " . $categoryOption->name.'"';
                }


                # Define sub option code as null
                $categorySubOptionCode = "";

                # Define sub option name as null
                $categorySubOptionName = "";

                # Pull all the sub codings
                $categorySubOptionValues = $categoryOptionValue->getCategorySubOptionValues()->all();

                # Check to make sure there is exactly one for this coding (TODO: refactor this at the model level or use a different query above)
                if (count($categorySubOptionValues) == 1)
                {
                    # Loop through the array (should only have one value)
                    foreach ($categorySubOptionValues as $categorySubOptionValue)
                    {
                        # Define the sub code
                        $categorySubOptionCode = $categorySubOptionValue->getCategorySubOption()->one()->code;

                        # Define the sub code name
                        $categorySubOptionName = $categorySubOptionValue->getCategorySubOption()->one()->name;
                    }
                }

                $dataRow['"'.$category->display_name . " Sub Coding " . $user->email.'"'] = '"'.$categorySubOptionCode . " - " . $categorySubOptionName.'"';

                if ($user->email == "testuser0" && $this->id == "")
                {
                    print_r($dataRow);exit();
                }

            }
        }

        # Return
        return $dataRow;
    }

    public function getNextTweet()
    {
        $prevTweet = Tweet::find()->where(['<', 'tweet_id', $this->tweet_id])->andWhere(['handle_id' => $this->handle_id])->orderBy("tweet_id DESC")->one();
        if (!empty($prevTweet) && is_object($prevTweet) && isset($prevTweet->id) && !empty($prevTweet->id))
        {
            return $prevTweet;
        }
        return $this;
    }

    public function getPrevTweet()
    {
        $nextTweet = Tweet::find()->where(['>', 'tweet_id', $this->tweet_id])->andWhere(['handle_id' => $this->handle_id])->orderBy("tweet_id ASC")->one();
        if (!empty($nextTweet) && is_object($nextTweet) && isset($nextTweet->id) && !empty($nextTweet->id))
        {
            return $nextTweet;
        }
        return $this;
    }
}
