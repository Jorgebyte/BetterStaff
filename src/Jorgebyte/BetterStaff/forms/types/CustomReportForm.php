<?php

namespace Jorgebyte\BetterStaff\forms\types;

use Jorgebyte\BetterStaff\utils\ConfigUtils;
use Jorgebyte\BetterStaff\utils\SoundUtils;
use Jorgebyte\BetterStaff\utils\WebhookUtils;
use pocketmine\player\Player;
use pocketmine\Server;
use Vecnavium\FormsUI\CustomForm;

class CustomReportForm extends CustomForm
{
    private array $playerNames = [];

    public function __construct()
    {
        parent::__construct(function (Player $player, ?array $data) {
            if ($data === null) {
                return;
            }

            $selectedPlayerIndex = (int)$data[0];
            $reportedPlayerName = $this->playerNames[$selectedPlayerIndex];
            $reportReason = $data[1];

            if ($reportedPlayerName === "" || $reportReason === "") {
                $player->sendMessage("ERROR: Be sure to provide a reason for the report.");
                return;
            }

            if ($reportedPlayerName === $player->getName()) {
                $player->sendMessage("ERROR: You cannot report yourself.");
                return;
            }

            foreach (Server::getInstance()->getOnlinePlayers() as $staff) {
                if ($staff->hasPermission("betterstaff.reportview")) {
                    $staff->sendMessage(str_replace(["{PLAYER}", "{REPORTED}", "{REASON}"], [$player->getName(), $reportedPlayerName, $reportReason],
                        ConfigUtils::getConfigValue("messages", "report-staff-success")));
                    SoundUtils::addSound($staff, "random.pop");
                }
            }
            WebhookUtils::sendReportWebhook($player->getName(), $reportedPlayerName, $reportReason);
        });

        $this->setTitle("Report");
        $this->addInput("Reason for the report");
    }

    public function setPlayerNames(array $playerNames): void
    {
        $this->playerNames = $playerNames;
        $this->addDropdown("Select a player to report", $playerNames);
    }
}