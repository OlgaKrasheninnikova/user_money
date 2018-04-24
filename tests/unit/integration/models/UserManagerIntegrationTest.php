<?php
require_once 'UserTrait.php';

use \PHPUnit\Framework\TestResult;
use \app\models\UserManager;

class UserManagerIntegrationTest extends \Codeception\Test\Unit
{
    use UserTrait;
    /**
     * @var \UnitTester
     */
    protected $tester;

    /** @var UserManager */
    private $userManager;

    /**
     * @var \yii\db\Transaction
     */
    protected $transaction;
    
    protected function _before()
    {
        $this->userManager = new UserManager();
        $this->transaction = Yii::$app->db->beginTransaction();
    }

    protected function _after()
    {
        $this->transaction->rollBack();
        Yii::$app->user ?? Yii::$app->user->logout();
    }

    public function count() : int {
        return 1;
    }

    public function run(TestResult $result = null): TestResult
    {
        return parent::run($result);
    }

    public function testFindIdentity() {
        $name = 'test1';
        $user = $this->createUser($name);
        $foundUser = $this->userManager->findIdentity($user->id);
        $this->assertInstanceOf('app\models\User', $foundUser);
        $this->assertEquals($foundUser->username, $name);
        $this->assertEquals($foundUser->id, $user->id);
    }

    public function testFindByUsername() {
        $name = 'test1';
        $user = $this->createUser($name);
        $foundUser = $this->userManager->findByUsername($name);
        $this->assertInstanceOf('app\models\User', $foundUser);
        $this->assertEquals($foundUser->username, $name);
        $this->assertEquals($foundUser->id, $user->id);
    }

    public function testGetUsersByIds() {
        $user1 = $this->createUser('alice');
        $user2 = $this->createUser('olga');
        $users = $this->userManager->getUsersByIds([$user1->id, $user2->id]);
        $this->assertInternalType('array', $users);
        $this->assertEquals($users[0], $user1);
        $this->assertEquals($users[1], $user2);
    }

    public function testGetNamesByIds() {
        $name1 = 'test1';
        $name2 = 'test2';
        $user1 = $this->createUser($name1);
        $user2 = $this->createUser($name2);
        $idList = [$user1->id, $user2->id];
        $res = $this->userManager->getNamesByIds($idList);
        $this->assertInternalType('array',$res);
        $this->assertEquals(count($res), 2);
        $this->assertArrayHasKey($user1->id, $res);
        $this->assertArrayHasKey($user2->id, $res);
        $this->assertEquals($res[$user1->id], $user1->username);
        $this->assertEquals($res[$user2->id], $user2->username);
    }
    public function testGetUserByNameExist() {
        $user = $this->createUser();
        $userReturned = $this->userManager->getUserByName($user->username);
        $this->assertInternalType('object', $userReturned);
        Yii::$app->getUser();
    }

    public function testGetUserByNameNew() {
        $username = 'newname';
        $user = $this->userManager->getUserByName($username);
        $this->assertInternalType('object', $user);
        $userInDb = UserManager::findByUsername($username);
        $this->assertInternalType('object',$userInDb);
        $this->assertEquals($userInDb->username, $username);
    }

    public function testLoginExist() {
        $user = $this->createUser();
        $ok = $this->userManager->login($user->username);
        $this->assertTrue($ok);
        $this->assertInternalType('object',Yii::$app->getUser());
        $this->assertInternalType('object',Yii::$app->getUser()->getIdentity());
        $this->assertEquals(Yii::$app->getUser()->getIdentity()->username, $user->username);
    }

    public function testLoginNew() {
        $username = 'newname';
        $ok = $this->userManager->login($username);
        $this->assertTrue($ok);
        $this->assertInternalType('object',Yii::$app->getUser());
        $this->assertInternalType('object',Yii::$app->getUser()->getIdentity());
        $this->assertEquals(Yii::$app->getUser()->getIdentity()->username, $username);
    }

}