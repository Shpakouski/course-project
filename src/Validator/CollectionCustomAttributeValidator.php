<?php

namespace App\Validator;

use App\Entity\CustomAttribute;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CollectionCustomAttributeValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        /* @var CollectionCustomAttribute $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_iterable($value)) {
            return;
        }

        $countPerType = [];

        /**@var CustomAttribute[] */
        foreach ($value as $attribute) {
            $countPerType[$attribute->getType()->name] = isset($countPerType[$attribute->getType()->name]) ? ++$countPerType[$attribute->getType()->name] : 1;
        }

        foreach ($countPerType as $type => $count) {
            if ($count > $constraint->maxItemPerType) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter("{{ max }}", $constraint->maxItemPerType)
                    ->setParameter("{{ count }}", $count)
                    ->setParameter("{{ type }}", $type)
                    ->addViolation();
            }
        }


    }
}
