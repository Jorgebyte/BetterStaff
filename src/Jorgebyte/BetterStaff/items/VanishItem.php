<?php

namespace Jorgebyte\BetterStaff\items;

use Jorgebyte\BetterStaff\utils\ConfigUtils;
use pocketmine\block\utils\DyeColor;
use pocketmine\item\Dye;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemTypeIds;

class VanishItem extends Dye
{

    public function __construct()
    {
        $name = ConfigUtils::getConfigValue("itemnames", "vanish-item");
        parent::__construct(new ItemIdentifier(ItemTypeIds::DYE), $name);
        $this->setCustomName($name);
        $this->setColor(DyeColor::GREEN());
    }
}
