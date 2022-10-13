<?php

namespace Frosh\AutoKeywordLinker\Service;

use Frosh\AutoKeywordLinker\Entity\FroshKeywordsEntity;
use Shopware\Core\Content\Seo\SeoUrlPlaceholderHandler;

class KeywordContentUpdaterService
{

    public function setPlaceholders(FroshKeywordsEntity $keyword, $html)
    {
        $document = new \DOMDocument();
        $document->loadHTML($html, LIBXML_HTML_NOIMPLIED + LIBXML_HTML_NODEFDTD + LIBXML_NOERROR);
        foreach ($document->getElementsByTagName('a') as $href) {
            if ($href->getAttribute('data-seolink') && $href->parentNode !== null) {
                $linkText = $document->createTextNode($href->textContent);
                $href->parentNode->insertBefore($linkText, $href);
                $href->parentNode->removeChild($href);
            }
        }
        $html = $document->saveHTML();

        $linkHref = $keyword->getTargetType();

        if (strpos($html, $keyword->getKeyword()) !== false) {
            $link = SeoUrlPlaceholderHandler::DOMAIN_PLACEHOLDER . '/';
            switch ($linkHref['entity']) {
                case 'product':
                    $link .= 'detail/' . $linkHref['id'] . '#';
                    break;
                case 'category':
                    $link .= 'navigation/' . $linkHref['id'] . '#';
                    break;
                case 'absolute_url':
                    $link = $linkHref['url'];
                    break;
            }

            return str_replace(
                $keyword->getKeyword(),
                "<a href=\"$link\" data-seolink=\"true\">" . $keyword->getKeyword() . '</a>',
                $html
            );
        }

        return $html;
    }
}
