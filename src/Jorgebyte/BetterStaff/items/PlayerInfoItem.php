<?php

namespace Jorgebyte\BetterStaff\items;

use Jorgebyte\BetterStaff\utils\ConfigUtils;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemTypeIds;

class PlayerInfoItem extends Item
{

    public function __construct()
    {
        parent::__construct(new ItemIdentifier(ItemTypeIds::BAMBOO));
        $this->setCustomName(ConfigUtils::getConfigValue("itemnames", "playerinfo-item"));
    }
}