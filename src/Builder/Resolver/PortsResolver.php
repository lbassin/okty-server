<?php declare(strict_types=1);

namespace App\Builder\Resolver;

use App\Builder\Validator\PortConstraint;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PortsResolver
{
    private $validator;
    private $warnings = [];

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function resolve(array $ports): array
    {
        $output = [];
        foreach ($ports as $value) {
            $errors = $this->validator->validate($value, new PortConstraint());
            foreach ($errors as $error) {
                $this->warnings[] = $error->getMessage();
            }

            if (count($errors) > 0) {
                continue;
            }

            $output[] = $value;
        }
        return $output;
    }
}
