<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Short;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Default Users ──────────────────────────────────────────────────
        User::create([
            'name'         => 'Rizky Pratama',
            'username'     => 'rizky',
            'display_name' => 'Rizky Pratama',
            'email'        => 'rizky@nexus.id',
            'password'     => Hash::make('pewarta123'),
            'avatar_color' => 'from-purple-500 to-violet-400',
            'role'         => 'pewarta',
        ]);

        User::create([
            'name'         => 'Dian Kusuma',
            'username'     => 'dian',
            'display_name' => 'Dian Kusuma',
            'email'        => 'dian@nexus.id',
            'password'     => Hash::make('redaksi123'),
            'avatar_color' => 'from-blue-500 to-sky-400',
            'role'         => 'redaksi',
        ]);

        // ─── Seed Articles ──────────────────────────────────────────────────
        $articles = [
            // GAMES
            [
                'title'     => '10 Game RPG Open World Terbaik 2025 yang Wajib Kamu Mainkan',
                'excerpt'   => 'Dari dunia fantasi epik hingga masa depan sci-fi yang dystopian, berikut daftar game RPG open world yang merebut hati para gamer di seluruh dunia tahun ini.',
                'content'   => "Tahun 2025 menjadi tahun yang luar biasa bagi para pecinta RPG open world. Developer game di seluruh dunia berlomba-lomba menghadirkan dunia yang lebih imersif, cerita yang lebih dalam, dan mekanik gameplay yang semakin inovatif.\n\n## 1. Elden Ring: Shadow of the New World\nEkspansi terbaru dari FromSoftware ini membawa pemain ke benua baru yang penuh misteri. Dengan sistem combat yang telah disempurnakan dan lore yang semakin kaya, game ini layak mendapatkan posisi teratas daftar ini.\n\n## 2. The Witcher 4: Ciri's Chronicles\nCD Projekt Red akhirnya memperkenalkan Ciri sebagai protagonis utama. Dunia yang lebih luas dari The Witcher 3, dipadu dengan visual next-gen yang memukau, membuat game ini menjadi salah satu yang paling dinantikan.\n\n## 3. Final Fantasy XVII\nSquare Enix kembali membuktikan diri dengan entry terbaru Final Fantasy yang menggabungkan elemen turn-based klasik dengan aksi real-time yang dinamis.\n\n## 4. Starfield: New Horizons\nBethesda melanjutkan petualangan antariksa dengan ekspansi masif yang menambahkan puluhan planet baru untuk dijelajahi dan ratusan misi baru yang menantang.\n\n## 5. Dragon Age: The Veilguard Chronicles\nBioWare bangkit kembali dengan sebuah RPG yang menempatkan pilihan moral pemain sebagai inti dari setiap aspek permainan.\n\nDengan begitu banyaknya pilihan berkualitas tinggi, tahun 2025 membuktikan bahwa genre RPG open world masih menjadi raja di industri gaming global.",
                'category'  => 'games',
                'image'     => 'https://images.unsplash.com/photo-1657053136972-241e05e6623f?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=1080',
                'author'    => 'Rizky Pratama',
                'read_time' => '8 menit',
                'rating'    => 9.2,
                'featured'  => true,
                'tags'      => ['RPG', 'Open World', '2025', 'Gaming'],
            ],
            [
                'title'     => 'Review: The Legend of Zelda: Echoes of the Past — Sebuah Mahakarya Baru',
                'excerpt'   => 'Nintendo kembali membuktikan mengapa Zelda selalu menjadi tolak ukur kualitas dalam dunia gaming. Echoes of the Past adalah pengalaman yang tak terlupakan.',
                'content'   => "Nintendo sekali lagi berhasil menciptakan sesuatu yang luar biasa dengan The Legend of Zelda: Echoes of the Past.\n\n## Dunia yang Hidup dan Bernafas\nMap Hyrule dalam game ini tiga kali lebih besar dari Breath of the Wild, namun setiap sudut terasa memiliki cerita dan tujuan.\n\n## Sistem Mekanik Echo yang Revolusioner\nMechanic baru \"Echo\" memungkinkan Link untuk menyalin objek dan musuh yang ditemui, lalu menggunakannya sebagai alat untuk memecahkan puzzle atau bertarung.\n\n## Skor: 9.5/10\nGame ini adalah contoh sempurna mengapa Nintendo terus menjadi pemimpin dalam inovasi game. Wajib dimainkan oleh siapapun yang memiliki Nintendo Switch 2.",
                'category'  => 'games',
                'image'     => 'https://images.unsplash.com/photo-1613974089244-916ec6dda17c?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=1080',
                'author'    => 'Dian Kusuma',
                'read_time' => '6 menit',
                'rating'    => 9.5,
                'featured'  => false,
                'tags'      => ['Nintendo', 'Zelda', 'Review', 'Switch 2'],
            ],
            [
                'title'     => 'Esports Asia Tenggara Kian Bersinar di Panggung Global',
                'excerpt'   => 'Tim-tim dari Indonesia, Filipina, dan Thailand terus membuktikan diri di turnamen internasional. Era keemasan esports Asia Tenggara telah dimulai.',
                'content'   => "Asia Tenggara tidak lagi hanya menjadi penonton dalam ekosistem esports global. Dengan munculnya bakat-bakat muda yang luar biasa dan infrastruktur yang terus berkembang, kawasan ini kini menjadi pemain utama yang diperhitungkan.\n\n## Indonesia di Puncak\nTim Indonesia berhasil meraih posisi runner-up di turnamen Mobile Legends: Bang Bang World Championship 2025, sebuah pencapaian bersejarah yang membakar semangat seluruh komunitas gaming di tanah air.\n\n## Ekosistem yang Berkembang\nInvestasi besar dari berbagai brand global ke tim-tim esports lokal, ditambah dengan dukungan pemerintah yang semakin meningkat, menciptakan lingkungan yang kondusif bagi pertumbuhan industri ini.",
                'category'  => 'games',
                'image'     => 'https://images.unsplash.com/photo-1610561212775-b191f21b6998?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=1080',
                'author'    => 'Andi Wijaya',
                'read_time' => '5 menit',
                'featured'  => false,
                'tags'      => ['Esports', 'Indonesia', 'Asia Tenggara', 'Gaming'],
            ],
            [
                'title'     => 'Panduan Lengkap Memulai Karir Sebagai Game Developer di Indonesia',
                'excerpt'   => 'Industri game lokal terus berkembang. Kami berbicara dengan developer sukses untuk mengetahui jalur karir terbaik di bidang yang penuh peluang ini.',
                'content'   => "Industri game Indonesia sedang dalam periode pertumbuhan yang luar biasa. Dengan lebih dari 100 juta gamer aktif, pasar lokal yang besar menjadi daya tarik tersendiri.\n\n## Mulai dari Mana?\nBanyak developer sukses Indonesia memulai perjalanan mereka dari game jam, hackathon, atau bahkan membuat game sederhana untuk smartphone.\n\n## Tools yang Perlu Dipelajari\n- **Unity**: Platform terpopuler untuk game mobile dan PC indie\n- **Unreal Engine**: Untuk grafis AAA yang memukau\n- **Godot**: Open-source, ringan, cocok untuk pemula\n\n## Komunitas Game Dev Indonesia\nBergabunglah dengan IGDX, GameDev.id, atau komunitas lokal di kota kamu untuk networking dan kolaborasi.",
                'category'  => 'games',
                'image'     => 'https://images.unsplash.com/photo-1585504198199-20277593b94f?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=1080',
                'author'    => 'Maya Sari',
                'read_time' => '10 menit',
                'featured'  => false,
                'tags'      => ['Game Dev', 'Karir', 'Indonesia', 'Programming'],
            ],
            // MUSIK
            [
                'title'     => 'TWICE World Tour 2026: Pengalaman K-Pop Terbesar di Jakarta',
                'excerpt'   => 'Ribuan ONCE berkumpul di Stadion Utama GBK untuk menyaksikan TWICE memukau dengan penampilan spektakuler mereka. Ini adalah ulasan lengkap konser yang akan dikenang selamanya.',
                'content'   => "Jumat malam, Jakarta berdegup kencang. Stadion Utama GBK yang berkapasitas 80.000 orang nyaris penuh sesak ketika TWICE memasuki panggung dengan formasi ikonik mereka.\n\n## Opening yang Menggetarkan\nKonser dibuka dengan \"Feel Special\" yang langsung memicu histeria massal. Ribuan light stick berwarna pastel menciptakan lautan cahaya yang memukau.\n\n## Production Value yang Luar Biasa\nTata panggung setinggi 30 meter dengan layar LED raksasa dan efek pyrotechnic yang tersinkronisasi sempurna menjadikan ini salah satu konser dengan production value tertinggi yang pernah ada di Indonesia.\n\n## Setlist Highlights\nDari \"Cheer Up\" hingga \"FANCY\" dan penutup yang emosional dengan \"The Feels\", setiap lagu terasa seperti perayaan untuk para penggemar setia.",
                'category'  => 'musik',
                'image'     => 'https://images.unsplash.com/photo-1470229722913-7c0e2dbbafd3?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=1080',
                'author'    => 'Sari Dewi',
                'read_time' => '7 menit',
                'rating'    => 9.8,
                'featured'  => true,
                'tags'      => ['TWICE', 'K-Pop', 'Konser', 'Jakarta'],
            ],
            [
                'title'     => 'Album Terbaik 2025: Musik Indonesia Mengguncang Dunia',
                'excerpt'   => 'Dari pop indie hingga hip-hop lokal, musisi Indonesia membuktikan diri di panggung internasional. Inilah 10 album terbaik yang mendefinisikan 2025.',
                'content'   => "2025 adalah tahun musik Indonesia. Dari platform streaming global hingga festival internasional, artis-artis kita mulai mendapat pengakuan yang selama ini memang layak mereka dapatkan.\n\n## 1. Hindia — \"Lagipula Hidup Akan Berakhir\" (EP)\nKetajaman lirik Baskara Putra dalam EP terbaru Hindia ini mencapai level baru. Setiap track adalah meditasi tentang eksistensi modern.\n\n## 2. Pamungkas — \"Solitude\"\nAlbum full bahasa Inggris pertama Pamungkas yang berhasil menembus Spotify Global Chart dengan single \"Love You Like That 2.0\".\n\n## 3. Weird Genius — \"Digital Soul\"\nDuo elektronik ini semakin memantapkan posisi mereka sebagai pioneer EDM Indonesia dengan sound yang semakin mature.\n\n## 4. Yura Yunita — \"Takdir\"\nKolaborasi Yura dengan berbagai musisi jazz internasional menghasilkan album yang berani dan memukau secara musikalitas.",
                'category'  => 'musik',
                'image'     => 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=1080',
                'author'    => 'Bagas Nugroho',
                'read_time' => '9 menit',
                'featured'  => false,
                'tags'      => ['Album', 'Musik Indonesia', '2025', 'Review'],
            ],
            [
                'title'     => 'Tren Musik 2026: Lo-Fi, Hyperpop, dan Kebangkitan Indie Folk',
                'excerpt'   => 'Lanskap musik global terus berevolusi. Tiga genre ini mendominasi playlist dan chart streaming di awal 2026, mengubah cara kita mendengarkan musik.',
                'content'   => "Memasuki 2026, tren musik global menunjukkan pergeseran yang menarik dari dominasi mainstream menuju keberagaman genre yang semakin inklusif.\n\n## Lo-Fi: Dari Niche ke Mainstream\nLo-fi hip hop yang dulunya hanya dikenal kalangan terbatas kini menjadi kategori tersendiri di Spotify dengan ratusan juta pendengar aktif.\n\n## Hyperpop Evolusi\nGenre yang dipopulerkan oleh 100 gecs ini terus berevolusi dengan pengaruh dari noise, punk, dan bahkan musik klasik.\n\n## Indie Folk: Kerinduan akan Keaslian\nDi tengah kejenuhan musik digital, pendengar mulai kembali menghargai musik yang terasa lebih manusiawi dan mentah.",
                'category'  => 'musik',
                'image'     => 'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=1080',
                'author'    => 'Sari Dewi',
                'read_time' => '6 menit',
                'featured'  => false,
                'tags'      => ['Lo-Fi', 'Hyperpop', 'Indie Folk', 'Tren 2026'],
            ],
            [
                'title'     => 'Kolaborasi Lintas Genre: Ketika K-Pop Bertemu Dangdut Koplo',
                'excerpt'   => 'Kolaborasi mengejutkan antara idol K-Pop dengan penyanyi dangdut Indonesia menciptakan fenomena baru yang viral di seluruh Asia.',
                'content'   => "Siapa yang menyangka bahwa perpaduan K-Pop dan Dangdut Koplo akan menjadi salah satu fenomena musik terbesar di Asia 2026?\n\n## Bagaimana Ini Dimulai\nSemuanya berawal dari sebuah cover video yang diunggah seorang musisi Indonesia yang memadukan beat koplo dengan melodi lagu K-Pop populer.\n\n## Viral Tak Terbendung\nVideo tersebut dalam 48 jam meraih 50 juta views di TikTok, menarik perhatian label K-Pop besar yang kemudian menghubungi sang musisi untuk kolaborasi resmi.\n\n## Dampak Industri\nKolaborasi ini tidak hanya menjadi hit, tapi juga membuka dialog baru tentang identitas musik Asia dan bagaimana genre-genre tradisional dapat berevolusi tanpa kehilangan esensinya.",
                'category'  => 'musik',
                'image'     => 'https://images.unsplash.com/photo-1526478806334-5fd488fcaabc?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=1080',
                'author'    => 'Bagas Nugroho',
                'read_time' => '5 menit',
                'featured'  => false,
                'tags'      => ['K-Pop', 'Dangdut', 'Kolaborasi', 'Viral'],
            ],
            // FILM
            [
                'title'     => 'Review: Dune: Messiah — Denis Villeneuve Ciptakan Mahakarya Terakhir',
                'excerpt'   => 'Trilogi Dune akhirnya mencapai puncaknya. Denis Villeneuve menutup saga ini dengan film yang lebih emosional, lebih berani, dan lebih ambisius dari dua pendahulunya.',
                'content'   => "Ada film yang kita tonton, dan ada film yang kita rasakan. Dune: Messiah termasuk dalam kategori kedua — sebuah pengalaman sinematik yang meresap jauh ke dalam kesadaran.\n\n## Visual yang Melampaui Kata-Kata\nGreig Fraser kembali di belakang kamera, dan hasilnya bahkan lebih menakjubkan dari film sebelumnya. Setiap frame terasa seperti lukisan dari peradaban yang jauh.\n\n## Performa Timothée Chalamet\nIni adalah penampilan terbaik Chalamet sepanjang karirnya. Paul Atreides dalam film ini adalah karakter yang jauh lebih kompleks dan morally ambiguous.\n\n## Skor Musik Hans Zimmer\nSoundtrack Zimmer semakin berani dan eksperimental. Beberapa track terasa hampir seperti ritual daripada musik film.\n\n**Verdict: 10/10** — Salah satu trilogi film terbaik dalam sejarah sinema.",
                'category'  => 'film',
                'image'     => 'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=1080',
                'author'    => 'Farhan Ardiansyah',
                'read_time' => '8 menit',
                'rating'    => 9.7,
                'featured'  => true,
                'tags'      => ['Dune', 'Review', 'Sci-Fi', 'Villeneuve'],
            ],
            [
                'title'     => 'Perfilman Indonesia 2026: Era Baru Telah Dimulai',
                'excerpt'   => 'Dari KKN di Desa Penari hingga generasi pembuat film baru yang mendobrak batasan, perfilman Indonesia sedang dalam momentum terbaik sepanjang sejarahnya.',
                'content'   => "Perfilman Indonesia tidak lagi sekadar menghibur penonton lokal. Di 2026, film-film Indonesia mulai mengukir nama di festival-festival bergengsi dunia.\n\n## Momentum yang Tidak Boleh Dilewatkan\n\"Bumi\" karya sutradara muda Rania Aziz berhasil masuk seleksi resmi Cannes Film Festival 2026, sebuah pencapaian yang belum pernah terjadi sebelumnya.\n\n## Tren Genre yang Menarik\nHorror Indonesia tetap menjadi genre terkuat, namun kini diiringi pertumbuhan film drama sosial, thriller psikologis, dan bahkan science fiction yang mulai digarap serius.\n\n## Dukungan Pemerintah dan Swasta\nPeningkatan subsidi produksi film dari Kemendikbud serta masuknya investor streaming global ke ekosistem film lokal menjadi katalis pertumbuhan yang signifikan.",
                'category'  => 'film',
                'image'     => 'https://images.unsplash.com/photo-1536440136628-849c177e76a1?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=1080',
                'author'    => 'Liana Putri',
                'read_time' => '7 menit',
                'featured'  => false,
                'tags'      => ['Film Indonesia', '2026', 'Cannes', 'Industri Film'],
            ],
            [
                'title'     => '10 Film yang Wajib Ditonton di Bioskop Bulan Ini',
                'excerpt'   => 'Dari superhero epik hingga drama yang menyentuh hati, bulan ini jadwal bioskop dipenuhi film-film berkualitas tinggi. Mana yang harus kamu prioritaskan?',
                'content'   => "Bulan ini adalah salah satu jadwal bioskop terbaik yang pernah ada. Berikut panduan lengkap kami untuk membantu kamu memilih film yang tepat.\n\n## 1. Avengers: Secret Wars\nMomen yang sudah 10 tahun ditunggu-tunggu. Pertemuan ultimate antara semua versi Avengers dari berbagai multiverse.\n\n## 2. Oppenheimer 2: Aftermath\nChristopher Nolan kembali dengan sekuel yang berfokus pada dampak jangka panjang bom atom pada dunia dan kemanusiaan.\n\n## 3. Studio Ghibli: The Red Turtle Sequel\nStudio Ghibli hadir dengan film terbaru yang kembali menjanjikan keajaiban visual tanpa dialog.\n\n## 4. KKN 3: Ritual Terakhir\nSeri horor Indonesia yang paling ditunggu kembali hadir dengan lore yang semakin dalam dan ketegangan yang tak tertahankan.",
                'category'  => 'film',
                'image'     => 'https://images.unsplash.com/photo-1594908900066-3f47337549d8?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=1080',
                'author'    => 'Farhan Ardiansyah',
                'read_time' => '6 menit',
                'featured'  => false,
                'tags'      => ['Bioskop', 'Rekomendasi', 'Film Baru', '2026'],
            ],
            [
                'title'     => 'Cannes 2026: Ketika Asia Mengambil Alih Sorotan Dunia',
                'excerpt'   => 'Festival Film Cannes 2026 menjadi milik Asia. Film-film dari Korea, Jepang, Thailand, dan Indonesia mendominasi kompetisi dan mencuri perhatian dunia.',
                'content'   => "Cannes 2026 akan diingat sebagai tahun ketika Asia benar-benar tiba di panggung sinema dunia.\n\n## Palme d\'Or: Korea Raih Kemenangan Kedua\nFilm \"The Last Monsoon\" dari sutradara Korea Park Ji-won berhasil meraih Palme d\'Or, menjadikan Korea sebagai negara Asia pertama yang meraih penghargaan tertinggi Cannes dua kali.\n\n## Indonesia di Seleksi Resmi\n\"Bumi\" dari Rania Aziz bukan hanya masuk seleksi resmi, tapi juga memenangkan penghargaan Jury Prize — sebuah lompatan bersejarah untuk perfilman Indonesia.\n\n## Tren yang Terlihat\nFilm-film Asia yang menang cenderung mengeksplorasi tema universalisme versus tradisi lokal, sebuah tegangan yang rupanya sangat relevan dengan kondisi dunia saat ini.",
                'category'  => 'film',
                'image'     => 'https://images.unsplash.com/photo-1522869635100-9f4c5e86aa37?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=1080',
                'author'    => 'Liana Putri',
                'read_time' => '7 menit',
                'featured'  => false,
                'tags'      => ['Cannes', 'Festival Film', 'Asia', 'Korea'],
            ],
            // ENTERTAINMENT
            [
                'title'     => 'Fenomena Anime Global: Kenapa Dunia Jatuh Cinta dengan Animasi Jepang',
                'excerpt'   => 'Dari One Piece hingga Demon Slayer, anime telah menjadi fenomena budaya global yang tidak bisa diabaikan. Kami menyelami faktor-faktor yang mendorong popularitas luar biasa ini.',
                'content'   => "Tidak ada fenomena budaya yang lebih menarik untuk dipelajari di era modern ini selain kebangkitan anime sebagai kekuatan budaya global.\n\n## Angka yang Berbicara\n- Revenue industri anime global 2025: \$36 miliar USD\n- Jumlah penonton anime di Indonesia: 65 juta orang aktif\n- Netflix mengalokasikan 20% budget konten Asia untuk anime\n- Crunchyroll mencatat 150 juta subscriber global\n\n## Mengapa Anime Berhasil?\n**1. Kedalaman Narasi**\nBerbeda dengan stereotip \"kartun anak-anak,\" anime sering kali mengeksplorasi tema-tema kompleks seperti existentialisme, trauma, identitas, dan moralitas dengan cara yang nuanced.\n\n**2. Diversitas Genre**\nDari slice-of-life yang tenang hingga action shounen yang intens — ada anime untuk setiap selera.\n\n**3. Community dan Fandom**\nKomunitas anime adalah salah satu yang paling passionate dan kreatif di dunia.",
                'category'  => 'entertainment',
                'image'     => 'https://images.unsplash.com/photo-1769874825261-ef30d63f6817?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=1080',
                'author'    => 'Nadia Rahman',
                'read_time' => '9 menit',
                'featured'  => true,
                'tags'      => ['Anime', 'Pop Culture', 'Global', 'Tren'],
            ],
            [
                'title'     => 'Netflix vs Disney+ vs Prime: Pertarungan Platform Streaming 2026',
                'excerpt'   => 'Perang streaming semakin sengit di 2026. Dengan harga yang terus naik dan konten eksklusif yang semakin banyak, platform mana yang paling worth it untuk kamu berlangganan?',
                'content'   => "Era streaming telah mengubah cara kita mengonsumsi hiburan, tetapi kini pertanyaannya adalah: dengan budget terbatas, platform mana yang harus kamu pilih?\n\n## Netflix: Masih Raja, Tapi Semakin Mahal\nNetflix tetap menjadi pemimpin dengan portofolio konten terluas. Namun, kenaikan harga yang konsisten dan pembatasan sharing password membuat banyak subscriber mempertimbangkan ulang.\n\n**Kelebihan:** Konten original terbanyak, library film dan serial terluas\n**Kekurangan:** Harga paling mahal di kelasnya\n\n## Disney+: Kualitas yang Konsisten\nDari Marvel hingga Star Wars, Disney+ menawarkan franchise yang paling kuat.\n\n## Amazon Prime Video: Hidden Gem\nPrime Video sering diremehkan, padahal mereka memiliki beberapa original terbaik.",
                'category'  => 'entertainment',
                'image'     => 'https://images.unsplash.com/photo-1512070750041-b9479c107194?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=1080',
                'author'    => 'Rafi Hidayat',
                'read_time' => '7 menit',
                'featured'  => false,
                'tags'      => ['Streaming', 'Netflix', 'Disney+', 'Review'],
            ],
            [
                'title'     => 'Pop Culture 2026: 7 Tren yang Mendominasi Tahun Ini',
                'excerpt'   => 'Dari kebangkitan Y2K aesthetic hingga AI-generated art yang kontroversial, inilah tren pop culture yang paling banyak diperbincangkan di awal 2026.',
                'content'   => "Pop culture tidak pernah stagnan, dan 2026 membuktikannya dengan serangkaian tren yang segar, mengejutkan, dan kadang kontroversial.\n\n## 1. Y2K 2.0 Aesthetic\nEstetika awal 2000-an kembali lagi, namun kali ini dengan sentuhan modern yang lebih sophisticated.\n\n## 2. AI-Generated Content: Berkah atau Kutukan?\nPerdebatan soal konten yang dibuat AI terus berlanjut.\n\n## 3. Nostalgia Economy\nReboot, remake, sequel — formula nostalgia terus menghasilkan uang.\n\n## 4. Creator Economy 3.0\nKreator konten kini lebih dari sekedar influencer.\n\n## 5. Wellness Entertainment\nKonten yang berkaitan dengan kesehatan mental, meditasi, dan self-care terus meledak.\n\n## 6. Phygital Experiences\nPengalaman yang menggabungkan dunia fisik dan digital semakin mainstream.\n\n## 7. Southeast Asian Creative Renaissance\nAsia Tenggara semakin diakui sebagai pusat kreativitas global yang baru.",
                'category'  => 'entertainment',
                'image'     => 'https://images.unsplash.com/photo-1772535262208-45511a3dd90b?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=1080',
                'author'    => 'Nadia Rahman',
                'read_time' => '8 menit',
                'featured'  => false,
                'tags'      => ['Pop Culture', 'Tren', '2026', 'Entertainment'],
            ],
            [
                'title'     => 'Comic Con Jakarta 2026: Semua yang Perlu Kamu Tahu Sebelum Datang',
                'excerpt'   => 'Comic Con terbesar di Asia Tenggara kembali hadir di Jakarta. Dari guest star internasional hingga preview eksklusif, ini adalah event yang tidak boleh kamu lewatkan.',
                'content'   => "Comic Con Jakarta 2026 siap menjadi event pop culture terbesar dalam sejarah Indonesia.\n\n## Tanggal & Lokasi\n**15-18 Mei 2026**\nJakarta Convention Center, Senayan, Jakarta\n\n## Guest Stars\n- Tom Holland (Spider-Man franchise)\n- Florence Pugh (Black Widow, Avengers)\n- Yoshiaki Nishimura (Producer Studio Ghibli)\n- Hajime Isayama (Creator, Attack on Titan)\n- Dan 20+ tamu spesial lainnya!\n\n## Highlight Events\n- **World Premiere**: Trailer perdana film superhero Indonesia pertama\n- **Artist Alley**: Lebih dari 300 seniman lokal dan internasional\n- **Cosplay Competition**: Hadiah total senilai 500 juta rupiah\n\n## Tiket\nEarly bird masih tersedia dengan harga mulai dari Rp 250.000 untuk single day pass.",
                'category'  => 'entertainment',
                'image'     => 'https://images.unsplash.com/photo-1769874825261-ef30d63f6817?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=1080',
                'author'    => 'Rafi Hidayat',
                'read_time' => '5 menit',
                'featured'  => false,
                'tags'      => ['Comic Con', 'Event', 'Jakarta', 'Pop Culture'],
            ],
        ];

        foreach ($articles as $data) {
            Article::create(array_merge($data, [
                'slug'   => Article::generateSlug($data['title']),
                'source' => 'seed',
            ]));
        }

        // ─── Seed Shorts ────────────────────────────────────────────────────
        $shorts = [
            [
                'title'       => 'GTA VI Gameplay Leak: Open World yang Bikin Gasping 🔥',
                'description' => 'Bocoran gameplay GTA VI akhirnya tersebar! Peta yang sangat luas dengan grafis next-gen. Rockstar Games beneran gila sih ini!',
                'author'      => 'NEXUS Games',
                'handle'      => '@nexusgames',
                'category'    => 'games',
                'video_url'   => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4',
                'thumbnail'   => 'https://images.unsplash.com/photo-1770067665792-9975acdec4fb?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=800',
                'likes'       => 24800,
                'comments'    => 1243,
                'shares'      => 891,
                'views'       => '1.2M',
                'duration'    => '0:58',
                'tags'        => ['GTA6', 'Rockstar', 'Gaming'],
                'verified'    => true,
            ],
            [
                'title'       => 'Coldplay Jakarta: Momen Terbaik yang Gak Akan Terlupakan 🎶',
                'description' => 'Konser Coldplay di GBK kemarin luar biasa! Ribuan lampu LED wristband menciptakan lautan cahaya yang menakjubkan.',
                'author'      => 'NEXUS Musik',
                'handle'      => '@nexusmusik',
                'category'    => 'musik',
                'video_url'   => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerEscapes.mp4',
                'thumbnail'   => 'https://images.unsplash.com/photo-1635961726947-0f821cf9ba28?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=800',
                'likes'       => 58200,
                'comments'    => 3781,
                'shares'      => 4200,
                'views'       => '4.7M',
                'duration'    => '1:12',
                'tags'        => ['Coldplay', 'Konser', 'Jakarta'],
                'verified'    => true,
            ],
            [
                'title'       => 'Behind The Scenes: Cara Sutradara Bikin Adegan Laga Tanpa CGI 🎬',
                'description' => 'Gak semua adegan laga di film Hollywood pakai CGI. Ini cara mereka bikin yang 100% nyata dan bikin jantung deg-degan saat syuting!',
                'author'      => 'NEXUS Film',
                'handle'      => '@nexusfilm',
                'category'    => 'film',
                'video_url'   => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerJoyrides.mp4',
                'thumbnail'   => 'https://images.unsplash.com/photo-1768885512270-92224c501be6?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=800',
                'likes'       => 19700,
                'comments'    => 876,
                'shares'      => 1230,
                'views'       => '987K',
                'duration'    => '1:05',
                'tags'        => ['FilmMaking', 'Hollywood', 'BehindTheScenes'],
                'verified'    => false,
            ],
            [
                'title'       => 'Cosplay Anime Terbaik di Comic Con Jakarta 2026 💥',
                'description' => 'Comic Con Jakarta tahun ini beneran spektakuler! Cosplayer dari seluruh Indonesia unjuk kebolehan dengan kostum yang super detail dan kreatif.',
                'author'      => 'NEXUS Entertainment',
                'handle'      => '@nexusentertainment',
                'category'    => 'entertainment',
                'video_url'   => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ElephantsDream.mp4',
                'thumbnail'   => 'https://images.unsplash.com/photo-1689785916276-12cf30c160dd?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&w=800',
                'likes'       => 27600,
                'comments'    => 1432,
                'shares'      => 2890,
                'views'       => '1.8M',
                'duration'    => '1:18',
                'tags'        => ['ComicCon', 'Cosplay', 'Anime', 'Jakarta'],
                'verified'    => true,
            ],
        ];

        foreach ($shorts as $data) {
            Short::create($data);
        }
    }
}
