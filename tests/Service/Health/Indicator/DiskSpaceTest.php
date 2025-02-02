<?php

declare(strict_types=1);

namespace Chaos\ActuatorBundle\Tests\Service\Health\Indicator;

use Akondas\ActuatorBundle\Service\Health\Health;
use Akondas\ActuatorBundle\Service\Health\Indicator\DiskSpace;
use PHPUnit\Framework\TestCase;

class DiskSpaceTest extends TestCase
{
    public function testName(): void
    {
        $diskSpaceHealthIndicator = new DiskSpace(
            sys_get_temp_dir(),
            10000
        );

        self::assertEquals('diskSpace', $diskSpaceHealthIndicator->name());
    }

    public function testNotHealthyIfDiskFreeSpaceReturnedFalse(): void
    {
        // given
        $diskSpaceHealthIndicator = new DiskSpace(
            '/not-existing',
            10000
        );

        // when
        $health = $diskSpaceHealthIndicator->health();

        // then
        self::assertEquals(Health::UNKNOWN, $health->getStatus());
    }

    public function testNotHealthyIfDiskFreeSpaceIsBelowThreshold(): void
    {
        // given
        $diskSpaceHealthIndicator = new DiskSpace(
            sys_get_temp_dir(),
            PHP_INT_MAX
        );

        // when
        $health = $diskSpaceHealthIndicator->health();

        // then
        self::assertInstanceOf(Health::class, $health);
        self::assertEquals(Health::DOWN, $health->getStatus());

        self::assertArrayHasKey('disk_free_space', $health->getDetails());
        self::assertEquals(disk_free_space(sys_get_temp_dir()), $health->getDetails()['disk_free_space']);

        self::assertArrayHasKey('threshold', $health->getDetails());
        self::assertEquals(PHP_INT_MAX, $health->getDetails()['threshold']);
    }

    public function testHealthyIfDiskFreeSpaceIsBelowThreshold(): void
    {
        // given
        $diskSpaceHealthIndicator = new DiskSpace(
            sys_get_temp_dir(),
            0
        );

        // when
        $health = $diskSpaceHealthIndicator->health();

        // then
        self::assertInstanceOf(Health::class, $health);
        self::assertEquals(Health::UP, $health->getStatus());

        self::assertArrayHasKey('disk_free_space', $health->getDetails());
        self::assertEquals(disk_free_space(sys_get_temp_dir()), $health->getDetails()['disk_free_space']);

        self::assertArrayHasKey('threshold', $health->getDetails());
        self::assertEquals(0, $health->getDetails()['threshold']);
    }
}
