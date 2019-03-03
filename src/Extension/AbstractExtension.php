<?php
namespace Donnetbr\Fire\Extension;

use Donnetbr\Fire\Extension;

/**
 *
 * @author Donnet do Brasil <dev@donnet.host>
 *
 */
abstract class AbstractExtension implements Extension
{
    public function getAttributes()
    {
        return array();
    }

    public function getNodes()
    {
        return array();
    }

    public function getSubscribers()
    {
        return array();
    }
}
