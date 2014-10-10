<?php

namespace Artax\Ext\Progress;

use Artax\Ext\Extension,
    Artax\ObservableSubject,
    Artax\Observable,
    Artax\ObservableClient,
    Artax\Response;

class ProgressExtension implements Extension, Observable {
    
    use ObservableSubject;
    
    const PROGRESS = 'progress';
    const RESPONSE = 'response';
    const ERROR = 'error';
    
    private $requests;
    private $progressBarSize = 40;
    private $progressBarIncrementChar = '=';
    private $progressBarEmptyIncrementChar = '.';
    private $progressBarLeadingChar = '>';
    private $observation;
    
    function __construct() {
        $this->requests = new \SplObjectStorage;
    }
    
    function setProgressBarSize($charWidth) {
        $this->progressBarSize = filter_var($charWidth, FILTER_VALIDATE_INT, ['options' => [
            'default' => 40,
            'min_range' => 10
        ]]);
        
        return $this;
    }
    
    function setProgressBarIncrementChar($char) {
        if (is_string($char) && strlen($char) === 1) {
            $this->progressBarIncrementChar = $char;
        } else {
            throw new \InvalidArgumentException(
                'Single character string required'
            );
        }
        
        return $this;
    }
    
    function setProgressBarEmptyIncrementChar($char) {
        if (is_string($char) && strlen($char) === 1) {
            $this->progressBarEmptyIncrementChar = $char;
        } else {
            throw new \InvalidArgumentException(
                'Single character string required'
            );
        }
        
        return $this;
    }
    
    function setProgressBarLeadingChar($char) {
        if (is_string($char) && strlen($char) === 1) {
            $this->progressBarLeadingChar = $char;
        } else {
            throw new \InvalidArgumentException(
                'Single character string required'
            );
        }
        
        return $this;
    }
    
    function unextend() {
        if ($this->observation) {
            $this->observation->cancel();
            $this->observation = NULL;
        }
        
        return $this;
    }
    
    function extend(ObservableClient $client) {
        $this->unextend();
        $this->observation = $client->addObservation([
            ObservableClient::REQUEST => function($dataArr) { $this->onRequest($dataArr); },
            ObservableClient::SOCKET => function($dataArr) { $this->onSocket($dataArr); },
            ObservableClient::SOCK_DATA_IN => function($dataArr) { $this->onData($dataArr); },
            ObservableClient::HEADERS => function($dataArr) { $this->onHeaders($dataArr); },
            ObservableClient::REDIRECT => function($dataArr) { $this->onRedirect($dataArr); },
            ObservableClient::RESPONSE => function($dataArr) { $this->onResponse($dataArr); },
            ObservableClient::CANCEL => function($dataArr) { $this->clear($dataArr); },
            ObservableClient::ERROR => function($dataArr) { $this->onError($dataArr); }
        ]);
        
        return $this;
    }
    
    private function onRequest(array $dataArr) {
        $request = $dataArr[0];
        $progress = new ProgressState;
        $this->requests->attach($request, $progress);
    }
    
    private function onSocket(array $dataArr) {
        $request = $dataArr[0];
        $progress = $this->requests->offsetGet($request);
        $progress->socketReadyAt = microtime(TRUE);
        $this->requests->attach($request, $progress);
    }
    
    private function onData(array $dataArr) {
        list($request, $dataRcvd) = $dataArr;
        
        $progress = $this->requests->offsetGet($request);
        
        $progress->bytesRcvd += strlen($dataRcvd);
        $elapsedTime = microtime(TRUE) - $progress->socketReadyAt;
        $progress->bytesPerSecond = round($progress->bytesRcvd / $elapsedTime);
        
        if (isset($progress->headerBytes, $progress->contentLength)) {
            $part = $progress->bytesRcvd;
            $whole = $progress->headerBytes + $progress->contentLength;
            $percentComplete = $part/$whole;
            $progress->percentComplete = $percentComplete;
            $progress->progressBar = $this->generateProgressBar($percentComplete);
        } else {
            $progress->progressBar = $this->generateProgressBarOfUnknownSize();
        }
        
        $this->notifyObservations(self::PROGRESS, [$request, clone $progress]);
    }
    
    private function generateProgressBar($percentComplete) {
        $maxIncrements = $this->progressBarSize - 3;
        $displayIncrements = round($percentComplete * $maxIncrements);
        $emptyIncrements = $maxIncrements - $displayIncrements;
        
        $bar = '[';
        $bar.= str_repeat($this->progressBarIncrementChar, $displayIncrements);
        $bar.= $this->progressBarLeadingChar;
        $bar.= str_repeat($this->progressBarEmptyIncrementChar, $emptyIncrements);
        $bar.= ']';
        
        return $bar;
    }
    
    private function generateProgressBarOfUnknownSize() {
        $maxIncrements = $this->progressBarSize - 2;
        $msg = 'SIZE UNKNOWN (chunks)';
        $emptyIncrements = $maxIncrements - strlen($msg);
        if (!$emptyIncrements%2) {
            $leftEmpty = $rightEmpty = $emptyIncrements / 2;
        } else {
            $leftEmpty = floor($emptyIncrements / 2);
            $rightEmpty = $leftEmpty + 1;
        }
        
        $bar = '[';
        $bar.= str_repeat($this->progressBarEmptyIncrementChar, $leftEmpty);
        $bar.= $msg;
        $bar.= str_repeat($this->progressBarEmptyIncrementChar, $rightEmpty);
        $bar.= ']';
        
        return $bar;
    }
    
    private function onHeaders(array $dataArr) {
        list($request, $parsedResponseArr) = $dataArr;
        
        $progress = $this->requests->offsetGet($request);
        
        $response = (new Response)->setAllHeaders($parsedResponseArr['headers']);
        
        if ($response->hasHeader('Content-Length')) {
             $progress->contentLength = (int) current($response->getHeader('Content-Length'));
             $progress->headerBytes = strlen($parsedResponseArr['trace']);
        }
    }
    
    private function onRedirect(array $dataArr) {
        $request = $dataArr[0];
        $progress = $this->requests->offsetGet($request);
        $redirectCount = $progress->redirectCount + 1;
        
        $progress = new ProgressState;
        $progress->redirectCount = $redirectCount;
        $this->requests->attach($request, $progress);
    }
    
    private function onResponse(array $dataArr) {
        $request = $dataArr[0];
        $progress = $this->requests->offsetGet($request);
        $progress->percentComplete = 1.0;
        $progress->progressBar = $this->generateProgressBar(1.0);
        $this->requests->detach($request);
        
        $this->notifyObservations(self::RESPONSE, [$request, $progress]);
    }
    
    private function onError(array $dataArr) {
        $request = $dataArr[0];
        $error = $dataArr[2];
        $progress = $this->requests->offsetGet($request);
        
        $this->notifyObservations(self::ERROR, [$request, $progress, $error]);
    }
    
    private function clear(array $dataArr) {
        $request = $dataArr[0];
        $this->requests->detach($request);
    }
    
}

