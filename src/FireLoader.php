<?php
namespace Donnetbr\Fire;

use Donnetbr\Fire\SourceAdapter\HTML5Adapter;
use Donnetbr\Fire\SourceAdapter\XHTMLAdapter;
use Donnetbr\Fire\SourceAdapter\XMLAdapter;

/**
 * This is a Fire Loader.
 * Compiles a Fire template into a Twig template.
 *
 * @author Donnet do Brasil <dev@donnet.host>
 */
class FireLoader implements \Twig_LoaderInterface, \Twig_ExistsLoaderInterface, \Twig_SourceContextLoaderInterface
{
    /**
     * Array of patterns used to decide if a template is fire-compilable or not.
     * Items are strings or callbacks
     *
     * @var array
     */
    protected $sourceAdapters = array();

    /**
     * The wrapped Twig loader
     *
     * @var \Twig_LoaderInterface
     */
    protected $loader;

    /**
     * The internal Fire compiler
     *
     * @var Fire
     */
    protected $fire;

    /**
     * Creates a new Fire loader.
     * @param \Twig_LoaderInterface $loader
     * @param Fire $fire
     * @param bool $addDefaults If NULL, some standard rules will be used (`*.fire.*` and `*.fire`).
     */
    public function __construct(\Twig_LoaderInterface $loader = null, Fire $fire = null, $addDefaults = true)
    {
        $this->loader = $loader;
        $this->fire = $fire;

        if ($addDefaults === true || (is_array($addDefaults) && in_array('html', $addDefaults))) {
            $this->addSourceAdapter('/\.fire\.html$/i', new HTML5Adapter());
        }
        if ($addDefaults === true || (is_array($addDefaults) && in_array('xml', $addDefaults))) {
            $this->addSourceAdapter('/\.fire\.xml$/i', new XMLAdapter());
        }
        if ($addDefaults === true || (is_array($addDefaults) && in_array('xhtml', $addDefaults))) {
            $this->addSourceAdapter('/\.fire\.xhtml$/i', new XHTMLAdapter());
        }
    }

    public function getSourceContext($name)
    {
        if (\Twig_Environment::MAJOR_VERSION === 2 || $this->loader instanceof \Twig_SourceContextLoaderInterface) {
            $originalContext = $this->loader->getSourceContext($name);
            $code = $originalContext->getCode();
            $path = $originalContext->getPath();
        } else {
            $code = $this->loader->getSource($name);
            $path = null;
        }

        if ($adapter = $this->getSourceAdapter($name)) {
            $code = $this->getFire()->compile($adapter, $code);
        }

        return new \Twig_Source($code, $name, $path);
    }

    /**
     * Add a new pattern that can decide if a template is fire-compilable or not.
     * If $pattern is a string, then must be a valid regex that matches the template filename.
     * If $pattern is a callback, then must return true if the template is compilable, false otherwise.
     *
     * @param string|callback $pattern
     * @param SourceAdapter $adapter
     * @return FireLoader
     */
    public function addSourceAdapter($pattern, SourceAdapter $adapter)
    {
        $this->sourceAdapters[$pattern] = $adapter;

        return $this;
    }

    /**
     * Get all patterns used to choose if a template is fire-compilable or not
     *
     * @return array:
     */
    public function getSourceAdapters()
    {
        return $this->sourceAdapters;
    }

    /**
     * Decide if a template is fire-compilable or not.
     *
     * @param string $name
     * @return SourceAdapter
     */
    public function getSourceAdapter($name)
    {
        foreach (array_reverse($this->sourceAdapters) as $pattern => $adapter) {
            if (preg_match($pattern, $name)) {
                return $adapter;
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getSource($name)
    {
        return $this->getSourceContext($name)->getCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheKey($name)
    {
        return $this->loader->getCacheKey($name);
    }

    /**
     * {@inheritdoc}
     */
    public function isFresh($name, $time)
    {
        return $this->loader->isFresh($name, $time);
    }

    /**
     * {@inheritdoc}
     */
    public function exists($name)
    {
        if (\Twig_Environment::MAJOR_VERSION === 2 || $this->loader instanceof \Twig_ExistsLoaderInterface) {
            return $this->loader->exists($name);
        } else {
            try {
                $this->getSourceContext($name);

                return true;
            } catch (\Twig_Error_Loader $e) {
                return false;
            }
        }
    }

    /**
     * Get the wrapped Twig loader
     *
     * @return \Twig_LoaderInterface
     */
    public function getLoader()
    {
        return $this->loader;
    }

    /**
     * Set the wrapped Twig loader
     *
     * @param \Twig_LoaderInterface $loader
     * @return FireLoader
     */
    public function setLoader(\Twig_LoaderInterface $loader)
    {
        $this->loader = $loader;

        return $this;
    }

    /**
     * @return Fire
     */
    public function getFire()
    {
        if ($this->fire === null) {
            $this->fire = new Fire();
        }

        return $this->fire;
    }
}
