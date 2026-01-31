<?php

declare(strict_types=1);

namespace Tests\Unit\DDD\Shared\Domain\Criteria;

use App\DDD\Shared\Domain\Criteria\Criteria;
use App\DDD\Shared\Domain\Criteria\Filter;
use App\DDD\Shared\Domain\Criteria\FilterOperator;
use App\DDD\Shared\Domain\Criteria\Order;
use App\DDD\Shared\Domain\Criteria\OrderDirection;
use PHPUnit\Framework\TestCase;

final class CriteriaTest extends TestCase
{
    public function test_creates_empty_criteria(): void
    {
        $criteria = Criteria::empty();

        $this->assertFalse($criteria->hasFilters());
        $this->assertNull($criteria->order());
        $this->assertNull($criteria->limit());
        $this->assertNull($criteria->offset());
    }

    public function test_creates_criteria_with_filters(): void
    {
        $criteria = Criteria::create([
            Filter::equal('status', 'active'),
            Filter::greaterThan('age', 18),
        ]);

        $this->assertTrue($criteria->hasFilters());
        $this->assertCount(2, $criteria->filters());
    }

    public function test_adds_filter_immutably(): void
    {
        $criteria1 = Criteria::empty();
        $criteria2 = $criteria1->withFilter(Filter::equal('name', 'John'));

        $this->assertFalse($criteria1->hasFilters());
        $this->assertTrue($criteria2->hasFilters());
    }

    public function test_adds_order(): void
    {
        $criteria = Criteria::empty()
            ->withOrder(Order::desc('created_at'));

        $this->assertNotNull($criteria->order());
        $this->assertEquals('created_at', $criteria->order()->field);
        $this->assertEquals(OrderDirection::DESC, $criteria->order()->direction);
    }

    public function test_adds_pagination(): void
    {
        $criteria = Criteria::empty()
            ->withLimit(10)
            ->withOffset(20);

        $this->assertEquals(10, $criteria->limit());
        $this->assertEquals(20, $criteria->offset());
    }

    public function test_filter_operators(): void
    {
        $equal = Filter::equal('field', 'value');
        $this->assertEquals(FilterOperator::EQUAL, $equal->operator);

        $like = Filter::like('field', 'value');
        $this->assertEquals(FilterOperator::LIKE, $like->operator);

        $in = Filter::in('field', [1, 2, 3]);
        $this->assertEquals(FilterOperator::IN, $in->operator);

        $between = Filter::between('field', 10, 20);
        $this->assertEquals(FilterOperator::BETWEEN, $between->operator);
        $this->assertEquals([10, 20], $between->value);
    }
}
