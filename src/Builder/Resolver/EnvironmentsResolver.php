<?php declare(strict_types=1);

namespace App\Builder\Resolver;

use App\Builder\Validator\EnvironmentConstraint;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EnvironmentsResolver
{
    private $validator;
    private $warnings = [];

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function resolve(array $environments): array
    {
        $output = [];
        foreach ($environments as $value) {
            $errors = $this->validator->validate($value, new EnvironmentConstraint());
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
