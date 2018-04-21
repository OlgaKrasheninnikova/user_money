<?php
require_once 'UserTrait.php';


use \PHPUnit\Framework\TestResult;
use \app\models\UserManager;
use \app\models\OperationManager;
use \app\models\Operation;

class OperationManagerTest extends \Codeception\Test\Unit
{
    use UserTrait;
    /**
     * @var \UnitTester
     */
    protected $tester;

    /** @var OperationManager */
    private $operationManager;

    /**
     * @var \yii\db\Transaction
     */
    protected $transaction;
    
    protected function _before()
    {
        $this->operationManager = new OperationManager();
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
    public function testCreateOperationOk()
    {
        $user1 = $this->createUser('user1');
        $user2 = $this->createUser('user2');
        $amount = 100;
        $ok = $this->operationManager->createOperation($user1, $user2, $amount);
        $this->assertTrue($ok);
        $foundOp = Operation::find()
            ->where(['from_user_id' => $user1->id, 'to_user_id' => $user2->id, 'amount' => $amount])
            ->one();
        $this->assertInstanceOf('app\models\Operation', $foundOp);
    }

    public function testCreateOperationError()
    {
        $user1 = $this->createUser('user1');
        $amount = 100;
        $ok = $this->operationManager->createOperation($user1, $user1, $amount);
        $this->assertFalse($ok);
        $foundOp = Operation::find()
            ->where(['from_user_id' => $user1->id, 'to_user_id' => $user1->id, 'amount' => $amount])
            ->one();
        $this->assertNull($foundOp);
    }

    public function testExecute() {
        $user1 = $this->createUser('user1');
        $user2 = $this->createUser('user2');
        $amount = 30.3;
        $ok = $this->operationManager->execute($user1, $user2, $amount);
        $this->assertTrue($ok);
        $user1 = UserManager::findIdentity($user1->id);
        $user2 = UserManager::findIdentity($user2->id);
        $this->assertEquals($user1->balance, -$amount);
        $this->assertEquals($user2->balance, $amount);
        $foundOp = Operation::find()
            ->where(['from_user_id' => $user1->id, 'to_user_id' => $user2->id])
            ->one();
        $this->assertInstanceOf('app\models\Operation', $foundOp);
    }

}