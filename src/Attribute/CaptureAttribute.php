<?php
namespace Donnetbr\Fire\Attribute;

use Donnetbr\Fire\Attribute;
use Donnetbr\Fire\Compiler;

/**
 *
 * @author Donnet do Brasil <dev@donnet.host>
 *
 */
class CaptureAttribute implements Attribute
{
    public function visit(\DOMAttr $att, Compiler $context)
    {
        $node = $att->ownerElement;

        $pi = $context->createControlNode("set " . html_entity_decode($att->value));
        $node->parentNode->insertBefore($pi, $node);

        $pi = $context->createControlNode("endset");

        $node->parentNode->insertBefore($pi, $node->nextSibling); // insert after

        $node->removeAttributeNode($att);
    }
}
