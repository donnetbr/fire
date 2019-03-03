<?php

namespace Donnetbr\Fire\EventSubscriber;

use Donnetbr\Fire\EventDispatcher\CompilerEvents;
use Donnetbr\Fire\EventDispatcher\SourceEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 *
 * @author Donnet do Brasil <dev@donnet.host>
 *
 */
class ReplaceDoctypeAsTwigExpressionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            CompilerEvents::PRE_LOAD => array('replaceDoctype', 130),
        );
    }

    /**
     *
     * @param SourceEvent $event
     */
    public function replaceDoctype(SourceEvent $event)
    {
        $source = $event->getTemplate();

        $source = preg_replace_callback('/^<!doctype.*?>/im', function ($mch) {
            return '{{ \'' . addslashes($mch[0]) . '\' }}';
        }, $source);

        $event->setTemplate($source);
    }
}
