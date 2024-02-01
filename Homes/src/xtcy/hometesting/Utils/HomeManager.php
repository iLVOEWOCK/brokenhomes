<?php

namespace xtcy\hometesting\Utils;

use pocketmine\player\Player;
use pocketmine\Server;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use xtcy\hometesting\Loader;

final class HomeManager
{
    private bool $isConnected = false;

    public function __construct(private readonly UuidInterface $uuid)
    {
    }

    public function isConnected(): bool
    {
        return $this->isConnected;
    }

    public function setConnected(bool $connected): void
    {
        $this->isConnected = $connected;
    }

    public function getUuid(): UuidInterface
    {
        return $this->uuid;
    }

    public function getPocketminePlayer(): ?Player
    {
        return Server::getInstance()->getPlayerByUUID($this->uuid);
    }

    public function getUsername(): string
    {
        return $this->getPocketminePlayer()->getName();
    }

    /**
     * Save or update a home in the database and in-memory storage
     *
     * @param string $homeName
     * @param string $worldName
     * @param int $x
     * @param int $y
     * @param int $z
     * @return void
     */
    public function UpdateHome(string $homeName, string $worldName, int $x, int $y, int $z): void
    {
        // Retrieve the corresponding HomeManager instance from the global storage
        $homeManager = Home::getInstance()->getHomesSessionByUuid($this->uuid);

        $existingHome = Loader::getSessionManager()->getHomeSessionByName($homeName);

        if ($existingHome !== null) {
            var_dump("Overwrote existing home:", $homeName);
            return;
        }

        if ($homeManager === null) {
            var_dump("Error: HomeManager not found for UUID " . $this->uuid->toString());
            return;
        }

        // Update in-memory storage
        Home::$homes[$homeName] = [
            "home_name" => $homeName,
            "world_name" => $worldName,
            "x" => $x,
            "y" => $y,
            "z" => $z,
        ];

        // Update in the database
        Loader::getDatabase()->executeChange(QueryConstants::PLAYERS_UPDATE, [
            "uuid" => $this->uuid->toString(),
            "home_name" => $homeName,
            "world_name" => $worldName,
            "x" => $x,
            "y" => $y,
            "z" => $z,
        ]);

        var_dump("Updated home:", $homeName);
    }

    /**
     * Get all homes for the player from in-memory storage
     *
     * @return void
     */
    public function getHomes(\Closure $callback): void
    {
        var_dump("UUID for getHomes:", $this->uuid->toString());

        Loader::getDatabase()->executeSelect(QueryConstants::PLAYERS_SELECT, [
            "uuid" => Uuid::fromString($this->uuid),
        ], function($result) use ($callback) : void {
            $callback(null, $result);
        });

    }


    /**
     * Delete a home from the database and in-memory storage
     *
     * @param string $homeName
     * @return void
     */
    public function deleteHome(string $homeName): void
    {
        // Retrieve the corresponding HomeManager instance from the global storage
        $homeManager = Home::getInstance()->getHomesSessionByUuid($this->uuid);

        // Remove from in-memory storage
        unset(Home::$homes[$homeName]);

        // Remove from the database
        Loader::getDatabase()->executeChange(QueryConstants::PLAYERS_DELETE, [
            "uuid" => $this->uuid->toString(),
            "home_name" => $homeName,
        ]);
    }

    /**
     * Update player information in the database and in-memory storage
     *
     * @return void
     */
    private function updateDb(string $homeName, string $worldName, int $x, int $y, int $z): void
    {
        // Retrieve the corresponding HomeManager instance from the global storage
        $homeManager = Home::getInstance()->getHomesSessionByUuid($this->uuid);

        if ($homeManager === null) {
            // Log an error or throw an exception
            var_dump("Error: HomeManager not found for UUID " . $this->uuid->toString());
            return;
        }

        Home::$homes[$homeName] = [
            "home_name" => $homeName,
            "world_name" => $worldName,
            "x" => $x,
            "y" => $y,
            "z" => $z,
        ];

        // Update in the database
        Loader::getDatabase()->executeChange(QueryConstants::PLAYERS_UPDATE, [
            "uuid" => $this->uuid->toString(),
            "home_name" => $homeName,
            "world_name" => $worldName,
            "x" => $x,
            "y" => $y,
            "z" => $z,
        ]);
    }
}
