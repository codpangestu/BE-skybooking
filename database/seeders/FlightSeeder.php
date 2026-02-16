<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Airport;
use App\Models\Airline;
use App\Models\Flight;
use App\Models\FlightSegment;
use App\Models\FlightClass;
use App\Models\FlightSeat;
use App\Models\Facility;
use Illuminate\Support\Facades\DB;

class FlightSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            DB::beginTransaction();

            // 1. Create Airports
            $airport1 = Airport::updateOrCreate(['iata_code' => 'CGK'], [
                'name' => 'Soekarno-Hatta International Airport',
                'city' => 'Jakarta',
                'country' => 'Indonesia',
                'image' => 'https://via.placeholder.com/150'
            ]);

            $airport2 = Airport::updateOrCreate(['iata_code' => 'DPS'], [
                'name' => 'Ngurah Rai International Airport',
                'city' => 'Denpasar',
                'country' => 'Indonesia',
                'image' => 'https://via.placeholder.com/150'
            ]);

            // 2. Create Airline
            $airline = Airline::updateOrCreate(['code' => 'SBA'], [
                'name' => 'SkyBooking Air',
                'logo' => 'https://via.placeholder.com/150'
            ]);

            // 3. Create Flight
            $flight = Flight::updateOrCreate(['flight_number' => 'SB-101'], [
                'airline_id' => $airline->id
            ]);

            // 4. Create Flight Segments
            FlightSegment::updateOrCreate([
                'flight_id' => $flight->id,
                'airport_id' => $airport1->id,
                'sequence' => 1
            ], [
                'time' => '2026-02-13 08:00:00'
            ]);

            FlightSegment::updateOrCreate([
                'flight_id' => $flight->id,
                'airport_id' => $airport2->id,
                'sequence' => 2
            ], [
                'time' => '2026-02-13 11:00:00'
            ]);

            // 5. Create Facilities
            $facility1 = Facility::updateOrCreate(['name' => 'Wi-Fi'], [
                'image' => 'https://via.placeholder.com/150',
                'description' => 'High-speed internet access available throughout the flight.'
            ]);
            $facility2 = Facility::updateOrCreate(['name' => 'Meal'], [
                'image' => 'https://via.placeholder.com/150',
                'description' => 'Complimentary hot meals and beverages.'
            ]);

            // 6. Create Flight Class
            $class = FlightClass::updateOrCreate([
                'flight_id' => $flight->id,
                'class_type' => 'economy'
            ], [
                'price' => 1200000,
                'total_seats' => 50
            ]);

            $class->facilities()->syncWithoutDetaching([$facility1->id, $facility2->id]);

            // 7. Create Flight Seats
            for ($i = 1; $i <= 5; $i++) {
                FlightSeat::updateOrCreate([
                    'flight_id' => $flight->id,
                    'name' => '1' . chr(64 + $i)
                ], [
                    'row' => '1',
                    'column' => chr(64 + $i),
                    'class_type' => 'economy',
                    'is_available' => true
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
