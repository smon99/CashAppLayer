<?php declare(strict_types=1);

namespace Test\Components\UserReg\Business;

use App\Components\User\Persistence\UserEntityManager;
use App\Components\UserReg\Business\EMailValidator;
use App\Components\UserReg\Business\EmptyFieldValidator;
use App\Components\UserReg\Business\PasswordValidator;
use App\Components\UserReg\Business\UserDuplicationValidator;
use App\Components\UserReg\Business\UserValidation;
use App\Components\UserReg\Business\UserValidationException;
use App\Global\Persistence\SqlConnector;
use App\Global\Persistence\UserDTO;
use App\Global\Persistence\UserMapper;
use PHPUnit\Framework\TestCase;

class UserValidationTest extends TestCase
{
    private UserDTO $userDTO;
    private UserEntityManager $userEntityManager;
    private SqlConnector $sqlConnector;

    protected function setUp(): void
    {
        $this->userDTO = new UserDTO();

        $this->userDTO->username = 'Benutzer';
        $this->userDTO->email = 'Benutzer@Benutzer.de';
        $this->userDTO->password = 'Benutzer123#';

        $this->sqlConnector = new SqlConnector();
        $userMapper = new UserMapper();

        $this->userEntityManager = new UserEntityManager($this->sqlConnector, $userMapper);
    }

    protected function tearDown(): void
    {
        $this->sqlConnector->execute("DELETE FROM Users;", []);
    }

    public function testUserValidationTrue(): void
    {
        $userDTO = new UserDTO();

        $user = 'Benutzer';
        $eMail = 'eMail@eMail.de';
        $password = 'Pa123#';

        $userDTO->username = $user;
        $userDTO->email = $eMail;
        $userDTO->password = $password;

        $validation = new UserValidation(
            new UserDuplicationValidator(),
            new PasswordValidator(),
            new EMailValidator()
        );

        $this->expectNotToPerformAssertions();

        $validation->collectErrors($userDTO);
    }

    public function testUserValidationEMailFalse(): void
    {
        $userDTO = new UserDTO();

        $user = 'Benutzer';
        $eMail = 'eMaileMailJa';
        $password = 'Passwort123#';

        $userDTO->username = $user;
        $userDTO->email = $eMail;
        $userDTO->password = $password;

        $validation = new UserValidation(
            new UserDuplicationValidator(),
            new PasswordValidator(),
            new EMailValidator()
        );

        $this->expectException(UserValidationException::class);
        $this->expectExceptionMessage('Bitte gültige eMail eingeben!');

        $validation->collectErrors($userDTO);
    }

    public function testUserValidationDuplicationEMailFalse(): void
    {
        $this->userEntityManager->save($this->userDTO);

        $userTestDTO = new UserDTO();
        $user = 'BenutzerJa';
        $eMail = 'Benutzer@Benutzer.de';
        $password = 'Benutzer123#';

        $userTestDTO->username = $user;
        $userTestDTO->email = $eMail;
        $userTestDTO->password = $password;

        $validation = new UserValidation(
            new UserDuplicationValidator(),
            new PasswordValidator(),
            new EMailValidator()
        );

        $this->expectException(UserValidationException::class);
        $this->expectExceptionMessage('Fehler eMail bereits vergeben!');

        $validation->collectErrors($userTestDTO);
    }

    public function testUserValidationDuplicationUserFalse(): void
    {
        $this->userEntityManager->save($this->userDTO);

        $userTestDTO = new UserDTO();
        $user = 'Benutzer';
        $eMail = 'Benutzer@BenutzerJa.de';
        $password = 'Benutzer123#';

        $userTestDTO->username = $user;
        $userTestDTO->email = $eMail;
        $userTestDTO->password = $password;

        $validation = new UserValidation(
            new UserDuplicationValidator(),
            new PasswordValidator(),
            new EMailValidator()
        );

        $this->expectException(UserValidationException::class);
        $this->expectExceptionMessage('Fehler Name bereits vergeben!');

        $validation->collectErrors($userTestDTO);
    }

    public function testValidationPasswordFalseShort(): void
    {
        $userDTO = new UserDTO();

        $user = 'Benutzer';
        $eMail = 'eMail@eMail.de';
        $password = 'Pa12#'; // Invalid password

        $userDTO->username = $user;
        $userDTO->email = $eMail;
        $userDTO->password = $password;

        $validation = new UserValidation(
            new UserDuplicationValidator(),
            new PasswordValidator(),
            new EMailValidator()
        );

        $this->expectException(UserValidationException::class);
        $this->expectExceptionMessage('Passwort Anforderungen nicht erfüllt');

        $validation->collectErrors($userDTO);
    }

    public function testValidationPasswordFalseNoUppercase(): void
    {
        $userDTO = new UserDTO();

        $user = 'Benutzer';
        $eMail = 'eMail@eMail.de';
        $password = 'pa123#'; // Invalid password

        $userDTO->username = $user;
        $userDTO->email = $eMail;
        $userDTO->password = $password;

        $validation = new UserValidation(
            new UserDuplicationValidator(),
            new PasswordValidator(),
            new EMailValidator()
        );

        $this->expectException(UserValidationException::class);
        $this->expectExceptionMessage('Passwort Anforderungen nicht erfüllt');

        $validation->collectErrors($userDTO);
    }

    public function testValidationEmptyFieldTrue(): void
    {
        $userDTO = new UserDTO();

        $user = '';
        $eMail = 'Simon@Simon.de';
        $password = 'Simon123#';

        $userDTO->username = $user;
        $userDTO->email = $eMail;
        $userDTO->password = $password;

        $validation = new UserValidation(
            new EmptyFieldValidator()
        );

        $this->expectException(UserValidationException::class);
        $this->expectExceptionMessage('Alle Felder müssen ausgefüllt sein!');

        $validation->collectErrors($userDTO);
    }
}
