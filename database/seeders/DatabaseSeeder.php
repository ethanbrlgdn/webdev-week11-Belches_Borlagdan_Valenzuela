<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Participant;
use App\Models\Venue;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 3 venues
        $hall = Venue::create(['name' => 'Main Hall', 'address' => '1 Campus Road', 'capacity' => 300]);
        $lab = Venue::create(['name' => 'Computer Lab', 'address' => '2 Tech Building', 'capacity' => 40]);
        $court = Venue::create(['name' => 'Open Court', 'address' => '3 Sports Complex', 'capacity' => 500]);

        // 6 events, created through the venue relationship
        $e1 = $hall->events()->create(['title' => 'Freshmen Welcome', 'description' => 'Orientation for new students', 'starts_at' => '2026-12-01 09:00:00']);
        $e2 = $hall->events()->create(['title' => 'Career Fair', 'description' => 'Meet local employers', 'starts_at' => '2026-12-05 10:00:00']);
        $e3 = $lab->events()->create(['title' => 'Laravel Workshop', 'description' => 'Hands-on intro to Laravel', 'starts_at' => '2026-12-08 13:00:00']);
        $e4 = $lab->events()->create(['title' => 'Hackathon', 'description' => '24-hour coding challenge', 'starts_at' => '2026-12-12 08:00:00']);
        $e5 = $court->events()->create(['title' => 'Sports Fest', 'description' => 'Inter-department games', 'starts_at' => '2026-12-15 08:00:00']);
        $e6 = $court->events()->create(['title' => 'Fun Run', 'description' => '5K charity run', 'starts_at' => '2026-12-18 06:00:00']);

        // 8 participants
        $names = ['Ana Reyes', 'Ben Cruz', 'Carla Santos', 'Dan Lopez', 'Ella Garcia', 'Franco Diaz', 'Gina Torres', 'Hugo Ramos'];
        $p = [];
        foreach ($names as $i => $name) {
            $p[] = Participant::create([
                'name' => $name,
                'email' => 'participant' . ($i + 1) . '@campus.test',
            ]);
        }

        // 12 registrations through the many-to-many relationship (both statuses used)
        $e1->participants()->attach($p[0]->id, ['status' => 'registered']);
        $e1->participants()->attach($p[1]->id, ['status' => 'registered']);
        $e1->participants()->attach($p[2]->id, ['status' => 'waitlisted']);
        $e2->participants()->attach($p[0]->id, ['status' => 'registered']);
        $e2->participants()->attach($p[3]->id, ['status' => 'registered']);
        $e3->participants()->attach($p[1]->id, ['status' => 'registered']);
        $e3->participants()->attach($p[4]->id, ['status' => 'waitlisted']);
        $e3->participants()->attach($p[5]->id, ['status' => 'registered']);
        $e4->participants()->attach($p[6]->id, ['status' => 'registered']);
        $e5->participants()->attach($p[2]->id, ['status' => 'registered']);
        $e5->participants()->attach($p[7]->id, ['status' => 'waitlisted']);
        $e6->participants()->attach($p[0]->id, ['status' => 'registered']);

        // 6 notes through the polymorphic relationship (3 on events, 3 on venues)
        $e1->notes()->create(['body' => 'Bring a valid school ID.']);
        $e3->notes()->create(['body' => 'Laptops required; chargers provided.']);
        $e5->notes()->create(['body' => 'Wear sports attire.']);
        $hall->notes()->create(['body' => 'Air conditioning is under repair.']);
        $lab->notes()->create(['body' => 'Maximum of 40 people for safety.']);
        $court->notes()->create(['body' => 'Closed if it rains heavily.']);
    }
}
