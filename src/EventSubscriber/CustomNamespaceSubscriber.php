<?php
namespace Donnetbr\Fire\EventSubscriber;

use Donnetbr\Fire\EventDispatcher\CompilerEvents;
use Donnetbr\Fire\EventDispatcher\SourceEvent;
use Donnetbr\Fire\EventDispatcher\TemplateEvent;
use Donnetbr\Fire\Helper\DOMHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 *
 * @author Donnet do Brasil <dev@donnet.host>
 *
 */
class CustomNamespaceSubscriber implements EventSubscriberInterface
{
    protected $customNamespaces = array();

    public function __construct(array $customNamespaces)
    {
        $this->customNamespaces = $customNamespaces;
    }

    public static function getSubscribedEvents()
    {
        return array(
            CompilerEvents::POST_LOAD => 'addCustomNamespace',
            CompilerEvents::POST_DUMP => 'removeCustomNamespaces',
        );
    }

    public function addCustomNamespace(TemplateEvent $event)
    {
        foreach (iterator_to_array($event->getTemplate()->getDocument()->childNodes) as $child) {
            if ($child instanceof \DOMElement) {
                DOMHelper::checkNamespaces($child, $this->customNamespaces);
            }
        }
    }

    public function removeCustomNamespaces(SourceEvent $event)
    {
        $template = $event->getTemplate();
        foreach ($this->customNamespaces as $prefix => $ns) {
            $template = preg_replace('#<(.*) xmlns:' . $prefix . '="' . $ns . '"(.*)>#mi', "<\\1\\2>", $template);
        }
        $event->setTemplate($template);
    }
}
