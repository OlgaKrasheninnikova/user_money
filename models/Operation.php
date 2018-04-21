<?php

namespace app\models;

use yii\db\ActiveQuery;

/**
 * This is the model class for table "operations". Operation = money transfer.
 *
 * @property int $id
 * @property int $from_user_id
 * @property int $to_user_id
 * @property double $amount
 * @property int $executed_at
 *
 * @property User $fromUser
 * @property User $toUser
 */
class Operation extends \yii\db\ActiveRecord
{

    /**
     * @var string
     */
    const SCENARIO_DEFAULT = "default";

    /**
     * @var string
     */
    const SCENARIO_USER_PAY = "USER_PAY";

    /**
     * @var float
     */
    const AMOUNT_VALUE_MIN = 0.01;

    /**
     * @var float
     */
    const AMOUNT_VALUE_MAX = 10000;

    /**
     * @var string
     */
    public $to_user_name;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'operations';
    }


    /**
     * @return array
     */
    public function scenarios()
    {
        return [
            self::SCENARIO_DEFAULT => ['from_user_id', 'to_user_id', 'amount', 'executed_at'],
            self::SCENARIO_USER_PAY => ['to_user_name', 'amount'],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['from_user_id','to_user_id','to_user_name', 'amount'], 'required'],
            [['to_user_id', 'from_user_id', 'executed_at'], 'integer'],
            [['to_user_name'], 'string', 'min' => 2, 'max' => 255],
            [
                ['amount'],
                'number',
                'min' => self::AMOUNT_VALUE_MIN,
                'max' => self::AMOUNT_VALUE_MAX,
                'tooSmall' => 'Amount have to be positive',
                'numberPattern' => '/^\s*[-+]?[0-9]*[.,]?[0-9]{1,2}\s*$/',
                'message' => '{attribute} has to be a number with 2 numbers after dot.'
            ],
            [['from_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['from_user_id' => 'id']],
            [['to_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['to_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() : array
    {
        return [
            'id' => 'ID',
            'from_user_id' => 'From User ID',
            'to_user_id' => 'To User ID',
            'to_user_name' => 'Username to move money',
            'amount' => 'Amount',
            'executed_at' => 'Executed At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFromUser() : ActiveQuery
    {
        return $this->hasOne(User::className(), ['id' => 'from_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getToUser() : ActiveQuery
    {
        return $this->hasOne(User::className(), ['id' => 'to_user_id']);
    }

}
