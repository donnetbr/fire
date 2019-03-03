<?php
namespace Donnetbr\Fire\Node;

use Donnetbr\Fire\Compiler;
use Donnetbr\Fire\Exception;
use Donnetbr\Fire\Helper\DOMHelper;
use Donnetbr\Fire\Node;

/**
 *
 * @author Donnet do Brasil <dev@donnet.host>
 *
 */
class MacroNode implements Node
{
    public function visit(\DOMElement $node, Compiler $context)
    {
        if (!$node->hasAttribute("name")) {
            throw new Exception("Name attribute is required");
        }

        $context->compileChilds($node);

        $set = iterator_to_array($node->childNodes);

        $start = $context->createControlNode("macro " . $node->getAttribute("name") . "(" . $node->getAttribute("args") . ")");
        array_unshift($set, $start);

        $set[] = $context->createControlNode("endmacro");

        DOMHelper::replaceWithSet($node, $set);
    }
}
