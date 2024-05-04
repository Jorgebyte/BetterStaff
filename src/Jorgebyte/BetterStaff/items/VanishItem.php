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
        parent::__construct(new ItemIdentifier(ItemTypeIds::DYE));
        $this->setCustomName(Utils::getConfigValue("itemnames", "vanish-item"));
        $this->setColor(DyeColor::GREEN());
    }
}