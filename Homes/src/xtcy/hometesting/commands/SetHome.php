<?php

namespace xtcy\hometesting\commands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use xtcy\hometesting\Loader;
use xtcy\hometesting\Utils\HomeManager;

class SetHome extends BaseCommand
{

    /**
     * @throws ArgumentOrderException
     */
    public function prepare(): void
    {
        $this->setPermission("hometesting.command");
        $this->registerArgument(0, new RawStringArgument("name", false));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player) {
            return;
        }

        $homeManager = Loader::getSessionManager()->getHomeSession($sender);

        $homeManager->UpdateHome($args["name"], $sender->getPosition()->getWorld()->getFolderName(), $sender->getPosition()->getFloorX(), $sender->getPosition()->getFloorY(), $sender->getPosition()->getFloorZ());

        $sender->sendMessage("Home set successfully!");
    }
}
