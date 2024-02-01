<?php

namespace xtcy\hometesting\Utils;

use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use xtcy\hometesting\Loader;

final class Home
{

    use SingletonTrait;

    /** @var HomeManager[] */
    public static array $homes;

    public function __construct(public Loader $plugin) {
        self::setInstance($this);

        $this->loadHomes();
    }
    /**
     * Store all player data in $sessions property
     *
     * @return void
     */
    private function loadHomes(): void
    {
        var_dump("Load Homes");

        Loader::getDatabase()->executeSelect(QueryConstants::PLAYERS_SELECT, [], function (array $rows): void {
            if ($rows === null) {
                // Log an error or handle it in an appropriate way
                var_dump("Error fetching rows from database.");
                return;
            }

            var_dump("Rows from database:", $rows);

            if (empty($rows)) {
                var_dump("No rows found in the database.");
                return;
            }

            foreach ($rows as $row) {
                $uuid = Uuid::fromString($row["uuid"]);
                var_dump("Loaded UUID:", $uuid->toString());

                if (!isset(Home::$homes[$uuid->toString()])) {
                    Home::$homes[$uuid->toString()] = new HomeManager($uuid);
                    var_dump("HomeManager created for UUID:", $uuid->toString());
                }
            }
        });
    }

    /**
     * Create a session
     *
     * @param Player $player
     * @return HomeManager
     */
    public function createHomes(Player $player): HomeManager
    {
        $args = [
            "uuid" => $player->getUniqueId()->toString(),
            "home_name" => "",
            "world_name" => "",
            "x" => 0,
            "y" => 0,
            "z" => 0,
        ];

        Loader::getDatabase()->executeInsert(QueryConstants::PLAYERS_CREATE, $args);

        self::$homes[$player->getUniqueId()->toString()] = new HomeManager(
            $player->getUniqueId()
        );
        return self::$homes[$player->getUniqueId()->toString()];
    }

    /**
     * Get homes by player object
     *
     * @param Player $player
     * @return HomeManager|null
     */
    public function getHomeSession(Player $player) : ?HomeManager
    {
        return $this->getHomesSessionByUuid($player->getUniqueId());
    }

    /**
     * Get levels by player name
     *
     * @param string $name
     * @return HomeManager|null
     */
    public function getHomeSessionByName(string $name) : ?HomeManager
    {
        foreach (self::$homes as $session) {
            if (strtolower($session->getUsername()) === strtolower($name)) {
                return $session;
            }
        }
        return null;
    }

    /**
     * Get levels by UuidInterface
     *
     * @param UuidInterface $uuid
     * @return HomeManager|null
     */
    public function getHomesSessionByUuid(UuidInterface $uuid) : ?HomeManager
    {
        return self::$homes[$uuid->toString()] ?? null;
    }

    public function destroyHomeSession(HomeManager $session) : void
    {
        Loader::getDatabase()->executeChange(QueryConstants::PLAYERS_DELETE, ["uuid", $session->getUuid()->toString()]);

        unset(self::$homes[$session->getUuid()->toString()]);
    }

    public function getHomeSessions() : array
    {
        return self::$homes;
    }
}