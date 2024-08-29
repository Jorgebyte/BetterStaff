<?php

namespace Jorgebyte\BetterStaff\commands\types;

use Jorgebyte\BetterStaff\session\SessionManager;
use Jorgebyte\BetterStaff\utils\ConfigUtils;
use Jorgebyte\BetterStaff\utils\SoundUtils;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\PluginOwned;
use pocketmine\plugin\PluginOwnedTrait;

class BetterStaffCommand extends Command implements PluginOwned
{
    use PluginOwnedTrait;

    public function __construct()
    {
        parent::__construct("betterstaff", "BetterStaff - Start in administrator mode", null, ["staff", "mod"]);
        $this->setPermission("betterstaff.command.staff");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage("This command can only be run in the game.");
            return;
        }

        $session = SessionManager::getSession($sender, 'staff');

        if ($session !== null) {
            SessionManager::endSession($sender);
        } else {
            SessionManager::startSession($sender, 'staff');
        }

        $prefix = ConfigUtils::getPrefix();

        $messageKey = $session ?  "exit-staffmode-message" : "join-staffmode-message";
        $message = ConfigUtils::getConfigValue("messages", $messageKey);
        $sender->sendMessage($prefix . $message);
        SoundUtils::addSound($sender, "random.orb");
    }
}