<?php

namespace xtcy\hometesting\commands;

use CortexPE\Commando\BaseCommand;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use xtcy\hometesting\Loader;

class ListHomes extends BaseCommand
{
    public function prepare(): void
    {
        $this->setPermission("hometesting.command");
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player) {
            return;
        }

        $homeManager = Loader::getSessionManager()->getHomeSession($sender);

        $homeManager->getHomes(function ($error, $homes) use ($sender) {
            if ($error !== null) {
                $sender->sendMessage("Error fetching homes: " . $error->getMessage());
                return;
            }

            $validHomesExist = false; // Flag to check if there are valid homes

            foreach ($homes as $home) {
                if (!isset($home["home_name"], $home["world_name"], $home["x"], $home["y"], $home["z"]) ||
                    $home["home_name"] === "" || $home["world_name"] === "") {
                    continue; // Skip homes with missing or invalid values
                }

                $validHomesExist = true; // Set the flag to true if at least one valid home is found
                $sender->sendMessage("- " . $home["home_name"] . " in " . $home["world_name"] . " at X: " . $home["x"] . ", Y: " . $home["y"] . ", Z: " . $home["z"]);
            }

            if (!$validHomesExist) {
                $sender->sendMessage(TextFormat::DARK_RED . "No Homes");
            }
        });
    }
}
