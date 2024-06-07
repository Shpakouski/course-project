<?php

namespace App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class JiraService
{
    private Client $client;
    private string $baseUrl;
    private string $authHeader;

    public function __construct(string $baseUrl, string $apiToken, string $email)
    {
        $this->client = new Client();
        $this->baseUrl = $baseUrl;
        $this->authHeader = 'Basic ' . base64_encode($email . ':' . $apiToken);
    }

    public function createTicket(array $ticketData): array
    {
        try {
            $response = $this->client->post($this->baseUrl . '/rest/api/2/issue', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => $this->authHeader,
                ],
                'json' => $ticketData,
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            throw new \Exception($e->getResponse()->getBody()->getContents());
        }
    }

    public function createUser(string $email, string $displayName): array
    {
        try {
            $response = $this->client->post($this->baseUrl . '/rest/api/2/user', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => $this->authHeader,
                ],
                'json' => [
                    'emailAddress' => $email,
                    'displayName' => $displayName,
                    'name' => $email,
                    'notification' => false,
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            throw new \Exception($e->getResponse()->getBody()->getContents());
        }
    }

    public function userExists(string $email): bool
    {
        try {
            $response = $this->client->get($this->baseUrl . '/rest/api/2/user/search', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => $this->authHeader,
                ],
                'query' => [
                    'query' => $email,
                ],
            ]);

            $users = json_decode($response->getBody()->getContents(), true);

            return !empty($users);
        } catch (RequestException $e) {
            if ($e->getResponse()->getStatusCode() === 404) {
                return false;
            }
            throw new \Exception($e->getResponse()->getBody()->getContents());
        }
    }

    public function getTicketsByReporter(string $email): array
    {
        try {
            $accountId = $this->getAccountIdByEmail($email);
            $response = $this->client->get($this->baseUrl . '/rest/api/2/search', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => $this->authHeader,
                ],
                'query' => [
                    'jql' => 'reporter = ' . $accountId,
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true)['issues'];
        } catch (RequestException $e) {
            throw new \Exception($e->getResponse()->getBody()->getContents());
        }
    }

    public function getAccountIdByEmail(string $email)
    {
        $response = $this->client->get($this->baseUrl . '/rest/api/3/user/search?query=' . urlencode($email), [
            'headers' => [
                'Authorization' => $this->authHeader,
            ],
        ]);

        $data = json_decode($response->getBody(), true);

        if (!empty($data) && isset($data[0]['accountId'])) {
            return $data[0]['accountId'];
        }

        return null;
    }
}