<?php
namespace Donnetbr\Fire\Attribute;

use Donnetbr\Fire\Attribute as AttributeBase;
use Donnetbr\Fire\Compiler;

/**
 *
 * @author Donnet do Brasil <dev@donnet.host>
 *
 */
class BaseAttribute implements AttributeBase
{
    public function visit(\DOMAttr $att, Compiler $context)
    {
        $node = $att->ownerElement;

        $pi = $context->createControlNode("{$att->localName} " . html_entity_decode($att->value));
        $node->parentNode->insertBefore($pi, $node);

        $pi = $context->createControlNode("end{$att->localName}");

        $node->parentNode->insertBefore($pi, $node->nextSibling); // insert after

        $node->removeAttributeNode($att);
    }
}
