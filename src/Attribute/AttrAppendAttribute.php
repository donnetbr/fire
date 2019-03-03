<?php
namespace Donnetbr\Fire\Attribute;

use Donnetbr\Fire\Compiler;
use Donnetbr\Fire\Helper\ParserHelper;

/**
 *
 * @author Donnet do Brasil <dev@donnet.host>
 *
 */
class AttrAppendAttribute extends AttrAttribute
{
    public function visit(\DOMAttr $att, Compiler $context)
    {
        $node = $att->ownerElement;
        $expressions = ParserHelper::staticSplitExpression($att->value, ",");

        $attributes = array();
        foreach ($expressions as $k => $expression) {
            $expressions[$k] = $attrExpr = self::splitAttrExpression($expression);
            $attNode = null;
            if (!isset($attributes[$attrExpr['name']])) {
                $attributes[$attrExpr['name']] = array();
            }
            if ($node->hasAttribute($attrExpr['name'])) {
                $attNode = $node->getAttributeNode($attrExpr['name']);
                $node->removeAttributeNode($attNode);
                $attributes[$attrExpr['name']][] = "'" . addcslashes($attNode->value, "'") . "'";
            }
            if ($attrExpr['test'] === "true" || $attrExpr['test'] === "1") {
                unset($expressions[$k]);
                $attributes[$attrExpr['name']][] = $attrExpr['expr'];
            }
        }

        $code = array();

        $varName = self::getVarname($node);
        $code[] = $context->createControlNode("if $varName is not defined");
        $code[] = $context->createControlNode("set $varName = {" . ParserHelper::implodeKeyedDouble(",", $attributes, true) . " }");
        $code[] = $context->createControlNode("else");

        foreach ($attributes as $attribute => $values) {
            $code[] = $context->createControlNode("if {$varName}['{$attribute}'] is defined");
            $code[] = $context->createControlNode("set $varName = $varName|merge({ '$attribute' : ({$varName}['{$attribute}']|merge([" . implode(",", $values) . "])) })");
            $code[] = $context->createControlNode("else");
            $code[] = $context->createControlNode("set $varName = $varName|merge({ '$attribute' : [" . implode(",", $values) . "]})");
            $code[] = $context->createControlNode("endif");
        }

        $code[] = $context->createControlNode("endif");

        foreach ($expressions as $attrExpr) {
            $code[] = $context->createControlNode("if {$attrExpr['test']}");
            $code[] = $context->createControlNode(
                "set {$varName} = {$varName}|merge({ '{$attrExpr['name']}':{$varName}.{$attrExpr['name']}|merge([{$attrExpr['expr']}]) })"
            );
            $code[] = $context->createControlNode("endif");
        }

        $this->addSpecialAttr($node, $varName, $code);
        $node->removeAttributeNode($att);
    }
}
