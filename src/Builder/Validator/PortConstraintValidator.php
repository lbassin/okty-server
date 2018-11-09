<?php declare(strict_types=1);

namespace App\Builder\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class PortConstraintValidator extends ConstraintValidator
{
    const MIN_PORT = 0;
    const MAX_PORT = 65536;

    public function validate($value, Constraint $constraint): void
    {
        $ports = explode(':', $value);

        $ports[0] = $ports[0] ?? -1;
        $ports[1] = $ports[1] ?? -1;

        foreach ($ports as $port) {
            if ($port > self::MIN_PORT && $port < self::MAX_PORT) {
                continue;
            }

            $this->context
                ->buildViolation($constraint->message)
                ->setParameter('{{ port }}', $port)
                ->addViolation();
        }
    }
}
