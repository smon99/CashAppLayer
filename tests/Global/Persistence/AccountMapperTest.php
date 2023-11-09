<?php declare(strict_types=1);

namespace Test\Global\Persistence;

use App\Components\Account\Persistence\AccountEntityManager;
use App\Components\Account\Persistence\AccountRepository;
use App\Global\Persistence\AccountDTO;
use App\Global\Persistence\AccountMapper;
use App\Global\Persistence\SqlConnector;
use PHPUnit\Framework\TestCase;

class AccountMapperTest extends TestCase
{
    private AccountEntityManager $accountEntityManager;
    private AccountRepository $accountRepository;

    protected function setUp(): void
    {
        $accountDTO = new AccountDTO();
        $accountDTO->userID = 1;
        $accountDTO->value = 1.00;
        $accountDTO->purpose = 'testing';
        $accountDTO->transactionTime = date('H:i:s');
        $accountDTO->transactionDate = date('Y-m-d');

        $this->accountEntityManager = new AccountEntityManager(new SqlConnector(), new AccountMapper());
        $this->accountEntityManager->saveDeposit($accountDTO);

        $this->accountRepository = new AccountRepository(new SqlConnector(), new AccountMapper());
    }

    protected function tearDown(): void
    {
        $connector = new SqlConnector();
        $connector->execute("DELETE FROM Transactions;", []);
    }

    public function testSqlToDTO(): void
    {
        $mapper = new AccountMapper();

        $data = [
            [
                'transactionID' => 1,
                'value' => 100.0,
                'userID' => 123,
                'transactionDate' => '2023-10-07',
                'transactionTime' => '12:34:56',
                'purpose' => 'Deposit',
            ],
            [
                'transactionID' => 2,
                'value' => 50.0,
                'userID' => 456,
                'transactionDate' => '2023-10-08',
                'transactionTime' => '14:45:30',
                'purpose' => 'Withdrawal',
            ],
        ];

        $result = $mapper->sqlToDTO($data);

        $this->assertCount(2, $result);

        $this->assertInstanceOf(AccountDTO::class, $result[0]);
        $this->assertEquals(1, $result[0]->transactionID);
        $this->assertEquals(100.0, $result[0]->value);
        $this->assertEquals(123, $result[0]->userID);
        $this->assertEquals('2023-10-07', $result[0]->transactionDate);
        $this->assertEquals('12:34:56', $result[0]->transactionTime);
        $this->assertEquals('Deposit', $result[0]->purpose);

        $this->assertInstanceOf(AccountDTO::class, $result[1]);
        $this->assertEquals(2, $result[1]->transactionID);
        $this->assertEquals(50.0, $result[1]->value);
        $this->assertEquals(456, $result[1]->userID);
        $this->assertEquals('2023-10-08', $result[1]->transactionDate);
        $this->assertEquals('14:45:30', $result[1]->transactionTime);
        $this->assertEquals('Withdrawal', $result[1]->purpose);
    }

    public function testDtoToArray(): void
    {
        $mapper = new AccountMapper();
        $accountDTO = new AccountDTO();
        $accountDTO->transactionID = 1;
        $accountDTO->value = 100.0;
        $accountDTO->userID = 123;
        $accountDTO->transactionDate = '2023-10-07';
        $accountDTO->transactionTime = '12:34:56';
        $accountDTO->purpose = 'Deposit';

        $result = $mapper->dtoToArray($accountDTO);

        $expected = [
            'transactionID' => 1,
            'value' => 100.0,
            'userID' => 123,
            'transactionDate' => '2023-10-07',
            'transactionTime' => '12:34:56',
            'purpose' => 'Deposit',
        ];

        $this->assertEquals($expected, $result);
    }

    public function testTypeCast(): void
    {
        $result = $this->accountRepository->transactionPerUserID(1);

        self::assertIsInt($result[0]->userID);
        self::assertIsFloat($result[0]->value);
        self::assertIsString($result[0]->transactionTime);
        self::assertIsString($result[0]->transactionDate);
        self::assertIsString($result[0]->purpose);
    }
}