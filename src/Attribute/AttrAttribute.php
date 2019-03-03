<?php
namespace Donnetbr\Fire\Attribute;

use Donnetbr\Fire\Attribute;
use Donnetbr\Fire\Compiler;
use Donnetbr\Fire\Exception;
use Donnetbr\Fire\Helper\ParserHelper;
use Donnetbr\Fire\Fire;

/**
 *
 * @author Donnet do Brasil <dev@donnet.host>
 *
 */
class AttrAttribute implements Attribute
{
    public static function getVarname(\DOMElement $node)
    {
        return "__a" . sha1($node->getAttributeNS(Fire::NS, '__internal-id__') . spl_object_hash($node));
    }

    public function visit(\DOMAttr $att, Compiler $context)
    {
        $node = $att->ownerElement;
        $expressions = ParserHelper::staticSplitExpression($att->value, ",");

        $attributes = array();
        foreach ($expressions as $k => $expression) {
            $expressions[$k] = $attrExpr = self::splitAttrExpression($expression);
            $attNode = null;

            if ($node->hasAttribute($attrExpr['name'])) {
                $attNode = $node->getAttributeNode($attrExpr['name']);
                $node->removeAttributeNode($attNode);
            }

            if ($attrExpr['test'] === "true" || $attrExpr['test'] === "1") {
                unset($expressions[$k]);
                $attributes[$attrExpr['name']] = "[{$attrExpr['expr']}]";
            } elseif ($attNode) {
                $attributes[$attrExpr['name']] = "['" . addcslashes($attNode->value, "'") . "']";
            } else {
                $attributes[$attrExpr['name']] = "[]";
            }
        }

        $varName = self::getVarname($node);
        $code = array();
        $code[] = $context->createControlNode("if $varName is not defined");
        $code[] = $context->createControlNode("set $varName = {" . ParserHelper::implodeKeyed(",", $attributes, true) . " }");
        $code[] = $context->createControlNode("else");
        $code[] = $context->createControlNode("set $varName = $varName|merge({" . ParserHelper::implodeKeyed(",", $attributes, true) . "})");
        $code[] = $context->createControlNode("endif");

        foreach ($expressions as $attrExpr) {
            $code[] = $context->createControlNode("if {$attrExpr['test']}");
            $code[] = $context->createControlNode("set {$varName} = {$varName}|merge({ '{$attrExpr['name']}':[{$attrExpr['expr']}] })");
            $code[] = $context->createControlNode("endif");
        }

        $this->addSpecialAttr($node, $varName, $code);
        $node->removeAttributeNode($att);
    }

    public static function splitAttrExpression($str)
    {
        $parts = ParserHelper::staticSplitExpression($str, "?");
        if (count($parts) == 1) {
            $attr = self::findAttrParts($parts[0]);
            $attr['test'] = 'true';

            return $attr;
        } elseif (count($parts) == 2) {
            $attr = self::findAttrParts($parts[1]);
            $attr['test'] = $parts[0];

            return $attr;
        } else {
            throw new Exception(__CLASS__ . "::splitAttrExpression error in '$str'");
        }
    }

    protected function addSpecialAttr(\DOMElement $node, $varName, array $code)
    {
        $node->setAttribute("__attr__", $varName);

        $ref = $node;
        foreach (array_reverse($code) as $line) {
            $node->parentNode->insertBefore($line, $ref);
            $ref = $line;
        }
    }

    protected static function findAttrParts($str)
    {
        $mch = array();
        if (preg_match("/^([a-z_][a-z0-9\\-_]*:[a-z][a-z0-9\\-_]*)\\s*=\\s*/i", $str, $mch)) {
            return array(
                'name' => $mch[1],
                'expr' => trim(substr($str, strlen($mch[0])))
            );
        } elseif (preg_match("/^([a-z_][a-z0-9\\-_]*)\\s*=\\s*/i", $str, $mch)) {
            return array(
                'name' => $mch[1],
                'expr' => trim(substr($str, strlen($mch[0])))
            );
        } else {
            throw new Exception(__CLASS__ . "::findAttrParts error in '$str'");
        }
    }
}
