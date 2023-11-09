<?php declare(strict_types=1);

namespace Test\Components\Account\Persistence;

use App\Components\Account\Persistence\AccountEntityManager;
use App\Components\Account\Persistence\AccountRepository;
use App\Global\Persistence\AccountDTO;
use App\Global\Persistence\AccountMapper;
use App\Global\Persistence\SqlConnector;
use PHPUnit\Framework\TestCase;

class AccountRepositoryTest extends TestCase
{
    private AccountRepository $accountRepository;
    private AccountEntityManager $accountEntityManager;

    protected function setUp(): void
    {
        $this->accountRepository = new AccountRepository(new SqlConnector(), new AccountMapper());
        $this->accountEntityManager = new AccountEntityManager(new SqlConnector(), new AccountMapper());

        $accountDTOList = [
            new AccountDTO(),
            new AccountDTO(),
            new AccountDTO(),
            new AccountDTO(),
            new AccountDTO(),
            new AccountDTO(),
        ];

        $accountDTOList[0]->transactionID = 1;
        $accountDTOList[0]->value = 10.0;
        $accountDTOList[0]->userID = 1;
        $accountDTOList[0]->transactionDate = date('Y-m-d');
        $accountDTOList[0]->transactionTime = date('H:i:s');
        $accountDTOList[0]->purpose = 'deposit';

        $accountDTOList[1]->transactionID = 2;
        $accountDTOList[1]->value = 15.0;
        $accountDTOList[1]->userID = 1;
        $accountDTOList[1]->transactionDate = date('Y-m-d');
        $accountDTOList[1]->transactionTime = date('H:i:s');
        $accountDTOList[1]->purpose = 'deposit';

        $time = new \DateTime(date('H:i:s'));
        $intervall = new \DateInterval('PT3599S');
        $time->sub($intervall);

        $accountDTOList[2]->transactionID = 3;
        $accountDTOList[2]->value = 5.0;
        $accountDTOList[2]->userID = 1;
        $accountDTOList[2]->transactionDate = date('Y-m-d');
        $accountDTOList[2]->transactionTime = $time->format('H:i:s');
        $accountDTOList[2]->purpose = 'deposit';

        $accountDTOList[3]->transactionID = 4;
        $accountDTOList[3]->value = 12.0;
        $accountDTOList[3]->userID = 1;
        $accountDTOList[3]->transactionDate = '2023-10-22';
        $accountDTOList[3]->transactionTime = date('H:i:s');
        $accountDTOList[3]->purpose = 'deposit';

        $time = new \DateTime(date('H:i:s'));
        $intervall = new \DateInterval('PT3601S');
        $time->sub($intervall);

        $accountDTOList[4]->transactionID = 5;
        $accountDTOList[4]->value = 12.0;
        $accountDTOList[4]->userID = 1;
        $accountDTOList[4]->transactionDate = date('Y-m-d');
        $accountDTOList[4]->transactionTime = $time->format('H:i:s');
        $accountDTOList[4]->purpose = 'deposit';

        $time = new \DateTime(date('H:i:s'));
        $intervall = new \DateInterval('PT1H');
        $time->sub($intervall);

        $accountDTOList[5]->transactionID = 5;
        $accountDTOList[5]->value = 12.0;
        $accountDTOList[5]->userID = 1;
        $accountDTOList[5]->transactionDate = date('Y-m-d');
        $accountDTOList[5]->transactionTime = $time->format('H:i:s');
        $accountDTOList[5]->purpose = 'deposit';

        foreach ($accountDTOList as $accountDTO) {
            $this->accountEntityManager->saveDeposit($accountDTO);
        }
    }

    protected function tearDown(): void
    {
        $connector = new SqlConnector();
        $connector->execute("DELETE FROM Transactions;", []);
    }

    public function testFetchAllTransactions(): void
    {
        $transactions = $this->accountRepository->fetchAllTransactions();
        $assertion = $transactions[0];

        self::assertSame(10.0, $assertion->value);
    }

    public function testCalculateBalance(): void
    {
        $balance = $this->accountRepository->calculateBalance(1);

        self::assertSame(66.0, $balance);
    }

    public function testCalculateBalancePerHour(): void
    {
        $balancePerHour = $this->accountRepository->calculateBalancePerHour(1);

        self::assertSame(30.0, $balancePerHour);
    }

    public function testCalculateBalancePerDay(): void
    {
        $balancePerDay = $this->accountRepository->calculateBalancePerDay(1);

        self::assertSame(54.0, $balancePerDay);
    }

    public function testTransactionPerUserID(): void
    {
        $userTransactions = $this->accountRepository->transactionPerUserID(1);
        $transactionEntity = $userTransactions[0];

        self::assertSame(10.0, $transactionEntity->value);
    }
}
