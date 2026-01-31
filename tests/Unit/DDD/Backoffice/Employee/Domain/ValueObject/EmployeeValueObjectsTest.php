<?php

declare(strict_types=1);

namespace Tests\Unit\DDD\Backoffice\Employee\Domain\ValueObject;

use App\DDD\Backoffice\Employee\Domain\Exception\InvalidEmployeeEmailException;
use App\DDD\Backoffice\Employee\Domain\Exception\InvalidEmployeeNameException;
use App\DDD\Backoffice\Employee\Domain\Exception\InvalidEmployeeNidException;
use App\DDD\Backoffice\Employee\Domain\Exception\InvalidEmployeePhoneException;
use App\DDD\Backoffice\Employee\Domain\ValueObject\EmployeeEmail;
use App\DDD\Backoffice\Employee\Domain\ValueObject\EmployeeLastName;
use App\DDD\Backoffice\Employee\Domain\ValueObject\EmployeeName;
use App\DDD\Backoffice\Employee\Domain\ValueObject\EmployeeNid;
use App\DDD\Backoffice\Employee\Domain\ValueObject\EmployeePhone;
use PHPUnit\Framework\TestCase;

final class EmployeeValueObjectsTest extends TestCase
{
    // EmployeeEmail tests
    public function test_creates_valid_email(): void
    {
        $email = EmployeeEmail::create('test@example.com');
        $this->assertEquals('test@example.com', $email->value());
    }

    public function test_normalizes_email_to_lowercase(): void
    {
        $email = EmployeeEmail::create('TEST@EXAMPLE.COM');
        $this->assertEquals('test@example.com', $email->value());
    }

    public function test_trims_email(): void
    {
        $email = EmployeeEmail::create('  test@example.com  ');
        $this->assertEquals('test@example.com', $email->value());
    }

    public function test_rejects_invalid_email(): void
    {
        $this->expectException(InvalidEmployeeEmailException::class);
        EmployeeEmail::create('invalid-email');
    }

    public function test_rejects_empty_email(): void
    {
        $this->expectException(InvalidEmployeeEmailException::class);
        EmployeeEmail::create('');
    }

    // EmployeePhone tests
    public function test_creates_valid_phone(): void
    {
        $phone = EmployeePhone::create('612345678');
        $this->assertEquals('612345678', $phone->value());
    }

    public function test_normalizes_phone_with_prefix(): void
    {
        $phone = EmployeePhone::create('+34 612 345 678');
        $this->assertEquals('+34612345678', $phone->value());
    }

    public function test_removes_spaces_and_dashes(): void
    {
        $phone = EmployeePhone::create('612-345-678');
        $this->assertEquals('612345678', $phone->value());
    }

    public function test_rejects_short_phone(): void
    {
        $this->expectException(InvalidEmployeePhoneException::class);
        EmployeePhone::create('12345');
    }

    public function test_rejects_phone_with_letters(): void
    {
        $this->expectException(InvalidEmployeePhoneException::class);
        EmployeePhone::create('612ABC678');
    }

    // EmployeeNid tests (DNI/NIE español)
    public function test_creates_valid_dni(): void
    {
        $nid = EmployeeNid::create('12345678Z');
        $this->assertEquals('12345678Z', $nid->value());
    }

    public function test_creates_valid_nie(): void
    {
        $nid = EmployeeNid::create('X1234567L');
        $this->assertEquals('X1234567L', $nid->value());
    }

    public function test_normalizes_dni_to_uppercase(): void
    {
        $nid = EmployeeNid::create('12345678z');
        $this->assertEquals('12345678Z', $nid->value());
    }

    public function test_removes_separators_from_dni(): void
    {
        $nid = EmployeeNid::create('12.345.678-Z');
        $this->assertEquals('12345678Z', $nid->value());
    }

    public function test_rejects_invalid_dni_letter(): void
    {
        $this->expectException(InvalidEmployeeNidException::class);
        EmployeeNid::create('12345678A'); // La letra correcta sería Z
    }

    public function test_rejects_invalid_dni_format(): void
    {
        $this->expectException(InvalidEmployeeNidException::class);
        EmployeeNid::create('1234567');
    }

    // EmployeeName tests
    public function test_creates_valid_name(): void
    {
        $name = EmployeeName::create('Juan');
        $this->assertEquals('Juan', $name->value());
    }

    public function test_trims_name(): void
    {
        $name = EmployeeName::create('  Juan  ');
        $this->assertEquals('Juan', $name->value());
    }

    public function test_rejects_empty_name(): void
    {
        $this->expectException(InvalidEmployeeNameException::class);
        EmployeeName::create('');
    }

    public function test_rejects_short_name(): void
    {
        $this->expectException(InvalidEmployeeNameException::class);
        EmployeeName::create('A');
    }

    // EmployeeLastName tests
    public function test_creates_valid_last_name(): void
    {
        $lastName = EmployeeLastName::create('García');
        $this->assertEquals('García', $lastName->value());
    }

    public function test_rejects_empty_last_name(): void
    {
        $this->expectException(InvalidEmployeeNameException::class);
        EmployeeLastName::create('');
    }
}
