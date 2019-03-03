<?php
namespace Donnetbr\Fire\Attribute;

use Donnetbr\Fire\Attribute as AttributeBase;
use Donnetbr\Fire\Compiler;
use Donnetbr\Fire\Fire;

/**
 *
 * @author Donnet do Brasil <dev@donnet.host>
 *
 */
class IfAttribute implements AttributeBase
{
    public function visit(\DOMAttr $att, Compiler $context)
    {
        $node = $att->ownerElement;
        $pi = $context->createControlNode("if " . html_entity_decode($att->value));
        $node->parentNode->insertBefore($pi, $node);

        if (!($nextElement = self::findNextElement($node)) || (!$nextElement->hasAttributeNS(Fire::NS, 'elseif') && !$nextElement->hasAttributeNS(Fire::NS, 'else'))) {
            $pi = $context->createControlNode("endif");
            $node->parentNode->insertBefore($pi, $node->nextSibling); // insert after
        } else {
            self::removeWhitespace($node);
        }
        $node->removeAttributeNode($att);
    }

    public static function removeWhitespace(\DOMElement $element)
    {
        while ($el = $element->nextSibling) {
            if ($el instanceof \DOMText) {
                $element->parentNode->removeChild($el);
            } else {
                break;
            }
        }
    }

    public static function findNextElement(\DOMElement $element)
    {
        $next = $element;
        while ($next = $next->nextSibling) {
            if ($next instanceof \DOMText && trim($next->textContent)) {
                return null;
            }
            if ($next instanceof \DOMElement) {
                return $next;
            }
        }

        return null;
    }

    public static function findPrevElement(\DOMElement $element)
    {
        $prev = $element;
        while ($prev = $prev->previousSibling) {
            if ($prev instanceof \DOMText && trim($prev->textContent)) {
                return null;
            }
            if ($prev instanceof \DOMElement) {
                return $prev;
            }
        }

        return null;
    }
}
