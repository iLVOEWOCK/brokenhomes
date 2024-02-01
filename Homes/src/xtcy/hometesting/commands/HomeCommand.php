<?php

namespace xtcy\hometesting\commands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\world\Position;
use xtcy\hometesting\Utils\HomeManager;
use xtcy\hometesting\Loader; // Import the Loader class

class HomeCommand extends BaseCommand
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

        $homeName = $args["name"];
        $homeManager = Loader::getSessionManager()->getHomeSession($sender);

        $homeManager->getHomes(function ($error, $homes) use ($homeName, $sender) {
            if ($error !== null) {
                $sender->sendMessage("Error fetching homes: " . $error->getMessage());
                return;
            }

            foreach ($homes as $home) {
                if ($home["home_name"] === $homeName) {
                    // Home found, teleport the player
                    $worldName = $home["world_name"];
                    $x = $home["x"];
                    $y = $home["y"];
                    $z = $home["z"];

                    $sender->teleport(new Position($x, $y, $z, $sender->getServer()->getWorldManager()->getWorldByName($worldName)));

                    $sender->sendMessage("Teleported to home: $homeName");
                    return;
                }
            }

            $sender->sendMessage("Home '$homeName' not found.");
        });
    }
}
