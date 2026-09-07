<?php

namespace App;

Class AmoCrmClient{
 
    private Client $httpClient;
    private string $domain;
    private string $token;

    public function __construct(){
        $this->domain = $_ENV['AMOCRM_DOMAIN'];
        $this->token = $_ENV['AMOCRM_ACCESS_TOKEN'];
        $this->httpClient = new Client([
            'base_url' => "https://{$this->domain}/api/v4/",
            'headers' => [
                'Authorization' => 'Bearer ',
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]
        ]);
    }

    public function createContact(string $name, string $email, string $phone){
        $phoneFieldId = 2257127;
        $emailFieldId = 2257129;

        try {
            $response = $this->$httpClient->post('contacts',[
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

            $data = json_decode($response->getBody());

            return $data[0][id];
        } catch (RequestException $e){
            error_log('AmoCRM API Error: ', $e->getMessage());
            throw new \Exception('Failed to create contact');
        }

    }

   
}