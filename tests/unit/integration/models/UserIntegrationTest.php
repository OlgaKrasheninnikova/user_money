<?php
require_once 'UserTrait.php';


use \PHPUnit\Framework\TestResult;
use \app\models\User;
use \app\models\UserManager;
use \yii\base\Exception;

class UserIntegrationTest extends \Codeception\Test\Unit
{
    use UserTrait;
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var \yii\db\Transaction
     */
    protected $transaction;
    
    protected function _before()
    {
        $this->transaction = Yii::$app->db->beginTransaction();
    }

    protected function _after()
    {
        $this->transaction->rollBack();
    }

    public function count() : int {
        return 1;
    }

    public function run(TestResult $result = null): TestResult
    {
        return parent::run($result);
    }


    // tests
    public function testIncreaseBalance()
    {
        $user = $this->createUser();
        $user->increaseBalance(20);
        $this->assertEquals($user->balance, 20);

        $user = $this->createUser('test2', 100.11);
        $user->increaseBalance(0.22);
        $this->assertEquals($user->balance, 100.33);
    }


    /**
     * @throws Exception
     */
    public function testDecreaseBalanceCorrect()
    {
        $user = $this->createUser();
        $user->decreaseBalance(20);
        $this->assertEquals($user->balance, -20);

        $user = $this->createUser('test2', -100);
        $user->decreaseBalance(20);
        $this->assertEquals($user->balance, -120);

        $user = $this->createUser('test3', 520.55);
        $user->decreaseBalance(20.33);
        $this->assertEquals($user->balance, 500.22);
    }


    /**
     * @throws Exception
     */
    public function testDecreaseBalanceIncorrect()
    {
        $this->expectException(Exception::class);
        $user = $this->createUser();
        $user->decreaseBalance(-UserManager::BALANCE_MIN*2);
    }
}