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
class IncludeNode implements Node
{
    public function visit(\DOMElement $node, Compiler $context)
    {
        $code = "include ";

        if ($node->hasAttribute("from-exp")) {
            $code .= $node->getAttribute("from-exp");
        } elseif ($node->hasAttribute("from")) {
            $code .= '"' . $node->getAttribute("from") . '"';
        } else {
            throw new Exception("The 'from' or 'from-exp' attribute is required");
        }

        if ($node->hasAttribute("ignore-missing") && $node->getAttribute("ignore-missing") !== "false") {
            $code .= " ignore missing";
        }
        if ($node->hasAttribute("with")) {
            $code .= " with " . $node->getAttribute("with");
        }
        if ($node->hasAttribute("only") && $node->getAttribute("only") !== "false") {
            $code .= " only";
        }
        if ($node->hasAttribute("sandboxed") && $node->getAttribute("sandboxed") !== "false") {
            $code .= " sandboxed = true";
        }

        $pi = $context->createControlNode($code);
        $node->parentNode->replaceChild($pi, $node);
    }
}
