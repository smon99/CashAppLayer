<?php declare(strict_types=1);

namespace Test\Components\User\Persistence;

use App\Components\User\Persistence\UserEntityManager;
use App\Components\User\Persistence\UserRepository;
use App\Global\Persistence\SqlConnector;
use App\Global\Persistence\UserDTO;
use App\Global\Persistence\UserMapper;
use PHPUnit\Framework\TestCase;

class UserEntityManagerTest extends TestCase
{
    private SqlConnector $sqlConnector;
    private UserRepository $userRepository;
    private UserEntityManager $userEntityManager;

    protected function setUp(): void
    {
        $this->sqlConnector = new SqlConnector();
        $userMapper = new UserMapper();

        $this->userRepository = new UserRepository($this->sqlConnector, $userMapper);
        $this->userEntityManager = new UserEntityManager($this->sqlConnector, $userMapper);
    }

    protected function tearDown(): void
    {
        $this->sqlConnector->execute("DELETE FROM Users;", []);
    }

    public function testSaveUser(): void
    {
        $user = new UserDTO();
        $user->username = 'Tester';
        $user->email = 'Tester@Tester.de';
        $user->password = 'Tester123#';

        $this->userEntityManager->save($user);

        $users[] = $this->userRepository->fetchAllUsers();
        $userEntity = $users[0][0];

        self::assertSame('Tester', $userEntity->username);
    }
}
