<?php declare(strict_types=1);

namespace App\Builder\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
class PortConstraint extends Constraint
{
    public $message = "The port '{{ port }}' is not valid.";
}
