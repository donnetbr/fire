<?php
namespace Donnetbr\Fire\Extension;

use Donnetbr\Fire\EventSubscriber\CustomNamespaceSubscriber;
use Donnetbr\Fire\EventSubscriber\FixTwigExpressionSubscriber;
use Donnetbr\Fire\EventSubscriber\ReplaceDoctypeAsTwigExpressionSubscriber;
use Donnetbr\Fire\Fire;

/**
 *
 * @author Donnet do Brasil <dev@donnet.host>
 *
 */
class FullCompatibilityTwigExtension extends AbstractExtension
{
    public function getSubscribers()
    {
        return array(
            new ReplaceDoctypeAsTwigExpressionSubscriber(),
            new FixTwigExpressionSubscriber(),
            new CustomNamespaceSubscriber(array(
                'f' => Fire::NS
            )),
        );
    }
}
