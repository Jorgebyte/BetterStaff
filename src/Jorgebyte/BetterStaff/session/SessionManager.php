<?php

namespace Jorgebyte\BetterStaff\session;

use Jorgebyte\BetterStaff\session\type\FreezeSession;
use Jorgebyte\BetterStaff\session\type\StaffChatSession;
use Jorgebyte\BetterStaff\session\type\StaffSession;
use Jorgebyte\BetterStaff\session\type\VanishSession;

use pocketmine\player\Player;

class SessionManager
{
    /** We use this to map sessions */
    private static array $sessionMap = [
        'staff' => StaffSession::class,
        'vanish' => VanishSession::class,
        'freeze' => FreezeSession::class,
        'staffchat' => StaffChatSession::class,
    ];

    private static array $sessions = [];

    public static function startSession(Player $player, string $sessionType): void
    {
        if (!isset(self::$sessionMap[$sessionType])) {
            throw new \InvalidArgumentException("ERROR: Session type " . $sessionType . " is not recognized");
        }

        $sessionClass = self::$sessionMap[$sessionType];
        self::$sessions[$player->getName()][$sessionType] = new $sessionClass($player);
    }

    public static function endSession(Player $player, string $sessionType = null): void
    {
        $playerName = $player->getName();

        if ($sessionType === null) {
            if (isset(self::$sessions[$playerName])) {
                foreach (self::$sessions[$playerName] as $session) {
                    $session->endSession();
                }
                unset(self::$sessions[$playerName]);
            }
        } else {
            if (isset(self::$sessions[$playerName][$sessionType])) {
                self::$sessions[$playerName][$sessionType]->endSession();
                unset(self::$sessions[$playerName][$sessionType]);

                if (empty(self::$sessions[$playerName])) {
                    unset(self::$sessions[$playerName]);
                }
            }
        }
    }

    public static function getSession(Player $player, string $sessionType): ?Session
    {
        return self::$sessions[$player->getName()][$sessionType] ?? null;
    }

    public static function getActiveSessions(): array
    {
        return self::$sessions;
    }
}
