<?php
/**
 * KK-Framework
 * Author: kookxiang <r18@ikk.me>
 */

namespace Helper;

use ReflectionFunctionAbstract;

class Reflection
{
    public static function parseDocComment(ReflectionFunctionAbstract $reflectionFunction)
    {
        $markers = array();
        /**
         * Ignore standard doc comment
         * @link https://phpdoc.org/docs/latest/references/phpdoc/basic-syntax.html
         */
        if ($reflectionFunction instanceof \ReflectionMethod) {
            $commentBlackList = array('param', 'return', 'throws', 'author', 'version', 'copyright');
        } elseif ($reflectionFunction instanceof \ReflectionClass) {
            $commentBlackList = array('category', 'package', 'subpackage', 'author', 'version', 'copyright');
        } elseif ($reflectionFunction instanceof \ReflectionProperty) {
            $commentBlackList = array('var', 'author', 'version', 'copyright');
        } else {
            $commentBlackList = array('author', 'version', 'copyright');
        }

        // Normalize line-ending, \r\n from Windows, \n from others
        $comment = str_replace("\r\n", "\n", $reflectionFunction->getDocComment());

        // Split to lines
        $lines = explode("\n", $comment);
        foreach ($lines as $line) {
            $line = trim($line, "\t\r\n   \0\x0B* ");
            if ($line{0} != '@') {
                // Only parse comment start with '@'
                continue;
            }
            if (preg_match('/^@([A-Za-z0-9\-_]+) ?(.*)$/', $line, $matches)) {
                if (in_array($matches[1], $commentBlackList)) {
                    continue;
                }
                $markers[$matches[1]] = $matches[2] !== '' ? $matches[2] : true;
            }
        }

        return $markers;
    }
}
