<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'Front\IndexController@index');
Route::get('/single/{slug}','Front\IndexController@single')->name('single');
//Route::get('/berita/{slug}','Front\IndexController@single')->name('berita.depan');

Auth::routes();

Route::group(['prefix'=>'admin','middleware' => ['role:admin']], function () {
    Route::get('/', 'Admin\HomeController@index')->name('admin.home');
	Route::get('/inkubator', 'Inkubator\InkubatorController@index')->name('admin.inkubator');
	Route::get('/inkubator/{view}', 'Inkubator\InkubatorController@tampil')->name('admin.inkubator.view');
	Route::get('/tenant', 'Tenant\TenantController@index')->name('admin.tenant');
	Route::get('/tenant/{kategori}', 'Tenant\TenantController@kategori')->name('admin.tenant-kategori');
	Route::get('/tenant/{kategori}/{id}', 'Tenant\TenantController@detail')->name('admin.tenant-detail');
	Route::get('/chat', 'Chat\ChatController@index')->name('admin.chat');

});

Route::group(['prefix'=>'inkubator','middleware' => ['role:inkubator']], function () {
    Route::get('/', 'Inkubator\HomeController@index')->name('inkubator.home');
    Route::get('/tenant', 'Tenant\TenantController@index')->name('inkubator.tenant');
	Route::get('/tenant/{kategori}', 'Tenant\TenantController@kategori')->name('inkubator.tenant-kategori');
	Route::get('/tenant/{kategori}/{id}', 'Tenant\TenantController@detail')->name('inkubator.tenant-detail');

	Route::get('/mentor', 'Mentor\MentorController@index')->name('inkubator.mentor');
	Route::get('/produk', 'Produk\ProdukController@index')->name('inkubator.produk');
	Route::get('/aktifitas', 'Produk\ProdukController@index')->name('inkubator.aktifitas');
	Route::get('/keuangan', 'Produk\ProdukController@index')->name('inkubator.keuangan');
	Route::get('/pencapaian', 'Produk\ProdukController@index')->name('inkubator.pencapaian');
	Route::get('/laporan', 'Produk\ProdukController@index')->name('inkubator.laporan');
	Route::get('/surat', 'Persuratan\PersuratanController@index')->name('inkubator.surat');
	//Route::get('/event', 'Produk\ProdukController@index')->name('inkubator.event');
	//Route::get('/berita', 'Produk\ProdukController@index')->name('inkubator.berita');
	//Route::get('/pengumuman', 'Produk\ProdukController@index')->name('inkubator.pengumuman');
	Route::get('/event', 'Event\EventController@index')->name('inkubator.event-list');
	Route::get('/event/calendar', 'Event\EventController@calendar')->name('inkubator.event-calendar');
    Route::get('/pengumuman', 'Pengumuman\PengumumanController@index')->name('inkubator.pengumuman');
    Route::get('/berita', 'Berita\BeritaController@index')->name('inkubator.berita');
    //Alvi Adnan Vazshola
    Route::get('/berita/create', 'Berita\BeritaController@create')->name('inkubator.formBerita');
    Route::post('/berita/store', 'Berita\BeritaController@store')->name('inkubator.storeBerita');
    Route::delete('/berita/destroy/{berita}', 'Berita\BeritaController@destroy')->name('inkubator.destroyBerita');
    Route::get('berita/edit/{id}','Berita\BeritaController@edit')->name('inkubator.editBerita');
    Route::put('berita/update/{id}','Berita\BeritaController@update')->name('inkubator.updateBerita');
    Route::get('/berita/{slug}', 'Berita\BeritaController@show')->name('inkubator.showBerita');
    Route::post('/berita/komentar','Berita\BeritaController@komentar')->name('inkubator.komentarBerita');
    //End
    Route::get('/berita/kategori', 'Berita\KategoriController@kategori')->name('inkubator.kategori');
    Route::get('/chat', 'Chat\ChatController@index')->name('inkubator.chat');
    Route::get('/pesan', 'Pesan\PesanController@index')->name('inkubator.pesan');
	Route::get('/profile', 'Profile\ProfileUserController@index')->name('inkubator.profile');
	
	//komentar
	//Route::get('/{slug}', 'Berita\BeritaKomentarController@show');
	Route::post('/berita/comment', 'Berita\BeritaKomentarController@comment')->name('inkubator.berita.comment');
	Route::get('/berita/destroy/{id}', 'Berita\BeritaKomentarController@destroy')->name('inkubator.berita.destroy');


    /*========================================================== Kategori ===================================================================*/
    // Route::resource('kategori', 'Berita\KategoriController')->except(['create', 'show']);

    //Route::get('/berita/kategori', 'Berita\KategoriController@index')->name('inkubator.kategori.index');

    Route::get('/berita/kategori/create', 'Berita\KategoriController@create')->name('inkubator.kategori.create');
    Route::post('/berita/kategori/create','Berita\KategoriController@store');

    Route::get('/berita/kategori/{kategori}/edit', 'Berita\KategoriController@edit')->name('inkubator.kategori.edit');

    Route::patch('/berita/kategori/{kategori}/edit', 'Berita\KategoriController@update')->name('inkubator.kategori.update');
    Route::delete('/berita/kategori/{kategori}/delete', 'Berita\KategoriController@destroy')->name('inkubator.kategori.destroy');

    //Search
    Route::get('cariberita','Berita\BeritaController@search')->name('cariberita');
});




Route::group(['prefix'=>'mentor','middleware' => ['role:mentor']], function () {
    Route::get('/', 'Mentor\HomeController@index')->name('mentor.home');
	Route::get('/chat', 'Chat\ChatController@index')->name('mentor.chat');
});

Route::group(['prefix'=>'tenant','middleware' => ['role:tenant']], function () {
    Route::get('/', 'Tenant\HomeController@index')->name('tenant.home');
	Route::get('/chat', 'Chat\ChatController@index')->name('tenant.chat');
});

Route::group(['prefix'=>'user','middleware' => ['role:user']], function () {
    Route::get('/', 'User\HomeController@index')->name('user.home');
    Route::get('/chat', 'Chat\ChatController@index')->name('user.chat');
});
