<?php

declare(strict_types=1);

use App\Factory\EnvironmentFactory;
use PHPUnit\Framework\TestCase;

class EnvironmentFactoryTest extends TestCase
{
    public function test_build_with_empty_data(): void
    {
        $request = $this->getRequestWithEnvs([]);

        $envs = (new EnvironmentFactory())->createAllFromRequest($request);

        $this->assertEmpty($envs);
    }

    public function test_build_with_right_data(): void
    {
        $request = $this->getRequestWithEnvs([
            ['key' => 'MYSQL_ROOT_PASSWORD', 'value' => 'root'],
            ['key' => 'user', 'value' => 'John'],
        ]);

        $envs = (new EnvironmentFactory())->createAllFromRequest($request);

        $this->assertCount(2, $envs);

        $this->assertSame('MYSQL_ROOT_PASSWORD', $envs[0]->getKey());
        $this->assertSame('root', $envs[0]->getValue());

        $this->assertSame('user', $envs[1]->getKey());
        $this->assertSame('John', $envs[1]->getValue());
    }

    private function getRequestWithEnvs(array $envs): array
    {
        return [
            "template" => "test",
            "args" => [
                "environments" => $envs,
            ],
        ];
    }
}
