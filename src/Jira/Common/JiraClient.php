<?php

namespace Jira\Common;

use Guzzle\Http\Exception\BadResponseException;
use Guzzle\Service\Client;

class JiraClient extends Client
{
    /**
     * Helper method to include HTTP response body in exception message.
     */
    protected function addBodyToBadResponseException(BadResponseException $badResponseException)
    {
        $request = $badResponseException->getRequest();
        $response = $badResponseException->getResponse();

        $jsonOptions = defined('JSON_PRETTY_PRINT') ? JSON_PRETTY_PRINT : 0;

        // For POST requests include the body
        $requestBody = '';
        if ($request->getMethod() == 'POST') {
            $requestBody = json_encode(json_decode($request->getBody(true)), $jsonOptions);
        }

        // Create an error message with body data included
        $message = implode(
            PHP_EOL,
            array (
                $badResponseException->getMessage(),
                '[response body] ' . json_encode(json_decode($response->getBody(true)), $jsonOptions),
                '[request body] ' . $requestBody,
            )
        );

        // Rethrow the exception
        $exceptionClass = get_class($badResponseException);
        $badResponseExceptionWithBody = new $exceptionClass($message);
        $badResponseExceptionWithBody->setResponse($response);
        $badResponseExceptionWithBody->setRequest($request);

        return $badResponseExceptionWithBody;
    }

    /**
     * Helper method to send a GET request and return parsed JSON.
     *
     * @param string $path
     * @param array $variables
     *   Variables used to expand the URI expressions.
     *
     * @return array
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     *
     * @see http://docs.guzzlephp.org/en/latest/http-client/uri-templates.html
     */
    public function sendGet($path, $variables = array())
    {
        $response = null;
        try {
            $response = $this->get(array($path, $variables))->send();
        } catch (BadResponseException $exception) {
            throw $this->addBodyToBadResponseException($exception);
        }

        return $response->json();
    }

    /**
     * Helper method to send a PUT request and return parsed JSON.
     *
     * @param string $path
     * @param array $variables
     *   Variables used to expand the URI expressions.
     * @param string $body
     *
     * @return array
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     *
     * @see http://docs.guzzlephp.org/en/latest/http-client/uri-templates.html
     */
    public function sendPut($path, $variables = array(), $body)
    {
        $response = null;
        try {
            $response = $this->put(array($path, $variables), null, $body)->send();
        } catch (BadResponseException $exception) {
            throw $this->addBodyToBadResponseException($exception);
        }

        return $response->json();
    }

    /**
     * Helper method to send a POST request and return parsed JSON.
     *
     * @param string $path
     * @param array $variables
     *   Variables used to expand the URI expressions.
     * @param string $body
     *
     * @return array
     *
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     *
     * @see http://docs.guzzlephp.org/en/latest/http-client/uri-templates.html
     */
    public function sendPost($path, $variables = array(), $body)
    {
        $response = null;
        try {
            $response = $this->post(array($path, $variables), null, $body)->send();
        } catch (BadResponseException $exception) {
            throw $this->addBodyToBadResponseException($exception);
        }

        return $response->json();
    }
}
