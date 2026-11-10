   # Week 11 - Optimized & Advanced Queries (Campus Events at Scale)

   Group: Belches, Borlagdan, Valenzuela

   Setup: `composer install`, copy `.env.example` to `.env` and set the database to `webdev_week11`, then run `php artisan migrate:fresh --seed`.

   ## Query counts (Step 4)

   | Version of /events | Queries sent | What was sent |
   |---|---|---|
   | Naive (lazy loading) | 101 | 1 for the events, 50 for each event's venue, 50 for each event's participant count |
   | Fixed (eager loading + withCount) | 2 | 1 for the events with the participant count as a subquery, 1 for all the venues |

   The naive page sends one extra query for every row, because each time the page reads an event's venue or participant count, Laravel goes back to the database. The fixed page asks for the venues and the participant counts together with the events, so the number of queries stays at 2 no matter how many events are listed.

   ## Local scopes on Event

   | Scope | What it does |
   |---|---|
   | `upcoming()` | Keeps only events that start now or later. |
   | `atVenue($venueId)` | Keeps only events at the given venue. |
   | `soonestFirst()` | Orders events by start time, earliest first. |

   Chained example (used on `/events/filter`): `Event::upcoming()->atVenue($venue)->soonestFirst()`.

   ## Query plans (Step 7)

   All times come from `EXPLAIN (ANALYZE)` on 20,000 events, using the rare values from the seeder (`telescope` appears in 80 events, `dress_code = costume` in 100).

   | Query | Index that exists | Plan type | Execution time |
   |---|---|---|---|
   | A: full-text search for "telescope" | None | Seq Scan | 558.044 ms |
   | A: full-text search for "telescope" | GIN on `to_tsvector('english', description)` | Bitmap Index Scan + Bitmap Heap Scan | 0.529 ms |
   | B: JSONB containment `details @> '{"dress_code": "costume"}'` | None | Seq Scan | 6.377 ms |
   | B: JSONB containment `details @> '{"dress_code": "costume"}'` | GIN on `details` | Bitmap Index Scan + Bitmap Heap Scan | 1.036 ms |
   | C: JSONB arrow form `details->>'dress_code' = 'costume'` | None | Seq Scan | 4.721 ms |
   | C: JSONB arrow form `details->>'dress_code' = 'costume'` | GIN on `details` (exists, **not used**) | Seq Scan | 8.413 ms |
   | C: JSONB arrow form `details->>'dress_code' = 'costume'` | B-tree on `(details->>'dress_code')` | Bitmap Index Scan + Bitmap Heap Scan | 0.201 ms |
   | D: upcoming events at venue 1 ordered by `starts_at` | None | Seq Scan + Sort | 6.136 ms |
   | D: upcoming events at venue 1 ordered by `starts_at` | B-tree on `(venue_id, starts_at)` | Bitmap Index Scan + Bitmap Heap Scan + Sort | 3.878 ms |

   **Why the GIN index is not used for Query C:** a default GIN index on a jsonb column only supports operators such as `@>` and `?`. The arrow form uses `->>`, which first extracts the value as text, so PostgreSQL cannot use the GIN index and falls back to a Seq Scan. An index on the extracted expression, `(details->>'dress_code')`, is the one that matches this query.

   **Notes:** each time comes from a single run, so small differences (for example the 4.721 ms against 8.413 ms Seq Scans for Query C) are caching noise and not a real difference. Query D returns about 2,000 rows, roughly 10% of the table, so its index helps less than the indexes on the selective queries A, B and C.

   ## Why the indexes are in migrations (Step 8)

Every index is created inside a migration so that `php artisan migrate:fresh --seed` rebuilds the whole database, indexes included. An index created by hand in psql disappears as soon as the tables are dropped. Laravel's schema builder cannot create a GIN index or an index on an expression such as `details->>'dress_code'`, so those use `DB::statement()` raw SQL inside the migration, with `IF NOT EXISTS` so running it twice does not fail.

## What goes in JSONB and what goes in a column (Step 9)

- **In the JSONB column (`details`): `dress_code`.** Only some events need a dress code, and it is an optional detail that can change shape later (for example new keys such as `tags`). JSONB lets us add or skip it per event without a migration or a column full of NULLs.
- **In a normal column: `starts_at`.** Every event has a start time, and we filter and sort by it all the time. A real `timestamp` column gives us type checking, a NOT NULL guarantee, and a fast composite B-tree index with `venue_id`, which a value buried in JSON could not offer as simply.