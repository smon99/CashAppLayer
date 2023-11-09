<?php declare(strict_types=1);

namespace Test\Components\Account\Persistence;

use App\Components\Account\Persistence\AccountEntityManager;
use App\Components\Account\Persistence\AccountRepository;
use App\Global\Persistence\AccountDTO;
use App\Global\Persistence\AccountMapper;
use App\Global\Persistence\SqlConnector;
use PHPUnit\Framework\TestCase;

class AccountEntityManagerTest extends TestCase
{
    private SqlConnector $sqlConnector;
    private AccountMapper $accountMapper;
    private AccountRepository $accountRepository;

    protected function setUp(): void
    {
        $this->sqlConnector = new SqlConnector();
        $this->accountMapper = new AccountMapper();

        $this->accountRepository = new AccountRepository($this->sqlConnector, $this->accountMapper);
    }

    protected function tearDown(): void
    {
        $connector = new SqlConnector();
        $connector->execute("DELETE FROM Transactions;", []);
    }

    public function testSaveDeposit(): void
    {
        $entityManager = new AccountEntityManager($this->sqlConnector, $this->accountMapper);

        $deposit = new AccountDTO();
        $deposit->userID = 1;
        $deposit->value = 10.0;
        $deposit->transactionTime = date('H:i:s');
        $deposit->transactionDate = date('Y-m-d');

        $entityManager->saveDeposit($deposit);
        $transaction[] = $this->accountRepository->transactionPerUserID(1);
        $result = $transaction[0][0];

        self::assertSame(10.0, $result->value);
    }
}
