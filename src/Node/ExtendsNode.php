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
class ExtendsNode implements Node
{
    public function visit(\DOMElement $node, Compiler $context)
    {
        if ($node->hasAttribute("from-exp")) {
            $filename = $node->getAttribute("from-exp");
        } elseif ($node->hasAttribute("from")) {
            $filename = '"' . $node->getAttribute("from") . '"';
        } else {
            throw new Exception("The 'from' or 'from-exp' attribute is required");
        }

        $context->compileChilds($node);

        $ext = $context->createControlNode("extends {$filename}");

        $set = iterator_to_array($node->childNodes);
        if (count($set)) {
            $n = $node->ownerDocument->createTextNode("\n");
            array_unshift($set, $n);
        }
        array_unshift($set, $ext);

        DOMHelper::replaceWithSet($node, $set);
    }
}
