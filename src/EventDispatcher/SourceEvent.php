<?php
namespace Donnetbr\Fire\EventDispatcher;

use Donnetbr\Fire\Fire;
use Symfony\Component\EventDispatcher\Event;

/**
 *
 * @author Donnet do Brasil <dev@donnet.host>
 *
 */
class SourceEvent extends Event
{
    /**
     *
     * @var Fire
     */
    protected $fire;
    /**
     *
     * @var string
     */
    protected $template;

    public function __construct(Fire $fire, $template)
    {
        $this->fire = $fire;
        $this->template = $template;
    }

    /**
     * @return \Donnetbr\Fire\Fire
     */
    public function getFire()
    {
        return $this->fire;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param string $template
     * @return void
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }
}
