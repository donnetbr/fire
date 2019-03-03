<?php
namespace Donnetbr\Fire;

/**
 * Represents the handler for custom nodes.
 *
 * @author Donnet do Brasil <dev@donnet.host>
 *
 */
interface Node
{
    /**
     * Visit a node.
     *
     * @param \DOMElement $node
     * @param Compiler $context
     * @return void
     */
    public function visit(\DOMElement $node, Compiler $context);
}
