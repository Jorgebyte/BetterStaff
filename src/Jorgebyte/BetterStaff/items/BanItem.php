<?php

namespace Jorgebyte\BetterStaff\items;

use Jorgebyte\BetterStaff\Main;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemTypeIds;

class BanItem extends Item
{

    public function __construct()
    {
        parent::__construct(new ItemIdentifier(ItemTypeIds::BRICK));
        $this->setCustomName(Main::getInstance()->getItemNames("ban-item"));
    }
}