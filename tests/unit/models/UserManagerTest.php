<?php
use \PHPUnit\Framework\TestResult;
use \app\models\UserManager;
use \app\models\User;
use \Codeception\Stub;

class UserManagerTest extends \Codeception\Test\Unit
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
        Yii::$app->user ?? Yii::$app->user->logout();
    }

    public function count() : int {
        return 1;
    }

    public function run(TestResult $result = null): TestResult
    {
        return parent::run($result);
    }

    public function testGetNamesByIds() {
        $user1 = $this->make(User::class, ['username' => 'davert', 'id' => 5]);
        $user2 = $this->make(User::class, ['username' => 'alice', 'id' => 8]);
        /** @var UserManager $um */
        $um = $this->make(UserManager::class, ['getUsersByIds' => function(array $idList) use ($user1, $user2) {return [$user1, $user2];}]);
        $idList = [$user2->getId(), $user2->getId()];
        $res = $um->getNamesByIds($idList);
        $this->assertInternalType('array',$res);
        $this->assertEquals(count($res), 2);
        $this->assertArrayHasKey($user1->getId(), $res);
        $this->assertArrayHasKey($user2->getId(), $res);
        $this->assertEquals($res[$user1->getId()], $user1->getUsername());
        $this->assertEquals($res[$user2->getId()], $user2->getUsername());
    }


    public function testGetUserByNameExist() {
        $user = Stub::make(User::class, ['username' => 'davert', 'id' => 5]);
        /** @var UserManager $um */
        $um = $this->make(UserManager::class, [
            'findByUsername' => $user,
            'createUserByName' => $this->_mockCreateUserByName($user->username)
        ]);
        $userReturned = $um->getUserByName($user->username);
        $this->assertInternalType('object', $userReturned);
        $this->assertEquals($userReturned->username, $user->username);
    }

    public function testLoginExist() {
        $user = Stub::make(User::class, ['username' => 'davert', 'id' => 5]);
        /** @var UserManager $um */
        $um = $this->make(UserManager::class, [
            'getUserByName' => $user
        ]);
        $ok = $um->login($user->username);
        $this->assertTrue($ok);
        $this->assertInternalType('object',Yii::$app->getUser());
        $this->assertInternalType('object',Yii::$app->getUser()->getIdentity());
        $this->assertEquals(Yii::$app->getUser()->getIdentity()->username, $user->username);
    }


    public function testLoginNew() {
        $username = 'newname';
        /** @var UserManager $um */
        $um = $this->make(UserManager::class, [
            'getUserByName' => $this->_mockCreateUserByName($username)
        ]);
        $ok = $um->login($username);
        $this->assertTrue($ok);
        $this->assertInternalType('object',Yii::$app->getUser());
        $this->assertInternalType('object',Yii::$app->getUser()->getIdentity());
        $this->assertEquals(Yii::$app->getUser()->getIdentity()->username, $username);
    }

    private function _mockCreateUserByName(string $userName) : ?User {
        return $this->make(User::class, ['username' => $userName, 'id' => 5]);
    }

}