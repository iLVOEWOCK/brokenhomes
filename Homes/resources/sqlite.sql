-- #!sqlite
-- # { homes
-- #  { initialize
CREATE TABLE IF NOT EXISTS homes (
    uuid VARCHAR(36),
    home_name VARCHAR(32),
    world_name VARCHAR(32),
    x INT,
    y INT,
    z INT,
    PRIMARY KEY (uuid, home_name)
    );
-- #  }
-- # { select
SELECT * FROM homes WHERE uuid = :uuid;
-- # }
-- #  { create
-- #      :uuid string
-- #      :home_name string
-- #      :world_name string
-- #      :x int
-- #      :y int
-- #      :z int
INSERT OR REPLACE INTO homes(uuid, home_name, world_name, x, y, z)
VALUES (:uuid, :home_name, :world_name, :x, :y, :z);
-- #  }
-- #  { update
-- #      :uuid string
-- #      :home_name string
-- #      :world_name string
-- #      :x int
-- #      :y int
-- #      :z int
UPDATE homes
SET home_name = :home_name,
    world_name = :world_name,
    x = :x,
    y = :y,
    z = :z
WHERE uuid = :uuid;
-- #  }
-- #  { delete
-- #      :uuid int
DELETE FROM homes
WHERE uuid = :uuid;
-- #  }
-- # }
