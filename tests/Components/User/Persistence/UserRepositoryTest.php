<?php declare(strict_types=1);

namespace Test\Components\User\Persistence;

use App\Components\User\Persistence\UserEntityManager;
use App\Components\User\Persistence\UserRepository;
use App\Global\Persistence\SqlConnector;
use App\Global\Persistence\UserDTO;
use App\Global\Persistence\UserMapper;
use PHPUnit\Framework\TestCase;

class UserRepositoryTest extends TestCase
{
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $userMapper = new UserMapper();
        $sqlConnector = new SqlConnector();
        $this->sqlConnector = new SqlConnector();

        $userEntityManager = new UserEntityManager($sqlConnector, $userMapper);
        $this->userRepository = new UserRepository($sqlConnector, $userMapper);

        $userEntityManager->save($this->createUserDTO(1, 'user1', 'user1@example.com', 'password1'));
        $userEntityManager->save($this->createUserDTO(2, 'user2', 'user2@example.com', 'password2'));
        $userEntityManager->save($this->createUserDTO(3, 'user3', 'user3@example.com', 'password3'));
    }

    protected function tearDown(): void
    {
        $this->sqlConnector->execute("DELETE FROM Users;", []);
    }

    public function testFetchAllUsers(): void
    {
        $users = $this->userRepository->fetchAllUsers();
        $this->assertCount(3, $users);
    }

    public function testFindByMail(): void
    {
        $user = $this->userRepository->findByMail('user2@example.com');
        $this->assertInstanceOf(UserDTO::class, $user);
        $this->assertEquals('user2@example.com', $user->email);
    }

    public function testFindByMailNotFound(): void
    {
        $user = $this->userRepository->findByMail('nonexistent@example.com');
        $this->assertNull($user);
    }

    public function testFindByUsername(): void
    {
        $user = $this->userRepository->findByUsername('user3');
        $this->assertInstanceOf(UserDTO::class, $user);
        $this->assertEquals('user3', $user->username);
    }

    public function testFindByUsernameNotFound(): void
    {
        $user = $this->userRepository->findByUsername('nonexistentuser');
        $this->assertNull($user);
    }

    private function createUserDTO(int $userID, string $username, string $email, string $password): UserDTO
    {
        $userDTO = new UserDTO();
        $userDTO->userID = $userID;
        $userDTO->username = $username;
        $userDTO->email = $email;
        $userDTO->password = $password;
        return $userDTO;
    }
}
