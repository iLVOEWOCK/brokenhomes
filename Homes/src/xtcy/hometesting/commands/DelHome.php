<?php

namespace xtcy\hometesting\commands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use xtcy\hometesting\Loader;

class DelHome extends BaseSubCommand
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

        $homeName = $args["name"] ?? "home";
        $homeManager = Loader::getSessionManager()->getHomeSession($sender);

        $homeManager->deleteHome($homeName);

        $sender->sendMessage("Home '$homeName' deleted successfully!");
    }
}
