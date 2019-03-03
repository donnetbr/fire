<?php
namespace Donnetbr\Fire\Attribute;

use Donnetbr\Fire\Attribute as AttributeBase;
use Donnetbr\Fire\Compiler;
use Donnetbr\Fire\Helper\DOMHelper;
use Donnetbr\Fire\Fire;

/**
 * @author Donnet do Brasil <dev@donnet.host>
 */
class ExtendsAttribute implements AttributeBase
{
    public function visit(\DOMAttr $att, Compiler $context)
    {
        $node = $att->ownerElement;

        $filename = '"' . $att->value . '"';


        $xp = new \DOMXPath($att->ownerDocument);
        $xp->registerNamespace("t", Fire::NS);

        $candidates = array();
        foreach ($xp->query(".//*[@t:block-inner or @t:block-outer]|.//t:*", $node) as $blockNode) {
            $ancestors = $xp->query("ancestor::*[@t:block-inner or @t:block-outer or @t:extends]", $blockNode);

            if ($ancestors->length === 1) {
                $candidates[] = $blockNode;
            }
        }

        // having block-inner makes no sense when child of an t:extends (t:extend can have only t:block child)
        // so lets convert them to t:block nodes
        $candidates = $this->convertBlockInnerIntoBlock($candidates, $node);

        foreach ($candidates as $candidate) {
            if ($candidate->parentNode !== $node) {
                $candidate->parentNode->removeChild($candidate);
                $node->appendChild($candidate);
            }
        }
        if ($candidates) {
            foreach (iterator_to_array($node->childNodes) as $k => $item) {
                if (!in_array($item, $candidates, true)) {
                    $node->removeChild($item);
                }
            }
        }

        $context->compileChilds($node);

        $set = iterator_to_array($node->childNodes);
        if (count($set)) {
            $n = $node->ownerDocument->createTextNode("\n");
            array_unshift($set, $n);
        }
        $ext = $context->createControlNode("extends {$filename}");
        array_unshift($set, $ext);

        DOMHelper::replaceWithSet($node, $set);
    }

    /**
     * @param array $candidates
     * @param \DOMNode $node
     * @return array
     */
    private function convertBlockInnerIntoBlock(array $candidates, \DOMNode $node)
    {
        /**
         * @var $candidate \DOMElement
         */
        foreach ($candidates as $k => $candidate) {
            if ($candidate->hasAttributeNS(Fire::NS, "block-inner")) {
                $blockName = $candidate->getAttributeNS(Fire::NS, "block-inner");

                $block = $node->ownerDocument->createElementNS(Fire::NS, "block");
                $block->setAttribute("name", $blockName);

                $candidate->parentNode->insertBefore($block, $candidate);

                // move all child to the new block node
                while ($candidate->firstChild) {
                    $child = $candidate->removeChild($candidate->firstChild);
                    $block->appendChild($child);
                }
                $candidate->parentNode->removeChild($candidate);
                $candidates[$k] = $block;
            }
        }

        return $candidates;
    }
}
