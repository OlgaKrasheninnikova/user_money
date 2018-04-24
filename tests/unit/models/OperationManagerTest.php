<?php
use \PHPUnit\Framework\TestResult;
use \app\models\OperationManager;
use \app\models\Operation;
use \app\models\User;

class OperationManagerTest extends \Codeception\Test\Unit
{
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
    }

    protected function _after()
    {
    }

    public function count() : int {
        return 1;
    }

    public function run(TestResult $result = null): TestResult
    {
        return parent::run($result);
    }


    // tests
    public function testCreateOperationOk()
    {
        $user1 = $this->make(User::class, ['id' => 10]);
        $user2 = $this->make(User::class, ['id' => 12]);
        $operation = $this->make(Operation::class, ['id' => 12, 'save' => true]);
        $amount = 100;

        $om = new OperationManager();
        $ok = $om->createOperation($user1, $user2, $amount, $operation);
        $this->assertTrue($ok);
    }

    public function testCreateOperationError()
    {
        $user = $this->make(User::class, ['id' => 10]);
        $operation = $this->make(Operation::class, ['id' => 12, 'save' => true]);
        $amount = 100;

        $om = new OperationManager();
        $ok = $om->createOperation($user, $user, $amount, $operation);
        $this->assertFalse($ok);
    }


    public function testExecute() {
        $user1BeforeBalance = 100;
        $user2BeforeBalance = 200;
        $amount = 30.3;
        $user1 = $this->make(User::class, ['id' => 10, 'balance' => $user1BeforeBalance, 'save' => true]);
        $user2 = $this->make(User::class, ['id' => 12, 'balance' => $user2BeforeBalance, 'save' => true]);
        $operation = $this->make(Operation::class, ['id' => 12, 'save' => true]);
        $om = new OperationManager();
        $ok = $om->execute($user1, $user2, $amount, $operation);
        $this->assertTrue($ok);
        $this->assertEquals($user1->balance, $user1BeforeBalance - $amount);
        $this->assertEquals($user2->balance, $user2BeforeBalance + $amount);
        $this->assertEquals($operation->amount, $amount);
    }

}