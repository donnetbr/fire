<?php
namespace Donnetbr\Fire\Node;

use Donnetbr\Fire\Compiler;
use Donnetbr\Fire\Exception;
use Donnetbr\Fire\Helper\DOMHelper;
use Donnetbr\Fire\Node;
use Donnetbr\Fire\Fire;

/**
 *
 * @author Donnet do Brasil <dev@donnet.host>
 *
 */
class BlockNode implements Node
{
    public function visit(\DOMElement $node, Compiler $context)
    {
        if (!$node->hasAttribute("name")) {
            throw new Exception("Name attribute is required");
        }

        $sandbox = $node->ownerDocument->createElementNS(Fire::NS, "sandbox");
        $node->parentNode->insertBefore($sandbox, $node);
        $node->parentNode->removeChild($node);
        $sandbox->appendChild($node);

        $context->compileAttributes($node);
        $context->compileChilds($node);

        $start = $context->createControlNode("block " . $node->getAttribute("name"));
        $end = $context->createControlNode("endblock");

        $sandbox->insertBefore($start, $sandbox->firstChild);
        $sandbox->appendChild($end);

        DOMHelper::replaceWithSet($sandbox, iterator_to_array($sandbox->childNodes));
        DOMHelper::replaceWithSet($node, iterator_to_array($node->childNodes));
    }
}
