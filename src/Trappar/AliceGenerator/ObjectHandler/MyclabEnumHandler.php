<?php
/**
 * @author: eliberty
 * @author: xlembo@eliberty.fr
 * @since : 13/04/2020
 */

namespace Trappar\AliceGenerator\ObjectHandler;

//MyCLabs\Enum
use MyCLabs\Enum\Enum;
use Trappar\AliceGenerator\DataStorage\ValueContext;

class MyclabEnumHandler implements ObjectHandlerInterface
{
    public function handle(ValueContext $valueContext)
    {
        /** Enum $enum */
        if (!($enum = $valueContext->getValue()) instanceof Enum) {
            return false;
        }

        $val = $enum->getValue();
        $valueContext->setValue($val);

        return true;
    }
}