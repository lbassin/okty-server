<?php declare(strict_types=1);

namespace App\Builder\Resolver;

use App\Builder\ValueObject\Volume;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class VolumesResolver
{
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function resolve(array $volumes): array
    {
        $output = [];
        foreach ($volumes as $data) {
            $host = $data['host'] ?? '';
            $container = $data['container'] ?? '';

            $volume = new Volume($host, $container);

            $output[] = sprintf('%s:%s', $volume->getHost(), $volume->getContainer());
        }

        return $output;
    }
}
