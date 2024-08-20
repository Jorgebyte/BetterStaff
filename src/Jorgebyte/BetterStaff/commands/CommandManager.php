<?php

namespace Jorgebyte\BetterStaff\commands;

use Jorgebyte\BetterStaff\commands\types\{
    BetterStaffCommand,
    CheckTempBanCommand,
    CheckTempMuteCommand,
    FreezeCommand,
    PlayerInfoCommand,
    ReportCommand,
    StaffChatCommand,
    StaffListCommand,
    TempBanCommand,
    TempMuteCommand,
    UnTempBanCommand,
    UnTempMuteCommand
};
use pocketmine\Server;

class CommandManager
{
    public static function loadCommand()
    {
        $commands = [
            new BetterStaffCommand(),
            new FreezeCommand(),
            new StaffChatCommand(),
            new TempBanCommand(),
            new UnTempBanCommand(),
            new CheckTempBanCommand(),
            new PlayerInfoCommand(),
            new ReportCommand(),
            new StaffListCommand(),
            new TempMuteCommand(),
            new CheckTempMuteCommand(),
            new UnTempMuteCommand()
        ];
        Server::getInstance()->getCommandMap()->registerAll("BetterStaff", $commands);
    }
}