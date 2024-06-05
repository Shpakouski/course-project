<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class JiraService
{
    private $client;
    private $baseUrl;
    private $email;
    private $apiToken;
    private $projectKey;

    public function __construct(HttpClientInterface $client, string $baseUrl, string $email, string $apiToken, string $projectKey)
    {
        $this->client = $client;
        $this->baseUrl = $baseUrl;
        $this->email = $email;
        $this->apiToken = $apiToken;
        $this->projectKey = $projectKey;
    }

    public function createTicket($summary, $priority, $reporterEmail, $collection = null, $link = null)
    {
        $response = $this->client->request('POST', $this->baseUrl . '/rest/api/2/issue', [
            'auth_basic' => [$this->email, $this->apiToken],
            'json' => [
                'fields' => [
                    'project' => ['key' => $this->projectKey],
                    'summary' => $summary,
                    'priority' => ['name' => $priority],
                    'reporter' => ['emailAddress' => $reporterEmail],
                    'customfield_10010' => $collection, // Assuming customfield_10010 is for collection
                    'customfield_10011' => $link, // Assuming customfield_10011 is for link
                ],
            ],
        ]);

        return $response->toArray();
    }
}