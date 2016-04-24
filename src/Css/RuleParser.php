<?php 

namespace Spipu\Html2Pdf\Css;

/**
 * Class RuleParser
 */
class RuleParser
{
    public function parse(SelectorProvider $selectorProvider, $text)
    {
        $partial = $text;

        $selectors = array();
        $previous = null;
        while (strlen($partial)) {
            foreach ($selectorProvider->getParsers() as $parser) {
                if ($selector = $parser->match($partial)) {
                    $selector->setPrevious($previous);
                    $previous = $selector;

                    $selectors[] = $selector;
                    $partial = substr($partial, strlen($selector->getText()));
                    continue (2);
                }
            }
            throw new \Exception('Unsupported selector');
        }
        return $selectors;
    }
}
