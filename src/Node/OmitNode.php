<?php
namespace Donnetbr\Fire\Node;

use Donnetbr\Fire\Compiler;
use Donnetbr\Fire\Helper\DOMHelper;
use Donnetbr\Fire\Node;

/**
 *
 * @author Donnet do Brasil <dev@donnet.host>
 *
 */
class OmitNode implements Node
{
    public function visit(\DOMElement $node, Compiler $context)
    {
        $context->compileAttributes($node);
        $context->compileChilds($node);
        DOMHelper::replaceWithSet($node, iterator_to_array($node->childNodes));
    }
}
