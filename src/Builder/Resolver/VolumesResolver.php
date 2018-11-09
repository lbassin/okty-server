<?php declare(strict_types=1);

namespace App\Builder\Resolver;

use App\Builder\Validator\VolumeConstraint;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class VolumesResolver
{
    private $validator;
    private $warnings = [];

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function resolve(array $volumes): array
    {
        $output = [];
        foreach ($volumes as $value) {
            $errors = $this->validator->validate($value, new VolumeConstraint());
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
