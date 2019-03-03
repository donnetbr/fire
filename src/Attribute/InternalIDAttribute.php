<?php
namespace Donnetbr\Fire\Attribute;

use Donnetbr\Fire\Attribute as AttributeBase;
use Donnetbr\Fire\Compiler;

/**
 *
 * @author Donnet do Brasil <dev@donnet.host>
 *
 */
class InternalIDAttribute implements AttributeBase
{
    public function visit(\DOMAttr $att, Compiler $context)
    {
        $att->ownerElement->removeAttributeNode($att);
    }
}
