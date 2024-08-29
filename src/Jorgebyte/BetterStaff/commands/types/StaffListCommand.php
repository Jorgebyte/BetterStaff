<?php

namespace Jorgebyte\BetterStaff\commands\types;

use Jorgebyte\BetterStaff\utils\ConfigUtils;
use Jorgebyte\BetterStaff\utils\SoundUtils;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\PluginOwned;
use pocketmine\plugin\PluginOwnedTrait;
use pocketmine\Server;

class StaffListCommand extends Command implements PluginOwned
{
    use PluginOwnedTrait;

    public function __construct()
    {
        parent::__construct("stafflist", "BetterStaff - List all Staff");
        $this->setPermission("betterstaff.command.stafflist");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage("This command can only be used in-game.");
            return;
        }


        $staffList = [];
        foreach (Server::getInstance()->getOnlinePlayers() as $player) {
            if ($player->hasPermission("betterstaff.stafflist")) {
                $staffList[] = $player->getName();
            }
        }

        if (empty($staffList)) {
            $sender->sendMessage(ConfigUtils::getConfigValue("messages", "no-staff-online"));
            SoundUtils::addSound($sender, "note.bass");
            return;
        }

        $staffCount = count($staffList);
        $message = str_replace("{STAFFS}", $staffCount, ConfigUtils::getConfigValue("messages", "stafflist-list") . "\n");
        foreach ($staffList as $index => $staff) {
            $message .= ($index + 1) . ". $staff" . "\n";
        }
        $sender->sendMessage($message);
        SoundUtils::addSound($sender, "random.pop2");
    }
}