<?php
namespace Donnetbr\Fire\Extension;

use Donnetbr\Fire\Attribute;
use Donnetbr\Fire\EventSubscriber\ContextAwareEscapingSubscriber;
use Donnetbr\Fire\EventSubscriber\CustomNamespaceRawSubscriber;
use Donnetbr\Fire\EventSubscriber\DOMMessSubscriber;
use Donnetbr\Fire\EventSubscriber\FixHtmlEntitiesInExpressionSubscriber;
use Donnetbr\Fire\EventSubscriber\IDNodeSubscriber;
use Donnetbr\Fire\Node;
use Donnetbr\Fire\Fire;

/**
 *
 * @author Donnet do Brasil <dev@donnet.host>
 *
 */
class CoreExtension extends AbstractExtension
{
    public function getSubscribers()
    {
        return array(
            new DOMMessSubscriber(),
            new CustomNamespaceRawSubscriber(array(
                'f' => Fire::NS
            )),
            new FixHtmlEntitiesInExpressionSubscriber(),
            new ContextAwareEscapingSubscriber(),
            new IDNodeSubscriber()
        );
    }

    public function getAttributes()
    {
        $attributes = array();
        $attributes[Fire::NS]['__base__'] = new Attribute\BaseAttribute();
        $attributes[Fire::NS]['__internal-id__'] = new Attribute\InternalIDAttribute();

        $attributes[Fire::NS]['if'] = new Attribute\IfAttribute();
        $attributes[Fire::NS]['elseif'] = new Attribute\ElseIfAttribute();
        $attributes[Fire::NS]['else'] = new Attribute\ElseAttribute();

        $attributes[Fire::NS]['omit'] = new Attribute\OmitAttribute();
        $attributes[Fire::NS]['set'] = new Attribute\SetAttribute();

        $attributes[Fire::NS]['content'] = new Attribute\ContentAttribute();
        $attributes[Fire::NS]['capture'] = new Attribute\CaptureAttribute();
        $attributes[Fire::NS]['replace'] = new Attribute\ReplaceAttribute();

        $attributes[Fire::NS]['attr'] = new Attribute\AttrAttribute();
        $attributes[Fire::NS]['attr-append'] = new Attribute\AttrAppendAttribute();

        $attributes[Fire::NS]['extends'] = new Attribute\ExtendsAttribute();

        $attributes[Fire::NS]['block'] = new Attribute\BlockInnerAttribute();
        $attributes[Fire::NS]['block-inner'] = new Attribute\BlockInnerAttribute();
        $attributes[Fire::NS]['block-outer'] = new Attribute\BlockOuterAttribute();

        return $attributes;
    }

    public function getNodes()
    {
        $nodes = array();
        $nodes[Fire::NS]['extends'] = new Node\ExtendsNode();
        $nodes[Fire::NS]['block'] = new Node\BlockNode();
        $nodes[Fire::NS]['macro'] = new Node\MacroNode();
        $nodes[Fire::NS]['import'] = new Node\ImportNode();
        $nodes[Fire::NS]['include'] = new Node\IncludeNode();
        $nodes[Fire::NS]['omit'] = new Node\OmitNode();
        $nodes[Fire::NS]['embed'] = new Node\EmbedNode();
        $nodes[Fire::NS]['use'] = new Node\UseNode();

        return $nodes;
    }
}
