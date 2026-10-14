<?php
namespace App\Helpers;

use DOMDocument;
use DOMElement;
use DOMXPath;
use HTMLPurifier;
use HTMLPurifier_Config;

class Sanitizer
{
    public static function clean($html)
    {
        if (empty($html)) return '';

        // 1. معالجة الصور المحشورة داخل كتل الأكواد
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        // تحميل النص مع ترميز UTF-8
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);
        $nestedImages = $xpath->query('//div[contains(@class, "ql-code-block-container")]//img');

        foreach ($nestedImages as $img) {
            $container = $img;
            while ($container->parentNode && ($container instanceof DOMElement) && !str_contains($container->getAttribute('class'), 'ql-code-block-container')) {
                $container = $container->parentNode;
            }

            if ($container && $container->parentNode) {
                $container->parentNode->insertBefore($img->cloneNode(true), $container->nextSibling);
                $img->parentNode->removeChild($img);
            }
        }

        // استخراج الـ HTML فقط بدون ترويسات XML
        $preparedHtml = '';
        foreach ($dom->childNodes as $node) {
            if ($node->nodeType !== XML_PI_NODE) { // استبعاد <?xml ... >
                $preparedHtml .= $dom->saveHTML($node);
            }
        }

        // 2. التنظيف الأمني عبر HTMLPurifier
        $config = HTMLPurifier_Config::createDefault();

        // الوسوم المسموحة فقط
        $config->set('HTML.Allowed', 'p[dir],div[dir|class],span[dir|class|style],b,strong,i,em,u,s,strike,a[href|target|rel],ul,ol,li[dir|data-list],br,img[src|alt|width|height|class],code,pre[class],h1,h2,h3,blockquote');

        // الفئات (Classes) المسموح بها لـ Quill
        $config->set('Attr.AllowedClasses', [
            'ql-syntax',
            'ql-code-block-container',
            'ql-code-block',
            'ql-align-center',
            'ql-align-right',
            'ql-align-left',
            'ql-align-justify',
            'ql-indent-1',
            'ql-indent-2'
        ]);

        // السماح بخصائص محددة لتنسيقات المحرر (مثل الألوان والمحاذاة)
        $config->set('CSS.AllowedProperties', 'text-align,color,background-color');

        // تعريف الخاصية data-list المخصصة لـ Quill في HTMLPurifier
        $def = $config->getHTMLDefinition(true);
        $def->addAttribute('li', 'data-list', 'Text');

        $purifier = new HTMLPurifier($config);

        return $purifier->purify($preparedHtml);
    }
}