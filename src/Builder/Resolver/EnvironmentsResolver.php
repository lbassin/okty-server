<?php declare(strict_types=1);

namespace App\Builder\Resolver;

use App\Builder\ValueObject\Environment;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EnvironmentsResolver
{
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function resolve(array $environments): array
    {
        $output = [];
        foreach ($environments as $data) {
            $key = $data['key'] ?? '';
            $value = $data['value'] ?? '';

            $env = new Environment($key, $value);

            $output[] = sprintf('%s=%s', $env->getKey(), $env->getValue());
        }

        return $output;
    }
}
