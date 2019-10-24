# README
Untuk Tugas Besar 1 Mata Kuliah **IF-3110 Pengembangan Aplikasi Berbasis Web**.
## ***Requirement***

1. Sistem Operasi disarankan berbasis **Linux**.
2. **Docke**r dan **Docker-Compose**.
3. **MYSQL Workbench** atau perangkat lunak semacamnya untuk mengakses basis data.

## **Cara instalasi dan menjalankan *server* :**

***Server untuk saat ini dapat dijalankan di sistem operasi berbasis Linux. Untuk sistem operasi lainnya harap menyesuaikan.***

1. Buat sebuah *file* `.env` di direktori. Setelah itu, *copy* isi dari *file* `ENV.SAMPLE` ke dalam *file* `.env`. Sesuaikan isinya dengan *environment* perangkat yang digunakan.
2. Unduh dahulu Docker dan Docker Compose. Pengunduhan dapat dilakukan melalui https://docs.docker.com/install/linux/docker-ce/ubuntu/ dan https://docs.docker.com/compose/install/ atau melalui *package manager* yang dimiliki.
2. Setelah dipastikan Docker dan Docker Compose terinstall dengan baik, jalankan perintah `docker-compose up` atau `sudo docker-compose up` di direktori yang terdapat `docker-compose.yml`. Docker akan mulai mengunduh *image* yang dibutuhkan. Setelah proses mengunduh selesai, Docker akan langsung menjalankan *container* untuk Apache-PHP *webserver* dan MYSQL. Untuk *container* MYSQL mungkin akan membutuhkan waktu beberapa menit sebelum selesai melakukan inisialisasi.
3. Setelah dipastikan *container* Apache-PHP *webserver* dan MYSQL berjalan dengan baik (dengan mencoba mengakses *localhost* melalui browser untuk Apache PHP *webserver* dan membuka basis data MYSQL dengan MYSQL Workbench atau sejenisnya), jalankan perintah `make db-migrate` di direktori yang terdapat `Makefile`. Proses migrasi basis data akan dimulai. Untuk me-*revert* migrasi, jalankan perintah `make db-drop` yang akan meng-*drop* semua tabel pada basis data.
4. Apache-PHP *webserver* dan MYSQL siap digunakan dan dapat diakses.

## Cara Kerja
Semua *request* ke *webserver* akan diarahkan ke `router.php`. Setelah itu, dilakukan proses *routing*. Proses *routing* bekerja dengan cara membagi URL menjadi tiga bagian, yaitu:

`domain/controller/action`

Sebagai contoh, URL `localhost/user/register` akan dibagi menjadi:
- *Domain*: `localhost`

- *Controller*: `user`

- *Action*: `register`

Setelah itu, *router* akan mengarahkan halaman ke *controller* dan *action* yang sesuai. Apabila tidak terdapat *action* pada URL, maka akan diarahkan ke *action* `index`. Adapun *domain* dibagi menjadi dua, yaitu `domain` dan `api.domain`, yang membedakan antara *request* biasa dengan *request* untuk API.

## Tech Stack
* **Front-End**
	* HTML
	* CSS
	* Javascript
	
* **Back-End**
	* PHP

## Useful Links
* https://docs.docker.com/install/linux/docker-ce/ubuntu/
* https://docs.docker.com/compose/install/
* https://stackoverflow.com/questions/23111631/cannot-download-docker-images-behind-a-proxy