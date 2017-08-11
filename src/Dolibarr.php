<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Dolibarr
{
    protected $app;
    protected $client;

    public function __construct(&$app)
    {
        $this->app = &$app;
        $this->client = new Client([
            'base_uri' => 'https://picsou.uttnetgroup.fr/',
            'headers' => ['DOLAPIKEY' => $app['dolikey']]
        ]);
    }

    public function getMemberByLogin($login)
    {
        try {
            $response = $this->client->get('api/index.php/members', [
                'query' => [
                    'sqlfilters' => "(t.login:=:'" . $login . "')"
                ]
            ]);
        } catch (GuzzleException $e) {
            return $e->getMessage();
        }
        $json = json_decode($response->getBody()->getContents(), true)[0];

        // Test subscription
        $json['subscription_active'] = ($json['last_subscription_date'] && $json['datefin'] && (time() >= strtotime($json['last_subscription_date']) ) && (time() <= $json['datefin']));

        if(isset($json['error']))
            return null;
        else return $json;
    }

    public function getSubscriptionsById($id)
    {
        try {
            $response = $this->client->get('api/index.php/members/'.intval($id).'/subscriptions');
        } catch (GuzzleException $e) {
            return $e->getMessage();
        }
        $json = json_decode($response->getBody()->getContents(), true);

        if(isset($json['error']))
            return null;
        else return $json;
    }

    public function createSubscriptionById($id, $start, $end, $amount, $payment=null)
    {
        try {
            $response = $this->client->post('api/index.php/subscriptions', [
                'form_params' => [
                    'fk_adherent'    =>  $id,
                    'dateh' => strtotime($start),
                    'datef'  =>  $end,
                    'note' =>  'Adhésion en ligne ('.$payment.')',
                    'amount'    =>  $amount
                ]
            ]);
        } catch (GuzzleException $e) {
            return $e->getMessage();
        }
        $json = json_decode($response->getBody()->getContents(), true);

        if(isset($json['error']))
            return null;
        else return $json;
    }

    public function _request()
    {
        /**
        try {
            $response = $client->get('/api/private/user/account?access_token=' . $json['access_token']);
        } catch (GuzzleException $e) {
            abort(500);
        }
        $json = json_decode($response->getBody()->getContents(), true)['data'];
         **/
    }
}