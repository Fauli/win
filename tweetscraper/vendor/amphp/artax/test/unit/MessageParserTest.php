<?php

use Artax\MessageParser;

class MessageParserTest extends PHPUnit_Framework_TestCase {
    
    /**
     * @dataProvider provideParseExpectations
     */
    function testParse($msg, $method, $uri, $protocol, $headers, $body) {
        $msgParser = new MessageParser;
        $parsedRequestArr = $msgParser->parse($msg);
        
        $actualBody = $parsedRequestArr['body']
            ? stream_get_contents($parsedRequestArr['body'])
            : $parsedRequestArr['body'];
        
        $this->assertEquals($method, $parsedRequestArr['method']);
        $this->assertEquals($uri, $parsedRequestArr['uri']);
        $this->assertEquals($protocol, $parsedRequestArr['protocol']);
        $this->assertEquals($headers, $parsedRequestArr['headers']);
        $this->assertEquals($body, $actualBody);
    }
    
    function testKeepAliveHeadResponseParse() {
        $request = "HTTP/1.1 200 OK\n\n";
        $msgParser = new MessageParser(MessageParser::MODE_RESPONSE);
        $msgParser->enqueueResponseMethodMatch('HEAD');
        $parsedResponseArr = $msgParser->parse($request);
        
        $this->assertEquals(200, $parsedResponseArr['status']);
    }
    
    /**
     * @dataProvider provideParseExpectations
     */
    function testIncrementalParse($msg, $method, $uri, $protocol, $headers, $body) {
        $msgParser = new MessageParser;
        
        $byteIncrement = 1;
        $msgLen = strlen($msg);
        for ($i=0; $i < $msgLen; $i+=$byteIncrement) {
            $msgPart = $msg[$i];
            $parsedRequestArr = $msgParser->parse($msgPart);
            if (NULL !== $parsedRequestArr) {
                break;
            }
        }
        
        $actualBody = $parsedRequestArr['body']
            ? stream_get_contents($parsedRequestArr['body'])
            : $parsedRequestArr['body'];
        
        $this->assertEquals($method, $parsedRequestArr['method']);
        $this->assertEquals($uri, $parsedRequestArr['uri']);
        $this->assertEquals($protocol, $parsedRequestArr['protocol']);
        $this->assertEquals($headers, $parsedRequestArr['headers']);
        $this->assertEquals($body, $actualBody);
    }
    
    function provideParseExpectations() {
        $return = [];
        
        // 0 -------------------------------------------------------------------------------------->
        $msg = "" .
            "GET / HTTP/1.1" . "\r\n" . 
            "Host: localhost:80" . "\r\n" . 
            "\r\n"
        ;
        
        $method = 'GET';
        $uri = '/';
        $protocol = '1.1';
        $headers = ['Host' => ['localhost:80']];
        $body = NULL;
        
        $return[] = [$msg, $method, $uri, $protocol, $headers, $body];
        
        // 1 -------------------------------------------------------------------------------------->
        $msg = "" .
            "POST /post-endpoint HTTP/1.0" . "\r\n" . 
            "Host: localhost" . "\r\n" . 
            "Cookie: cookie1" . "\r\n" . 
            "Cookie: cookie2" . "\r\n" . 
            "Content-Length: 3" . "\r\n" .
            "\r\n" .
            "123"
        ;
        
        $method = 'POST';
        $uri = '/post-endpoint';
        $protocol = '1.0';
        $headers = [
            'Host' => ['localhost'],
            'Cookie' => ['cookie1', 'cookie2'],
            'Content-Length' => [3]
        ];
        $body = '123';
        
        $return[] = [$msg, $method, $uri, $protocol, $headers, $body];
        
        // 2 -------------------------------------------------------------------------------------->
        $msg = "" .
            "OPTIONS * HTTP/1.0" . "\r\n" . 
            "\r\n"
        ;
        
        $method = 'OPTIONS';
        $uri = '*';
        $protocol = '1.0';
        $headers = [];
        $body = NULL;
        
        $return[] = [$msg, $method, $uri, $protocol, $headers, $body];
        
        // 3 -------------------------------------------------------------------------------------->
        
        $msg = "" .
            "GET /test HTTP/1.1\r\n" .
            "Host: localhost\r\n" .
            "Transfer-Encoding: chunked\r\n" .
            "\r\n" .
            "00000\r\n\r\n"
        ;
        
        $method = 'GET';
        $uri = '/test';
        $protocol = '1.1';
        $headers = [
            'Host' => ['localhost'],
            'Transfer-Encoding' => ['chunked']
        ];
        $body = NULL;
        
        $return[] = [$msg, $method, $uri, $protocol, $headers, $body];
        
        // 4 -------------------------------------------------------------------------------------->
        
        $len = 1992;
        $body = str_repeat('x', $len);
        
        $msg = '' .
            "GET /test HTTP/1.1\r\n" .
            "Host: localhost\r\n" .
            "Connection: keep-alive\r\n" .
            "User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.97 Safari/537.11\r\n" .
            "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\n" .
            "Accept-Encoding: gzip,deflate,sdch\r\n" .
            "Accept-Language: en-US,en;q=0.8\r\n" .
            "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.3\r\n" .
            "Content-Length: {$len}\r\n" .
            "\r\n" .
            "{$body}"
        ;
        
        $method = 'GET';
        $uri = '/test';
        $protocol = '1.1';
        $headers = [
            'Host' => ['localhost'],
            'Connection' => ['keep-alive'],
            'User-Agent' => ['Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.97 Safari/537.11'],
            'Accept' => ['text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'],
            'Accept-Encoding' => ['gzip,deflate,sdch'],
            'Accept-Language' => ['en-US,en;q=0.8'],
            'Accept-Charset' => ['ISO-8859-1,utf-8;q=0.7,*;q=0.3'],
            'Content-Length' => [$len]
        ];
        
        $return[] = [$msg, $method, $uri, $protocol, $headers, $body];
        
        // 5 -------------------------------------------------------------------------------------->
        $msg = "" .
            "GET /test HTTP/1.1\r\n" .
            "Host: localhost\r\n" .
            "Header-Lws-Split: line1\r\n" .
            "\t\x20line2\r\n" .
            "\x20\x20line3\n" .
            "\x20line4\r\n" .
            "\tline5\r\n" .
            "\r\n"
        ;
        
        $method = 'GET';
        $uri = '/test';
        $protocol = '1.1';
        $headers = [
            'Host' => ['localhost'],
            'Header-Lws-Split' => ['line1 line2 line3 line4 line5']
        ];
        $body = NULL;
        
        $return[] = [$msg, $method, $uri, $protocol, $headers, $body];
        
        // 6 -------------------------------------------------------------------------------------->
        $msg = "" .
            "GET /test HTTP/1.1\r\n" .
            "Host: localhost\r\n" .
            "Transfer-Encoding: chunked\r\n" .
            "\r\n" .
            "5\r\n" .
            "woot!\r\n" .
            "4\r\n" .
            "test\r\n" .
            "0\r\n" .
            "My-Trailer: testval\r\n" .
            "\r\n"
        ;
        
        $method = 'GET';
        $uri = '/test';
        $protocol = '1.1';
        $headers = [
            'Host' => ['localhost'],
            'Transfer-Encoding' => ['chunked'],
            'My-Trailer' => ['testval']
        ];
        $body = 'woot!test';
        
        $return[] = [$msg, $method, $uri, $protocol, $headers, $body];
        
        // x -------------------------------------------------------------------------------------->
        
        return $return;
    }
    
}

