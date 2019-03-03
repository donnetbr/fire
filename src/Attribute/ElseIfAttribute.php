<?php
namespace Donnetbr\Fire\Attribute;

use Donnetbr\Fire\Attribute as AttributeBase;
use Donnetbr\Fire\Compiler;
use Donnetbr\Fire\Exception;
use Donnetbr\Fire\Fire;

/**
 *
 * @author Donnet do Brasil <dev@donnet.host>
 *
 */
class ElseIfAttribute implements AttributeBase
{
    public function visit(\DOMAttr $att, Compiler $context)
    {
        $node = $att->ownerElement;

        if (!$prev = IfAttribute::findPrevElement($node)) {
            throw new Exception("The attribute 'elseif' must be the very next sibling of an 'if' of 'elseif' attribute");
        }

        $pi = $context->createControlNode("elseif " . html_entity_decode($att->value));
        $node->parentNode->insertBefore($pi, $node);

        if (!($nextElement = IfAttribute::findNextElement($node)) || (!$nextElement->hasAttributeNS(Fire::NS, 'elseif') && !$nextElement->hasAttributeNS(Fire::NS, 'else'))) {
            $pi = $context->createControlNode("endif");
            $node->parentNode->insertBefore($pi, $node->nextSibling); // insert after
        } else {
            IfAttribute::removeWhitespace($node);
        }

        $node->removeAttributeNode($att);
    }
}
