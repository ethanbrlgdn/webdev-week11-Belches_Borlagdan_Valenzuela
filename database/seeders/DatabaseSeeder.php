<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Venue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        mt_srand(42);
        $now = now();

        $venues = [
            ['name' => 'Main Auditorium', 'address' => '1 Campus Drive', 'capacity' => 800],
            ['name' => 'Science Hall', 'address' => '12 Library Road', 'capacity' => 300],
            ['name' => 'Student Center', 'address' => '5 Garden Lane', 'capacity' => 450],
            ['name' => 'Sports Complex', 'address' => '20 Stadium Way', 'capacity' => 1200],
            ['name' => 'Open Courtyard', 'address' => '8 Central Plaza', 'capacity' => 600],
        ];
        foreach ($venues as $venue) {
            Venue::create($venue);
        }
        $venueIds = Venue::pluck('id')->all();

        $participants = [];
        for ($i = 1; $i <= 200; $i++) {
            $participants[] = [
                'name' => "Participant $i",
                'email' => "participant{$i}@campus.test",
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('participants')->insert($participants);

        $openers = [
            'Join us for an engaging session',
            'A hands-on gathering for students',
            'An open evening for the whole campus',
            'A friendly meetup with guest speakers',
            'A relaxed afternoon of activities',
        ];
        $middles = [
            'featuring live music and food stalls',
            'with group discussions and short talks',
            'including a workshop and networking time',
            'with games, prizes and community booths',
            'covering career tips and project demos',
        ];
        $closers = [
            'Everyone is welcome to attend.',
            'Registration is required.',
            'Seats are limited, so come early.',
            'Refreshments will be provided.',
            'Bring a friend along.',
        ];
        $types = ['Workshop', 'Concert', 'Seminar', 'Fair', 'Meetup', 'Talk', 'Tournament'];
        $dressCodes = ['casual', 'formal', 'smart casual', 'business', 'sportswear'];
        $tagPool = ['music', 'outdoor', 'food', 'workshop', 'networking', 'sports', 'art', 'talk'];

        foreach (array_chunk(range(1, 20000), 1000) as $chunk) {
            $rows = [];
            foreach ($chunk as $n) {
                $description = $openers[mt_rand(0, 4)] . ' ' . $middles[mt_rand(0, 4)] . '. ' . $closers[mt_rand(0, 4)];
                if ($n % 250 === 0) {
                    $description .= ' Bring a telescope for the stargazing session.';
                }

                $dressCode = ($n % 200 === 0) ? 'costume' : $dressCodes[mt_rand(0, 4)];
                $tags = array_values(array_unique([
                    $tagPool[mt_rand(0, 7)],
                    $tagPool[mt_rand(0, 7)],
                    $tagPool[mt_rand(0, 7)],
                ]));

                $rows[] = [
                    'venue_id' => $venueIds[mt_rand(0, count($venueIds) - 1)],
                    'title' => $types[mt_rand(0, 6)] . " #$n",
                    'description' => $description,
                    'starts_at' => $now->copy()->addDays(mt_rand(-180, 180))->setTime(mt_rand(8, 20), 0)->format('Y-m-d H:i:s'),
                    'details' => json_encode([
                        'dress_code' => $dressCode,
                        'capacity' => mt_rand(20, 500),
                        'tags' => $tags,
                    ]),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            DB::table('events')->insert($rows);
        }

        $eventIds = DB::table('events')->orderBy('id')->limit(100)->pluck('id');
        $participantIds = DB::table('participants')->pluck('id')->all();
        $registrations = [];
        foreach ($eventIds as $eventId) {
            shuffle($participantIds);
            foreach (array_slice($participantIds, 0, mt_rand(4, 8)) as $participantId) {
                $registrations[] = [
                    'event_id' => $eventId,
                    'participant_id' => $participantId,
                    'status' => mt_rand(1, 100) <= 25 ? 'waitlisted' : 'registered',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }
        DB::table('event_participant')->insert($registrations);

        $firstVenues = Venue::orderBy('id')->limit(3)->get();
        foreach ($firstVenues as $venue) {
            $venue->notes()->create(['body' => "Check the sound system before events at {$venue->name}."]);
        }
        $firstEvents = Event::orderBy('id')->limit(3)->get();
        foreach ($firstEvents as $event) {
            $event->notes()->create(['body' => "Remember to confirm the guest list for {$event->title}."]);
        }
    }
}