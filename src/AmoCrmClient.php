<?php
namespace App;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

Class AmoCrmClient{
 
    private Client $httpClient;
    private string $domain;
    private string $token;

    public function __construct(){
        $this->domain = $_ENV['AMOCRM_DOMAIN'];
        $this->token = $_ENV['AMOCRM_ACCESS_TOKEN'];
        $this->httpClient = new Client([
            'base_uri' => "https://{$this->domain}/api/v4/",
            'headers' => [
                'Authorization' => "Bearer {$this->token}",
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]
        ]);
    }

    public function createContact(string $name, string $email, string $phone) : int
    {
        $phoneFieldId = (int)$_ENV['AMOCRM_PHONE_FIELD_ID'];
        $emailFieldId = (int)$_ENV['AMOCRM_EMAIL_FIELD_ID'];
        try {
            $response = $this->httpClient->post('contacts',[
                'json' => [
                    [
                        'name' => $name,
                        'custom_fields_values' => [
                            [
                                'field_id' => $phoneFieldId,
                                'values' => [['value' => $phone]]
                            ],
                            [
                                'field_id' => $emailFieldId,
                                'values' => [['value' => $email]]
                            ]
                        ]
                    ]
                ]
            ]);

            $data = json_decode($response->getBody(), true);

            return $data['_embedded']['contacts'][0]['id'];
        } catch (RequestException $e){
            error_log('AmoCRM API Error: '. $e->getMessage());

            $response = $e->getResponse();
            if ($response !== null) {
                $body = (string)$response->getBody();
                error_log('AmoCRM contact response: ' . $body);
            }
            throw new \Exception('Failed to create contact');
        }
    }

    public function createLead(int $contactId , float $price, int $timeOnSite) : int
    {
    $timeFieldId = (int)$_ENV['AMOCRM_TIME_FIELD_ID'];

        try {
            $response = $this->httpClient->post('leads',[
                'json' => [
                    [
                        'price' => $price,
                        'custom_fields_values' => [
                            [
                                'field_id' => $timeFieldId,
                                'values' => [['value' => $timeOnSite]],
                            ],
                        ],
                        '_embedded' => [
                            'contacts' => [
                                [
                                    'id' => $contactId,
                                ]
                            ]
                        ]
                    ]
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            
            return $data['_embedded']['leads'][0]['id'];
        } catch (RequestException $e) {
            error_log('AmoCRM API error: ' . $e->getMessage());

            $response = $e->getResponse();
            if ($response !== null) {
                $body = (string)$response->getBody();
                error_log('AmoCRM lead response: ' . $body);
            }

            throw new \Exception('Failed to create lead: ' . $e->getMessage());
        }
    }
}