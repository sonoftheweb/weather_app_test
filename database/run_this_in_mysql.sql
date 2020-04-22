-- This adds the custom function to calculate the distance. Should be much faster in MySQL.
DROP function IF EXISTS `haversine`;
CREATE FUNCTION haversine(
    lat1 REAL,
    lng1 REAL,
    lat2 REAL,
    lng2 REAL
) RETURNS REAL NO SQL RETURN ATAN2(
    SQRT(
        POW(
            COS(RADIANS(lat2)) * SIN(RADIANS(lng1 - lng2)),
            2
        ) + POW(
            COS(RADIANS(lat1)) * SIN(RADIANS(lat2)) - SIN(RADIANS(lat1)) * COS(RADIANS(lat2)) * COS(RADIANS(lng1 - lng2)),
            2
        )
    ),
    (
        SIN(RADIANS(lat1)) * SIN(RADIANS(lat2)) + COS(RADIANS(lat1)) * COS(RADIANS(lat2)) * COS(RADIANS(lng1 - lng2))
    )
) * 6371.0;
