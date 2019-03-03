<?php
namespace Donnetbr\Fire\SourceAdapter;

use Donnetbr\Fire\SourceAdapter;
use Donnetbr\Fire\Template;
use Masterminds\HTML5;

/**
 *
 * @author Donnet do Brasil <dev@donnet.host>
 *
 */
class HTML5Adapter implements SourceAdapter
{
    private $html5;

    /**
     * {@inheritdoc}
     */
    public function load($source)
    {
        $html5 = $this->getHtml5();

        if (stripos(rtrim($source), '<!DOCTYPE html>') === 0) {
            $dom = $html5->loadHTML($source);
        } else {
            $f = $html5->loadHTMLFragment($source);
            $dom = new \DOMDocument('1.0', 'UTF-8');
            if ('' !== trim($source)) {
                $dom->appendChild($dom->importNode($f, true));
            }
        }
        return new Template($dom, $this->collectMetadata($dom, $source));
    }

    /**
     * {@inheritdoc}
     */
    public function dump(Template $template)
    {
        $metadata = $template->getMetadata();
        $dom = $template->getDocument();
        $html5 = $this->getHtml5();
        return $html5->saveHTML($metadata['fragment'] ? $dom->childNodes : $dom);
    }

    /**
     * Collect some metadata about $dom and $content
     * @param \DOMDocument $dom
     * @param string $source
     * @return mixed
     */
    protected function collectMetadata(\DOMDocument $dom, $source)
    {
        $metadata = array();

        $metadata['doctype'] = !!$dom->doctype;
        $metadata['fragment'] = stripos(rtrim($source), '<!DOCTYPE html>') !== 0;

        return $metadata;
    }

    private function getHtml5()
    {
        if (!$this->html5) {
            $this->html5 = new HTML5(array(
                "xmlNamespaces" => true
            ));
        }
        return $this->html5;
    }
}
