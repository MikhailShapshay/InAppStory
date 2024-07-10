<?php

use app\models\User;
use Codeception\Test\Unit;

class UserTest extends Unit
{
	/**
	 * @var \UnitTester
	 */
	protected $tester;

	public function testFindUserById()
	{
		expect_that($user = User::findOne(1));
		expect($user->username)->equals('admin');
	}

	public function testFindUserByUsername()
	{
		expect_that($user = User::findByUsername('admin'));
		expect_not(User::findByUsername('not-admin'));
	}

	public function testValidateUser()
	{
		$user = User::findByUsername('admin');
		expect_that($user->validatePassword('admin'));
		expect_not($user->validatePassword('wrong-password'));
	}
}
