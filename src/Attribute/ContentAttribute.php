<?php
namespace Donnetbr\Fire\Attribute;

use Donnetbr\Fire\Attribute;
use Donnetbr\Fire\Compiler;
use Donnetbr\Fire\Helper\DOMHelper;

/**
 *
 * @author Donnet do Brasil <dev@donnet.host>
 *
 */
class ContentAttribute implements Attribute
{
    public function visit(\DOMAttr $att, Compiler $context)
    {
        $node = $att->ownerElement;
        DOMHelper::removeChilds($node);
        $pi = $context->createPrintNode(html_entity_decode($att->value));
        $node->appendChild($pi);

        $node->removeAttributeNode($att);
    }
}
