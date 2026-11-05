# RE Hubs API

All endpoints return public entries only. Records marked **Do not list** are excluded.

## Endpoints

| Data | Endpoint |
| --- | --- |
| Regions with Places of Interest | `GET /api/re-hubs/regions/places-of-interest` |
| Regions with School Speakers | `GET /api/re-hubs/regions/school-speakers` |
| Places of Interest | `GET /api/re-hubs/places-of-interest` |
| School Speakers | `GET /api/re-hubs/school-speakers` |

## Filters

The entry endpoints accept:

- `search`: searches entry name, contact name, organisation email, and religion or belief.
- `region`: filters by region ID.
- `religion`: filters by religion or belief ID.

The region endpoints accept:

- `search`: searches the region title.
- `religion`: returns regions containing matching entries for that religion or belief ID.

Examples:

```text
/api/re-hubs/school-speakers?search=humanism&region=1
/api/re-hubs/places-of-interest?religion=2
/api/re-hubs/regions/school-speakers?search=london
```

Entries classified as both a Place of Interest and a School Speaker appear in both relevant endpoints.
