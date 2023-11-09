<?php declare(strict_types=1);

namespace Test\Global\Persistence;

use App\Global\Persistence\UserDTO;
use App\Global\Persistence\UserMapper;
use PHPUnit\Framework\TestCase;

class UserMapperTest extends TestCase
{
    private array $data;
    private UserDTO $userDTO;
    private UserMapper $userMapper;

    protected function setUp(): void
    {
        $this->data = [
            ['userID' => 1, 'username' => 'user1', 'email' => 'user1@example.com', 'password' => 'password1'],
            ['userID' => 2, 'username' => 'user2', 'email' => 'user2@example.com', 'password' => 'password2'],
        ];

        $this->userDTO = new UserDTO();
        $this->userDTO->userID = 100;
        $this->userDTO->username = 'Benutzer';
        $this->userDTO->email = 'Benutzer@Benutzer.de';
        $this->userDTO->password = 'Benutzer123#';

        $this->userMapper = new UserMapper();
    }

    protected function tearDown(): void
    {
        unset($this->userMapper);
    }

    public function testSqlToDTO(): void
    {
        $resultDTOs = $this->userMapper->sqlToDTO($this->data);

        $this->assertCount(2, $resultDTOs);
        $this->assertInstanceOf(UserDTO::class, $resultDTOs[0]);
        $this->assertInstanceOf(UserDTO::class, $resultDTOs[1]);
        $this->assertEquals(1, $resultDTOs[0]->userID);
        $this->assertEquals('user1', $resultDTOs[0]->username);
        $this->assertEquals('user1@example.com', $resultDTOs[0]->email);
        $this->assertEquals('password1', $resultDTOs[0]->password);
        $this->assertEquals(2, $resultDTOs[1]->userID);
        $this->assertEquals('user2', $resultDTOs[1]->username);
        $this->assertEquals('user2@example.com', $resultDTOs[1]->email);
        $this->assertEquals('password2', $resultDTOs[1]->password);
    }

    public function testDtoToArray(): void
    {
        $resultArray = $this->userMapper->dtoToArray($this->userDTO);

        $this->assertEquals([
            'userID' => 100,
            'username' => 'Benutzer',
            'email' => 'Benutzer@Benutzer.de',
            'password' => 'Benutzer123#',
        ], $resultArray);
    }
}