<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Imperium;

class BadgerFish
{
    /**
     * Convert an XML String into JSON serialization
     * @param string $xml XML String
     * @return string JSON Serialization
     */
    public static function xmlToJson($xml)
    {
        return json_encode(self::XmlToPhp($xml));
    }

    /**
     * Convert an XML String into a PHP datastructure
     * @param string $xml XML String
     * @return mixed PHP datastructure
     */
    public static function xmlToPhp($xml)
    {
        $sxe = \simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NOERROR);
        if (!($sxe instanceof \SimpleXMLElement)) {
            throw new \Exception('XML is not parsable');
        }
        return self::SimpleXmlToPhp($sxe);
    }
    
    /**
     * Convert a SimpleXML datastructure into a JSON serialization
     * @param \SimpleXMLElement $sxe
     * @return string JSON Serialization
     */
    public static function simpleXmlToJson(\SimpleXMLElement $sxe)
    {
        return \json_encode(self::SimpleXmlToPhp($sxe));
    }

    /**
     * Convert a SimpleXML datastructure into a PHP datastructure
     * @param \SimpleXMLElement $sxe
     * @return mixed PHP datastructure
     */
    public static function simpleXmlToPhp(\SimpleXMLElement $sxe)
    {
        $name = $sxe->getName();
        $wrap = array($name=>$sxe);
        return self::badgerfy($wrap);
    }

    /**
     * Perform BadgerFish transform on the supplied parameter
     * @param mixed $data
     * @return mixed
     */
    public static function badgerfy($data)
    {
        $return = array();
        if ($data instanceof \SimpleXMLElement) {
            $attrs = $data->attributes();
            $children = $data->children();
            if (count($attrs)==0 && count($children)==0) {
                return self::badgerfy((string)$data);
            }
            if (count($attrs)) {
                foreach ($attrs as $aname => $avalue) {
                    $return["@$aname"] = self::badgerfy($avalue);
                }
            }
            if (count($children)) {
                $counter = array();
                foreach ($children as $name => $child) {
                    $counter[$name]++;
                    if ($counter[$name]==1) {
                        $return[$name] = self::badgerfy($child);
                    } elseif ($counter[$name]==2) {
                        $return[$name] = array($return[$name], self::badgerfy($child));
                    } else {
                        $return[$name][] = self::badgerfy($child);
                    }
                }
            } else {
                $return['$'] = self::badgerfy((string)$data);
            }
        } elseif (\is_object($data)) {
            foreach((array)$data as $key => $value) {
                $return[$key] = self::badgerfy($value);
            }
        } elseif (\is_array($data)) {
            foreach ($data as $key => $value) {
                $return[$key] = self::badgerfy($value);
            }
        } else {
            if (strtolower($data) == 'true') {
                return true;
            } elseif (strtolower($data) == 'false') {
                return false;
            } elseif (is_numeric($data)) {
                return floatval($data);
            } else {
                return $data;
            }
        }
        return $return;
    }

    /**
     * Convert JSON serialization into XML
     * @param string $json
     * @return string
     */
    public static function jsonToXml($json)
    {
        return self::phpToXml(json_decode($json, false));
    }

    /**
     * Convert a PHP datastructure into a Valid XML document
     * @param mixed $php
     * @return string
     */
    public static function phpToXml($php)
    {
        return self::phpToSXE($php)->asXML();
    }

    /**
     * Convert a PHP datastructure into a SimpleXMLElement structure
     * @param mixed $php
     * @return SimpleXMLElement
     */
    public static function phpToSXE($php)
    {
        if (\is_object($php)) {
            $php = (array)$php;
        }
        if (!\is_array($php) || count($php)!==1) {
            throw new \Exception('Data is not properly formatted for generating XML root');
        }
        list($root) = \array_keys($php);
        $sxe = \simplexml_load_string("<$root/>", 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NOERROR);
        if (!($sxe instanceof \SimpleXMLElement)) {
            throw new \Exception('Can not be created as XML');
        }
        self::debadger($php[$root], $sxe);
        return $sxe;
    }

    /**
     * Recursively transform PHP datastructure into a SimpleXMLElement
     * @param mixed $data
     * @param \SimpleXMLElement $sxe
     * @param mixed $field
     */
    public static function debadger($data, \SimpleXMLElement $sxe, $field = null)
    {
        if (\is_object($data) || (\is_array($data) && \array_keys($data) !== range(0,count($data)-1))) {
            $data = (array)$data;
            if ($field) {
                if(isset($data['$'])) {
                    $c = $sxe->addChild($field, $data['$']);
                } else {
                    $c = $sxe->addChild($field);
                }
            } else {
                $c = $sxe;
            }
            foreach((array)$data as $key => $value) {
                self::debadger($value, $c, $key);
            }
        } elseif ($field == null) {
            throw new \Exception('Invalid processing');
        } elseif (\is_array($data)) {
            foreach ($data as $value) {
                self::debadger($value, $sxe, $field);
            }
        } elseif ($data === true) {
            self::addString('true', $sxe, $field);
        } elseif ($data === false) {
            self::addString('false', $sxe, $field);
        } elseif ($data) {
            self::addString("$data", $sxe, $field);
        } else {
            self::addString('', $sxe, $field);
        }
    }

    /**
     * Add the string value as a child or attribute of the SimpleXMLElement
     * @param string $string
     * @param \SimpleXMLElement $sxe
     * @param string $field
     */
    public static function addString($string, \SimpleXMLElement $sxe, $field)
    {
        if (preg_match('/^@/', $field)) {
            $sxe->addAttribute(\mb_substr($field, 1), $string);
        } else {
            if ($string) {
                $sxe->addChild($field, $string);
            } else {
                $sxe->addChild($field);
            }
        }
    }
}