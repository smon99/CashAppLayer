<?php declare(strict_types=1);

namespace Test\Components\Account\Communication;

use App\Components\Account\Communication\TransactionController;
use App\Components\Account\Persistence\AccountEntityManager;
use App\Components\Account\Persistence\AccountRepository;
use App\Components\User\Persistence\UserEntityManager;
use App\Components\User\Persistence\UserRepository;
use App\Global\Business\Container;
use App\Global\Business\DependencyProvider;
use App\Global\Business\Session;
use App\Global\Persistence\AccountDTO;
use App\Global\Persistence\AccountMapper;
use App\Global\Persistence\SqlConnector;
use App\Global\Persistence\UserDTO;
use App\Global\Persistence\UserMapper;
use PHPUnit\Framework\TestCase;

class TransactionControllerTest extends TestCase
{
    private Session $session;
    private UserDTO $userDTO;
    private AccountDTO $accountDTO;
    private AccountRepository $accountRepository;
    private AccountEntityManager $accountEntityManager;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $sqlConnector = new SqlConnector();
        $accountMapper = new AccountMapper();
        $userMapper = new UserMapper();
        $container = new Container();
        $provider = new DependencyProvider();
        $provider->provide($container);

        $this->session = new Session();

        $this->container = $container;
        $this->controller = new TransactionController($this->container);
        $this->accountRepository = new AccountRepository($sqlConnector, $accountMapper);
        $userEntityManager = new UserEntityManager($sqlConnector, $userMapper);
        $this->accountEntityManager = new AccountEntityManager($sqlConnector, $accountMapper);
        $this->userRepository = new UserRepository($sqlConnector, $userMapper);

        $this->userDTO = new UserDTO();
        $this->userDTO->password = '$2y$10$HFOgNpyX6Ubjs74FjwGA8eALPXqNZV23rQ/fZzDowcwt/hDOzOE1u';
        $this->userDTO->username = 'Simon';
        $this->userDTO->email = 'Simon@Simon.de';
        $this->userDTO->userID = 1;

        $userReceiverDTO = new UserDTO();
        $userReceiverDTO->password = '$2y$10$rqTcf57sIEVAZsertDU7P.8O3kObwxc17jL6Cec.6oMcX/VWdFX0i';
        $userReceiverDTO->username = 'Nico';
        $userReceiverDTO->email = 'Nico@Nico.de';
        $userReceiverDTO->userID = 2;

        $this->accountDTO = new AccountDTO();
        $this->accountDTO->value = 10.0;
        $this->accountDTO->userID = $this->userDTO->userID;
        $this->accountDTO->transactionDate = date('Y-m-d');
        $this->accountDTO->transactionTime = date('H:i:s');
        $this->accountDTO->purpose = 'testing';

        $userEntityManager->save($this->userDTO);
        $userEntityManager->save($userReceiverDTO);

        session_start();
    }

    protected function tearDown(): void
    {
        $connector = new SqlConnector();
        $connector->execute("DELETE FROM Transactions;", []);
        $connector->execute("DELETE FROM Users;", []);
        $this->session->logout();

        unset($_POST["logout"], $_POST["receiver"], $_POST["amount"], $_POST["transfer"], $this->userDTO, $this->session);
    }

    public function testAction(): void
    {
        $this->accountEntityManager->saveDeposit($this->accountDTO);
        $this->session->loginUser($this->userDTO, 'Simon123#');

        $_POST["amount"] = '1';
        $_POST["receiver"] = 'Nico@Nico.de';
        $_POST["transfer"] = true;

        $this->controller->action();

        $transactionsPerSender[] = $this->accountRepository->transactionPerUserID($this->session->getUserID());
        $transactionsPerReceiver[] = $this->accountRepository->transactionPerUserID($this->userRepository->findByMail('Nico@Nico.de')->userID);

        $resultReceiver = null;
        foreach ($transactionsPerReceiver[0] as $transaction) {
            if ($transaction->purpose === 'Zahlung erhalten von Simon') {
                $resultReceiver = $transaction;
                break;
            }
        }

        self::assertSame('Zahlung erhalten von Simon', $resultReceiver->purpose);

        $resultSender = null;
        foreach ($transactionsPerSender[0] as $transaction) {
            if ($transaction->purpose === 'Geldtransfer an Nico') {
                $resultSender = $transaction;
                break;
            }
        }

        self::assertSame('Geldtransfer an Nico', $resultSender->purpose);
    }

    public function testActionNoSession(): void
    {
        $this->session->logout();

        $this->controller->action();
        //url assertion missing
    }

    public function testActionLogOut(): void
    {
        $this->session->loginUser($this->userDTO, 'Simon123#');
        $_POST["logout"] = true;

        $this->controller->action();
        $loginStatus = $this->session->loginStatus();

        self::assertFalse($loginStatus);
        //url assertion missing
    }

    public function testActionTransaction(): void
    {
        $this->accountEntityManager->saveDeposit($this->accountDTO);
        $this->session->loginUser($this->userDTO, 'Simon123#');

        $this->controller->action();

        $_POST["amount"] = "1";
        $_POST["receiver"] = 'Nico@Nico.de';
        $_POST["transfer"] = true;

        $this->controller->action();

        $transactions[] = $this->accountRepository->fetchAllTransactions();
        $entry = $transactions[0][2];

        self::assertSame(1.0, $entry->value);
    }

    public function testActionException(): void
    {
        $this->session->loginUser($this->userDTO, 'Simon123#');
        $_POST["amount"] = '500';
        $_POST["receiver"] = 'Nico@Nico.de';
        $_POST["transfer"] = true;

        $viewParams = $this->controller->action()->getParameters();

        self::assertContains("Bitte einen Betrag von mindestens 0.01€ und maximal 50€ eingeben!", $viewParams);
    }

    public function testActionReceiverInvalid(): void
    {
        $this->accountEntityManager->saveDeposit($this->accountDTO);
        $this->session->loginUser($this->userDTO, 'Simon123#');

        $_POST["amount"] = "1";
        $_POST["receiver"] = 'InvalidUser';
        $_POST["transfer"] = true;

        $viewParams = $this->controller->action()->getParameters();

        self::assertContains("Empfänger existiert nicht! ", $viewParams);
    }

    public function testActionBalanceInvalid(): void
    {
        $this->accountEntityManager->saveDeposit($this->accountDTO);
        $this->session->loginUser($this->userDTO, 'Simon123#');

        $_POST["amount"] = "11";
        $_POST["receiver"] = 'Nico@Nico.de';
        $_POST["transfer"] = true;

        $viewParams = $this->controller->action()->getParameters();

        self::assertContains("Guthaben zu gering! ", $viewParams);
    }

    public function testActionViewParameters(): void
    {
        $this->accountEntityManager->saveDeposit($this->accountDTO);
        $this->session->loginUser($this->userDTO, 'Simon123#');

        $_POST["amount"] = "10";
        $_POST["receiver"] = 'Nico@Nico.de';
        $_POST["transfer"] = true;

        $params = $this->controller->action()->getParameters();

        self::assertContains("Simon", $params);
        self::assertContains($this->session->loginStatus(), $params);
        self::assertContains(($this->accountRepository->calculateBalance($this->session->getUserID())) + 10.00, $params);
        self::assertContains("Die Transaktion wurde erfolgreich durchgeführt!", $params);
    }

    public function testActionTemplatePath(): void
    {
        self::assertSame('transaction.twig', $this->controller->action()->getTpl());
    }
}