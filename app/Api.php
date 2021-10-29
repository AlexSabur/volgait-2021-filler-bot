<?php

namespace App;

use Illuminate\Support\Facades\Http;

class Api
{
    /**
     * @var \Illuminate\Http\Client\PendingRequest
     */
    protected $client;

    public function __construct()
    {
        $this->client = Http::withOptions([
            'base_uri' => config('app.api.base_uri'),
            // 'timeout' => config('app.api.timeout'),
            // 'debug' => true,
        ]);
    }

    public function getState($gameId)
    {
        return $this->client->get("/game/$gameId")->json();
    }

    public function makeStep($gameId, $playerId, $color)
    {
        return $this->client->put("/game/$gameId", compact('playerId', 'color'))->json();
    }
}
