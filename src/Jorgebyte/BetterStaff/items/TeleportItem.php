<?php

namespace Jorgebyte\BetterStaff\items;

use Jorgebyte\BetterStaff\utils\Utils;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemTypeIds;

class TeleportItem extends Item
{

    public function __construct()
    {
        parent::__construct(new ItemIdentifier(ItemTypeIds::COMPASS));
        $this->setCustomName(Utils::getConfigValue("itemnames", "teleport-item"));
    }
}