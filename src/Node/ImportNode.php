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
class ImportNode implements Node
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

        if ($node->hasAttribute("as")) {
            $code = "import $filename as " . $node->getAttribute("as");
            $context->createControlNode("import " . ($node->getAttribute("fro-exp") ? $node->getAttribute("name-exp") : ("'" . $node->getAttribute("name") . "'")) . " as " . $node->getAttribute("as"));
        } elseif ($node->hasAttribute("aliases")) {
            $code = "from $filename import " . $node->getAttribute("aliases");
        } else {
            throw new Exception("As or Alias attribute is required");
        }

        $pi = $context->createControlNode($code);

        $node->parentNode->replaceChild($pi, $node);
    }
}
