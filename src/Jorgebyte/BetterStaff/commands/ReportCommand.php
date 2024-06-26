<?php

namespace Jorgebyte\BetterStaff\commands;

use Jorgebyte\BetterStaff\Forms;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class ReportCommand extends Command
{

    public function __construct()
    {
        parent::__construct("report", "BetterStaff - Report");
        $this->setPermission("betterstaff.command.report");
        $this->setUsage("/report <player>");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage("This command can only be used in-game.");
            return;
        }

        Forms::customReportUI($sender);
    }

}