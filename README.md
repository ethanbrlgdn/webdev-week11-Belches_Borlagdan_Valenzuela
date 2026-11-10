# Campus Events (Week 10: Eloquent Relationships and Foreign Keys in PostgreSQL)

A small Laravel + PostgreSQL data layer for a Campus Events system. Venues host events, participants register for events, and notes can be attached to either an event or a venue.

**Authors:** Belches, Borlagdan, Valenzuela

## Tech stack

- Laravel (PHP 8.3)
- PostgreSQL
- Blade (report page)

## Setup

1. Clone the repository and install dependencies:
   ```bash
   composer install
   ```
2. Copy the environment file and generate an app key:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
3. Create a PostgreSQL database (for example `webdev_week10`) and set these values in `.env`:
   ```
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=webdev_week10
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```
4. Build the schema and seed the sample data:
   ```bash
   php artisan migrate:fresh --seed
   ```
5. Start the app and open the report page:
   ```bash
   php artisan serve
   ```
   Then visit `http://127.0.0.1:8000/report` (replace `/report` with the route defined in `routes/web.php` if yours differs).

## Tables

| Table | Primary key | Foreign keys |
|---|---|---|
| `venues` | `id` | none |
| `events` | `id` | `venue_id` → `venues.id` |
| `participants` | `id` | none |
| `event_participant` (pivot) | composite (`event_id`, `participant_id`) | `event_id` → `events.id`, `participant_id` → `participants.id` |
| `notes` | `id` | none enforced (`notable_type` + `notable_id` are a polymorphic pair) |

The pivot table also stores a `status` column (`registered` or `waitlisted`).

## Relationships

| Link | Type | Model / table holding the foreign key | Relationship methods | ON DELETE | Reason |
|---|---|---|---|---|---|
| Venue and Event | One-to-many | `Event` (`events.venue_id`) | `Venue::events()` (hasMany), `Event::venue()` (belongsTo) | RESTRICT | A venue that still has events must not be deletable. |
| Event and Participant | Many-to-many with `status` | `event_participant` pivot (`event_id`, `participant_id`) | `Event::participants()` (belongsToMany), `Participant::events()` (belongsToMany) | CASCADE | A registration has no meaning once its event or participant is gone, so it is removed automatically. |
| Note and Event / Venue | Polymorphic one-to-many | `Note` (`notes.notable_type` + `notes.notable_id`) | `Note::notable()` (morphTo), `Event::notes()` (morphMany), `Venue::notes()` (morphMany) | None (app-level only) | A foreign key can only point to one table, so PostgreSQL cannot enforce a link that points at either `events` or `venues`. |

## What PostgreSQL enforces and what it does not

**Enforced by the database:**
- An event cannot be inserted with a venue that does not exist.
- A venue that still has events cannot be deleted (`RESTRICT`).
- The same participant cannot register for the same event twice (composite primary key on the pivot).
- Deleting an event or a participant removes its registrations (`CASCADE`).

**Only our code can enforce:**
- That a note's `notable_id` points to a real row in the table named by `notable_type`. PostgreSQL accepts an orphan note because the polymorphic columns have no foreign key constraint.

## Report page

The report page uses Eloquent relationships (`withCount`, `whereHas`, `with`, `morphTo`) rather than hand-written JOINs, and shows:

1. Every venue with the number of events it hosts
2. All events at one chosen venue
3. All participants of one chosen event, with each registration status
4. All events one chosen participant has joined
5. All events that have at least one waitlisted participant
6. All notes of one chosen event and of one chosen venue
7. For one chosen note, the name and kind of the thing it belongs to

## Seed data

`php artisan migrate:fresh --seed` creates at least 3 venues, 6 events, 8 participants, 12 registrations (both statuses used), and 6 notes (at least two on events and two on venues).
