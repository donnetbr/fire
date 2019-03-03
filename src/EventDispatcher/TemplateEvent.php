<?php
namespace Donnetbr\Fire\EventDispatcher;

use Donnetbr\Fire\Template;
use Donnetbr\Fire\Fire;
use Symfony\Component\EventDispatcher\Event;

/**
 *
 * @author Donnet do Brasil <dev@donnet.host>
 *
 */
class TemplateEvent extends Event
{
    /**
     *
     * @var Fire
     */
    protected $fire;
    /**
     *
     * @var Template
     */
    protected $template;

    public function __construct(Fire $fire, Template $template)
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
     * @return \Donnetbr\Fire\Template
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param \Donnetbr\Fire\Template $template
     * @return void
     */
    public function setTemplate(Template $template)
    {
        $this->template = $template;
    }
}
