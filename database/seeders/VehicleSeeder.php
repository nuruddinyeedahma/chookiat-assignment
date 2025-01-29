<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vehicles = [
            [
                'make' => 'Toyota',
                'model' => 'Camry',
                'year' => 2023,
                'color' => 'Silver',
                'price' => 1299000.00,
                'stock' => 5,
                'description' => 'รถยนต์ซีดานขนาดกลางที่มีประสิทธิภาพการใช้น้ำมันที่ดี',
            ],
            [
                'make' => 'Honda',
                'model' => 'CR-V',
                'year' => 2024,
                'color' => 'Blue',
                'price' => 1459000.00,
                'stock' => 3,
                'description' => 'รถ SUV ขนาดกะทัดรัดพร้อมระบบความปลอดภัยขั้นสูง',
            ],
            [
                'make' => 'Ford',
                'model' => 'Ranger',
                'year' => 2023,
                'color' => 'Black',
                'price' => 999000.00,
                'stock' => 4,
                'description' => 'กระบะที่มีประสิทธิภาพสูงพร้อมตัวเลือกเครื่องยนต์ที่ทรงพลัง',
            ],
            [
                'make' => 'MG',
                'model' => 'ZS EV',
                'year' => 2024,
                'color' => 'White',
                'price' => 1179000.00,
                'stock' => 2,
                'description' => 'รถยนต์ไฟฟ้า SUV ที่ทันสมัยพร้อมเทคโนโลยีล้ำสมัย',
            ],
            [
                'make' => 'BMW',
                'model' => 'X5',
                'year' => 2023,
                'color' => 'Gray',
                'price' => 4999000.00,
                'stock' => 3,
                'description' => 'รถ SUV หรูพร้อมคุณสมบัติระดับพรีเมียม',
            ],
            [
                'make' => 'Isuzu',
                'model' => 'D-Max',
                'year' => 2024,
                'color' => 'Red',
                'price' => 869000.00,
                'stock' => 6,
                'description' => 'กระบะที่แข็งแกร่งและประหยัดน้ำมัน',
            ],
            [
                'make' => 'Mercedes-Benz',
                'model' => 'C-Class',
                'year' => 2023,
                'color' => 'Black',
                'price' => 2899000.00,
                'stock' => 2,
                'description' => 'รถซีดานหรูพร้อมเทคโนโลยีล้ำสมัย',
            ],
            [
                'make' => 'Mazda',
                'model' => 'CX-5',
                'year' => 2024,
                'color' => 'Soul Red Crystal',
                'price' => 1359000.00,
                'stock' => 4,
                'description' => 'ครอสโอเวอร์ SUV สไตล์สปอร์ตหรูหรา',
            ],
            [
                'make' => 'Nissan',
                'model' => 'Kicks e-POWER',
                'year' => 2023,
                'color' => 'Blue',
                'price' => 989000.00,
                'stock' => 3,
                'description' => 'รถครอสโอเวอร์ไฮบริดประหยัดพลังงาน',
            ],
            [
                'make' => 'Mitsubishi',
                'model' => 'Pajero Sport',
                'year' => 2024,
                'color' => 'White Diamond',
                'price' => 1459000.00,
                'stock' => 2,
                'description' => 'รถ PPV สมรรถนะสูงเหมาะสำหรับทุกการใช้งาน',
            ],
        ];

        foreach ($vehicles as $vehicle) {
            Vehicle::create($vehicle);
        }
    }
}
