<?php

namespace Jorgebyte\BetterStaff\forms;

use Jorgebyte\BetterStaff\forms\types\CustomBanForm;
use Jorgebyte\BetterStaff\forms\types\CustomMuteForm;
use Jorgebyte\BetterStaff\forms\types\CustomReportForm;
use Jorgebyte\BetterStaff\forms\types\FreezeForm;
use Jorgebyte\BetterStaff\forms\types\TeleportForm;
use Jorgebyte\BetterStaff\utils\SoundUtils;
use pocketmine\player\Player;
use Vecnavium\FormsUI\Form;

class FormManager
{
    /** We use this to map forms*/
    private static array $formMap = [
        'ban' => CustomBanForm::class,
        'mute' => CustomMuteForm::class,
        'report' => CustomReportForm::class,
        'freeze' => FreezeForm::class,
        'teleport' => TeleportForm::class,
    ];

    private static function sendFormWithSound(Player $player, Form $form): void
    {
        $player->sendForm($form);
        SoundUtils::addSound($player, "random.pop2");
    }

    public static function sendForm(Player $player, string $formType): void
    {
        if (!isset(self::$formMap[$formType])) {
            throw new \InvalidArgumentException("ERROR: Form type " . $formType . " is not recognized");
        }

        $formClass = self::$formMap[$formType];
        $form = new $formClass();
        self::sendFormWithSound($player, $form);
    }
}