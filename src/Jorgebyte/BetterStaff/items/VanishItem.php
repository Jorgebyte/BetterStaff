<?php

namespace Jorgebyte\BetterStaff\items;

use Jorgebyte\BetterStaff\utils\Utils;
use pocketmine\block\utils\DyeColor;
use pocketmine\item\Dye;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemTypeIds;

class VanishItem extends Dye
{

    public function __construct()
    {
        $name = Utils::getConfigValue("itemnames", "vanish-item");
        parent::__construct(new ItemIdentifier(ItemTypeIds::DYE), $name);
        $this->setCustomName($name);
        $this->setColor(DyeColor::GREEN());
    }
}
