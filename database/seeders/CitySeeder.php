<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\District;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $cities = [
            [
                'name' => 'İstanbul',
                'slug' => 'istanbul',
                'plate_code' => 34,
                'districts' => [
                    'Kadıköy', 'Beşiktaş', 'Şişli', 'Beyoğlu', 'Üsküdar', 'Bakırköy', 
                    'Kartal', 'Maltepe', 'Pendik', 'Tuzla', 'Sarıyer', 'Ataşehir',
                    'Ümraniye', 'Bağcılar', 'Bahçelievler', 'Fatih', 'Zeytinburnu',
                ],
            ],
            [
                'name' => 'Ankara',
                'slug' => 'ankara',
                'plate_code' => 6,
                'districts' => [
                    'Çankaya', 'Keçiören', 'Yenimahalle', 'Mamak', 'Sincan', 'Etimesgut',
                    'Altındağ', 'Pursaklar', 'Gölbaşı', 'Polatlı', 'Akyurt', 'Beypazarı',
                ],
            ],
            [
                'name' => 'İzmir',
                'slug' => 'izmir',
                'plate_code' => 35,
                'districts' => [
                    'Konak', 'Bornova', 'Karşıyaka', 'Buca', 'Çiğli', 'Gaziemir',
                    'Bayraklı', 'Alsancak', 'Balçova', 'Narlıdere', 'Güzelbahçe',
                ],
            ],
            [
                'name' => 'Bursa',
                'slug' => 'bursa',
                'plate_code' => 16,
                'districts' => [
                    'Osmangazi', 'Nilüfer', 'Yıldırım', 'Mudanya', 'Gemlik', 'İnegöl',
                    'Mustafakemalpaşa', 'Orhangazi', 'Karacabey',
                ],
            ],
            [
                'name' => 'Antalya',
                'slug' => 'antalya',
                'plate_code' => 7,
                'districts' => [
                    'Muratpaşa', 'Kepez', 'Konyaaltı', 'Alanya', 'Manavgat', 'Kaş',
                    'Kemer', 'Serik', 'Kumluca', 'Finike',
                ],
            ],
            [
                'name' => 'Adana',
                'slug' => 'adana',
                'plate_code' => 1,
                'districts' => [
                    'Seyhan', 'Yüreğir', 'Çukurova', 'Sarıçam', 'Ceyhan', 'Kozan',
                    'İmamoğlu', 'Karaisalı', 'Karataş',
                ],
            ],
            [
                'name' => 'Gaziantep',
                'slug' => 'gaziantep',
                'plate_code' => 27,
                'districts' => [
                    'Şahinbey', 'Şehitkamil', 'Oğuzeli', 'Nizip', 'İslahiye', 'Nurdağı',
                    'Karkamış', 'Araban',
                ],
            ],
            [
                'name' => 'Konya',
                'slug' => 'konya',
                'plate_code' => 42,
                'districts' => [
                    'Selçuklu', 'Karatay', 'Meram', 'Akşehir', 'Ereğli', 'Beyşehir',
                    'Cihanbeyli', 'Ilgın', 'Kulu',
                ],
            ],
            [
                'name' => 'Kocaeli',
                'slug' => 'kocaeli',
                'plate_code' => 41,
                'districts' => [
                    'İzmit', 'Gebze', 'Darıca', 'Körfez', 'Gölcük', 'Karamürsel',
                    'Kandıra', 'Başiskele', 'Kartepe',
                ],
            ],
            [
                'name' => 'Mersin',
                'slug' => 'mersin',
                'plate_code' => 33,
                'districts' => [
                    'Yenişehir', 'Toroslar', 'Mezitli', 'Akdeniz', 'Tarsus', 'Erdemli',
                    'Silifke', 'Anamur', 'Mut',
                ],
            ],
        ];

        foreach ($cities as $cityData) {
            $districts = $cityData['districts'];
            unset($cityData['districts']);

            $city = City::updateOrCreate(
                ['slug' => $cityData['slug']],
                $cityData
            );

            foreach ($districts as $districtName) {
                District::updateOrCreate(
                    [
                        'city_id' => $city->id,
                        'name' => $districtName,
                    ],
                    [
                        'slug' => \Illuminate\Support\Str::slug($districtName),
                    ]
                );
            }
        }
    }
}

