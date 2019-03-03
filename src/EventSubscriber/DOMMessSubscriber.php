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
class DOMMessSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            CompilerEvents::POST_DUMP => array(
                array(
                    'removeCdata'
                ),
                array(
                    'fixAttributes'
                )
            )
        );
    }

    public function removeCdata(SourceEvent $event)
    {
        $event->setTemplate(str_replace(array(
            "<![CDATA[__[__",
            "__]__]]>"
        ), "", $event->getTemplate()));
    }

    public function fixAttributes(SourceEvent $event)
    {
        $event->setTemplate(preg_replace_callback('/ __attr__="(__a[0-9a-f]+)"/', function ($mch) {
            return '{% for ____ak,____av in ' . $mch[1] . ' if (____av|length>0) and not (____av|length == 1 and ____av[0] is same as(false)) %} {{____ak | raw}}{% if ____av|length > 1 or ____av[0] is not same as(true) %}="{{ ____av|join(\'\') }}"{% endif %}{% endfor %}';
        }, $event->getTemplate()));
    }
}
