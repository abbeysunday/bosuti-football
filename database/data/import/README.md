# Importing the real BOUESTI football data

Fill in these CSV files (Excel or Google Sheets both work — save/download as **CSV**), put photos in the
`photos/` folders, then run:

```bash
php artisan football:import --dry-run   # check everything, saves nothing
php artisan football:import             # import
```

(`php artisan db:seed --class=RealDataSeeder` does the same import.)

- Every file is optional; fill in what you have and run the import again later. Re-running **updates** existing rows instead of duplicating them.
- If any row has a problem, **nothing is saved** and you get a list like `players.csv line 7: Unknown team "Amapro"`.
- Keep the first row (the column names) exactly as it is. Columns marked **required** must not be empty.
- Yes/no columns accept `yes`, `no`, `1`, `0` (empty = the default shown).

> Only real, verified information should go in these files. Matric numbers and state of origin are stored for admins only and are never shown on the website.

---

## teams.csv

Already lists the seven teams. Fill in the extra details you have.

| Column | Notes |
|---|---|
| `name` **required** | Team name, e.g. `Amapro FC`. Used to match existing teams. |
| `short_name` | Up to 12 characters, shown on badges, e.g. `AMA` |
| `primary_color`, `secondary_color` | Hex colours, e.g. `#0A7A3D` |
| `coach_name`, `captain_name` | Optional |
| `founded_year` | e.g. `2019` |
| `description` | A short paragraph about the team |
| `logo` | File name inside `photos/teams/`, e.g. `amapro-fc.png` |
| `is_active` | default `yes` |

## players.csv

| Column | Notes |
|---|---|
| `team` **required** | Team name (or short name), e.g. `Amapro FC` |
| `first_name`, `last_name` **required** | |
| `jersey_number` | 1–99, must be unique within the team |
| `position` **required** | `goalkeeper`, `defender`, `midfielder` or `forward` (`GK`, `DEF`, `MID`, `FWD` also work) |
| `department` | e.g. `Computer Science` (shown publicly) |
| `level` | `100`, `200`, `300`, `400` or `500` (shown publicly) |
| `matric_number` | **Private.** Used to recognise the player on re-import |
| `state_of_origin` | **Private** |
| `dominant_foot` | `right`, `left` or `both` |
| `height` | e.g. `1.80 m` |
| `bio` | Short biography (shown on the profile) |
| `is_captain`, `is_featured` | default `no`; featured players appear on the homepage |
| `is_active` | default `yes` |
| `photo` | File name inside `photos/players/`, e.g. `tunde-adeyemi.jpg` |

Example row:

```
Amapro FC,Tunde,Adeyemi,9,forward,Computer Science,300,BOU/CSC/21/0045,Ekiti,right,1.78 m,,no,yes,yes,tunde-adeyemi.jpg
```

## staff.csv (management and coaches)

| Column | Notes |
|---|---|
| `name` **required** | Full name with title if wanted, e.g. `Mr. Kunle Ajayi` |
| `role` **required** | e.g. `Head Coach`, `Team Coordinator`, `Director of Sports` |
| `type` **required** | `coaching` (Coaching Staff page) or `management` (Management page) |
| `team` | Team name if they belong to one team; empty = the whole football programme |
| `bio`, `sort_order` | Lower `sort_order` appears first |
| `is_active` | default `yes` |
| `photo` | File name inside `photos/staff/` |

## fixtures.csv (fixtures and results)

| Column | Notes |
|---|---|
| `competition` | Competition name from the admin panel; empty = the current season's league |
| `date` **required** | `2026-10-14` or `14/10/2026` |
| `time` | `16:00` or `4:00 PM` |
| `home_team`, `away_team` **required** | Team names |
| `venue`, `matchday`, `referee`, `attendance` | Optional |
| `status` | `scheduled`, `live`, `completed`, `postponed`, `cancelled`. Empty = `completed` when both scores are filled, otherwise `scheduled` |
| `home_score`, `away_score` | Fill in for played matches |
| `featured` | `yes` to highlight on the homepage |
| `summary` | Short match summary shown on the match page |

A match is recognised by competition + date + home team + away team, so correcting a score and re-importing updates it.

## match_events.csv (goals, assists, cards, substitutions)

| Column | Notes |
|---|---|
| `date`, `home_team`, `away_team` **required** | Identify the match (it must be in `fixtures.csv` or already in the site) |
| `minute` **required** | e.g. `62` |
| `added_time` | e.g. `2` for 45+2 |
| `type` **required** | `goal`, `penalty_scored`, `own_goal`, `assist`, `yellow_card`, `red_card`, `substitution`, `penalty_missed` |
| `team` **required** | The team the player belongs to |
| `player` **required** | Full name as in `players.csv`, or shirt number like `#9` |
| `related_player` | For goals: the player who assisted. For substitutions: the player coming **on** (`player` = coming off) |
| `note` | Optional, e.g. `Header from a corner` |

For each match listed here, this file replaces that match's events, so keep all of a match's events together in the file.

Example rows:

```
2026-10-14,Amapro FC,Elite FC,62,,goal,Amapro FC,Tunde Adeyemi,Kola Bello,
2026-10-14,Amapro FC,Elite FC,70,,yellow_card,Elite FC,#4,,
```

## Photos

- `photos/players/`, `photos/staff/`, `photos/teams/` — JPG, PNG or WebP.
- Use the file name you wrote in the CSV (simple names like `tunde-adeyemi.jpg` are easiest).
- Photos are automatically resized, turned upright, converted to WebP and stripped of location data.
- Portrait photos (taller than wide) work best for players and staff; square images for team logos.
- Only use photos you have permission to publish.
