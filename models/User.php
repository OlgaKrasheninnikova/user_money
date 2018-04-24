<?php

namespace app\models;

use yii\base\Exception;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $username
 * @property double $balance
 * @property int $created_at
 *
 * @property Operation[] $operationsFrom
 * @property Operation[] $operationsTo
 */
class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{

    /**
     * {@inheritdoc}
     */
    public static function tableName() : string
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'created_at', 'balance'], 'required'],
            [['balance'], 'number'],
            [['created_at'], 'integer'],
            [['username'], 'string', 'min' => 2, 'max' => 255],
            [['username'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() : array
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'balance' => 'Balance',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return string
     */
    public function getUsername() : string {
        return $this->username;
    }

    /**
     * @param float $amount
     * @return bool
     */
    public function increaseBalance(float $amount) : bool {
        $this->setAttribute('balance', $this->balance + $amount);
        return true;
    }


    /**
     * @param float $amount
     * @return bool
     * @throws Exception
     */
    public function decreaseBalance(float $amount) : bool {
        $userManager = new UserManager();
        $balance = $this->getAttribute('balance');
        $balance -= $amount;
        if ($balance < $userManager->getMinPossibleBalance()) {
            throw new Exception("User balance is too small (can't move $amount)");
        }
        $this->setAttribute('balance', $balance);
        return true;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOperationsFrom() : ActiveQuery
    {
        return $this->hasMany(Operation::class, ['from_user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOperationsTo() : ActiveQuery
    {
        return $this->hasMany(Operation::class, ['to_user_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return UserQuery the active query used by this AR class.
     */
    public static function find() : ActiveQuery
    {
        return new UserQuery(get_called_class());
    }

    /**
     * {@inheritdoc}
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey() : string
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey) : bool
    {
        return true;
    }


    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id) : ?User
    {
        $um = new UserManager();
        return $um->findIdentity($id);
    }

}
