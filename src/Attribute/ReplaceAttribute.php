<?php
namespace Donnetbr\Fire\Attribute;

use Donnetbr\Fire\Attribute;
use Donnetbr\Fire\Compiler;

/**
 *
 * @author Donnet do Brasil <dev@donnet.host>
 *
 */
class ReplaceAttribute implements Attribute
{
    public function visit(\DOMAttr $att, Compiler $context)
    {
        $node = $att->ownerElement;
        $pi = $context->createPrintNode(html_entity_decode($att->value));

        $node->parentNode->replaceChild($pi, $node);

        $node->removeAttributeNode($att);
        return Attribute::STOP_NODE;
    }
}
