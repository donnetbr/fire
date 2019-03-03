<?php
namespace Donnetbr\Fire;

/**
 *
 * @author Donnet do Brasil <dev@donnet.host>
 *
 */
interface SourceAdapter
{
    /**
     * Gets the raw template source code and return a {Donnetbr\Fire\Template} instance.
     *
     * @param string $string
     * @return Template
     */
    public function load($string);

    /**
     * Gets a {Template}  instance and return the raw template source code.
     *
     * @param Template $dom
     * @return string
     */
    public function dump(Template $dom);
}
