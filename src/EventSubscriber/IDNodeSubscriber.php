<?php
namespace Donnetbr\Fire\EventSubscriber;

use Donnetbr\Fire\EventDispatcher\CompilerEvents;
use Donnetbr\Fire\EventDispatcher\TemplateEvent;
use Donnetbr\Fire\Fire;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 *
 * @author Donnet do Brasil <dev@donnet.host>
 *
 */
class IDNodeSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            CompilerEvents::POST_LOAD => array(
                array(
                    'addAttribute'
                )
            ),
            CompilerEvents::PRE_DUMP => array(
                array(
                    'removeAttribute'
                )
            )
        );
    }

    public function addAttribute(TemplateEvent $event)
    {
        $doc = $event->getTemplate()->getDocument();
        $xp = new \DOMXPath($doc);
        /**
         * @var \DOMElement[] $nodes
         */
        $nodes = $xp->query("//*[@*[namespace-uri()='" . Fire::NS . "']]");
        foreach ($nodes as $node) {
            $node->setAttributeNS(Fire::NS, '__internal-id__', microtime(1) . mt_rand());
        }
    }

    public function removeAttribute(TemplateEvent $event)
    {
        $doc = $event->getTemplate()->getDocument();
        $xp = new \DOMXPath($doc);
        $xp->registerNamespace('fire', Fire::NS);
        $attributes = $xp->query("//@fire:__internal-id__");
        foreach ($attributes as $attribute) {
            $attribute->ownerElement->removeAttributeNode($attribute);
        }
    }
}
