<?php

declare(strict_types=1);

namespace Tests\Domain\Generator\ValueObject\DockerComposer\Service;

use App\Domain\Generator\Exception\DockerCompose\Service\Id\EmptyServiceIdException;
use App\Domain\Generator\Exception\DockerCompose\Service\Id\WrongServiceIdFormatException;
use App\Domain\Generator\ValueObject\DockerCompose\Service\Id;
use PHPUnit\Framework\TestCase;

class IdTest extends TestCase
{
    public function test_empty_value(): void
    {
        $this->expectException(EmptyServiceIdException::class);

        new Id('');
    }

    /**
     * @dataProvider wrong_id_format_provider
     */
    public function test_wrong_value_format(string $value): void
    {
        $this->expectException(WrongServiceIdFormatException::class);

        new Id($value);
    }

    /**
     * @dataProvider right_id_format_provider
     */
    public function test_right_value_format(string $value): void
    {
        $id = new Id($value);

        $this->assertInstanceOf(Id::class, $id);
    }

    public function wrong_id_format_provider(): array
    {
        return [
            ['tést'],
            ['wrong-service-id-'],
            ['s@lut'],
            ['(Dummy)'],
            ['Doesn\'t work'],
        ];
    }

    public function right_id_format_provider(): array
    {
        return [
            ['php'],
            ['node-js'],
            ['long-service-id'],
        ];
    }
}
