<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use File;

class GoogleMapController extends Controller
{
    // ฟังก์ชันสำหรับค้นหาร้านอาหารใกล้เคียง
    public function getNearbyRestaurants(Request $request)
    {
        $latitude = $request->input('latitude'); // รับค่า latitude จาก request
        $longitude = $request->input('longitude'); // รับค่า longitude จาก request
        $distance = $request->input('distance'); // รับค่าระยะทางจาก request
        $apiKey = 'AIzaSyAbuNCVYTBT2fRxY2HHV6TPKRomXrpVCl8'; // กำหนด API Key
        $pageToken = $request->input('pageToken'); // รับค่า pageToken จาก request
        $client = new Client(); // สร้าง Client GuzzleHttp เพื่อใช้ในการเรียก API

        try {
            // ส่ง request ไปยัง Google Places API
            $response = $client->get('https://maps.googleapis.com/maps/api/place/nearbysearch/json', [
                'query' => [
                    'key' => $apiKey, // API Key
                    'location' => $latitude . ',' . $longitude, // ตำแหน่งที่ต้องการค้นหาด้วย latitude,longitude
                    'radius' =>  $distance * 1000, // รัศมีการค้นหา
                    'type' => 'restaurant', // ประเภทที่ต้องการค้นหาคือร้านอาหาร
                    'pagetoken' => $pageToken, // token สำหรับเปลี่ยนหน้า หรือ ดูข้อมูลมากกว่า 20 ร้าน
                ],
            ]);

            $data = json_decode($response->getBody(), true); // แปลง JSON ที่ได้รับมาเป็น array
            $restaurants = []; // สร้างตัวแปร array เพื่อเก็บข้อมูลร้านอาหารที่ได้จากการค้นหา

            // ตรวจสอบว่ามีข้อมูลที่ส่งกลับมาหรือไม่
            if ($data['status'] === 'OK') {
                // วนลูปเพื่อดึงข้อมูลร้านอาหารแต่ละร้าน
                foreach ($data['results'] as $result) {
                    $restaurant = [
                        'id' => $result['place_id'],
                        'name' => $result['name'],
                        'address' => $result['vicinity'],
                        'photo' => isset($result['photos'][0]['photo_reference']) ? 'https://maps.googleapis.com/maps/api/place/photo?maxwidth=400&photoreference=' . $result['photos'][0]['photo_reference'] . '&key=' . $apiKey : null,
                        'rating' => isset($result['rating']) ? $result['rating'] : null,
                        'latitude' => $result['geometry']['location']['lat'],
                        'longitude' => $result['geometry']['location']['lng'],
                        'open_now' => isset($result['opening_hours']['open_now']) ? $result['opening_hours']['open_now'] : null,
                        'types' => $result['types'],
                        'caldistance' => $this->getDistance($latitude, $longitude, $result['geometry']['location']['lat'], $result['geometry']['location']['lng']) // คำนวณหาระยะทางระหว่างตำแหน่งปัจจุบันกับตำแหน่งของร้านอาหาร
                    ];
                    $restaurants[] = $restaurant; // เก็บข้อมูลร้านอาหารลงในตัวแปร array
                }
            } 
            

            // ส่งข้อมูลร้านอาหารกลับไปยัง client
            return response()->json(['restaurants' => $restaurants , 'pageToken' => $data['next_page_token'] ?? null]);
        } catch (\Exception $e) {
            // กรณี error ให้ส่ง response กลับไปยัง client
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // ฟังก์ชันสำหรับค้นหาสถานที่ต่างๆ
    public function searchPlaces($keyword){
        $keyword = $keyword; // ตัดช่องว่างหน้าและหลังข้อความ
        $apiKey = 'AIzaSyAbuNCVYTBT2fRxY2HHV6TPKRomXrpVCl8'; // กำหนด API Key
        $client = new Client(); // สร้าง Client GuzzleHttp เพื่อใช้ในการเรียก API
        try{
            // ส่ง request ไปยัง Google Places API
            $response = $client->get('https://maps.googleapis.com/maps/api/place/findplacefromtext/json', [
                'query' => [
                    'key' => $apiKey, // API Key
                    'input' => $keyword, // คำค้นหา
                    'inputtype' => 'textquery', // ประเภทของคำค้นหา
                    'fields' => 'formatted_address,name,geometry,place_id', // ข้อมูลที่ต้องการให้ส่งกลับมา
                ],
            ]);

            // แปลง JSON ที่ได้รับมาเป็น array
            $data = json_decode($response->getBody(), true);

            // ตรวจสอบว่ามีข้อมูลที่ส่งกลับมาหรือไม่
            if($data['status'] === 'OK'){
                // ดึงข้อมูลไฟล์ keyword.json ออกมา
                $filePath = public_path('search-cache/keyword.json');
                // แปลงข้อมูลในไฟล์ keyword.json เป็น array
                $readfile = collect(json_decode(File::get($filePath), true));
                // ค้าหาข้อมูลที่มี keyword ตรงกับที่ส่งมา
                $search = $readfile->where('keyword', $keyword)->first();
                // ถ้าไม่มีข้อมูลให้เพิ่มข้อมูลลงไปในไฟล์ keyword.json
                if ($search == null) {
                    $readfile->push(['keyword' => $keyword]);
                    File::put($filePath, json_encode($readfile));
                }
                // ส่งข้อมูลกลับไปยัง client
                return response()->json($data['candidates']);
            } else{
                return response()->json(false);
            }
        } catch (\Exception $e) {
            // กรณี error ให้ส่ง response กลับไปยัง client
            return response()->json(false);
        }
    }

    public function searchCache(){
        // ดึงไฟล์ keyword.json ออกมา
        $filePath = public_path('search-cache/keyword.json');
        // ตรวจสอบว่ามีไฟล์ keyword.json หรือไม่
        if (!File::exists($filePath)) { 
            // ถ้าไม่มีให้สร้างไฟล์ keyword.json ขึ้นมา      
            File::put($filePath, json_encode([]));
        }  
        // อ่านข้อมูลจากไฟล์ keyword.json และแปลงเป็น array
        $readfile = collect(json_decode(File::get($filePath), true));
        return response()->json($readfile->pluck('keyword'));
    }

    function getDistance($latitude1, $longitude1, $latitude2, $longitude2)
    {
        // คำนวณหาระยะทางระหว่าง 2 จุด
        $earthRadius = 6371; // รัศมีของโลก
        $dLat = deg2rad($latitude2 - $latitude1); // แปลงค่า latitude ให้อยู่ในรูปแบบ radian
        $dLon = deg2rad($longitude2 - $longitude1); // แปลงค่า longitude ให้อยู่ในรูปแบบ radian
        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * sin($dLon / 2) * sin($dLon / 2); // คำนวณค่า a
        $c = 2 * asin(sqrt($a)); // คำนวณค่า c
        $distance = $earthRadius * $c; // คำนวณหาระยะทางระหว่างจุดทั้งสอง
        return number_format($distance,2); // ส่งค่าระยะทางกลับไป
    }
}
