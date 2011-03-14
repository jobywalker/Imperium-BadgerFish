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
        } elseif (is_array($data)) {
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
}