<?php

namespace Jira\Api;

use Guzzle\Common\Collection;
use Jira\Common\JiraClientAuthPlugin;

class JiraApiClient extends JiraApiClientV2
{
    const BASE_URL = 'http://localhost:2990/jira';
    const BASE_PATH = '/rest/api/2';

    /**
     * {@inheritdoc}
     *
     * @return \Jira\Common\JiraClient
     */
    public static function factory($config = array())
    {
        $required = array(
            'authentication_method',
            'authentication_key',
            'authentication_value',
            'base_url',
            'base_path',
        );

        $defaults = array(
            'base_url' => self::BASE_URL,
            'base_path' => self::BASE_PATH,
        );

        if (isset($config['authentication_method']) && $config['authentication_method'] == 'token') {
            $defaults['authentication_value'] = 'api_token';
        }

        $config = Collection::fromConfig($config, $defaults, $required);
        $client = new static($config->get('base_url'), $config);
        $client->setDefaultHeaders(array(
                'Content-Type' => 'application/json; charset=utf-8',
            ));

        $plugin = new JiraClientAuthPlugin($config->get('authentication_key'), $config->get('authentication_value'));
        $client->addSubscriber($plugin);

        return $client;
    }

    public function issue($issue)
    {
        $data = $this->sendGet('{+base_path}/issue/{+issue}?fields=id,key', array('issue' => $issue));
        return $data;
    }

    public function search($jql, $options = array())
    {
        $data = $this->sendGet('{+base_path}/search?jql={+jql}&fields=id,key,summary', array('jql' => ($jql)));
        return $data;
    }

    public function getIssueCreateMeta($projectKeys = null, $issuetypeIds = null)
    {
        $path = '{+base_path}/issue/createmeta?';
        $variables = array();
        if ($projectKeys) {
            $path .= 'projectKeys={+projectKeys}&';
            $variables['projectKeys'] = $projectKeys;
        }
        if ($issuetypeIds) {
            $path .= 'issuetypeIds={+issuetypeIds}&';
            $variables['issuetypeIds'] = $issuetypeIds;
        }
        $path .= 'expand=projects.issuetypes.fields';
        $data = $this->sendGet($path, $variables);
        return $data;
    }

    public function createIssue($options = array())
    {
        $path = '{+base_path}/issue';
        $variables = array();
        $body = json_encode(array('fields' => $options));
        return $this->sendPost($path, $variables, $body);
    }

    public function createRemoteLink($issue, $options = array())
    {
        $path = '{+base_path}/issue/{+issue}/remotelink';
        $variables = array(
            'issue' => $issue,
        );
        $body = json_encode($options);
        return $this->sendPost($path, $variables, $body);
    }

}
