@extends('layouts.app')
@section('css')
    <style>
        /** Added from this point */
        .twitter-typeahead {
            width: 97%;
        }

        .tt-dropdown-menu {
            width: 102%;
        }

        input.typeahead.tt-query {
            /* This is optional */
            width: 300px !important;
        }

        .typeahead {
            background-color: #fff;
            z-index: 1051;
        }

        .typeahead:focus {
            border: 2px solid #0097cf;
        }

        .tt-query {
            -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
            -moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
            box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
        }

        .tt-hint {
            color: #999
        }

        .tt-menu {
            width: 100%;
            margin: 12px 0;
            padding: 8px 0;
            background-color: #fff;
            border: 1px solid #ccc;
            border: 1px solid rgba(0, 0, 0, 0.2);
            -webkit-border-radius: 8px;
            -moz-border-radius: 8px;
            border-radius: 8px;
            -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, .2);
            -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, .2);
            box-shadow: 0 5px 10px rgba(0, 0, 0, .2);
        }

        .tt-suggestion {
            padding: 3px 20px;
            font-size: 18px;
            line-height: 24px;
        }

        .tt-suggestion:hover {
            cursor: pointer;
            color: #fff;
            background-color: #0097cf;
        }

        .tt-suggestion.tt-cursor {
            color: #fff;
            background-color: #0097cf;
        }

        .tt-suggestion p {
            margin: 0;
        }

        .gist {
            font-size: 14px;
        }

        #map {
            height: 400px;
            width: 100%;
        }
    </style>
@endsection
@section('content')
    <!-- ============================ Hero Banner  Start================================== -->
    <div class="image-cover hero-banner" style="background:#1373ea url(assets/img/banner-6.png) no-repeat;" data-overlay="0">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-md-11 col-sm-12">
                    <p class="lead-i text-light">กำลังหาร้านอาหารอยู่ใช่ไหม</p>
                    <h2>ให้เราช่วยหาให้ดีกว่า</h2>
                    <div class="full-search-2 eclip-search italian-search hero-search-radius shadow mt-5">
                        <div class="hero-search-content">
                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-12">
                                    <div class="form-group">
                                        <div class="input-with-icon">
                                            <select id="" class="form-control" v-model="distanceArea">
                                                <option value="1">1 กิโลเมตร</option>
                                                <option value="2">2 กิโลเมตร</option>
                                                <option value="3">3 กิโลเมตร</option>
                                                <option value="5">5 กิโลเมตร</option>
                                                <option value="10">10 กิโลเมตร</option>
                                                <option value="20">20 กิโลเมตร</option>
                                            </select>
                                            <i class="ti-dashboard"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">

                                <div class="col-lg-9 col-md-9 col-sm-8 col-9 px-xl-2 px-lg-2 px-md-2">
                                    <div class="form-group">
                                        <div class="input-with-icon">
                                            <input type="text" v-model="keyword" id="typeahead"
                                                class="form-control typeahead"  placeholder="ชื่อเมือง เขต อำเภอ ตำบล">
                                            <a href="javascript:void(0)" @click="getCurrentLocation()"><img src="assets/img/pin.svg" width="20"></i></a>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-3 col-sm-4 col-3 pr-5">
                                    <div class="form-group">
                                        <a href="javascript:void(0)" @click="searchPlaces()"
                                            class="btn search-btn">ค้นหา</a>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- ============================ Hero Banner End ================================== -->
    <section v-if="locationPermission == true || search == true">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div id="map"></div>
                </div>
            </div>
        </div>
    </section>
    <!-- ============================ All Property ================================== -->
    <section class="bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-12 col-md-12 text-center">
                    <div class="sec-heading center">
                        <h2>ร้านอาหารใกล้คุณ</h2>
                        <p>เราได้หาร้านอาหารใกล้คุณแล้วในระยะทาง @{{ distanceArea }} กิโลเมตร</p>
                        <div v-if="restaurants.length == 0" class="alert alert-danger" role="alert">
                            ไม่พบสถานที่ที่ค้นหาใกล้คุณกรุณาเปิด GPS และ อนุญาติให้เราเข้าถึงตำแหน่งของคุณ
                        </div>
                    </div>
                </div>
            </div>

            <div class="row list-layout">
                <!-- Single Property Start -->
                <div class="col-sm-12 col-lg-6 col-md-6" v-for="restaurant in restaurants">
                    <div class="property-listing property-1">

                        <div class="listing-img-wrapper">
                            <img v-if="restaurant.photo != null" :src="restaurant.photo" class="img-fluid mx-auto"
                                alt="" />
                            <img v-else src="https://placehold.co/1200x900?text=No%20picture" class="img-fluid mx-auto" alt="" />
                        </div>

                        <div class="listing-content">
                            <div class="listing-detail-wrapper-box">
                                <div class="listing-detail-wrapper">
                                    <div class="listing-short-detail">
                                        <h4 class="listing-name"><a href="javascript:void(0)"
                                                @click="navigateToLocation(restaurant.latitude,restaurant.longitude)">@{{ restaurant.name }}</a>
                                        </h4>
                                        <span class="reviews_text">@{{ restaurant.address }}</span><br>
                                        <span class="prt-types sale" v-for="type in restaurant.types"
                                            v-if="type == 'food' || type == 'restaurant'">@{{ type }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="listing-footer-wrapper mt-3">
                                <div class="listing-locate">
                                    <span class="reviews_text">ระยะทาง @{{ restaurant.caldistance }} กิโลเมตร</span>
                                </div>
                                <div class="listing-detail-btn">
                                    <a href="javascript:void(0)"
                                        @click="navigateToLocation(restaurant.latitude,restaurant.longitude)"
                                        class="more-btn">นำทาง</a>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
                <!-- Single Property End -->
            </div>
            <!-- Pagination -->
            <div class="row" v-if="pageToken != null && restaurants.length > 0">
                <div class="col-lg-12 col-md-12 col-sm-12 text-center">
                    <button href="javascript:void(0)" @click="loadmore(pageToken)"
                        class="btn btn-theme-light rounded">@{{ loadmoreBtnText }}</button>
                </div>
            </div>
        </div>
    </section>
    <!-- ============================ All Featured Property ================================== -->
@endsection

@section('scripts')
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAbuNCVYTBT2fRxY2HHV6TPKRomXrpVCl8&libraries=places&callback=Function.prototype"></script>
    <script src="{{ asset('assets/js/typeahead.js') }}"></script>
    <script>
        // เริ่มต้นใช้งาน vuejs
        new Vue({
            el: '#main-wrapper',
            data: {
                search: false,
                distanceArea: 5,
                results: [],
                restaurants: [],
                currentLatitude: '',
                currentLongitude: '',
                latitude: '',
                longitude: '',
                map: '',
                keyword: 'Bang sue',
                pageToken: '',
                loadmoreBtnText: 'โหลดเพิ่มเติม',
                locationPermission: false,
            },

            async mounted() {
                // กำหนดค่าเริ่มต้นการค้นหา Bang sue
                await this.searchPlaces()
                // ดึงคำที่เคยค้นหามาแสดงในรายการค้นหาล่าสุด
                await this.fetchWord();
                // ซ่อน preloader
                $('#preloader').delay(350).fadeOut('slow');
                $('body').delay(350).css({
                    'overflow': 'visible'
                });
            },
            methods: {
                getCurrentLocation() { // ดึงพิกัดปัจจุบัน         
                    // ตรวจสอบว่ามีการอนุญาติให้เข้าถึงพิกัดหรือไม่
                    if (navigator.geolocation) {
                        // ถ้าอนุญาติให้เข้าถึงพิกัดปัจจุบัน
                        navigator.geolocation.getCurrentPosition(
                            position => {
                                const latitude = position.coords.latitude;
                                const longitude = position.coords.longitude;
                                this.latitude = latitude;
                                this.longitude = longitude;
                                this.currentLatitude = latitude;
                                this.currentLongitude = longitude;
                                const data = {
                                    latitude: latitude,
                                    longitude: longitude,
                                    distance: 5
                                };

                                // ส่งพิกัดปัจจุบันไปค้นหาร้านอาหารที่อยู่ใกล้เคียง
                                axios
                                    .post('/api/get-nearby-restaurants', data)
                                    .then(response => {
                                        this.restaurants = response.data.restaurants;
                                        this.initMap(latitude,
                                        longitude); // สร้างแผนที่กำหนดพิกัดตำแหน่งปัจจุบัน
                                        this.addMarkers(); // สร้าง Marker ร้านค้าบนแผนที่     
                                        this.pageToken = response.data.pageToken; // รับค่า pageToken สำหรับใช้ในการโหลดเพิ่มเติม
                                    })
                                    .catch(error => {
                                        console.error('An error occurred while saving the location:',
                                            error);
                                    });
                            },
                            error => {
                                // ถ้าไม่อนุญาติให้เข้าถึงพิกัดที่ระบบตั้งไว้
                                this.initMap(13.8039324, 100.4774168);
                                this.addMarkers();
                            }
                        );
                    } else {
                        console.error('Geolocation is not supported by this browser.');
                    }
                },
                initMap(latitude, longitude) {
                    // สร้างแผนที่
                    this.map = new google.maps.Map(document.getElementById('map'), {
                        center: {
                            lat: latitude,
                            lng: longitude
                        }, // กำหนดจุดกึ่งกลาง
                        zoom: 12, // กำหนดขนาดการ zoom
                    });
                    // สร้าง Marker ตำแหน่งปัจจุบันด้วย lat และ lng ของตำแหน่งปัจจุบัน
                    const marker = new google.maps.Marker({
                        position: {
                            lat: latitude,
                            lng: longitude
                        },
                        map: this.map,
                        title: '',
                        label: 'ตำแหน่งของคุณ',
                    });
                },
                addMarkers() {
                    // เพิ่ม Marker ร้านค้า
                    this.restaurants.forEach((restaurant, i) => {
                        // สร้าง Marker ร้านค้าด้วย lat และ lng ของแต่ละร้านค้า
                        let marker = new google.maps.Marker({
                            position: {
                                lat: restaurant.latitude,
                                lng: restaurant.longitude
                            },
                            map: this.map,
                            title: restaurant.name,
                            label: restaurant.name,
                        });

                        // เพิ่ม Event ให้กับ Marker แต่ละตัว
                        marker.addListener("click", () => {
                            this.map.setZoom(20);
                            this.map.setCenter(marker.getPosition());
                        });
                    });
                },
                navigateToLocation(lat, lng) { // นำทางไปยังตำแหน่งที่เลือกโดยใช้ Google Maps
                    const navigateUrl =
                        `https://www.google.com/maps/dir/${this.latitude},${this.longitude}/${lat},${lng}`;
                    window.open(navigateUrl, '_blank');
                },
                searchPlaces() { // ค้นหาสถานที่เพื่อหาพิกัด
                    // ตรวจสอบว่ากรอกชื่อสถานที่หรือไม่
                    if (this.keyword == '') {
                        swal.fire({
                            title: 'กรุณากรอกชื่อสถานที่',
                            icon: 'warning',
                            confirmButtonText: 'ตกลง',
                        });
                        return false;
                    }
                    this.search = true;
                    // ค้นหาสถานที่ไปยัง Laravel API
                    axios.get(`/api/search-places/${this.keyword}`)
                        .then(response => {
                            if (response.data != false) {
                                this.results = response.data[0];
                                const data = {
                                    latitude: this.results.geometry.location.lat,
                                    longitude: this.results.geometry.location.lng,
                                    distance: this.distanceArea,
                                }
                                this.latitude = this.results.geometry.location.lat;
                                this.longitude = this.results.geometry.location.lng;
                                this.fetchWord(); // ดึงข้อมูลคำศัพท์จากไฟล์ keyword.json
                                // ส่งพิกัดที่ค้นหาไปค้นหาร้านอาหารที่อยู่ใกล้เคียง
                                axios.post('/api/get-nearby-restaurants', data)
                                    .then(response => {

                                        this.restaurants = response.data.restaurants;
                                        this.pageToken = response.data.pageToken;
                                        this.initMap(this.latitude, this
                                        .longitude); // สร้างแผนที่กำหนดพิกัดตำแหน่งที่ค้นหา
                                        this.addMarkers(); // สร้าง Marker ร้านค้าบนแผนที่

                                    })
                                    .catch(error => {
                                        console.error('An error occurred while saving the location:',
                                            error);
                                    });
                            } else {
                                swal.fire({
                                    title: 'ไม่พบสถานที่ที่ค้นหา',
                                    icon: 'warning',
                                    confirmButtonText: 'ตกลง',
                                });
                                this.search = false;
                            }
                        })
                        .catch(error => {
                            console.error('An error occurred while searching places:', error);
                        });
                },
                fetchWord() {
                    // ดึงข้อมูลคำศัพท์จากไฟล์ keyword.json
                    axios.get('/api/search-cache')
                        .then(response => {
                            // สร้างตัวแปร Bloodhound สำหรับการค้นหาคำศัพท์
                            var data = new Bloodhound({
                                datumTokenizer: Bloodhound.tokenizers.whitespace,
                                queryTokenizer: Bloodhound.tokenizers.whitespace,
                                local: response.data
                            });
                            // ลบคำสั่ง autocomplete ทิ้ง
                            $('.typeahead').typeahead('destroy'); 
                            // สร้าง autocomplete ใหม่
                            $('.typeahead').typeahead({
                                hint: true,
                                highlight: true, /* เปิดไฮไลต์จับคู่ตอนพิมพ์ */
                                minLength: 1 /* กำหนดให้เมื่อพิมพ์ 1 อักษรให้ค้นหาเลย */
                            }, {
                                source: data
                            }).on('typeahead:select', (event, suggestion) => {
                                this.keyword = suggestion;
                            })
                        })
                },

                async loadmore(token) { // โหลดเพิ่มเติมร้านอาหาร
                    // แสดงปุ่มโหลดเพิ่มเติมเป็น กำลังโหลด...
                    this.loadmoreBtnText = 'กำลังโหลด...';
                    const data = {
                        latitude: this.latitude,
                        longitude: this.longitude,
                        currentLatitude: this.currentLatitude,
                        currentLongitude: this.currentLongitude,
                        distance: this.distanceArea,
                        pageToken: token
                    }
                    // โหลดข้อมูลร้านอาหารจาก Laravel API
                    await axios.post('/api/get-nearby-restaurants', data)
                        .then(response => {
                            console.log(response.data.restaurants);
                            this.restaurants = this.restaurants.concat(response.data.restaurants);
                            this.loadmoreBtnText = 'โหลดเพิ่มเติม';
                            this.pageToken = response.data.pageToken != null ? response.data.pageToken :
                                null;
                        })
                        .catch(error => {
                            console.error('An error occurred while saving the location:',
                                error);
                        });
                },
            },
        });
    </script>
@endsection
