<?php

namespace Jorgebyte\BetterStaff\session\type;

use Jorgebyte\BetterStaff\session\Session;

class FreezeSession extends Session
{
    protected function init(): void
    {
        $player = $this->getPlayer();
        $player->setNoClientPredictions();
    }

    public function endSession(): void
    {
        $player = $this->getPlayer();
        $player->setNoClientPredictions(false);
    }
}