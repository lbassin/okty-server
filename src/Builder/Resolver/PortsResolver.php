<?php declare(strict_types=1);

namespace App\Builder\Resolver;

use App\Builder\ValueObject\Port;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PortsResolver
{
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function resolve(array $ports): array
    {
        $output = [];
        foreach ($ports as $data) {
            $host = (int)$data['host'] ?? -1;
            $container = (int)$data['container'] ?? -1;

            $port = new Port($host, $container);

            $output[] = sprintf('%d:%d', $port->getHost(), $port->getContainer());
        }
        return $output;
    }
}
