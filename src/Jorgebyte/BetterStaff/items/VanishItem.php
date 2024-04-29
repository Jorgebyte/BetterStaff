<?php

namespace Jorgebyte\BetterStaff\items;

use Jorgebyte\BetterStaff\Main;
use pocketmine\block\utils\DyeColor;
use pocketmine\item\Dye;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemTypeIds;

class VanishItem extends Dye
{

    public function __construct()
    {
        parent::__construct(new ItemIdentifier(ItemTypeIds::DYE));
        $this->setCustomName(Main::getInstance()->getItemNames("vanish-item"));
        $this->setColor(DyeColor::GREEN());
    }
}