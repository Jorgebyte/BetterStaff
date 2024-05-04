<?php

namespace Jorgebyte\BetterStaff\items;

use Jorgebyte\BetterStaff\utils\Utils;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemTypeIds;

class FreezeItem extends Item
{

    public function __construct()
    {
        parent::__construct(new ItemIdentifier(ItemTypeIds::BOOK));
        $this->setCustomName(Utils::getConfigValue("itemnames", "freeze-item"));
    }
}