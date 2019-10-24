# API Blueprint
Daftar Endpoint untuk API.

Semua *request* untuk API ditembak ke `api.domain` dengan `domain` yang sesuai, misal `api.localhost`.

## User
* **User Login**  

    Endpoint: `/user/login`

    Method: **Post**  
    
    Body Parameters:
 
    * `email` : Email pengguna.
 
    * `pass` : Password pengguna.
 
    Response: Status keberhasilan proses login. Adapun proses login adalah mengecek parameter `email` apakah terdaftar dan sudah sesuai dengan `pass`, dan akan mengeset *cookies* jika sesuai.

* **User Registration**  

    Endpoint: `/user/register`
    
    Method: **POST**
    
    Body Parameters:

    * `username` : Nama pengguna.

    * `email`: Email pengguna.

    * `pass`: Password pengguna.

    * `phone_number`: Nomor telepon pengguna.

    * `image` **[Optional]** : Foto pengguna yang akan diunggah.

    Response: Status keberhasilan pendaftaran, dan alasan jika gagal.

* **Get User Data**

    Endpoint: `/user/get_user`
    
    Method: **POST**
    
    Body Parameters:

    * `username` **[Optional]** : Nama pengguna.
 
    * `email` **[Optional]** : Email pengguna.
 
    * `phone_number` **[Optional]** : Nomor telepon pengguna.

    Response: Data dari pengguna jika ada.  
    
    Notes: Parameter harus ada, namun dipilih antara `username`, `email`, atau `phone_number`.

## Movies
* **Get Current Playing Movies**

    Endpoint: `/movies/current`
    
    Method: **GET**
    
    Response: Daftar dan data dari film yang sedang diputar.

* **Search Movies**

    Endpoint: `/movies/search`
 
    Method: **GET**
 
    Parameters:

    * `movie_name` : Nama film yang dicari.
    
    Response: Daftar dan data dari film yang memenuhi syarat pencarian.

* **Get Movie Detail**

    Endpoint: `/movies/get`
    
    Method: **GET**
    
    Parameters:

    * `movie_id` : ID dari film yang ingin dilihat.

    Response: Data dari film.

* **Get Movie Reviews**

    Endpoint: `/movies/reviews`
    
    Method: **GET**
    
    Parameters:

    * `movie_id` : ID dari film.

    Response: Daftar ulasan film dari berbagai pengguna.

* **Add Review**

    Endpoint: `/movies/add_review`
    
    Method: **POST**
    
    Body Parameters:
 
    * `user_id` : ID dari pengguna.
 
    * `movie_id` : ID dari film.
 
    * `score` : Nilai dari ulasan.
 
    * `content` : Pesan dari ulasan.
 
    Response: Status keberhasilan dari penambahan ulasan.

* **Get Movie Score**

    Endpoint: `/movies/score`
    
    Method: **GET**
    
    Parameters:
 
    * `movie_id` : ID dari film.
 
    Response: Nilai rata-rata ulasan film dari berbagai pengguna.

* **Get Movie Schedule**

    Endpoint: `/movies/schedule`
    
    Method: **GET**
    
    Parameters
 
    * `movie_id` : ID dari film.
 
    Response: Daftar dan data penayangan dari film.

* **Get Specific Showing**

    Endpoint: `/movies/showing`
    
    Method: **GET**
    
    Parameters:
 
    * `showing_id` : ID dari penayangan film.
 
    Response: Data dari penayangan film.

## Transactions

* **Get Transactions**

    Endpoint: `/transactions/get`
    
    Method: **GET**
    
    Parameters:
 
    * `user_id` **[Optional]** : ID dari pengguna.
    
    * `username` **[Optional]** : Nama dari pengguna.
 
    Response: Daftar dari transaksi dan data rincinya, serta status apakah pengguna belum dapat memberi ulasan, sudah dapat memberi ulasan, atau sudah memberi ulasan.
    
    Notes: Parameter harus ada, namun dipilih antara `user_id` atau `username`.

* **Buy a Seat of a Showing**

    Endpoint: `/transactions/buy`
    
    Method: **POST**
    
    Body Parameters:
    
    * `user_id` : ID dari pengguna.
 
    * `showing_id` : ID dari penayangan film.
 
    * `seat_id` : ID dari kursi.
 
    Response: Status keberhasilan pembelian.