<?php
namespace Donnetbr\Fire\Node;

use Donnetbr\Fire\Compiler;
use Donnetbr\Fire\Exception;
use Donnetbr\Fire\Node;

/**
 *
 * @author Donnet do Brasil <dev@donnet.host>
 *
 */
class UseNode implements Node
{
    public function visit(\DOMElement $node, Compiler $context)
    {
        $code = "use ";

        if ($node->hasAttribute("from")) {
            $code .= '"' . $node->getAttribute("from") . '"';
        } else {
            throw new Exception("The 'from' attribute is required");
        }

        if ($node->hasAttribute("with")) {
            $code .= " with " . $node->getAttribute("with");
        }

        $pi = $context->createControlNode($code);
        $node->parentNode->replaceChild($pi, $node);
    }
}
