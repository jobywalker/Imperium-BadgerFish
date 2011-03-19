# \Imperium\BadgerFish
### v1.1.0

A PHP Library to transform XML into JSON and JSON into XML following the [BadgerFish](http://ajaxian.com/archives/badgerfish-translating-xml-to-json) convention.

Usage:
    <?php
    use \Imperium\BadgerFish;
    
    $xml = '<Root><Children><Child><Order>1</Order><Name>John Doe</Name><Awesome>false</Awesome></Child>'
          .'<Child><Order>2</Order><Name>Jane Doe</Name><Awesome>true</Awesome></Child></Children></Root>';
    $json = BadgerFish::xmlToJson($xml);
    echo $json; # '{"Root":{"Children":{"Child":[{"Order":1,"Name":"John Doe","Awesome":false},{"Order":2,"Name":"Jane Doe","Awesome":true}]}}}'

    echo BadgerFish::jsonToXML($json);
