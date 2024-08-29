<?php

namespace Jorgebyte\BetterStaff\utils\webhook;

use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;

class DiscordWebhookSendTask extends AsyncTask
{

    protected $webhookUrl;
    protected $messageJson;

    public function __construct(string $webhookUrl, string $messageJson)
    {
        $this->webhookUrl = $webhookUrl;
        $this->messageJson = $messageJson;
    }

    public function onRun(): void
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->webhookUrl);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $this->messageJson);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $result = curl_exec($curl);
        $responseCode = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        curl_close($curl);
        $this->setResult([$result, $responseCode]);
    }

    public function onCompletion(): void
    {
        $response = $this->getResult();
        if (!in_array($response[1], [200, 204])) {
            Server::getInstance()->getLogger()->error("Got error ({$response[1]}): " . $response[0]);
        }
    }
}