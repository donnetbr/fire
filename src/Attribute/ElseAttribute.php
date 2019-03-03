<?php
namespace Donnetbr\Fire\Attribute;

use Donnetbr\Fire\Attribute as AttributeBase;
use Donnetbr\Fire\Compiler;
use Donnetbr\Fire\Exception;

/**
 *
 * @author Donnet do Brasil <dev@donnet.host>
 *
 */
class ElseAttribute implements AttributeBase
{
    public function visit(\DOMAttr $att, Compiler $context)
    {
        $node = $att->ownerElement;

        if (!$prev = IfAttribute::findPrevElement($node)) {
            throw new Exception("The attribute 'elseif' must be the very next sibling of an 'if' of 'elseif' attribute");
        }

        $pi = $context->createControlNode("else");
        $node->parentNode->insertBefore($pi, $node);

        $pi = $context->createControlNode("endif");
        $node->parentNode->insertBefore($pi, $node->nextSibling);

        $node->removeAttributeNode($att);
    }
}
