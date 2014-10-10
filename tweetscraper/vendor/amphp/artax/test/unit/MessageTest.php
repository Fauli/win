<?php

use Artax\Message;

class MessageTest extends PHPUnit_Framework_TestCase {
    
    function testGetAndSetProtocol() {
        $msg = $this->getMockForAbstractClass('Artax\Message');
        $msg->setProtocol('1.1');
        $this->assertEquals('1.1', $msg->getProtocol());
    }
    
    function testGetAndSetBody() {
        $msg = $this->getMockForAbstractClass('Artax\Message');
        $msg->setBody('test');
        $this->assertEquals('test', $msg->getBody());
    }
    
    function testHasBody() {
        $msg = $this->getMockForAbstractClass('Artax\Message');
        
        $this->assertFalse($msg->hasBody());
        
        $msg->setBody('test');
        $this->assertTrue($msg->hasBody());
        
        $msg->setBody('0');
        $this->assertTrue($msg->hasBody());
        
        $msg->setBody('');
        $this->assertFalse($msg->hasBody());
    }
    
    function testHasHeaderIsFalseBeforeAssignment() {
        $msg = $this->getMockForAbstractClass('Artax\Message');
        $this->assertFalse($msg->hasHeader('My-Header'));
    }
    
    function testHasHeaderFieldNameIsCaseInsensitive() {
        $msg = $this->getMockForAbstractClass('Artax\Message');
        $msg->setHeader('mY-hEaDeR', 'value');
        $this->assertTrue($msg->hasHeader('MY-HEADER'));
    }
    
    /**
     * @dataProvider provideHeaderExpectations
     */
    function testHasHeaderTrueWhenSpecified($header, $value) {
        $msg = $this->getMockForAbstractClass('Artax\Message');
        $msg->setHeader($header, $value);
        $this->assertTrue($msg->hasHeader($header));
    }
    
    function provideHeaderExpectations() {
        return [
            ['My-Header', ''],
            ['My-Header', ['']],
            ['My-Header', 'test'],
            ['My-Header', ['val1', 'val2', 'val3', 'val4']],
        ];
    }
    
    /**
     * @dataProvider provideHeaderExpectations
     */
    function testGetHeaderReturnsStoredValue($header, $value) {
        $msg = $this->getMockForAbstractClass('Artax\Message');
        $msg->setHeader($header, $value);
        $expectedValue = is_array($value) ? $value : [$value];
        $this->assertEquals($expectedValue, $msg->getHeader($header));
    }
    
    function testGetHeaderFieldNameIsCaseInsensitive() {
        $msg = $this->getMockForAbstractClass('Artax\Message');
        $msg->setHeader('mY-hEaDeR', 'value');
        $this->assertEquals(['value'], $msg->getHeader('MY-HEADER'));
    }
    
    /**
     * @expectedException DomainException
     */
    function testGetHeaderThrowsExceptionOnNonexistentHeaderField() {
        $msg = $this->getMockForAbstractClass('Artax\Message');
        $msg->getHeader('Some-Nonexistent-Header');
    }
    
    function testGetAllHeadersReturnsEmptyArrayIfNoHeadersStored() {
        $msg = $this->getMockForAbstractClass('Artax\Message');
        $this->assertEquals([], $msg->getAllHeaders());
    }
    
    function testGetAllHeadersReturnsArrayOfStoredHeaders() {
        $msg = $this->getMockForAbstractClass('Artax\Message');
        
        $msg->setHeader('My-Header1', 'val');
        $msg->setHeader('My-Header2', ['val1', 'val2']);
        
        $expected = [
            'My-Header1' => ['val'],
            'My-Header2' => ['val1', 'val2'],
        ];
        
        $this->assertEquals($expected, $msg->getAllHeaders());
    }
    
    /**
     * @dataProvider provideBadHeaderValues
     * @expectedException InvalidArgumentException
     */
    function testSetHeaderThrowsExceptionOnBadValue($badValue) {
        $msg = $this->getMockForAbstractClass('Artax\Message');
        $msg->setHeader('Some-Header', $badValue);
    }
    
    function provideBadHeaderValues() {
        return [
            [new StdClass],
            [[[]]]
        ];
    }
    
    function testSetAllHeaders() {
        $msg = $this->getMockForAbstractClass('Artax\Message');
        $msg->setAllHeaders([
            'My-Header1' => 'val1',
            'My-Header2' => ['val1', 'val2']
        ]);
        
        $this->assertEquals(['val1'], $msg->getHeader('MY-HEADER1'));
        $this->assertEquals(['val1', 'val2'], $msg->getHeader('MY-HEADER2'));
    }
    
    function testAppendHeaderAddsToExistingHeaderIfAlreadyExists() {
        $msg = $this->getMockForAbstractClass('Artax\Message');
        $msg->appendHeader('My-Header', 'val1');
        $this->assertEquals(['val1'], $msg->getHeader('my-header'));
        
        $msg->appendHeader('my-heAder', ['val2']);
        $msg->appendHeader('MY-HEADER', 'val3');
        
        $this->assertEquals(['val1', 'val2', 'val3'], $msg->getHeader('my-header'));
    }
    
    function testRemoveHeader() {
        $msg = $this->getMockForAbstractClass('Artax\Message');
        $msg->appendHeader('My-Header', ['val1', 'val2']);
        $this->assertTrue($msg->hasHeader('my-header'));
        $msg->removeHeader('MY-HEADER');
        $this->assertFalse($msg->hasHeader('my-header'));
    }
    
    function testRemoveAllHeaders() {
        $msg = $this->getMockForAbstractClass('Artax\Message');
        $msg->setHeader('My-Header', ['val1', 'val2']);
        $this->assertTrue($msg->hasHeader('my-header'));
        $msg->setHeader('My-Other-Header', ['val1', 'val2']);
        $this->assertTrue($msg->hasHeader('my-other-header'));
        
        $msg->removeAllHeaders();
        $this->assertEquals([], $msg->getAllHeaders());
    }
    
}





































