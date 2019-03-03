<?php
namespace Donnetbr\Fire\Attribute;

use Donnetbr\Fire\Attribute as AttributeBase;
use Donnetbr\Fire\Compiler;
use Donnetbr\Fire\Helper\DOMHelper;
use Donnetbr\Fire\Fire;

/**
 * This will translate '<div t:block-inner="name">foo</div>' into '<div>{% block name%}foo{% endblock %}</div>'
 * @author Donnet do Brasil <dev@donnet.host>
 *
 */
class BlockInnerAttribute implements AttributeBase
{
    public function visit(\DOMAttr $att, Compiler $context)
    {
        $node = $att->ownerElement;
        $node->removeAttributeNode($att);

        // create sandbox and append it to the node
        $sandbox = $node->ownerDocument->createElementNS(Fire::NS, "sandbox");

        // move all child to sandbox to sandbox
        while ($node->firstChild) {
            $child = $node->removeChild($node->firstChild);
            $sandbox->appendChild($child);
        }
        $node->appendChild($sandbox);

        //$context->compileAttributes($node);
        $context->compileChilds($sandbox);


        $start = $context->createControlNode("block " . $att->value);
        $end = $context->createControlNode("endblock");

        $sandbox->insertBefore($start, $sandbox->firstChild);
        $sandbox->appendChild($end);

        DOMHelper::replaceWithSet($sandbox, iterator_to_array($sandbox->childNodes));
    }
}
