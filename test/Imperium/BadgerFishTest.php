<?php
namespace Imperium;

use Assert;

class BadgerFishTest extends \PHPUnit_Framework_TestCase
{
    
    public function testXmlToJson()
    {
        $xml  = '<Billing><Model>Managed Server (Billed)</Model><ServerSize>2</ServerSize><BillSet><BillTo><Id>1</Id><Budget>14-0030</Budget><PCATask/><PCAOption/><PCAProject/><Share>100</Share></BillTo></BillSet><Items><Item><Id>4</Id><TariffVendor>Server &amp; U</TariffVendor><Units>2</Units><Monthly>true</Monthly><StartDate>2011-01-01</StartDate><EndDate>2014-12-31</EndDate></Item><Item><Id>6</Id><TariffVendor>Unix Hours</TariffVendor><Units>3</Units><Monthly>false</Monthly><StartDate>2011-02-24</StartDate><EndDate/></Item><Item><Id>1</Id><TariffVendor>Unix Hours</TariffVendor><Units>1</Units><Monthly>true</Monthly><StartDate>2011-01-01</StartDate><EndDate>2014-12-31</EndDate></Item><Item><Id>2</Id><TariffVendor>App Hours</TariffVendor><Units>1</Units><Monthly>true</Monthly><StartDate>2011-01-01</StartDate><EndDate>2014-12-31</EndDate></Item><Item><Id>5</Id><TariffVendor>RedHat OS</TariffVendor><Units>1</Units><Monthly>true</Monthly><StartDate>2011-01-01</StartDate><EndDate>2014-12-31</EndDate></Item><Item><Id>3</Id><TariffVendor>DBA Hours</TariffVendor><Units>2</Units><Monthly>true</Monthly><StartDate>2011-01-01</StartDate><EndDate>2014-12-31</EndDate></Item></Items></Billing>';
        $json = '{"Billing":{"Model":"Managed Server (Billed)","ServerSize":2,"BillSet":{"BillTo":{"Id":1,"Budget":"14-0030","PCATask":"","PCAOption":"","PCAProject":"","Share":100}},"Items":{"Item":[{"Id":4,"TariffVendor":"Server & U","Units":2,"Monthly":true,"StartDate":"2011-01-01","EndDate":"2014-12-31"},{"Id":6,"TariffVendor":"Unix Hours","Units":3,"Monthly":false,"StartDate":"2011-02-24","EndDate":""},{"Id":1,"TariffVendor":"Unix Hours","Units":1,"Monthly":true,"StartDate":"2011-01-01","EndDate":"2014-12-31"},{"Id":2,"TariffVendor":"App Hours","Units":1,"Monthly":true,"StartDate":"2011-01-01","EndDate":"2014-12-31"},{"Id":5,"TariffVendor":"RedHat OS","Units":1,"Monthly":true,"StartDate":"2011-01-01","EndDate":"2014-12-31"},{"Id":3,"TariffVendor":"DBA Hours","Units":2,"Monthly":true,"StartDate":"2011-01-01","EndDate":"2014-12-31"}]}}}';
        Assert::equals($json, BadgerFish::xmlToJson($xml));
    }

    public function testXmlToJsonWithAttr()
    {
        $xml  = '<Bob name="bob" job="builder">3</Bob>';
        $json = '{"Bob":{"@name":"bob","@job":"builder","$":3}}';
        Assert::equals($json, BadgerFish::xmlToJson($xml));
    }

    public function testObjectToJson()
    {
        $json = '{"Bob":{"@name":"bob","@job":"builder","$":3}}';
        Assert::equals($json, \json_encode(BadgerFish::badgerfy(\json_decode($json, false))));
    }

    public function testJsonToXml()
    {
        $xml = '<?xml version="1.0"?>'."\n".'<Billing><Model>Managed Server (Billed)</Model><ServerSize>2</ServerSize><BillSet><BillTo><Id>1</Id><Budget>14-0030</Budget><PCATask/><PCAOption/><PCAProject/><Share>100</Share></BillTo></BillSet><Items><Item><Id>4</Id><TariffVendor>Server &amp; U</TariffVendor><Units>2</Units><Monthly>true</Monthly><StartDate>2011-01-01</StartDate><EndDate>2014-12-31</EndDate></Item><Item><Id>6</Id><TariffVendor>Unix Hours</TariffVendor><Units>3</Units><Monthly>false</Monthly><StartDate>2011-02-24</StartDate><EndDate/></Item><Item><Id>1</Id><TariffVendor>Unix Hours</TariffVendor><Units>1</Units><Monthly>true</Monthly><StartDate>2011-01-01</StartDate><EndDate>2014-12-31</EndDate></Item><Item><Id>2</Id><TariffVendor>App Hours</TariffVendor><Units>1</Units><Monthly>true</Monthly><StartDate>2011-01-01</StartDate><EndDate>2014-12-31</EndDate></Item><Item><Id>5</Id><TariffVendor>RedHat OS</TariffVendor><Units>1</Units><Monthly>true</Monthly><StartDate>2011-01-01</StartDate><EndDate>2014-12-31</EndDate></Item><Item><Id>3</Id><TariffVendor>DBA Hours</TariffVendor><Units>2</Units><Monthly>true</Monthly><StartDate>2011-01-01</StartDate><EndDate>2014-12-31</EndDate></Item></Items></Billing>'."\n";
        $json = '{"Billing":{"Model":"Managed Server (Billed)","ServerSize":2,"BillSet":{"BillTo":{"Id":1,"Budget":"14-0030","PCATask":"","PCAOption":"","PCAProject":"","Share":100}},"Items":{"Item":[{"Id":4,"TariffVendor":"Server & U","Units":2,"Monthly":true,"StartDate":"2011-01-01","EndDate":"2014-12-31"},{"Id":6,"TariffVendor":"Unix Hours","Units":3,"Monthly":false,"StartDate":"2011-02-24","EndDate":""},{"Id":1,"TariffVendor":"Unix Hours","Units":1,"Monthly":true,"StartDate":"2011-01-01","EndDate":"2014-12-31"},{"Id":2,"TariffVendor":"App Hours","Units":1,"Monthly":true,"StartDate":"2011-01-01","EndDate":"2014-12-31"},{"Id":5,"TariffVendor":"RedHat OS","Units":1,"Monthly":true,"StartDate":"2011-01-01","EndDate":"2014-12-31"},{"Id":3,"TariffVendor":"DBA Hours","Units":2,"Monthly":true,"StartDate":"2011-01-01","EndDate":"2014-12-31"}]}}}';
        Assert::equals($xml, BadgerFish::jsonToXml($json));
    }
    
    public function testJsonToXmlBlankArray()
    {
        $json = '{"Bob":{"Ps":{"P":[]}}}';
        $xml  = '<?xml version="1.0"?>'."\n".'<Bob><Ps/></Bob>'."\n";
        Assert::equals($xml, BadgerFish::jsonToXml($json));
        
    }

    public function testJsonToXmlWithAttr()
    {
        $xml  = '<?xml version="1.0"?>'."\n".'<People><Bob name="bob" job="builder">3</Bob></People>'."\n";
        $json = '{"People":{"Bob":{"@name":"bob","@job":"builder","$":3}}}';
        Assert::equals($xml, BadgerFish::jsonToXml($json));
        $xml  = '<?xml version="1.0"?>'."\n".'<Bob name="bob" job="builder">3</Bob>'."\n";
        $json = '{"Bob":{"@name":"bob","@job":"builder","$":3}}';
        Assert::equals($xml, BadgerFish::jsonToXml($json));
    }

    public function testGetValue()
    {
        $xml0   = '<Billing><Model>Managed Server (Billed)</Model><ServerSize>2</ServerSize><BillSet><BillTo><Id>1</Id><Budget>14-0030</Budget><PCATask/><PCAOption/><PCAProject/><Share>100</Share></BillTo></BillSet><Items><Item><Id>4</Id><TariffVendor>Server U</TariffVendor><Units>2</Units><Monthly>true</Monthly><StartDate>2011-01-01</StartDate><EndDate>2014-12-31</EndDate></Item><Item><Id>6</Id><TariffVendor>Unix Hours</TariffVendor><Units>3</Units><Monthly>false</Monthly><StartDate>2011-02-24</StartDate><EndDate/></Item><Item><Id>1</Id><TariffVendor>Unix Hours</TariffVendor><Units>1</Units><Monthly>true</Monthly><StartDate>2011-01-01</StartDate><EndDate>2014-12-31</EndDate></Item><Item><Id>2</Id><TariffVendor>App Hours</TariffVendor><Units>1</Units><Monthly>true</Monthly><StartDate>2011-01-01</StartDate><EndDate>2014-12-31</EndDate></Item><Item><Id>5</Id><TariffVendor>RedHat OS</TariffVendor><Units>1</Units><Monthly>true</Monthly><StartDate>2011-01-01</StartDate><EndDate>2014-12-31</EndDate></Item><Item><Id>3</Id><TariffVendor>DBA Hours</TariffVendor><Units>2</Units><Monthly>true</Monthly><StartDate>2011-01-01</StartDate><EndDate>2014-12-31</EndDate></Item></Items></Billing>';
        $xml1   = '<?xml version="1.0"?>'."\n".'<People><Bob name="bob" job="builder">3</Bob></People>'."\n";
        $bfish0 = BadgerFish::xmlToPhp($xml0);
        $bfish1 = BadgerFish::xmlToPhp($xml1);

        Assert::equals(2, BadgerFish::getValue($bfish0, 'Billing.ServerSize'));
        Assert::equals(3, BadgerFish::getValue($bfish1, 'People.Bob'));
        Assert::equals(array(), BadgerFish::getArray($bfish0, 'Billing.Fakes.Fake'));
        
        $billto = array(array('Id'=>1,'Budget'=>'14-0030','PCATask'=>'','PCAOption'=>'','PCAProject'=>'','Share'=>100));
        Assert::equals($billto, BadgerFish::getArray($bfish0, 'Billing.BillSet.BillTo'));

        Assert::equals(6, count(BadgerFish::getArray($bfish0, 'Billing.Items.Item')));
    }

}