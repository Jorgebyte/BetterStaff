<?php

namespace Jorgebyte\BetterStaff\utils;

use Jorgebyte\BetterStaff\utils\webhook\DiscordWebhook;
use Jorgebyte\BetterStaff\utils\webhook\Embed;
use Jorgebyte\BetterStaff\utils\webhook\Message;

class WebhookUtils
{
    public static function sendReportWebhook(string $reporter, string $reportedPlayer, string $reason): void
    {
        $webhookURL = ConfigUtils::getConfigValue("settings", "webhook-url");
        $webhook = new DiscordWebhook($webhookURL);
        $message = new Message();
        $message->setUsername(ConfigUtils::getConfigValue("settings", "webhook-username"));
        $message->setContent(ConfigUtils::getConfigValue("settings", "webhook-content"));
        $embed = new Embed();
        $embed->setTitle(ConfigUtils::getConfigValue("settings", "webhook-title"));
        $embed->addField(ConfigUtils::getConfigValue("settings", "webhook-reporter"), $reporter);
        $embed->addField(ConfigUtils::getConfigValue("settings", "webhook-reported"), $reportedPlayer);
        $embed->addField(ConfigUtils::getConfigValue("settings", "webhook-reason"), $reason);
        $embed->setColor(0xFF0000);
        $message->addEmbed($embed);
        $webhook->send($message);
    }

    // More webhooks coming soon... //
}