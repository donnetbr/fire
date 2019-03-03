<?php
namespace Donnetbr\Fire\Attribute;

use Donnetbr\Fire\Attribute;
use Donnetbr\Fire\Compiler;
use Donnetbr\Fire\Helper\ParserHelper;

/**
 *
 * @author Donnet do Brasil <dev@donnet.host>
 *
 */
class SetAttribute implements Attribute
{
    public function visit(\DOMAttr $att, Compiler $context)
    {
        $node = $att->ownerElement;

        $sets = ParserHelper::staticSplitExpression(html_entity_decode($att->value), ",");
        foreach ($sets as $set) {
            $pi = $context->createControlNode("set " . $set);
            $node->parentNode->insertBefore($pi, $node);
        }

        $node->removeAttributeNode($att);
    }
}
