<?php declare(strict_types=1);

namespace Test\Components\Account\Business;

use App\Components\Account\Business\Validation\AccountValidation;
use App\Components\Account\Business\Validation\AccountValidationException;
use App\Components\Account\Business\Validation\DayValidator;
use App\Components\Account\Business\Validation\HourValidator;
use App\Components\Account\Business\Validation\SingleValidator;
use PHPUnit\Framework\TestCase;

class AccountValidationTest extends TestCase
{
    public function testDayDepositLimit(): void
    {
        $userID = 0;
        $random = random_int(1, 50);
        $amount = (float)$random;

        $validator = new AccountValidation();

        try {
            $validator->collectErrors($amount, $userID);
            self::assertTrue(true);
        } catch (AccountValidationException $e) {
            self::fail("Validation should not have thrown an exception: " . $e->getMessage());
        }
    }

    public function testCollectErrorsNotTrue(): void
    {
        $userID = 0;
        $validator = new AccountValidation(new DayValidator(), new HourValidator(), new SingleValidator());
        $amount = 510;

        try {
            $validator->collectErrors($amount, $userID);
            self::fail("Validation should have thrown an exception.");
        } catch (AccountValidationException $e) {
            self::assertSame('Tägliches Einzahlungslimit von 500€ überschritten!', $e->getMessage());
        }
    }

    public function testCollectErrorsEqual500(): void
    {
        $userID = 0;
        $validator = new AccountValidation(new DayValidator(), new HourValidator(), new SingleValidator());
        $amount = 500;

        try {
            $validator->collectErrors($amount, $userID);
            self::assertTrue(true);
        } catch (AccountValidationException $e) {
            self::assertSame("Stündliches Einzahlungslimit von 100€ überschritten!", $e->getMessage());
        }
    }

    public function testCollectErrorsEqual100(): void
    {
        $userID = 0;
        $validator = new AccountValidation(new DayValidator(), new HourValidator(), new SingleValidator());
        $amount = 100;

        try {
            $validator->collectErrors($amount, $userID);
            self::assertTrue(true);
        } catch (AccountValidationException $e) {
            self::assertSame("Bitte einen Betrag von mindestens 0.01€ und maximal 50€ eingeben!", $e->getMessage());
        }
    }

    public function testCollectErrorsEqual50(): void
    {
        $userID = 0;
        $validator = new AccountValidation(new DayValidator(), new HourValidator(), new SingleValidator());
        $amount = 50;

        try {
            $validator->collectErrors($amount, $userID);
            self::assertTrue(true);
        } catch (AccountValidationException $e) {
            self::assertSame("", $e->getMessage());
        }
    }

    public function testCollectErrorsEqual001(): void
    {
        $userID = 0;
        $validator = new AccountValidation(new DayValidator(), new HourValidator(), new SingleValidator());
        $amount = 0.01;

        try {
            $validator->collectErrors($amount, $userID);
            self::assertTrue(true);
        } catch (AccountValidationException $e) {
            self::assertSame("", $e->getMessage());
        }
    }

    public function testCollectErrorsTrue(): void
    {
        $userID = 0;
        $validator = new AccountValidation(new DayValidator(), new HourValidator(), new SingleValidator());

        try {
            $amount = 20;
            $validator->collectErrors($amount, $userID);
            self::assertTrue(true);
        } catch (AccountValidationException $e) {
            self::fail("Validation should not have thrown an exception: " . $e->getMessage());
        }
    }

    public function testSingleValidatorError(): void
    {
        $userID = 0;
        $validator = new AccountValidation(new SingleValidator());

        try {
            $amount = 51;
            $validator->collectErrors($amount, $userID);
            self::fail("Validation should have thrown an exception.");
        } catch (AccountValidationException $e) {
            self::assertSame('Bitte einen Betrag von mindestens 0.01€ und maximal 50€ eingeben!', $e->getMessage());
        }
    }

    public function testHourValidatorError(): void
    {
        $userID = 0;
        $validator = new AccountValidation(new HourValidator(), new DayValidator(), new SingleValidator());
        $amount = 101.0;

        try {
            $validator->collectErrors($amount, $userID);
        } catch (AccountValidationException $e) {
            self::assertSame('Stündliches Einzahlungslimit von 100€ überschritten!', $e->getMessage());
        }
    }
}
