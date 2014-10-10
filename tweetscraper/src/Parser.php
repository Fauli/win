<?php

namespace TweetScraper;

class Parser
{
    public function parse($content)
    {
        $parsed = [];

        $data = json_decode($content);

        if ($data === null) {
            throw new ParserException('invalid json');
        }

        if (!isset($data->response) || !isset($data->response->list) || !is_array($data->response->list)) {
            throw new MissingDataParserException('data format incorrect');
        }

        foreach ($data->response->list as $item) {
            $parsed[] = $item->content;
        }

        return $parsed;
    }
}

class ParserException extends \Exception {}
class JsonParserException extends ParserException {}
class MissingDataParserException extends ParserException {}