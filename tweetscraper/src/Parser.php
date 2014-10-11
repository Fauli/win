<?php

namespace TweetScraper;

class Parser
{
    public function parse(array $pages)
    {
        $out = [];
        foreach ($pages as $page) {
            $out = array_merge($out, $this->parsePage($page));
        }
        return $out;
    }

    public function parsePage($content)
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
            $parsed[] = [
                'timestamp' => $item->trackback_date,
                'value' => $item->content,
            ];
        }

        return $parsed;
    }
}

class ParserException extends \Exception {}
class JsonParserException extends ParserException {}
class MissingDataParserException extends ParserException {}