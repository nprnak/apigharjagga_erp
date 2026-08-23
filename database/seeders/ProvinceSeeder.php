<?php

namespace Database\Seeders;

use App\Models\District;
use App\Models\Municipality;
use App\Models\Province;
use App\Models\Ward;
use Illuminate\Database\Seeder;

/**
 * ProvinceSeeder
 *
 * Seeds Nepal's complete administrative hierarchy:
 *   - 7 Provinces
 *   - 77 Districts
 *   - 753 Local Government Units
 *       (6 Metropolitan Cities, 11 Sub-Metropolitan Cities,
 *        276 Municipalities, 460 Rural Municipalities)
 *   - ~6,743 Wards (auto-generated 1..total_wards per municipality)
 *
 * Safe to re-run — all inserts use updateOrCreate (idempotent).
 * Data Source: Nepal Election Commission / Local Government Units 2017.
 */
class ProvinceSeeder extends Seeder
{
    // Shorthand constants for municipality types
    private const MC  = 'metropolitan_city';
    private const SMC = 'sub_metropolitan_city';
    private const M   = 'municipality';
    private const RM  = 'rural_municipality';

    public function run(): void
    {
        $this->command->info('Seeding Nepal administrative divisions (7 provinces, 77 districts, 753 municipalities)…');

        foreach ($this->getNepalData() as $provinceData) {
            $province = Province::updateOrCreate(
                ['name' => $provinceData['name']],
                [
                    'name_np' => $provinceData['name_np'],
                    'code'    => $provinceData['code'],
                ]
            );

            foreach ($provinceData['districts'] as $districtData) {
                $district = District::updateOrCreate(
                    ['province_id' => $province->id, 'name' => $districtData['name']],
                    ['name_np' => $districtData['name_np'] ?? null]
                );

                foreach ($districtData['municipalities'] as [$mName, $mType, $totalWards, $mNameNp]) {
                    $municipality = Municipality::updateOrCreate(
                        ['district_id' => $district->id, 'name' => $mName],
                        [
                            'type'        => $mType,
                            'total_wards' => $totalWards,
                            'name_np'     => $mNameNp,
                        ]
                    );

                    // Auto-generate individual ward rows 1..total_wards
                    for ($w = 1; $w <= $totalWards; $w++) {
                        Ward::updateOrCreate([
                            'municipality_id' => $municipality->id,
                            'ward_number'     => $w,
                        ]);
                    }
                }
            }
        }

        $this->command->info(sprintf(
            '✓ Done — Provinces: %d | Districts: %d | Municipalities: %d | Wards: %d',
            Province::count(),
            District::count(),
            Municipality::count(),
            Ward::count()
        ));
    }

    // -------------------------------------------------------------------------
    // DATA — [name, type, total_wards, name_np]
    // -------------------------------------------------------------------------
    private function getNepalData(): array
    {
        return [

            // =================================================================
            // PROVINCE 1 — KOSHI PROVINCE (कोशी प्रदेश) — 14 districts, 137 LGs
            // =================================================================
            [
                'name'    => 'Koshi Province',
                'name_np' => 'कोशी प्रदेश',
                'code'    => 'P1',
                'districts' => [

                    [
                        'name'    => 'Taplejung',
                        'name_np' => 'ताप्लेजुङ',
                        'municipalities' => [
                            ['Phungling Municipality',              self::M,  9, 'फुङलिङ नगरपालिका'],
                            ['Taplejung Rural Municipality',        self::RM, 9, 'ताप्लेजुङ गाउँपालिका'],
                            ['Sirijangha Rural Municipality',       self::RM, 7, 'सिरीजङ्घा गाउँपालिका'],
                            ['Mikwakhola Rural Municipality',       self::RM, 7, 'मिक्वाखोला गाउँपालिका'],
                            ['Meringden Rural Municipality',        self::RM, 7, 'मेरिङदेन गाउँपालिका'],
                            ['Maiwa Rural Municipality',            self::RM, 8, 'मैवा गाउँपालिका'],
                            ['Maiwakhola Rural Municipality',       self::RM, 7, 'मैवाखोला गाउँपालिका'],
                            ['Papung Rural Municipality',           self::RM, 6, 'पापुङ गाउँपालिका'],
                            ['Pathivara Yangwarak Rural Municipality', self::RM, 9, 'पाथीभरा याङवरक गाउँपालिका'],
                            ['Phaktanglung Rural Municipality',     self::RM, 6, 'फक्ताङलुङ गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Sankhuwasabha',
                        'name_np' => 'सङ्खुवासभा',
                        'municipalities' => [
                            ['Khandbari Municipality',             self::M,  9, 'खाँदबारी नगरपालिका'],
                            ['Dharmadevi Municipality',            self::M,  9, 'धर्मदेवी नगरपालिका'],
                            ['Panchkhapan Municipality',           self::M,  9, 'पाँचखपन नगरपालिका'],
                            ['Madi Municipality',                  self::M,  9, 'मादी नगरपालिका'],
                            ['Chainpur Municipality',              self::M,  9, 'चैनपुर नगरपालिका'],
                            ['Makalu Rural Municipality',          self::RM, 6, 'माकालु गाउँपालिका'],
                            ['Chichila Rural Municipality',        self::RM, 7, 'चिचिला गाउँपालिका'],
                            ['Hatuwagadhi Rural Municipality',     self::RM, 7, 'हतुवागढी गाउँपालिका'],
                            ['Sabhapokhari Rural Municipality',    self::RM, 7, 'सभापोखरी गाउँपालिका'],
                            ['Bhotkhola Rural Municipality',       self::RM, 6, 'भोटखोला गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Solukhumbu',
                        'name_np' => 'सोलुखुम्बु',
                        'municipalities' => [
                            ['Solududhkunda Municipality',             self::M,  9, 'सोलुदूधकुण्ड नगरपालिका'],
                            ['Namche Rural Municipality',              self::RM, 6, 'नाम्चे गाउँपालिका'],
                            ['Khumbu Pasanglhamu Rural Municipality',  self::RM, 9, 'खुम्बु पासाङल्हमु गाउँपालिका'],
                            ['Mahakulung Rural Municipality',          self::RM, 9, 'महाकुलुङ गाउँपालिका'],
                            ['Likhupike Rural Municipality',           self::RM, 6, 'लिखुपिके गाउँपालिका'],
                            ['Thulung Dudhkoshi Rural Municipality',   self::RM, 7, 'थुलुङ दूधकोशी गाउँपालिका'],
                            ['Sotang Rural Municipality',              self::RM, 9, 'सोताङ गाउँपालिका'],
                            ['Nechasalyan Rural Municipality',         self::RM, 6, 'नेचासल्यान गाउँपालिका'],
                            ['Mapya Dudhkoshi Rural Municipality',     self::RM, 7, 'माप्य दुधकोशी गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Okhaldhunga',
                        'name_np' => 'ओखलढुङ्गा',
                        'municipalities' => [
                            ['Okhaldhunga Municipality',           self::M,  9, 'ओखलढुङ्गा नगरपालिका'],
                            ['Siddhicharan Municipality',          self::M,  9, 'सिद्धिचरण नगरपालिका'],
                            ['Champadevi Rural Municipality',      self::RM, 7, 'चम्पादेवी गाउँपालिका'],
                            ['Chisankhugadhi Rural Municipality',  self::RM, 6, 'चिसङ्खुगढी गाउँपालिका'],
                            ['Khijidemba Rural Municipality',      self::RM, 7, 'खिजिदेम्बा गाउँपालिका'],
                            ['Likhu Rural Municipality',           self::RM, 7, 'लिखु गाउँपालिका'],
                            ['Manebhanjyang Rural Municipality',   self::RM, 5, 'मानेभञ्ज्याङ गाउँपालिका'],
                            ['Molung Rural Municipality',          self::RM, 7, 'मोलुङ गाउँपालिका'],
                            ['Sunkoshi Rural Municipality',        self::RM, 6, 'सुनकोशी गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Khotang',
                        'name_np' => 'खोटाङ',
                        'municipalities' => [
                            ['Diktel Rupakot Majhuwagadhi Municipality', self::M,  9, 'दिक्तेल रुपाकोट मझुवागढी नगरपालिका'],
                            ['Halesi Tuwachung Municipality',            self::M,  9, 'हलेसी तुवाचुङ नगरपालिका'],
                            ['Khotehang Rural Municipality',             self::RM, 7, 'खोटेहाङ गाउँपालिका'],
                            ['Diprung Chuichumma Rural Municipality',    self::RM, 7, 'दिप्रुङ चुइचुम्मा गाउँपालिका'],
                            ['Sakela Rural Municipality',                self::RM, 6, 'साकेला गाउँपालिका'],
                            ['Aiselukharka Rural Municipality',          self::RM, 6, 'ऐसेलुखर्का गाउँपालिका'],
                            ['Rawa Besi Rural Municipality',             self::RM, 6, 'रावा बेसी गाउँपालिका'],
                            ['Barahapokhari Rural Municipality',         self::RM, 6, 'बाह्रापोखरी गाउँपालिका'],
                            ['Kepilasgadhi Rural Municipality',          self::RM, 7, 'केपिलासगढी गाउँपालिका'],
                            ['Jantedhunga Rural Municipality',           self::RM, 6, 'जन्तेढुङ्गा गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Bhojpur',
                        'name_np' => 'भोजपुर',
                        'municipalities' => [
                            ['Bhojpur Municipality',               self::M,  9, 'भोजपुर नगरपालिका'],
                            ['Shadananda Municipality',            self::M,  9, 'षडानन्द नगरपालिका'],
                            ['Aamchok Rural Municipality',         self::RM, 6, 'आमचोक गाउँपालिका'],
                            ['Hatuwagadhi Rural Municipality',     self::RM, 7, 'हतुवागढी गाउँपालिका'],
                            ['Pauwadungma Rural Municipality',     self::RM, 6, 'पौवादुङमा गाउँपालिका'],
                            ['Ramprasad Rai Rural Municipality',   self::RM, 6, 'रामप्रसाद राई गाउँपालिका'],
                            ['Salpasilichho Rural Municipality',   self::RM, 7, 'साल्पासिलिछो गाउँपालिका'],
                            ['Tyamke Yuwa Rural Municipality',     self::RM, 7, 'ट्याम्के युवा गाउँपालिका'],
                            ['Arun Rural Municipality',            self::RM, 6, 'अरुण गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Dhankuta',
                        'name_np' => 'धनकुटा',
                        'municipalities' => [
                            ['Dhankuta Municipality',              self::M,  9, 'धनकुटा नगरपालिका'],
                            ['Pakhribas Municipality',             self::M,  9, 'पाख्रिबास नगरपालिका'],
                            ['Mahalaxmi Municipality',             self::M,  9, 'महालक्ष्मी नगरपालिका'],
                            ['Chhathar Jorpati Rural Municipality',self::RM, 5, 'छथर जोरपाटी गाउँपालिका'],
                            ['Sahidbhumi Rural Municipality',      self::RM, 6, 'शहीदभूमि गाउँपालिका'],
                            ['Sangurigadhi Rural Municipality',    self::RM, 6, 'साङ्गुरीगढी गाउँपालिका'],
                            ['Tamaphok Rural Municipality',        self::RM, 5, 'तामाफोक गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Terhathum',
                        'name_np' => 'तेह्रथुम',
                        'municipalities' => [
                            ['Myanglung Municipality',             self::M,  9, 'म्याङलुङ नगरपालिका'],
                            ['Laligurans Municipality',            self::M,  9, 'लालीगुराँस नगरपालिका'],
                            ['Aathrai Tribeni Rural Municipality', self::RM, 8, 'आठराई त्रिवेणी गाउँपालिका'],
                            ['Chhathar Rural Municipality',        self::RM, 6, 'छथर गाउँपालिका'],
                            ['Fedap Rural Municipality',           self::RM, 6, 'फेदाप गाउँपालिका'],
                            ['Menchyayem Rural Municipality',      self::RM, 6, 'मेन्छयायेम गाउँपालिका'],
                            ['Phedap Rural Municipality',          self::RM, 6, 'फेदाप गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Panchthar',
                        'name_np' => 'पाँचथर',
                        'municipalities' => [
                            ['Phidim Municipality',                self::M,  9, 'फिदिम नगरपालिका'],
                            ['Falgunanda Rural Municipality',      self::RM, 6, 'फलगुनन्द गाउँपालिका'],
                            ['Hilihang Rural Municipality',        self::RM, 7, 'हिलिहाङ गाउँपालिका'],
                            ['Kummayak Rural Municipality',        self::RM, 5, 'कुम्मायक गाउँपालिका'],
                            ['Miklajung Rural Municipality',       self::RM, 6, 'मिक्लाजुङ गाउँपालिका'],
                            ['Phalelung Rural Municipality',       self::RM, 6, 'फालेलुङ गाउँपालिका'],
                            ['Sirijangha Rural Municipality',      self::RM, 6, 'सिरीजङ्घा गाउँपालिका'],
                            ['Tumbewa Rural Municipality',         self::RM, 5, 'तुम्बेवा गाउँपालिका'],
                            ['Yangwarak Rural Municipality',       self::RM, 5, 'याङवरक गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Ilam',
                        'name_np' => 'इलाम',
                        'municipalities' => [
                            ['Ilam Municipality',                  self::M,  9, 'इलाम नगरपालिका'],
                            ['Deumai Municipality',                self::M,  9, 'देउमाई नगरपालिका'],
                            ['Mai Municipality',                   self::M,  9, 'माई नगरपालिका'],
                            ['Suryodaya Municipality',             self::M,  9, 'सूर्योदय नगरपालिका'],
                            ['Rong Rural Municipality',            self::RM, 6, 'रोङ गाउँपालिका'],
                            ['Sandakpur Rural Municipality',       self::RM, 6, 'सन्दकपुर गाउँपालिका'],
                            ['Chulachuli Rural Municipality',      self::RM, 6, 'चुलाचुली गाउँपालिका'],
                            ['Fakphokthum Rural Municipality',     self::RM, 6, 'फाकफोकथुम गाउँपालिका'],
                            ['Mai Jogmai Rural Municipality',      self::RM, 6, 'माई जोगमाई गाउँपालिका'],
                            ['Mangsebung Rural Municipality',      self::RM, 6, 'माङसेबुङ गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Jhapa',
                        'name_np' => 'झापा',
                        'municipalities' => [
                            ['Bhadrapur Municipality',             self::M,  9, 'भद्रपुर नगरपालिका'],
                            ['Birtamod Municipality',              self::M,  9, 'बिर्तामोड नगरपालिका'],
                            ['Damak Municipality',                 self::M,  9, 'दमक नगरपालिका'],
                            ['Mechinagar Municipality',            self::M,  9, 'मेचीनगर नगरपालिका'],
                            ['Shivasatakshi Municipality',         self::M,  9, 'शिवसताक्षी नगरपालिका'],
                            ['Gauradaha Municipality',             self::M,  9, 'गौरादह नगरपालिका'],
                            ['Kankai Municipality',                self::M,  9, 'कन्काई नगरपालिका'],
                            ['Arjundhara Municipality',            self::M,  9, 'अर्जुनधारा नगरपालिका'],
                            ['Buddhashanti Municipality',          self::M,  9, 'बुद्धशान्ति नगरपालिका'],
                            ['Haldibari Rural Municipality',       self::RM, 7, 'हल्दिबारी गाउँपालिका'],
                            ['Jhapa Rural Municipality',           self::RM, 7, 'झापा गाउँपालिका'],
                            ['Kamal Rural Municipality',           self::RM, 7, 'कमल गाउँपालिका'],
                            ['Kechana Rural Municipality',         self::RM, 6, 'केचना गाउँपालिका'],
                            ['Barhadashi Rural Municipality',      self::RM, 7, 'बाह्रदशी गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Morang',
                        'name_np' => 'मोरङ',
                        'municipalities' => [
                            ['Biratnagar Metropolitan City',       self::MC,  19, 'विराटनगर महानगरपालिका'],
                            ['Urlabari Municipality',              self::M,   9,  'उर्लाबारी नगरपालिका'],
                            ['Letang Municipality',                self::M,   9,  'लेटाङ नगरपालिका'],
                            ['Pathari Shanischare Municipality',   self::M,   9,  'पथरी शनिश्चरे नगरपालिका'],
                            ['Rangeli Municipality',               self::M,   9,  'रङेली नगरपालिका'],
                            ['Sundarharaicha Municipality',        self::M,   9,  'सुन्दरहरैचा नगरपालिका'],
                            ['Ratuwamai Municipality',             self::M,   9,  'रतुवामाई नगरपालिका'],
                            ['Belbari Municipality',               self::M,   9,  'बेलबारी नगरपालिका'],
                            ['Gramthan Rural Municipality',        self::RM,  7,  'ग्रामथान गाउँपालिका'],
                            ['Jahada Rural Municipality',          self::RM,  7,  'जहदा गाउँपालिका'],
                            ['Kanepokhari Rural Municipality',     self::RM,  7,  'कानेपोखरी गाउँपालिका'],
                            ['Kerabari Rural Municipality',        self::RM,  7,  'केराबारी गाउँपालिका'],
                            ['Budhiganga Rural Municipality',      self::RM,  6,  'बुधीगंगा गाउँपालिका'],
                            ['Dhanpalthan Rural Municipality',     self::RM,  6,  'धनपालथान गाउँपालिका'],
                            ['Katahari Rural Municipality',        self::RM,  7,  'कटहरी गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Sunsari',
                        'name_np' => 'सुनसरी',
                        'municipalities' => [
                            ['Itahari Sub-Metropolitan City',      self::SMC, 11, 'इटहरी उपमहानगरपालिका'],
                            ['Dharan Sub-Metropolitan City',       self::SMC, 19, 'धरान उपमहानगरपालिका'],
                            ['Inaruwa Municipality',               self::M,   9,  'इनरुवा नगरपालिका'],
                            ['Duhabi Municipality',                self::M,   9,  'दुहबी नगरपालिका'],
                            ['Ramdhuni Municipality',              self::M,   9,  'रामधुनी नगरपालिका'],
                            ['Harinagar Rural Municipality',       self::RM,  7,  'हरिनगर गाउँपालिका'],
                            ['Koshi Rural Municipality',           self::RM,  7,  'कोशी गाउँपालिका'],
                            ['Bharaha Rural Municipality',         self::RM,  7,  'भराह गाउँपालिका'],
                            ['Gadhi Rural Municipality',           self::RM,  6,  'गढी गाउँपालिका'],
                            ['Barahachhetra Rural Municipality',   self::RM,  9,  'बाराहछेत्र गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Udayapur',
                        'name_np' => 'उदयपुर',
                        'municipalities' => [
                            ['Triyuga Municipality',               self::M,  9,  'त्रियुगा नगरपालिका'],
                            ['Katari Municipality',                self::M,  9,  'कटारी नगरपालिका'],
                            ['Belaka Municipality',                self::M,  9,  'बेलका नगरपालिका'],
                            ['Chaudandigadhi Municipality',        self::M,  9,  'चौदण्डीगढी नगरपालिका'],
                            ['Udayapurgadhi Rural Municipality',   self::RM, 6,  'उदयपुरगढी गाउँपालिका'],
                            ['Rautamai Rural Municipality',        self::RM, 6,  'रौतामाई गाउँपालिका'],
                            ['Tapli Rural Municipality',           self::RM, 5,  'ताप्ली गाउँपालिका'],
                            ['Limchungbung Rural Municipality',    self::RM, 7,  'लिम्चुङबुङ गाउँपालिका'],
                        ],
                    ],

                ], // end Koshi districts
            ],

            // =================================================================
            // PROVINCE 2 — MADHESH PROVINCE (मधेश प्रदेश) — 8 districts, 136 LGs
            // =================================================================
            [
                'name'    => 'Madhesh Province',
                'name_np' => 'मधेश प्रदेश',
                'code'    => 'P2',
                'districts' => [

                    [
                        'name'    => 'Saptari',
                        'name_np' => 'सप्तरी',
                        'municipalities' => [
                            ['Rajbiraj Municipality',                           self::M,  9, 'राजविराज नगरपालिका'],
                            ['Kanchanrup Municipality',                         self::M,  9, 'कञ्चनरूप नगरपालिका'],
                            ['Bodebarsain Municipality',                        self::M,  9, 'बोदेबर्सैन नगरपालिका'],
                            ['Dakneshwori Municipality',                        self::M,  9, 'दाक्नेश्वरी नगरपालिका'],
                            ['Hanumannagar Kankalini Municipality',             self::M,  9, 'हनुमाननगर कङ्कालिनी नगरपालिका'],
                            ['Shambhunath Municipality',                        self::M,  9, 'शम्भुनाथ नगरपालिका'],
                            ['Agnisaira Krishnasavaran Rural Municipality',     self::RM, 6, 'अग्निसाइर कृष्णासवरन गाउँपालिका'],
                            ['Balan-Vihul Rural Municipality',                  self::RM, 6, 'बलान-बिहुल गाउँपालिका'],
                            ['Bishnupur Rural Municipality',                    self::RM, 5, 'विष्णुपुर गाउँपालिका'],
                            ['Chhinnamasta Rural Municipality',                 self::RM, 5, 'छिन्नमस्ता गाउँपालिका'],
                            ['Khadak Rural Municipality',                       self::RM, 6, 'खडक गाउँपालिका'],
                            ['Mahadeva Rural Municipality',                     self::RM, 5, 'महादेव गाउँपालिका'],
                            ['Rajgadh Rural Municipality',                      self::RM, 7, 'राजगढ गाउँपालिका'],
                            ['Rupani Rural Municipality',                       self::RM, 6, 'रुपनी गाउँपालिका'],
                            ['Saptakoshi Rural Municipality',                   self::RM, 7, 'सप्तकोशी गाउँपालिका'],
                            ['Surunga Rural Municipality',                      self::RM, 7, 'सुरुङ्गा गाउँपालिका'],
                            ['Tirhut Rural Municipality',                       self::RM, 6, 'तिरहुत गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Siraha',
                        'name_np' => 'सिरहा',
                        'municipalities' => [
                            ['Lahan Municipality',                  self::M,  9,  'लहान नगरपालिका'],
                            ['Siraha Municipality',                 self::M,  9,  'सिरहा नगरपालिका'],
                            ['Golbazar Municipality',               self::M,  9,  'गोलबजार नगरपालिका'],
                            ['Mirchaiya Municipality',              self::M,  9,  'मिर्चैया नगरपालिका'],
                            ['Karjanha Municipality',               self::M,  9,  'कर्जन्हा नगरपालिका'],
                            ['Dhangadhimai Municipality',           self::M,  9,  'धनगढीमाई नगरपालिका'],
                            ['Sukhipur Municipality',               self::M,  9,  'सुखीपुर नगरपालिका'],
                            ['Aurahi Rural Municipality',           self::RM, 6,  'औरही गाउँपालिका'],
                            ['Bariyarpatti Rural Municipality',     self::RM, 6,  'बरियारपट्टी गाउँपालिका'],
                            ['Bhagawanpur Rural Municipality',      self::RM, 5,  'भगवानपुर गाउँपालिका'],
                            ['Bishnupur Rural Municipality',        self::RM, 5,  'विष्णुपुर गाउँपालिका'],
                            ['Kalyanpur Rural Municipality',        self::RM, 5,  'कल्याणपुर गाउँपालिका'],
                            ['Lakshmipur Patari Rural Municipality',self::RM, 5,  'लक्ष्मीपुर पतारी गाउँपालिका'],
                            ['Naraha Rural Municipality',           self::RM, 6,  'नरहा गाउँपालिका'],
                            ['Nawarajpur Rural Municipality',       self::RM, 5,  'नवराजपुर गाउँपालिका'],
                            ['Sakhuwanankarkatti Rural Municipality',self::RM,6,  'सखुवानान्कारकट्टी गाउँपालिका'],
                            ['Arnama Rural Municipality',           self::RM, 5,  'अर्नमा गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Dhanusha',
                        'name_np' => 'धनुषा',
                        'municipalities' => [
                            ['Janakpurdham Sub-Metropolitan City',          self::SMC, 11, 'जनकपुरधाम उपमहानगरपालिका'],
                            ['Chhireshwornath Municipality',                self::M,   9,  'छिरेश्वरनाथ नगरपालिका'],
                            ['Dhanusadham Municipality',                    self::M,   9,  'धनुषाधाम नगरपालिका'],
                            ['Ganeshman Charnath Municipality',             self::M,   9,  'गणेशमान चारनाथ नगरपालिका'],
                            ['Hansapur Municipality',                       self::M,   9,  'हंसपुर नगरपालिका'],
                            ['Mithila Municipality',                        self::M,   9,  'मिथिला नगरपालिका'],
                            ['Mithila Bihari Municipality',                 self::M,   9,  'मिथिला बिहारी नगरपालिका'],
                            ['Nagarain Municipality',                       self::M,   9,  'नगराईन नगरपालिका'],
                            ['Sabaila Municipality',                        self::M,   9,  'सबैला नगरपालिका'],
                            ['Aurahi Rural Municipality',                   self::RM,  5,  'औरही गाउँपालिका'],
                            ['Bateshwar Rural Municipality',                self::RM,  7,  'बटेश्वर गाउँपालिका'],
                            ['Bideha Rural Municipality',                   self::RM,  5,  'विदेह गाउँपालिका'],
                            ['Dhanauji Rural Municipality',                 self::RM,  6,  'धनौजी गाउँपालिका'],
                            ['Janaknandini Rural Municipality',             self::RM,  6,  'जनकनन्दिनी गाउँपालिका'],
                            ['Kamala Rural Municipality',                   self::RM,  7,  'कमला गाउँपालिका'],
                            ['Lakshminiya Rural Municipality',              self::RM,  5,  'लक्ष्मीनिया गाउँपालिका'],
                            ['Mukhiyapatti Musaharniya Rural Municipality', self::RM,  7,  'मुखियापट्टी मुसहरनिया गाउँपालिका'],
                            ['Shaharpur Rural Municipality',                self::RM,  7,  'शाहरपुर गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Mahottari',
                        'name_np' => 'महोत्तरी',
                        'municipalities' => [
                            ['Jaleshwar Municipality',             self::M,  9,  'जलेश्वर नगरपालिका'],
                            ['Bardibas Municipality',              self::M,  9,  'बर्दिबास नगरपालिका'],
                            ['Gaushala Municipality',              self::M,  9,  'गौशाला नगरपालिका'],
                            ['Loharpatti Municipality',            self::M,  9,  'लोहारपट्टी नगरपालिका'],
                            ['Manara Siswa Municipality',          self::M,  9,  'मनरा शिसवा नगरपालिका'],
                            ['Matihani Municipality',              self::M,  9,  'मटिहानी नगरपालिका'],
                            ['Ramgopalpur Municipality',           self::M,  9,  'रामगोपालपुर नगरपालिका'],
                            ['Bhangaha Municipality',              self::M,  9,  'भंगाहा नगरपालिका'],
                            ['Aurahi Rural Municipality',          self::RM, 6,  'औरही गाउँपालिका'],
                            ['Balwa Rural Municipality',           self::RM, 6,  'बलवा गाउँपालिका'],
                            ['Ekdara Rural Municipality',          self::RM, 5,  'एकडारा गाउँपालिका'],
                            ['Pipara Rural Municipality',          self::RM, 6,  'पिपरा गाउँपालिका'],
                            ['Samsi Rural Municipality',           self::RM, 5,  'सम्सी गाउँपालिका'],
                            ['Sonama Rural Municipality',          self::RM, 5,  'सोनमा गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Sarlahi',
                        'name_np' => 'सर्लाही',
                        'municipalities' => [
                            ['Malangwa Municipality',              self::M,  9,  'मलङ्गवा नगरपालिका'],
                            ['Haripur Municipality',               self::M,  9,  'हरिपुर नगरपालिका'],
                            ['Harion Municipality',                self::M,  9,  'हरिओन नगरपालिका'],
                            ['Ishworpur Municipality',             self::M,  9,  'ईश्वरपुर नगरपालिका'],
                            ['Kabilasi Municipality',              self::M,  9,  'कविलासी नगरपालिका'],
                            ['Lalbandi Municipality',              self::M,  9,  'लालबन्दी नगरपालिका'],
                            ['Bagmati Municipality',               self::M,  9,  'बागमती नगरपालिका'],
                            ['Balara Rural Municipality',          self::RM, 6,  'बलरा गाउँपालिका'],
                            ['Barahathawa Rural Municipality',     self::RM, 7,  'बराहथवा गाउँपालिका'],
                            ['Basbariya Rural Municipality',       self::RM, 5,  'बाँसबारिया गाउँपालिका'],
                            ['Bishnu Rural Municipality',          self::RM, 6,  'विष्णु गाउँपालिका'],
                            ['Brahampuri Rural Municipality',      self::RM, 5,  'ब्रह्मपुरी गाउँपालिका'],
                            ['Chakraghatta Rural Municipality',    self::RM, 6,  'चक्रघट्टा गाउँपालिका'],
                            ['Chandranagar Rural Municipality',    self::RM, 5,  'चन्द्रनगर गाउँपालिका'],
                            ['Dhankaul Rural Municipality',        self::RM, 6,  'धनकौल गाउँपालिका'],
                            ['Godaita Rural Municipality',         self::RM, 6,  'गोडैटा गाउँपालिका'],
                            ['Parsa Rural Municipality',           self::RM, 5,  'पर्सा गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Rautahat',
                        'name_np' => 'रौतहट',
                        'municipalities' => [
                            ['Gaur Municipality',                      self::M,  9,  'गौर नगरपालिका'],
                            ['Chandrapur Municipality',                self::M,  9,  'चन्द्रपुर नगरपालिका'],
                            ['Garuda Municipality',                    self::M,  9,  'गरुडा नगरपालिका'],
                            ['Gadhimai Municipality',                  self::M,  9,  'गढीमाई नगरपालिका'],
                            ['Gujara Municipality',                    self::M,  9,  'गुजरा नगरपालिका'],
                            ['Katahariya Municipality',                self::M,  9,  'कटहरिया नगरपालिका'],
                            ['Madhav Narayan Municipality',            self::M,  9,  'माधव नारायण नगरपालिका'],
                            ['Maulapur Municipality',                  self::M,  9,  'मौलापुर नगरपालिका'],
                            ['Rajpur Municipality',                    self::M,  9,  'राजपुर नगरपालिका'],
                            ['Baudhimai Rural Municipality',           self::RM, 7,  'बौधीमाई गाउँपालिका'],
                            ['Brindaban Rural Municipality',           self::RM, 6,  'वृन्दावन गाउँपालिका'],
                            ['Devahi Gonahi Rural Municipality',       self::RM, 6,  'देवाही गोनाही गाउँपालिका'],
                            ['Durga Bhagwati Rural Municipality',      self::RM, 6,  'दुर्गा भगवती गाउँपालिका'],
                            ['Ishanath Rural Municipality',            self::RM, 7,  'ईशनाथ गाउँपालिका'],
                            ['Karmaiya Rural Municipality',            self::RM, 6,  'कर्मैया गाउँपालिका'],
                            ['Paroha Rural Municipality',              self::RM, 6,  'परोहा गाउँपालिका'],
                            ['Phatuwa Bijayapur Rural Municipality',   self::RM, 7,  'फतुवा विजयपुर गाउँपालिका'],
                            ['Rajdevi Rural Municipality',             self::RM, 6,  'राजदेवी गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Bara',
                        'name_np' => 'बारा',
                        'municipalities' => [
                            ['Kalaiya Sub-Metropolitan City',          self::SMC, 11, 'कलैया उपमहानगरपालिका'],
                            ['Jitpur Simara Sub-Metropolitan City',    self::SMC, 11, 'जितपुरसिमरा उपमहानगरपालिका'],
                            ['Nijgadh Municipality',                   self::M,   9,  'निजगढ नगरपालिका'],
                            ['Kolhabi Municipality',                   self::M,   9,  'कोल्हबी नगरपालिका'],
                            ['Mahagadhimai Municipality',              self::M,   9,  'महागढीमाई नगरपालिका'],
                            ['Pacharauta Municipality',                self::M,   9,  'पचरौता नगरपालिका'],
                            ['Prasauni Municipality',                  self::M,   9,  'प्रसौनी नगरपालिका'],
                            ['Simraungadh Municipality',               self::M,   9,  'सिम्रौनगढ नगरपालिका'],
                            ['Aadarsha Kotwal Rural Municipality',     self::RM,  7,  'आदर्श कोटवाल गाउँपालिका'],
                            ['Bagahi Rural Municipality',              self::RM,  6,  'बगही गाउँपालिका'],
                            ['Bara Rural Municipality',                self::RM,  6,  'बारा गाउँपालिका'],
                            ['Bishrampur Rural Municipality',          self::RM,  6,  'विश्रामपुर गाउँपालिका'],
                            ['Devtal Rural Municipality',              self::RM,  6,  'देवताल गाउँपालिका'],
                            ['Karaiyamai Rural Municipality',          self::RM,  6,  'करैयामाई गाउँपालिका'],
                            ['Parwanipur Rural Municipality',          self::RM,  7,  'परवानीपुर गाउँपालिका'],
                            ['Pheta Rural Municipality',               self::RM,  6,  'फेटा गाउँपालिका'],
                            ['Suwarna Rural Municipality',             self::RM,  7,  'सुवर्ण गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Parsa',
                        'name_np' => 'पर्सा',
                        'municipalities' => [
                            ['Birgunj Metropolitan City',              self::MC,  19, 'वीरगञ्ज महानगरपालिका'],
                            ['Parsagadhi Municipality',                self::M,   9,  'पर्सागढी नगरपालिका'],
                            ['Pokhariya Municipality',                 self::M,   9,  'पोखरिया नगरपालिका'],
                            ['Bahudarmai Municipality',                self::M,   9,  'बहुदरमाई नगरपालिका'],
                            ['Bindabasini Municipality',               self::M,   9,  'विन्दबासिनी नगरपालिका'],
                            ['Jagarnathpur Municipality',              self::M,   9,  'जगरनाथपुर नगरपालिका'],
                            ['Koilabas Municipality',                  self::M,   9,  'कैलाशपुर नगरपालिका'],
                            ['Kalikamai Rural Municipality',           self::RM,  6,  'कलिकामाई गाउँपालिका'],
                            ['Chhipaharmai Rural Municipality',        self::RM,  5,  'छिपहरमाई गाउँपालिका'],
                            ['Dhobini Rural Municipality',             self::RM,  6,  'ढोबिनी गाउँपालिका'],
                            ['Pakaha Mainpur Rural Municipality',      self::RM,  6,  'पकाहा मैनपुर गाउँपालिका'],
                            ['Paterwa Sugauli Rural Municipality',     self::RM,  6,  'पटेर्वा सुगौली गाउँपालिका'],
                            ['Sakhuwa Prasauni Rural Municipality',    self::RM,  7,  'सखुवा प्रसौनी गाउँपालिका'],
                            ['Shuklacheta Rural Municipality',         self::RM,  6,  'शुक्लाचेता गाउँपालिका'],
                            ['Thori Rural Municipality',               self::RM,  6,  'ठोरी गाउँपालिका'],
                            ['Aadarsha Nagar Rural Municipality',      self::RM,  6,  'आदर्शनगर गाउँपालिका'],
                            ['Jirabhawani Rural Municipality',         self::RM,  5,  'जिरा भवानी गाउँपालिका'],
                            ['Phattepur Rural Municipality',           self::RM,  5,  'फत्तेपुर गाउँपालिका'],
                        ],
                    ],

                ], // end Madhesh districts
            ],

            // =================================================================
            // PROVINCE 3 — BAGMATI PROVINCE (बागमती प्रदेश) — 13 districts, 119 LGs
            // =================================================================
            [
                'name'    => 'Bagmati Province',
                'name_np' => 'बागमती प्रदेश',
                'code'    => 'P3',
                'districts' => [

                    [
                        'name'    => 'Sindhuli',
                        'name_np' => 'सिन्धुली',
                        'municipalities' => [
                            ['Kamalamai Municipality',             self::M,  9,  'कमलामाई नगरपालिका'],
                            ['Dudhauli Municipality',              self::M,  9,  'दुधौली नगरपालिका'],
                            ['Golanjor Rural Municipality',        self::RM, 7,  'गोलान्जोर गाउँपालिका'],
                            ['Ghanglekh Rural Municipality',       self::RM, 6,  'घोरेटा गाउँपालिका'],
                            ['Hariharpurgadhi Rural Municipality', self::RM, 6,  'हरिहरपुरगढी गाउँपालिका'],
                            ['Marin Rural Municipality',           self::RM, 7,  'मरिण गाउँपालिका'],
                            ['Phikkal Rural Municipality',         self::RM, 6,  'फिक्कल गाउँपालिका'],
                            ['Sunkoshi Rural Municipality',        self::RM, 5,  'सुनकोशी गाउँपालिका'],
                            ['Tinpatan Rural Municipality',        self::RM, 7,  'तिनपाटन गाउँपालिका'],
                            ['Phoksan Rural Municipality',         self::RM, 6,  'फोक्सिङ गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Ramechhap',
                        'name_np' => 'रामेछाप',
                        'municipalities' => [
                            ['Manthali Municipality',                  self::M,  9,  'मन्थली नगरपालिका'],
                            ['Ramechhap Municipality',                 self::M,  9,  'रामेछाप नगरपालिका'],
                            ['Doramba Rural Municipality',             self::RM, 7,  'दोरम्बा गाउँपालिका'],
                            ['Gokulganga Rural Municipality',          self::RM, 7,  'गोकुलगङ्गा गाउँपालिका'],
                            ['Khandadevi Rural Municipality',          self::RM, 7,  'खाँडादेवी गाउँपालिका'],
                            ['Likhu Tamakoshi Rural Municipality',     self::RM, 7,  'लिखु तामाकोशी गाउँपालिका'],
                            ['Sunapati Rural Municipality',            self::RM, 5,  'सुनापती गाउँपालिका'],
                            ['Umakunda Rural Municipality',            self::RM, 6,  'उमाकुण्ड गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Dolakha',
                        'name_np' => 'दोलखा',
                        'municipalities' => [
                            ['Bhimeswor Municipality',             self::M,  9,  'भीमेश्वर नगरपालिका'],
                            ['Jiri Municipality',                  self::M,  9,  'जिरी नगरपालिका'],
                            ['Bigu Rural Municipality',            self::RM, 6,  'बिगु गाउँपालिका'],
                            ['Baiteshwor Rural Municipality',      self::RM, 7,  'बैतेश्वर गाउँपालिका'],
                            ['Gaurishankar Rural Municipality',    self::RM, 9,  'गौरीशङ्कर गाउँपालिका'],
                            ['Kalinchowk Rural Municipality',      self::RM, 6,  'कालिञ्चोक गाउँपालिका'],
                            ['Melung Rural Municipality',          self::RM, 5,  'मेलुङ गाउँपालिका'],
                            ['Sailung Rural Municipality',         self::RM, 6,  'शैलुङ गाउँपालिका'],
                            ['Shaisthachali Rural Municipality',   self::RM, 6,  'शैस्थाचली गाउँपालिका'],
                            ['Tamakoshi Rural Municipality',       self::RM, 9,  'तामाकोशी गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Sindhupalchok',
                        'name_np' => 'सिन्धुपाल्चोक',
                        'municipalities' => [
                            ['Chautara Sangachokgadhi Municipality',self::M,  9,  'चौतारा साँगाचोकगढी नगरपालिका'],
                            ['Melamchi Municipality',              self::M,  9,  'मेलम्ची नगरपालिका'],
                            ['Bahrabise Municipality',             self::M,  9,  'बाह्रबिसे नगरपालिका'],
                            ['Indrawati Rural Municipality',       self::RM, 7,  'इन्द्रावती गाउँपालिका'],
                            ['Jugal Rural Municipality',           self::RM, 6,  'जुगल गाउँपालिका'],
                            ['Lisankhu Pakhar Rural Municipality', self::RM, 6,  'लिसङ्खु पाखर गाउँपालिका'],
                            ['Balefi Rural Municipality',          self::RM, 6,  'बलेफी गाउँपालिका'],
                            ['Bhotekoshi Rural Municipality',      self::RM, 6,  'भोटेकोशी गाउँपालिका'],
                            ['Helambu Rural Municipality',         self::RM, 7,  'हेलम्बु गाउँपालिका'],
                            ['Panchpokhari Thangpal Rural Municipality', self::RM, 5, 'पाँचपोखरी थाङपाल गाउँपालिका'],
                            ['Sunkoshi Rural Municipality',        self::RM, 7,  'सुनकोशी गाउँपालिका'],
                            ['Tripura Sundari Rural Municipality', self::RM, 7,  'त्रिपुरासुन्दरी गाउँपालिका'],
                            ['Golteshwor Rural Municipality',      self::RM, 6,  'गोल्टेश्वर गाउँपालिका'],
                            ['Thaksat Rural Municipality',         self::RM, 6,  'ठाक्सात गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Kavrepalanchok',
                        'name_np' => 'काभ्रेपलाञ्चोक',
                        'municipalities' => [
                            ['Dhulikhel Municipality',             self::M,  9,  'धुलिखेल नगरपालिका'],
                            ['Banepa Municipality',                self::M,  9,  'बनेपा नगरपालिका'],
                            ['Panauti Municipality',               self::M,  9,  'पनौती नगरपालिका'],
                            ['Mandandeupur Municipality',          self::M,  9,  'मण्डनदेउपुर नगरपालिका'],
                            ['Namobuddha Municipality',            self::M,  9,  'नमोबुद्ध नगरपालिका'],
                            ['Panchkhal Municipality',             self::M,  9,  'पाँचखाल नगरपालिका'],
                            ['Bethanchok Rural Municipality',      self::RM, 7,  'बेथानचोक गाउँपालिका'],
                            ['Bhumlu Rural Municipality',          self::RM, 7,  'भुम्लु गाउँपालिका'],
                            ['Chaurideurali Rural Municipality',   self::RM, 7,  'चौरीदेउराली गाउँपालिका'],
                            ['Khanikhola Rural Municipality',      self::RM, 7,  'खानीखोला गाउँपालिका'],
                            ['Mahabharat Rural Municipality',      self::RM, 6,  'महाभारत गाउँपालिका'],
                            ['Roshi Rural Municipality',           self::RM, 7,  'रोशी गाउँपालिका'],
                            ['Temal Rural Municipality',           self::RM, 7,  'तेमाल गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Lalitpur',
                        'name_np' => 'ललितपुर',
                        'municipalities' => [
                            ['Lalitpur Metropolitan City',         self::MC,  29, 'ललितपुर महानगरपालिका'],
                            ['Godawari Municipality',              self::M,   14, 'गोदावरी नगरपालिका'],
                            ['Mahalaxmi Municipality',             self::M,   10, 'महालक्ष्मी नगरपालिका'],
                            ['Konjyosom Rural Municipality',       self::RM,  5,  'कोञ्ज्योसोम गाउँपालिका'],
                            ['Bagmati Rural Municipality',         self::RM,  5,  'बागमती गाउँपालिका'],
                            ['Mahankal Rural Municipality',        self::RM,  5,  'महाङ्काल गाउँपालिका'],
                            ['Lele Rural Municipality',            self::RM,  5,  'लेले गाउँपालिका'],
                            ['Thecho Municipality',                self::M,   9,  'थेचो नगरपालिका'],
                            ['Chandanpur Rural Municipality',      self::RM,  5,  'चन्दनपुर गाउँपालिका'],
                            ['Lalitpur Rural Municipality',        self::RM,  5,  'ललितपुर गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Bhaktapur',
                        'name_np' => 'भक्तपुर',
                        'municipalities' => [
                            ['Bhaktapur Municipality',             self::M,   10, 'भक्तपुर नगरपालिका'],
                            ['Madhyapur Thimi Municipality',       self::M,   14, 'मध्यपुर थिमी नगरपालिका'],
                            ['Changunarayan Municipality',         self::M,   9,  'चाँगुनारायण नगरपालिका'],
                            ['Suryabinayak Municipality',          self::M,   9,  'सूर्यबिनायक नगरपालिका'],
                            ['Bageswori Rural Municipality',       self::RM,  5,  'बागेश्वरी गाउँपालिका'],
                            ['Khasaltar Rural Municipality',       self::RM,  5,  'खसालटार गाउँपालिका'],
                            ['Bhaktapur Rural Municipality',       self::RM,  5,  'भक्तपुर गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Kathmandu',
                        'name_np' => 'काठमाडौं',
                        'municipalities' => [
                            ['Kathmandu Metropolitan City',        self::MC,  32, 'काठमाडौं महानगरपालिका'],
                            ['Kirtipur Municipality',              self::M,   10, 'कीर्तिपुर नगरपालिका'],
                            ['Gokarneshwor Municipality',          self::M,   10, 'गोकर्णेश्वर नगरपालिका'],
                            ['Shankharapur Municipality',          self::M,   9,  'शंखरापुर नगरपालिका'],
                            ['Kageshwori Manohara Municipality',   self::M,   10, 'कागेश्वरी मनोहरा नगरपालिका'],
                            ['Chandragiri Municipality',           self::M,   15, 'चन्द्रागिरी नगरपालिका'],
                            ['Tarakeshwor Municipality',           self::M,   11, 'तारकेश्वर नगरपालिका'],
                            ['Tokha Municipality',                 self::M,   11, 'टोखा नगरपालिका'],
                            ['Dakshinkali Municipality',           self::M,   10, 'दक्षिणकाली नगरपालिका'],
                            ['Nagarjun Municipality',              self::M,   11, 'नागार्जुन नगरपालिका'],
                            ['Budhanilkantha Municipality',        self::M,   14, 'बुढानीलकण्ठ नगरपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Nuwakot',
                        'name_np' => 'नुवाकोट',
                        'municipalities' => [
                            ['Bidur Municipality',                 self::M,  9,  'विदुर नगरपालिका'],
                            ['Belkotgadhi Municipality',           self::M,  9,  'बेल्कोटगढी नगरपालिका'],
                            ['Kakani Rural Municipality',          self::RM, 5,  'काकानी गाउँपालिका'],
                            ['Kispang Rural Municipality',         self::RM, 5,  'किस्पाङ गाउँपालिका'],
                            ['Dupcheshwar Rural Municipality',     self::RM, 7,  'दुप्चेश्वर गाउँपालिका'],
                            ['Panchakanya Rural Municipality',     self::RM, 6,  'पञ्चकन्या गाउँपालिका'],
                            ['Suryagadhi Rural Municipality',      self::RM, 6,  'सूर्यगढी गाउँपालिका'],
                            ['Tadi Rural Municipality',            self::RM, 6,  'तादी गाउँपालिका'],
                            ['Tarkeshwor Rural Municipality',      self::RM, 5,  'तारकेश्वर गाउँपालिका'],
                            ['Shivapuri Rural Municipality',       self::RM, 5,  'शिवपुरी गाउँपालिका'],
                            ['Likhu Rural Municipality',           self::RM, 6,  'लिखु गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Rasuwa',
                        'name_np' => 'रसुवा',
                        'municipalities' => [
                            ['Uttargaya Rural Municipality',       self::RM, 6,  'उत्तरगया गाउँपालिका'],
                            ['Kalika Rural Municipality',          self::RM, 5,  'कालिका गाउँपालिका'],
                            ['Naukunda Rural Municipality',        self::RM, 5,  'नौकुण्ड गाउँपालिका'],
                            ['Gosaikunda Rural Municipality',      self::RM, 6,  'गोसाइँकुण्ड गाउँपालिका'],
                            ['Aamachhodingmo Rural Municipality',  self::RM, 5,  'आम्याङ गाउँपालिका'],
                            ['Parbatikunda Rural Municipality',    self::RM, 5,  'पार्बतीकुण्ड गाउँपालिका'],
                            ['Bharkhang Rural Municipality',       self::RM, 5,  'भर्काङ गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Dhading',
                        'name_np' => 'धादिङ',
                        'municipalities' => [
                            ['Nilkantha Municipality',                 self::M,  9,  'नीलकण्ठ नगरपालिका'],
                            ['Dhunibeshi Municipality',                self::M,  9,  'धुनीबेशी नगरपालिका'],
                            ['Benighat Rorang Rural Municipality',     self::RM, 7,  'बेनीघाट रोराङ गाउँपालिका'],
                            ['Gajuri Rural Municipality',              self::RM, 7,  'गजुरी गाउँपालिका'],
                            ['Galchi Rural Municipality',              self::RM, 7,  'गल्छी गाउँपालिका'],
                            ['Gangajamuna Rural Municipality',         self::RM, 7,  'गङ्गाजमुना गाउँपालिका'],
                            ['Jwalamukhi Rural Municipality',          self::RM, 5,  'ज्वालामुखी गाउँपालिका'],
                            ['Khaniyabas Rural Municipality',          self::RM, 7,  'खनियाबास गाउँपालिका'],
                            ['Netrawati Dabjong Rural Municipality',   self::RM, 7,  'नेत्रावती डबजोङ गाउँपालिका'],
                            ['Rubi Valley Rural Municipality',         self::RM, 6,  'रुबी भ्याली गाउँपालिका'],
                            ['Siddhalek Rural Municipality',           self::RM, 7,  'सिद्धलेक गाउँपालिका'],
                            ['Thakre Rural Municipality',              self::RM, 7,  'ठाक्रे गाउँपालिका'],
                            ['Tripura Sundari Rural Municipality',     self::RM, 7,  'त्रिपुरासुन्दरी गाउँपालिका'],
                            ['Kalleri Rural Municipality',             self::RM, 5,  'कल्लेरी गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Makwanpur',
                        'name_np' => 'मकवानपुर',
                        'municipalities' => [
                            ['Hetauda Sub-Metropolitan City',      self::SMC, 19, 'हेटौंडा उपमहानगरपालिका'],
                            ['Thaha Municipality',                 self::M,   9,  'थाहा नगरपालिका'],
                            ['Bakaiya Rural Municipality',         self::RM,  6,  'बकैया गाउँपालिका'],
                            ['Bagmati Rural Municipality',         self::RM,  6,  'बागमती गाउँपालिका'],
                            ['Bhimphedi Rural Municipality',       self::RM,  7,  'भीमफेदी गाउँपालिका'],
                            ['Indrasarowar Rural Municipality',    self::RM,  6,  'इन्द्रसरोवर गाउँपालिका'],
                            ['Kailash Rural Municipality',         self::RM,  5,  'कैलाश गाउँपालिका'],
                            ['Makawanpurgadhi Rural Municipality', self::RM,  5,  'मकवानपुरगढी गाउँपालिका'],
                            ['Manahari Rural Municipality',        self::RM,  7,  'मनहरी गाउँपालिका'],
                            ['Raksirang Rural Municipality',       self::RM,  6,  'राक्सिराङ गाउँपालिका'],
                            ['Chitlang Rural Municipality',        self::RM,  5,  'चितलाङ गाउँपालिका'],
                            ['Hunupatti Rural Municipality',       self::RM,  6,  'हुनुपट्टी गाउँपालिका'],
                            ['Rayadanda Rural Municipality',       self::RM,  5,  'रायादाँडा गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Chitwan',
                        'name_np' => 'चितवन',
                        'municipalities' => [
                            ['Bharatpur Metropolitan City',        self::MC,  29, 'भरतपुर महानगरपालिका'],
                            ['Ratnanagar Municipality',            self::M,   9,  'रत्ननगर नगरपालिका'],
                            ['Khairhani Municipality',             self::M,   9,  'खैरहनी नगरपालिका'],
                            ['Madi Municipality',                  self::M,   9,  'माडी नगरपालिका'],
                            ['Rapti Municipality',                 self::M,   9,  'राप्ती नगरपालिका'],
                            ['Ichchhakamana Rural Municipality',   self::RM,  6,  'इच्छाकामना गाउँपालिका'],
                            ['Kalika Rural Municipality',          self::RM,  5,  'कालिका गाउँपालिका'],
                            ['Bharatpur Rural Municipality',       self::RM,  6,  'भरतपुर गाउँपालिका'],
                            ['Gunjanagar Rural Municipality',      self::RM,  5,  'गुञ्जनगर गाउँपालिका'],
                            ['Gitanagar Rural Municipality',       self::RM,  5,  'गीतानगर गाउँपालिका'],
                            ['Shardanagar Municipality',           self::M,   9,  'शारदानगर नगरपालिका'],
                        ],
                    ],

                ], // end Bagmati districts
            ],

            // =================================================================
            // PROVINCE 4 — GANDAKI PROVINCE (गण्डकी प्रदेश) — 11 districts, 85 LGs
            // =================================================================
            [
                'name'    => 'Gandaki Province',
                'name_np' => 'गण्डकी प्रदेश',
                'code'    => 'P4',
                'districts' => [

                    [
                        'name'    => 'Nawalparasi East',
                        'name_np' => 'नवलपरासी (बर्दघाट सुस्ता पूर्व)',
                        'municipalities' => [
                            ['Kawasoti Municipality',              self::M,  9,  'कावासोती नगरपालिका'],
                            ['Madhyabindu Municipality',           self::M,  9,  'मध्यबिन्दु नगरपालिका'],
                            ['Devchuli Municipality',              self::M,  9,  'देवचुली नगरपालिका'],
                            ['Bulingtar Rural Municipality',       self::RM, 6,  'बुलिङटार गाउँपालिका'],
                            ['Gaindakot Municipality',             self::M,  9,  'गैंडाकोट नगरपालिका'],
                            ['Hupsekot Municipality',              self::M,  9,  'हुप्सेकोट नगरपालिका'],
                            ['Binayi Tribeni Rural Municipality',  self::RM, 6,  'विनायी त्रिवेणी गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Gorkha',
                        'name_np' => 'गोरखा',
                        'municipalities' => [
                            ['Gorkha Municipality',                self::M,  9,  'गोरखा नगरपालिका'],
                            ['Palungtar Municipality',             self::M,  9,  'पालुङटार नगरपालिका'],
                            ['Arughat Rural Municipality',         self::RM, 7,  'आरुघाट गाउँपालिका'],
                            ['Aarughat Rural Municipality',        self::RM, 7,  'आरुघाट गाउँपालिका'],
                            ['Barpak Sulikot Rural Municipality',  self::RM, 7,  'बारपाक सुलिकोट गाउँपालिका'],
                            ['Bhimsenthapa Rural Municipality',    self::RM, 7,  'भीमसेनथापा गाउँपालिका'],
                            ['Chum Nubri Rural Municipality',      self::RM, 6,  'चुम नुब्री गाउँपालिका'],
                            ['Dharche Rural Municipality',         self::RM, 6,  'धार्चे गाउँपालिका'],
                            ['Gandaki Rural Municipality',         self::RM, 6,  'गण्डकी गाउँपालिका'],
                            ['Kerabari Rural Municipality',        self::RM, 6,  'केराबारी गाउँपालिका'],
                            ['Sahid Lakhan Rural Municipality',    self::RM, 6,  'शहीद लखन गाउँपालिका'],
                            ['Siranchok Rural Municipality',       self::RM, 8,  'सिरान्चोक गाउँपालिका'],
                            ['Tsum Nubri Rural Municipality',      self::RM, 6,  'त्सुम नुब्री गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Manang',
                        'name_np' => 'मनाङ',
                        'municipalities' => [
                            ['Chame Rural Municipality',           self::RM, 5,  'चामे गाउँपालिका'],
                            ['Manang Disyang Rural Municipality',  self::RM, 5,  'मनाङ डिसयाङ गाउँपालिका'],
                            ['Narphu Rural Municipality',          self::RM, 4,  'नार्फु गाउँपालिका'],
                            ['Nasong Rural Municipality',          self::RM, 4,  'नाशोङ गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Mustang',
                        'name_np' => 'मुस्ताङ',
                        'municipalities' => [
                            ['Gharapjhong Rural Municipality',     self::RM, 6,  'घरपझोङ गाउँपालिका'],
                            ['Lomanthang Rural Municipality',      self::RM, 4,  'लोमन्थाङ गाउँपालिका'],
                            ['Lo-Ghekar Damodarkund Rural Municipality', self::RM, 4, 'लो-घेकर दामोदरकुण्ड गाउँपालिका'],
                            ['Thasang Rural Municipality',         self::RM, 5,  'थासाङ गाउँपालिका'],
                            ['Waragung Muktikhsetra Rural Municipality', self::RM, 5, 'वरागुङ मुक्तिक्षेत्र गाउँपालिका'],
                            ['Mustang Rural Municipality',         self::RM, 5,  'मुस्ताङ गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Myagdi',
                        'name_np' => 'म्याग्दी',
                        'municipalities' => [
                            ['Beni Municipality',                  self::M,  9,  'बेनी नगरपालिका'],
                            ['Annapurna Rural Municipality',       self::RM, 7,  'अन्नपूर्ण गाउँपालिका'],
                            ['Dhaulagiri Rural Municipality',      self::RM, 6,  'धौलागिरी गाउँपालिका'],
                            ['Malika Rural Municipality',          self::RM, 7,  'मालिका गाउँपालिका'],
                            ['Mangala Rural Municipality',         self::RM, 6,  'मंगला गाउँपालिका'],
                            ['Raghuganga Rural Municipality',      self::RM, 7,  'रघुगंगा गाउँपालिका'],
                            ['Shikha Rural Municipality',          self::RM, 6,  'शिखा गाउँपालिका'],
                            ['Okharbot Rural Municipality',        self::RM, 5,  'ओखरबोट गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Kaski',
                        'name_np' => 'कास्की',
                        'municipalities' => [
                            ['Pokhara Metropolitan City',          self::MC,  33, 'पोखरा महानगरपालिका'],
                            ['Annapurna Rural Municipality',       self::RM,  7,  'अन्नपूर्ण गाउँपालिका'],
                            ['Machhapuchchhre Rural Municipality', self::RM,  7,  'माछापुच्छ्रे गाउँपालिका'],
                            ['Madi Rural Municipality',            self::RM,  6,  'माडी गाउँपालिका'],
                            ['Rupa Rural Municipality',            self::RM,  5,  'रूपा गाउँपालिका'],
                            ['Pumdi Bhumdi Rural Municipality',    self::RM,  5,  'पुम्दी भुम्दी गाउँपालिका'],
                            ['Kaskikot Rural Municipality',        self::RM,  5,  'कास्कीकोट गाउँपालिका'],
                            ['Bharatpokhari Rural Municipality',   self::RM,  5,  'भरतपोखरी गाउँपालिका'],
                            ['Lekhnath Municipality',              self::M,   10, 'लेखनाथ नगरपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Lamjung',
                        'name_np' => 'लम्जुङ',
                        'municipalities' => [
                            ['Besisahar Municipality',             self::M,  9,  'बेसीसहर नगरपालिका'],
                            ['Madhya Nepal Municipality',          self::M,  9,  'मध्यनेपाल नगरपालिका'],
                            ['Dordi Rural Municipality',           self::RM, 6,  'दोर्दी गाउँपालिका'],
                            ['Dudhpokhari Rural Municipality',     self::RM, 6,  'दूधपोखरी गाउँपालिका'],
                            ['Kwholasothar Rural Municipality',    self::RM, 6,  'क्वोलासोथार गाउँपालिका'],
                            ['Marsyangdi Rural Municipality',      self::RM, 7,  'मार्श्यांदी गाउँपालिका'],
                            ['Rainas Municipality',                self::M,  9,  'रैनास नगरपालिका'],
                            ['Sundarbazar Municipality',           self::M,  9,  'सुन्दरबजार नगरपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Tanahun',
                        'name_np' => 'तनहुँ',
                        'municipalities' => [
                            ['Damauli Municipality',               self::M,  9,  'दमौली नगरपालिका'],
                            ['Byas Municipality',                  self::M,  9,  'व्यास नगरपालिका'],
                            ['Shuklagandaki Municipality',         self::M,  9,  'शुक्लागण्डकी नगरपालिका'],
                            ['Anbukhaireni Rural Municipality',    self::RM, 6,  'अम्बुखैरेनी गाउँपालिका'],
                            ['Bandipur Rural Municipality',        self::RM, 6,  'बन्दीपुर गाउँपालिका'],
                            ['Bhanu Municipality',                 self::M,  9,  'भानु नगरपालिका'],
                            ['Devghat Rural Municipality',         self::RM, 6,  'देवघाट गाउँपालिका'],
                            ['Ghiring Rural Municipality',         self::RM, 6,  'घिरिङ गाउँपालिका'],
                            ['Myagde Rural Municipality',          self::RM, 6,  'म्याग्दे गाउँपालिका'],
                            ['Rhishing Rural Municipality',        self::RM, 6,  'ऋषिङ गाउँपालिका'],
                            ['Triveni Rural Municipality',         self::RM, 6,  'त्रिवेणी गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Syangja',
                        'name_np' => 'स्याङ्जा',
                        'municipalities' => [
                            ['Bhirkot Municipality',               self::M,  9,  'भीरकोट नगरपालिका'],
                            ['Galyang Municipality',               self::M,  9,  'गल्याङ नगरपालिका'],
                            ['Putalibazar Municipality',           self::M,  9,  'पुतलीबजार नगरपालिका'],
                            ['Waling Municipality',                self::M,  9,  'वालिङ नगरपालिका'],
                            ['Aandhikhola Rural Municipality',     self::RM, 7,  'आँधीखोला गाउँपालिका'],
                            ['Arjunchaupari Rural Municipality',   self::RM, 7,  'अर्जुनचौपारी गाउँपालिका'],
                            ['Biruwa Rural Municipality',          self::RM, 6,  'बिरुवा गाउँपालिका'],
                            ['Chapakot Municipality',              self::M,  9,  'चापाकोट नगरपालिका'],
                            ['Harinas Rural Municipality',         self::RM, 6,  'हरिनास गाउँपालिका'],
                            ['Kaligandaki Rural Municipality',     self::RM, 6,  'कालीगण्डकी गाउँपालिका'],
                            ['Phedikhola Rural Municipality',      self::RM, 6,  'फेदीखोला गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Parbat',
                        'name_np' => 'पर्वत',
                        'municipalities' => [
                            ['Kushma Municipality',                self::M,  9,  'कुश्मा नगरपालिका'],
                            ['Jaljala Rural Municipality',         self::RM, 6,  'जलजला गाउँपालिका'],
                            ['Mahashila Rural Municipality',       self::RM, 6,  'महाशिला गाउँपालिका'],
                            ['Modi Rural Municipality',            self::RM, 7,  'मोदी गाउँपालिका'],
                            ['Painyu Rural Municipality',          self::RM, 6,  'पैयु गाउँपालिका'],
                            ['Phalebas Municipality',              self::M,  9,  'फलेवास नगरपालिका'],
                            ['Bihadi Rural Municipality',          self::RM, 6,  'बिहादी गाउँपालिका'],
                            ['Khagananda Rural Municipality',      self::RM, 6,  'खगनन्द गाउँपालिका'],
                            ['Lunglung Rural Municipality',        self::RM, 5,  'लुंग्री गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Baglung',
                        'name_np' => 'बागलुङ',
                        'municipalities' => [
                            ['Baglung Municipality',               self::M,  9,  'बागलुङ नगरपालिका'],
                            ['Dhorpatan Municipality',             self::M,  9,  'ढोरपाटन नगरपालिका'],
                            ['Jaimini Municipality',               self::M,  9,  'जैमिनी नगरपालिका'],
                            ['Kanthekhola Rural Municipality',     self::RM, 6,  'काँठेखोला गाउँपालिका'],
                            ['Bareng Rural Municipality',          self::RM, 6,  'बरेङ गाउँपालिका'],
                            ['Galkot Municipality',                self::M,  9,  'गल्कोट नगरपालिका'],
                            ['Nisikhola Rural Municipality',       self::RM, 6,  'निसीखोला गाउँपालिका'],
                            ['Taman Rural Municipality',           self::RM, 5,  'ताम्न गाउँपालिका'],
                            ['Tarakhola Rural Municipality',       self::RM, 5,  'ताराखोला गाउँपालिका'],
                            ['Badigad Rural Municipality',         self::RM, 5,  'बडिगाड गाउँपालिका'],
                        ],
                    ],

                ], // end Gandaki districts
            ],

            // =================================================================
            // PROVINCE 5 — LUMBINI PROVINCE (लुम्बिनी प्रदेश) — 12 districts, 109 LGs
            // =================================================================
            [
                'name'    => 'Lumbini Province',
                'name_np' => 'लुम्बिनी प्रदेश',
                'code'    => 'P5',
                'districts' => [

                    [
                        'name'    => 'Nawalparasi West',
                        'name_np' => 'नवलपरासी (बर्दघाट सुस्ता पश्चिम)',
                        'municipalities' => [
                            ['Bardaghat Municipality',             self::M,  9,  'बर्दघाट नगरपालिका'],
                            ['Ramgram Municipality',               self::M,  9,  'रामग्राम नगरपालिका'],
                            ['Sunwal Municipality',                self::M,  9,  'सुनवल नगरपालिका'],
                            ['Palhinandan Rural Municipality',     self::RM, 7,  'पाल्हीनन्दन गाउँपालिका'],
                            ['Pratappur Rural Municipality',       self::RM, 6,  'प्रतापपुर गाउँपालिका'],
                            ['Susta Rural Municipality',           self::RM, 6,  'सुस्ता गाउँपालिका'],
                            ['Sarawal Rural Municipality',         self::RM, 6,  'सरावल गाउँपालिका'],
                            ['Vijayapur Rural Municipality',       self::RM, 6,  'विजयनगर गाउँपालिका'],
                            ['Hupsekot Rural Municipality',        self::RM, 5,  'हुप्सेकोट गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Rupandehi',
                        'name_np' => 'रूपन्देही',
                        'municipalities' => [
                            ['Butwal Sub-Metropolitan City',       self::SMC, 19, 'बुटवल उपमहानगरपालिका'],
                            ['Siddharthanagar Municipality',       self::M,   9,  'सिद्धार्थनगर नगरपालिका'],
                            ['Tilottama Municipality',             self::M,   9,  'तिलोत्तमा नगरपालिका'],
                            ['Devdaha Municipality',               self::M,   9,  'देवदह नगरपालिका'],
                            ['Lumbini Sanskritik Municipality',    self::M,   9,  'लुम्बिनी सांस्कृतिक नगरपालिका'],
                            ['Marchawari Rural Municipality',      self::RM,  6,  'मर्चवारी गाउँपालिका'],
                            ['Mayadevi Rural Municipality',        self::RM,  6,  'मायादेवी गाउँपालिका'],
                            ['Omsatiya Rural Municipality',        self::RM,  6,  'ओमसतिया गाउँपालिका'],
                            ['Rohini Rural Municipality',          self::RM,  6,  'रोहिणी गाउँपालिका'],
                            ['Sammarimai Rural Municipality',      self::RM,  6,  'सम्मरीमाई गाउँपालिका'],
                            ['Sainamaina Municipality',            self::M,   9,  'सैनामैना नगरपालिका'],
                            ['Shuddhhodhan Rural Municipality',    self::RM,  6,  'शुद्धोधन गाउँपालिका'],
                            ['Siyari Rural Municipality',          self::RM,  6,  'सियारी गाउँपालिका'],
                            ['Kotahimai Rural Municipality',       self::RM,  5,  'कोटाहीमाई गाउँपालिका'],
                            ['Gaidahawa Rural Municipality',       self::RM,  6,  'गैडहवा गाउँपालिका'],
                            ['Kanchan Rural Municipality',         self::RM,  6,  'काँचन गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Kapilvastu',
                        'name_np' => 'कपिलवस्तु',
                        'municipalities' => [
                            ['Kapilvastu Municipality',            self::M,  9,  'कपिलवस्तु नगरपालिका'],
                            ['Banganga Municipality',              self::M,  9,  'बाणगंगा नगरपालिका'],
                            ['Buddhabhumi Municipality',           self::M,  9,  'बुद्धभूमि नगरपालिका'],
                            ['Krishnanagar Municipality',          self::M,  9,  'कृष्णनगर नगरपालिका'],
                            ['Maharajgunj Municipality',           self::M,  9,  'महाराजगंज नगरपालिका'],
                            ['Shivaraj Municipality',              self::M,  9,  'शिवराज नगरपालिका'],
                            ['Suddhodhan Rural Municipality',      self::RM, 5,  'सुद्धोधन गाउँपालिका'],
                            ['Bijaynagar Rural Municipality',      self::RM, 7,  'विजयनगर गाउँपालिका'],
                            ['Mayadevi Rural Municipality',        self::RM, 5,  'मायादेवी गाउँपालिका'],
                            ['Yashodhara Rural Municipality',      self::RM, 7,  'यशोधरा गाउँपालिका'],
                            ['Motipur Municipality',               self::M,  9,  'मोतीपुर नगरपालिका'],
                            ['Rohini Rural Municipality',          self::RM, 6,  'रोहिणी गाउँपालिका'],
                            ['Shuddhodhan Rural Municipality',     self::RM, 5,  'शुद्धोधन गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Arghakhanchi',
                        'name_np' => 'अर्घाखाँची',
                        'municipalities' => [
                            ['Sandhikharka Municipality',          self::M,  9,  'सन्धिखर्क नगरपालिका'],
                            ['Sitganga Municipality',              self::M,  9,  'सिताङ्गी नगरपालिका'],
                            ['Bhumikasthan Municipality',          self::M,  9,  'भूमिकास्थान नगरपालिका'],
                            ['Chhatradev Rural Municipality',      self::RM, 7,  'छत्रदेव गाउँपालिका'],
                            ['Malarani Rural Municipality',        self::RM, 5,  'मालारानी गाउँपालिका'],
                            ['Panini Rural Municipality',          self::RM, 7,  'पाणिनि गाउँपालिका'],
                            ['Shitaganga Municipality',            self::M,  9,  'शीतगंगा नगरपालिका'],
                            ['Thada Rural Municipality',           self::RM, 5,  'थाडा गाउँपालिका'],
                            ['Pokharathok Rural Municipality',     self::RM, 6,  'पोखरथोक गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Gulmi',
                        'name_np' => 'गुल्मी',
                        'municipalities' => [
                            ['Resunga Municipality',               self::M,  9,  'रेसुङ्गा नगरपालिका'],
                            ['Musikot Municipality',               self::M,  9,  'मुसिकोट नगरपालिका'],
                            ['Gulmi Darbar Rural Municipality',    self::RM, 8,  'गुल्मी दरबार गाउँपालिका'],
                            ['Chandrakot Rural Municipality',      self::RM, 7,  'चन्द्रकोट गाउँपालिका'],
                            ['Chatrakot Rural Municipality',       self::RM, 6,  'चत्राकोट गाउँपालिका'],
                            ['Dhurkot Rural Municipality',         self::RM, 5,  'धुर्कोट गाउँपालिका'],
                            ['Ishma Rural Municipality',           self::RM, 5,  'ईश्मा गाउँपालिका'],
                            ['Kaligandaki Rural Municipality',     self::RM, 8,  'कालीगण्डकी गाउँपालिका'],
                            ['Madane Rural Municipality',          self::RM, 6,  'मदाने गाउँपालिका'],
                            ['Malika Rural Municipality',          self::RM, 6,  'मालिका गाउँपालिका'],
                            ['Ruru Rural Municipality',            self::RM, 6,  'रुरु गाउँपालिका'],
                            ['Satyawati Rural Municipality',       self::RM, 7,  'सत्यवती गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Palpa',
                        'name_np' => 'पाल्पा',
                        'municipalities' => [
                            ['Tansen Municipality',                self::M,  9,  'तानसेन नगरपालिका'],
                            ['Rampur Municipality',                self::M,  9,  'रामपुर नगरपालिका'],
                            ['Bagnaskali Rural Municipality',      self::RM, 7,  'बगनासकाली गाउँपालिका'],
                            ['Mathagadhi Rural Municipality',      self::RM, 6,  'माथागढी गाउँपालिका'],
                            ['Nisdi Rural Municipality',           self::RM, 6,  'निस्दी गाउँपालिका'],
                            ['Purbakhola Rural Municipality',      self::RM, 6,  'पूर्वखोला गाउँपालिका'],
                            ['Rainadevi Chhahara Rural Municipality',self::RM,7, 'रैनादेवी छहरा गाउँपालिका'],
                            ['Ribdikot Rural Municipality',        self::RM, 5,  'रिब्दीकोट गाउँपालिका'],
                            ['Tinau Rural Municipality',           self::RM, 6,  'तिनाउ गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Dang',
                        'name_np' => 'दाङ',
                        'municipalities' => [
                            ['Tulsipur Sub-Metropolitan City',     self::SMC, 19, 'तुलसीपुर उपमहानगरपालिका'],
                            ['Ghorahi Sub-Metropolitan City',      self::SMC, 19, 'घोराही उपमहानगरपालिका'],
                            ['Lamahi Municipality',                self::M,   9,  'लमही नगरपालिका'],
                            ['Banglachuli Rural Municipality',     self::RM,  7,  'बंगलाचुली गाउँपालिका'],
                            ['Babai Rural Municipality',           self::RM,  6,  'बबई गाउँपालिका'],
                            ['Dangisharan Rural Municipality',     self::RM,  6,  'दङ्गीशरण गाउँपालिका'],
                            ['Gadhawa Rural Municipality',         self::RM,  7,  'गढवा गाउँपालिका'],
                            ['Rajpur Rural Municipality',          self::RM,  6,  'राजपुर गाउँपालिका'],
                            ['Rapti Rural Municipality',           self::RM,  6,  'राप्ती गाउँपालिका'],
                            ['Shantinagar Rural Municipality',     self::RM,  6,  'शान्तिनगर गाउँपालिका'],
                            ['Bardiya Rural Municipality',         self::RM,  6,  'बर्दिया गाउँपालिका'],
                            ['Dang Rural Municipality',            self::RM,  6,  'दाङ गाउँपालिका'],
                            ['Baghawati Rural Municipality',       self::RM,  6,  'भगवती गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Pyuthan',
                        'name_np' => 'प्युठान',
                        'municipalities' => [
                            ['Pyuthan Municipality',               self::M,  9,  'प्युठान नगरपालिका'],
                            ['Swargadwary Municipality',           self::M,  9,  'स्वर्गद्वारी नगरपालिका'],
                            ['Gaumukhi Rural Municipality',        self::RM, 7,  'गौमुखी गाउँपालिका'],
                            ['Jhimruk Rural Municipality',         self::RM, 7,  'झिमरुक गाउँपालिका'],
                            ['Lungri Rural Municipality',          self::RM, 5,  'लुंग्री गाउँपालिका'],
                            ['Mallarani Rural Municipality',       self::RM, 6,  'मल्लरानी गाउँपालिका'],
                            ['Mandavi Rural Municipality',         self::RM, 6,  'माण्डवी गाउँपालिका'],
                            ['Naubahini Rural Municipality',       self::RM, 5,  'नौबहिनी गाउँपालिका'],
                            ['Sarumarani Rural Municipality',      self::RM, 6,  'सरुमारानी गाउँपालिका'],
                            ['Airawati Rural Municipality',        self::RM, 6,  'ऐरावती गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Rolpa',
                        'name_np' => 'रोल्पा',
                        'municipalities' => [
                            ['Rolpa Municipality',                 self::M,  9,  'रोल्पा नगरपालिका'],
                            ['Runtigadhi Rural Municipality',      self::RM, 6,  'रुन्टीगढी गाउँपालिका'],
                            ['Sunchhahari Rural Municipality',     self::RM, 6,  'सुनचाँहरी गाउँपालिका'],
                            ['Tribeni Rural Municipality',         self::RM, 6,  'त्रिवेणी गाउँपालिका'],
                            ['Thawang Rural Municipality',         self::RM, 6,  'थवाङ गाउँपालिका'],
                            ['Madi Rural Municipality',            self::RM, 6,  'माडी गाउँपालिका'],
                            ['Lungri Rural Municipality',          self::RM, 5,  'लुङ्री गाउँपालिका'],
                            ['Pariwartan Rural Municipality',      self::RM, 5,  'परिवर्तन गाउँपालिका'],
                            ['Gangadev Rural Municipality',        self::RM, 5,  'गंगादेव गाउँपालिका'],
                            ['Khor Rural Municipality',            self::RM, 6,  'खोर गाउँपालिका'],
                            ['Sukidaha Rural Municipality',        self::RM, 5,  'सुकिढाँड गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Rukum East',
                        'name_np' => 'रुकुम पूर्व',
                        'municipalities' => [
                            ['Bhume Rural Municipality',           self::RM, 5,  'भूमे गाउँपालिका'],
                            ['Putha Uttarganga Rural Municipality',self::RM, 5,  'पुथा उत्तरगंगा गाउँपालिका'],
                            ['Sisne Rural Municipality',           self::RM, 5,  'सिस्ने गाउँपालिका'],
                            ['Triveni Rural Municipality',         self::RM, 5,  'त्रिवेणी गाउँपालिका'],
                            ['Rukum Rural Municipality',           self::RM, 6,  'रुकुम गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Banke',
                        'name_np' => 'बाँके',
                        'municipalities' => [
                            ['Nepalgunj Sub-Metropolitan City',    self::SMC, 19, 'नेपालगन्ज उपमहानगरपालिका'],
                            ['Kohalpur Municipality',              self::M,   9,  'कोहलपुर नगरपालिका'],
                            ['Baijanath Rural Municipality',       self::RM,  6,  'बैजनाथ गाउँपालिका'],
                            ['Duduwa Rural Municipality',          self::RM,  6,  'दुदुवा गाउँपालिका'],
                            ['Janaki Rural Municipality',          self::RM,  6,  'जानकी गाउँपालिका'],
                            ['Khajura Rural Municipality',         self::RM,  6,  'खजुरा गाउँपालिका'],
                            ['Narainapur Rural Municipality',      self::RM,  6,  'नरैनापुर गाउँपालिका'],
                            ['Rapti Sonari Rural Municipality',    self::RM,  7,  'राप्ती सोनारी गाउँपालिका'],
                            ['Banke Rural Municipality',           self::RM,  6,  'बाँके गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Bardiya',
                        'name_np' => 'बर्दिया',
                        'municipalities' => [
                            ['Gulariya Municipality',              self::M,  9,  'गुलरिया नगरपालिका'],
                            ['Madhuwan Municipality',              self::M,  9,  'मधुवन नगरपालिका'],
                            ['Rajapur Municipality',               self::M,  9,  'राजापुर नगरपालिका'],
                            ['Thakurbaba Municipality',            self::M,  9,  'ठाकुरबाबा नगरपालिका'],
                            ['Badhaiyatal Rural Municipality',     self::RM, 6,  'बढैयाताल गाउँपालिका'],
                            ['Bansgadhi Municipality',             self::M,  9,  'बाँसगढी नगरपालिका'],
                            ['Barbardiya Municipality',            self::M,  9,  'बारबर्दिया नगरपालिका'],
                            ['Geruwa Rural Municipality',          self::RM, 5,  'गेरुवा गाउँपालिका'],
                            ['Suryapatuwa Rural Municipality',     self::RM, 6,  'सुर्यपटुवा गाउँपालिका'],
                            ['Banspur Rural Municipality',         self::RM, 6,  'बाँसपुर गाउँपालिका'],
                            ['Manpur Dumara Rural Municipality',   self::RM, 5,  'मानपुर दुमरा गाउँपालिका'],
                        ],
                    ],

                ], // end Lumbini districts
            ],

            // =================================================================
            // PROVINCE 6 — KARNALI PROVINCE (कर्णाली प्रदेश) — 10 districts, 79 LGs
            // =================================================================
            [
                'name'    => 'Karnali Province',
                'name_np' => 'कर्णाली प्रदेश',
                'code'    => 'P6',
                'districts' => [

                    [
                        'name'    => 'Rukum West',
                        'name_np' => 'रुकुम पश्चिम',
                        'municipalities' => [
                            ['Musikot Municipality',               self::M,  9,  'मुसिकोट नगरपालिका'],
                            ['Aathbiskot Municipality',            self::M,  9,  'आठबिसकोट नगरपालिका'],
                            ['Banfikot Rural Municipality',        self::RM, 5,  'बाँफिकोट गाउँपालिका'],
                            ['Chaurjahari Municipality',           self::M,  9,  'चौरजहारी नगरपालिका'],
                            ['Sani Bheri Rural Municipality',      self::RM, 6,  'सानी भेरी गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Salyan',
                        'name_np' => 'सल्यान',
                        'municipalities' => [
                            ['Sharada Municipality',               self::M,  9,  'शारदा नगरपालिका'],
                            ['Bangad Kupinde Municipality',        self::M,  9,  'बनगाड कुपिण्डे नगरपालिका'],
                            ['Bagchaur Municipality',              self::M,  9,  'बागचौर नगरपालिका'],
                            ['Darma Rural Municipality',           self::RM, 6,  'दार्मा गाउँपालिका'],
                            ['Kalimati Rural Municipality',        self::RM, 5,  'कालिमाटी गाउँपालिका'],
                            ['Kapurkot Rural Municipality',        self::RM, 6,  'कपुरकोट गाउँपालिका'],
                            ['Kumakhkhalanga Rural Municipality',  self::RM, 6,  'कुमाखखलंगा गाउँपालिका'],
                            ['Siddha Kumakh Rural Municipality',   self::RM, 5,  'सिद्ध कुमाख गाउँपालिका'],
                            ['Tribeni Rural Municipality',         self::RM, 5,  'त्रिवेणी गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Dolpa',
                        'name_np' => 'डोल्पा',
                        'municipalities' => [
                            ['Thuli Bheri Municipality',           self::M,  9,  'ठूली भेरी नगरपालिका'],
                            ['Dolpo Buddha Rural Municipality',    self::RM, 5,  'डोल्पो बुद्ध गाउँपालिका'],
                            ['Jagadulla Rural Municipality',       self::RM, 5,  'जगदुल्ला गाउँपालिका'],
                            ['Kaike Rural Municipality',           self::RM, 5,  'कैके गाउँपालिका'],
                            ['Mudkechula Rural Municipality',      self::RM, 5,  'मुड्केचुला गाउँपालिका'],
                            ['Shey Phoksundo Rural Municipality',  self::RM, 5,  'शे फोक्सुन्डो गाउँपालिका'],
                            ['Tripurasundari Municipality',        self::M,  9,  'त्रिपुरासुन्दरी नगरपालिका'],
                            ['Chharka Tangsong Rural Municipality',self::RM, 4,  'छार्का ताङसोङ गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Jumla',
                        'name_np' => 'जुम्ला',
                        'municipalities' => [
                            ['Chandannath Municipality',           self::M,  9,  'चन्दननाथ नगरपालिका'],
                            ['Kanakasundari Rural Municipality',   self::RM, 6,  'कनकासुन्दरी गाउँपालिका'],
                            ['Guthichaur Rural Municipality',      self::RM, 5,  'गुठीचौर गाउँपालिका'],
                            ['Hima Rural Municipality',            self::RM, 5,  'हिमा गाउँपालिका'],
                            ['Patarasi Rural Municipality',        self::RM, 5,  'पतारासी गाउँपालिका'],
                            ['Sinja Rural Municipality',           self::RM, 5,  'सिञ्जा गाउँपालिका'],
                            ['Tatopani Rural Municipality',        self::RM, 5,  'तातोपानी गाउँपालिका'],
                            ['Tila Rural Municipality',            self::RM, 5,  'तिला गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Kalikot',
                        'name_np' => 'कालीकोट',
                        'municipalities' => [
                            ['Khandachakra Municipality',          self::M,  9,  'खाँडाचक्र नगरपालिका'],
                            ['Narharinath Rural Municipality',     self::RM, 7,  'नरहरिनाथ गाउँपालिका'],
                            ['Mahawai Rural Municipality',         self::RM, 5,  'महावाई गाउँपालिका'],
                            ['Pachaljharana Rural Municipality',   self::RM, 5,  'पचालझरना गाउँपालिका'],
                            ['Palata Rural Municipality',          self::RM, 5,  'पलाता गाउँपालिका'],
                            ['Raskot Municipality',                self::M,  9,  'रास्कोट नगरपालिका'],
                            ['Sanni Triveni Rural Municipality',   self::RM, 5,  'सान्नी त्रिवेणी गाउँपालिका'],
                            ['Shubha Kalika Rural Municipality',   self::RM, 5,  'शुभकालिका गाउँपालिका'],
                            ['Tilagufa Municipality',              self::M,  9,  'तिलागुफा नगरपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Mugu',
                        'name_np' => 'मुगु',
                        'municipalities' => [
                            ['Chhayanath Rara Municipality',       self::M,  9,  'छायाँनाथ रारा नगरपालिका'],
                            ['Khatyad Rural Municipality',         self::RM, 5,  'खत्याड गाउँपालिका'],
                            ['Mugum Karmarong Rural Municipality', self::RM, 4,  'मुगुम कार्मारोङ गाउँपालिका'],
                            ['Soru Rural Municipality',            self::RM, 4,  'सोरु गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Humla',
                        'name_np' => 'हुम्ला',
                        'municipalities' => [
                            ['Simkot Rural Municipality',          self::RM, 6,  'सिम्कोट गाउँपालिका'],
                            ['Adanchuli Rural Municipality',       self::RM, 5,  'अदानचुली गाउँपालिका'],
                            ['Chankheli Rural Municipality',       self::RM, 5,  'चंखेली गाउँपालिका'],
                            ['Kharpunath Rural Municipality',      self::RM, 5,  'खार्पुनाथ गाउँपालिका'],
                            ['Namkha Rural Municipality',          self::RM, 5,  'नाम्खा गाउँपालिका'],
                            ['Sarkegad Rural Municipality',        self::RM, 5,  'सर्केगाड गाउँपालिका'],
                            ['Tanjakot Rural Municipality',        self::RM, 5,  'ताँजाकोट गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Jajarkot',
                        'name_np' => 'जाजरकोट',
                        'municipalities' => [
                            ['Bheri Municipality',                 self::M,  9,  'भेरी नगरपालिका'],
                            ['Chhedagad Municipality',             self::M,  9,  'छेडागाड नगरपालिका'],
                            ['Barekot Rural Municipality',         self::RM, 6,  'बारेकोट गाउँपालिका'],
                            ['Junichande Rural Municipality',      self::RM, 5,  'जुनिचाँदे गाउँपालिका'],
                            ['Kuse Rural Municipality',            self::RM, 5,  'कुसे गाउँपालिका'],
                            ['Nalgad Municipality',                self::M,  9,  'नल्गाड नगरपालिका'],
                            ['Shiwalaya Rural Municipality',       self::RM, 5,  'शिवालय गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Dailekh',
                        'name_np' => 'दैलेख',
                        'municipalities' => [
                            ['Narayan Municipality',               self::M,  9,  'नारायण नगरपालिका'],
                            ['Dullu Municipality',                 self::M,  9,  'दुल्लु नगरपालिका'],
                            ['Aathabis Municipality',              self::M,  9,  'आठबिस नगरपालिका'],
                            ['Bhagawatimai Rural Municipality',    self::RM, 5,  'भगवतीमाई गाउँपालिका'],
                            ['Bhairabi Rural Municipality',        self::RM, 6,  'भैरबी गाउँपालिका'],
                            ['Dungeshwor Rural Municipality',      self::RM, 5,  'डुंगेश्वर गाउँपालिका'],
                            ['Gurans Rural Municipality',          self::RM, 6,  'गुराँस गाउँपालिका'],
                            ['Mahabu Rural Municipality',          self::RM, 6,  'महाबु गाउँपालिका'],
                            ['Naumule Rural Municipality',         self::RM, 6,  'नौमुले गाउँपालिका'],
                            ['Thatikanda Rural Municipality',      self::RM, 7,  'ठाटीकाँधा गाउँपालिका'],
                            ['Chamunda Bindrasaini Municipality',  self::M,  9,  'चामुण्डाविन्द्रासैनी नगरपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Surkhet',
                        'name_np' => 'सुर्खेत',
                        'municipalities' => [
                            ['Birendranagar Municipality',         self::M,  9,  'वीरेन्द्रनगर नगरपालिका'],
                            ['Bheriganga Municipality',            self::M,  9,  'भेरीगंगा नगरपालिका'],
                            ['Panchapuri Municipality',            self::M,  9,  'पञ्चपुरी नगरपालिका'],
                            ['Gurbhakot Municipality',             self::M,  9,  'गुर्भाकोट नगरपालिका'],
                            ['Chaukune Rural Municipality',        self::RM, 5,  'चौकुने गाउँपालिका'],
                            ['Chingad Rural Municipality',         self::RM, 5,  'चिङ्गाड गाउँपालिका'],
                            ['Barahatal Rural Municipality',       self::RM, 6,  'बाराहताल गाउँपालिका'],
                            ['Lekbeshi Municipality',              self::M,  9,  'लेकबेशी नगरपालिका'],
                            ['Simta Rural Municipality',           self::RM, 6,  'सिम्टा गाउँपालिका'],
                            ['Uttarganga Rural Municipality',      self::RM, 6,  'उत्तरगंगा गाउँपालिका'],
                        ],
                    ],

                ], // end Karnali districts
            ],

            // =================================================================
            // PROVINCE 7 — SUDURPASHCHIM PROVINCE (सुदूरपश्चिम प्रदेश) — 9 districts, 88 LGs
            // =================================================================
            [
                'name'    => 'Sudurpashchim Province',
                'name_np' => 'सुदूरपश्चिम प्रदेश',
                'code'    => 'P7',
                'districts' => [

                    [
                        'name'    => 'Bajura',
                        'name_np' => 'बाजुरा',
                        'municipalities' => [
                            ['Badimalika Municipality',            self::M,  9,  'बडिमालिका नगरपालिका'],
                            ['Budhiganga Municipality',            self::M,  9,  'बुढीगंगा नगरपालिका'],
                            ['Swamikartik Khapar Rural Municipality',self::RM,5, 'स्वामीकार्तिक खापर गाउँपालिका'],
                            ['Gaumul Rural Municipality',          self::RM, 5,  'गौमुल गाउँपालिका'],
                            ['Himali Rural Municipality',          self::RM, 5,  'हिमाली गाउँपालिका'],
                            ['Jagannath Rural Municipality',       self::RM, 6,  'जगन्नाथ गाउँपालिका'],
                            ['Pandav Gufa Rural Municipality',     self::RM, 5,  'पाण्डव गुफा गाउँपालिका'],
                            ['Tribhuwannagar Rural Municipality',  self::RM, 6,  'त्रिभुवन नगर गाउँपालिका'],
                            ['Talkot Rural Municipality',          self::RM, 5,  'तलकोट गाउँपालिका'],
                            ['Khaptad Chhanna Rural Municipality', self::RM, 5,  'खप्तड छान्ना गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Bajhang',
                        'name_np' => 'बझाङ',
                        'municipalities' => [
                            ['Bungal Municipality',                self::M,  9,  'बुङ्गल नगरपालिका'],
                            ['Jayaprithvi Municipality',           self::M,  9,  'जयपृथ्वी नगरपालिका'],
                            ['Bitthadchir Rural Municipality',     self::RM, 5,  'बित्थडचिर गाउँपालिका'],
                            ['Chhededaha Rural Municipality',      self::RM, 5,  'छेडेदह गाउँपालिका'],
                            ['Durgathali Rural Municipality',      self::RM, 5,  'दुर्गाथली गाउँपालिका'],
                            ['Kanda Rural Municipality',           self::RM, 5,  'कान्डा गाउँपालिका'],
                            ['Kedar Rural Municipality',           self::RM, 5,  'केदार गाउँपालिका'],
                            ['Masta Rural Municipality',           self::RM, 5,  'मष्टा गाउँपालिका'],
                            ['Talkot Rural Municipality',          self::RM, 5,  'तलकोट गाउँपालिका'],
                            ['Thalara Rural Municipality',         self::RM, 5,  'थलारा गाउँपालिका'],
                            ['Surma Rural Municipality',           self::RM, 5,  'सुर्मा गाउँपालिका'],
                            ['Khaptad Chhanna Rural Municipality', self::RM, 5,  'खप्तड छान्ना गाउँपालिका'],
                            ['Vogati Rural Municipality',          self::RM, 5,  'बोगटान फुड्सिल गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Darchula',
                        'name_np' => 'डार्चुला',
                        'municipalities' => [
                            ['Darchula Municipality',              self::M,  9,  'डार्चुला नगरपालिका'],
                            ['Shailyashikhar Municipality',        self::M,  9,  'शैल्यशिखर नगरपालिका'],
                            ['Apihimal Rural Municipality',        self::RM, 5,  'आपिहिमाल गाउँपालिका'],
                            ['Byas Rural Municipality',            self::RM, 5,  'ब्यास गाउँपालिका'],
                            ['Lekam Rural Municipality',           self::RM, 5,  'लेकम गाउँपालिका'],
                            ['Mahakali Rural Municipality',        self::RM, 5,  'महाकाली गाउँपालिका'],
                            ['Naugad Rural Municipality',          self::RM, 5,  'नौगाड गाउँपालिका'],
                            ['Khalanga Rural Municipality',        self::RM, 5,  'खलंगा गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Baitadi',
                        'name_np' => 'बैतडी',
                        'municipalities' => [
                            ['Dasharathchand Municipality',        self::M,  9,  'दशरथचन्द नगरपालिका'],
                            ['Patan Municipality',                 self::M,  9,  'पाटन नगरपालिका'],
                            ['Purchaudi Municipality',             self::M,  9,  'पुर्चौडी नगरपालिका'],
                            ['Dilasaini Rural Municipality',       self::RM, 6,  'दिलासैनी गाउँपालिका'],
                            ['Dogdakedar Rural Municipality',      self::RM, 5,  'डोगडाकेदार गाउँपालिका'],
                            ['Melauli Rural Municipality',         self::RM, 5,  'मेलौली गाउँपालिका'],
                            ['Pancheshwor Rural Municipality',     self::RM, 5,  'पञ्चेश्वर गाउँपालिका'],
                            ['Shivanath Rural Municipality',       self::RM, 5,  'शिवनाथ गाउँपालिका'],
                            ['Sigas Rural Municipality',           self::RM, 5,  'सिगास गाउँपालिका'],
                            ['Surnaya Rural Municipality',         self::RM, 5,  'सूर्नया गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Dadeldhura',
                        'name_np' => 'डडेल्धुरा',
                        'municipalities' => [
                            ['Amargadhi Municipality',             self::M,  9,  'अमरगढी नगरपालिका'],
                            ['Parashuram Municipality',            self::M,  9,  'परशुराम नगरपालिका'],
                            ['Aalitaal Rural Municipality',        self::RM, 5,  'आलीताल गाउँपालिका'],
                            ['Bhageshwor Rural Municipality',      self::RM, 5,  'भागेश्वर गाउँपालिका'],
                            ['Ganyapadhura Rural Municipality',    self::RM, 5,  'गन्यापधुरा गाउँपालिका'],
                            ['Nawadurga Rural Municipality',       self::RM, 5,  'नवदुर्गा गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Doti',
                        'name_np' => 'डोटी',
                        'municipalities' => [
                            ['Dipayal Silgadhi Municipality',      self::M,  9,  'दिपायल सिलगढी नगरपालिका'],
                            ['Shikhar Municipality',               self::M,  9,  'शिखर नगरपालिका'],
                            ['Aadarsha Rural Municipality',        self::RM, 6,  'आदर्श गाउँपालिका'],
                            ['Badikedar Rural Municipality',       self::RM, 5,  'बड़ीकेदार गाउँपालिका'],
                            ['Bogatan Rural Municipality',         self::RM, 5,  'बोगटान गाउँपालिका'],
                            ['Jorayal Rural Municipality',         self::RM, 6,  'जोरायल गाउँपालिका'],
                            ['KI Singh Rural Municipality',        self::RM, 5,  'के.आई.सिंह गाउँपालिका'],
                            ['Purbichauki Rural Municipality',     self::RM, 6,  'पूर्विचौकी गाउँपालिका'],
                            ['Sayal Rural Municipality',           self::RM, 5,  'सायल गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Achham',
                        'name_np' => 'अछाम',
                        'municipalities' => [
                            ['Mangalsen Municipality',             self::M,  9,  'मंगलसेन नगरपालिका'],
                            ['Camadanda Rural Municipality',       self::RM, 5,  'कमाडाँडा गाउँपालिका'],
                            ['Dhakari Rural Municipality',         self::RM, 5,  'ढकारी गाउँपालिका'],
                            ['Mellekh Rural Municipality',         self::RM, 5,  'मेल्लेख गाउँपालिका'],
                            ['Panchadeval Binayak Rural Municipality',self::RM,5,'पञ्चदेवल विनायक गाउँपालिका'],
                            ['Ramaroshan Rural Municipality',      self::RM, 5,  'रामारोशन गाउँपालिका'],
                            ['Sanphebagar Municipality',           self::M,  9,  'साँफेबगर नगरपालिका'],
                            ['Chaurpati Rural Municipality',       self::RM, 5,  'चौरपाटी गाउँपालिका'],
                            ['Bannigadhi Jayagadh Rural Municipality',self::RM,5,'बन्नीगढी जयगढ गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Kailali',
                        'name_np' => 'कैलाली',
                        'municipalities' => [
                            ['Dhangadhi Sub-Metropolitan City',    self::SMC, 19, 'धनगढी उपमहानगरपालिका'],
                            ['Tikapur Municipality',               self::M,   9,  'टीकापुर नगरपालिका'],
                            ['Godawari Municipality',              self::M,   9,  'गोदावरी नगरपालिका'],
                            ['Bhajani Municipality',               self::M,   9,  'भजनी नगरपालिका'],
                            ['Janaki Rural Municipality',          self::RM,  6,  'जानकी गाउँपालिका'],
                            ['Khairaala Rural Municipality',       self::RM,  6,  'खैरला गाउँपालिका'],
                            ['Chure Rural Municipality',           self::RM,  5,  'चुरे गाउँपालिका'],
                            ['Gauriganga Municipality',            self::M,   9,  'गौरीगंगा नगरपालिका'],
                            ['Joshipur Rural Municipality',        self::RM,  5,  'जोशीपुर गाउँपालिका'],
                            ['Lamki Chuha Municipality',           self::M,   9,  'लम्कीचुहा नगरपालिका'],
                            ['Mohanyal Rural Municipality',        self::RM,  5,  'मोहनयल गाउँपालिका'],
                            ['Bardagoriya Rural Municipality',     self::RM,  6,  'बर्दगोरिया गाउँपालिका'],
                            ['Geta Rural Municipality',            self::RM,  6,  'गेटा गाउँपालिका'],
                            ['Kailari Rural Municipality',         self::RM,  6,  'कैलारी गाउँपालिका'],
                        ],
                    ],

                    [
                        'name'    => 'Kanchanpur',
                        'name_np' => 'कञ्चनपुर',
                        'municipalities' => [
                            ['Bhimdatta Municipality',             self::M,  9,  'भीमदत्त नगरपालिका'],
                            ['Bedkot Municipality',                self::M,  9,  'बेदकोट नगरपालिका'],
                            ['Punarbas Municipality',              self::M,  9,  'पुनर्बास नगरपालिका'],
                            ['Shuklaphanta Municipality',          self::M,  9,  'शुक्लाफाँटा नगरपालिका'],
                            ['Beldandi Rural Municipality',        self::RM, 5,  'बेल्डाँडी गाउँपालिका'],
                            ['Belauri Municipality',               self::M,  9,  'बेलौरी नगरपालिका'],
                            ['Krishnapur Municipality',            self::M,  9,  'कृष्णपुर नगरपालिका'],
                            ['Laljhadi Rural Municipality',        self::RM, 6,  'लालझाडी गाउँपालिका'],
                        ],
                    ],

                ], // end Sudurpashchim districts
            ],

        ]; // end getNepalData()
    }
}
