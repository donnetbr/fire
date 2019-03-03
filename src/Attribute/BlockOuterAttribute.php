<?php
namespace Donnetbr\Fire\Attribute;

use Donnetbr\Fire\Attribute as AttributeBase;
use Donnetbr\Fire\Compiler;
use Donnetbr\Fire\Helper\DOMHelper;
use Donnetbr\Fire\Fire;

/**
 * This will translate '<div t:block-outer="name">foo</div>' into '{% block name%}<div>foo</div>{% endblock %}'
 *
 * @author Donnet do Brasil <dev@donnet.host>
 *
 */
class BlockOuterAttribute implements AttributeBase
{
    public function visit(\DOMAttr $att, Compiler $context)
    {
        $node = $att->ownerElement;
        $node->removeAttributeNode($att);

        // create sandbox
        $sandbox = $node->ownerDocument->createElementNS(Fire::NS, "sandbox");
        $node->parentNode->insertBefore($sandbox, $node);

        // move to sandbox
        $node->parentNode->removeChild($node);
        $sandbox->appendChild($node);

        $context->compileAttributes($node);
        $context->compileChilds($node);


        $start = $context->createControlNode("block " . $att->value);
        $end = $context->createControlNode("endblock");

        $sandbox->insertBefore($start, $sandbox->firstChild);
        $sandbox->appendChild($end);

        DOMHelper::replaceWithSet($sandbox, iterator_to_array($sandbox->childNodes));
    }
}
