<?php

namespace Jorgebyte\BetterStaff\utils\webhook;

use pocketmine\Server;

class DiscordWebhook
{
    protected $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function getURL(): string
    {
        return $this->url;
    }

    public function isValid(): bool
    {
        return filter_var($this->url, FILTER_VALIDATE_URL) !== false;
    }

    public function send(Message $message): void
    {
        $messageJson = json_encode($message);
        Server::getInstance()->getAsyncPool()->submitTask(new DiscordWebhookSendTask($this->url, $messageJson));
    }
}