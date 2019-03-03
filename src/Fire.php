<?php
namespace Donnetbr\Fire;

use Donnetbr\Fire\EventDispatcher\CompilerEvents;
use Donnetbr\Fire\EventDispatcher\SourceEvent;
use Donnetbr\Fire\EventDispatcher\TemplateEvent;
use Donnetbr\Fire\Extension\CoreExtension;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 *
 * @author Donnet do Brasil <dev@donnet.host>
 *
 */
class Fire
{
    const NS = 'urn:goetas:twital';

    protected $extensionsInitialized = false;

    /**
     *
     * @var EventDispatcher
     */
    protected $dispatcher;

    /**
     *
     * @var array
     */
    private $attributes = array();

    /**
     *
     * @var array
     */
    private $nodes = array();

    /**
     *
     * @var array
     */
    private $extensions = array();

    public function __construct(array $options = array())
    {
        $this->options = $options;
        $this->dispatcher = new EventDispatcher();

        $this->addExtension(new CoreExtension());
    }

    /**
     *
     * @return \Symfony\Component\EventDispatcher\EventDispatcher
     */
    public function getEventDispatcher()
    {
        $this->initExtensions();

        return $this->dispatcher;
    }

    public function getNodes()
    {
        $this->initExtensions();

        return $this->nodes;
    }

    public function getAttributes()
    {
        $this->initExtensions();

        return $this->attributes;
    }

    /**
     *
     * @param SourceAdapter $adapter
     * @param string $source
     * @return string
     */
    public function compile(SourceAdapter $adapter, $source)
    {
        $this->initExtensions();

        $sourceEvent = new SourceEvent($this, $source);
        $this->dispatcher->dispatch(CompilerEvents::PRE_LOAD, $sourceEvent);
        $template = $adapter->load($sourceEvent->getTemplate());

        $templateEvent = new TemplateEvent($this, $template);
        $this->dispatcher->dispatch(CompilerEvents::POST_LOAD, $templateEvent);

        $compiler = new Compiler($this, isset($this->options['lexer']) ? $this->options['lexer'] : array());
        $compiler->compile($templateEvent->getTemplate()->getDocument());

        $templateEvent = new TemplateEvent($this, $templateEvent->getTemplate());
        $this->dispatcher->dispatch(CompilerEvents::PRE_DUMP, $templateEvent);
        $source = $adapter->dump($templateEvent->getTemplate());

        $sourceEvent = new SourceEvent($this, $source);
        $this->dispatcher->dispatch(CompilerEvents::POST_DUMP, $sourceEvent);

        return $sourceEvent->getTemplate();
    }

    public function addExtension(Extension $extension)
    {
        $this->extensionsInitialized = false;

        return $this->extensions[] = $extension;
    }

    public function setExtensions(array $extensions)
    {
        $this->extensionsInitialized = false;

        $this->extensions = $extensions;
    }

    /**
     * @return Extension[]
     */
    public function getExtensions()
    {
        return $this->extensions;
    }

    protected function initExtensions()
    {
        if (!$this->extensionsInitialized) {
            foreach ($this->getExtensions() as $extension) {
                $this->attributes = array_merge_recursive($this->attributes, $extension->getAttributes());
                $this->nodes = array_merge_recursive($this->nodes, $extension->getNodes());

                foreach ($extension->getSubscribers() as $subscriber) {
                    $this->dispatcher->addSubscriber($subscriber);
                }
            }
            $this->extensionsInitialized = true;
        }
    }
}
