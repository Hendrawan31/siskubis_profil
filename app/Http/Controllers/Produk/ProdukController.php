<?php

namespace App\Http\Controllers\Produk;


use Auth;
use File;
use Image;
use App\User;
use App\Produk;
use App\ProdukBisnis;
use App\ProdukCanvas;
use App\ProdukIjin;
use App\ProdukImage;
use App\ProdukKI;
use App\ProdukRiset;
use App\ProdukSertifikasi;
use App\ProdukTeam;
use App\Tenant;
use App\Priority;
use App\Inkubator;
use App\ProfilUser;
use App\TenantUser;
use App\profil_user;
use App\TenantMentor;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Spatie\QueryBuilder\{QueryBuilder, AllowedFilter};

class ProdukController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $priority = Priority::orderBy('name', 'ASC')->get();

        if(request()->has('filter')){
            if(array_key_exists('title', $request->filter)){
                $title = request()->filter['title'];
            }else{
                $title = null;
            }
        }else{
            $title = null;
        }

        if ( $request->user()->hasRole('inkubator') ) {
            $ink = Tenant::where('inkubator_id', $request->user()->inkubator_id)->get('id');
            $produk = QueryBuilder::for(Produk::class)
                ->allowedFilters([
                    AllowedFilter::partial('title'),
                    AllowedFilter::exact('priority', 'priority_id'),
                ])
                ->with('tenant','priority','produk_image')
                ->whereIn('tenant_id', $ink )
                ->paginate(12);
        }elseif($request->user()->hasRole('mentor')){
            $mentor = TenantMentor::where('user_id', $request->user()->id)->get('id');
            $produk = QueryBuilder::for(Produk::class)
                ->allowedFilters([
                    AllowedFilter::partial('title'),
                    AllowedFilter::exact('priority', 'priority_id'),
                ])
                ->whereIn('tenant_id', $mentor)
                ->paginate(12);
        }elseif($request->user()->hasRole('tenant')){
            $tenant = TenantUser::where('user_id', $request->user()->id)->first();
            $produk = QueryBuilder::for(Produk::class)
                ->allowedFilters([
                    AllowedFilter::partial('title'),
                    AllowedFilter::exact('priority', 'priority_id'),
                ])
                ->where('tenant_id', $tenant->tenant_id)
                ->paginate(12);
        }
        return view('produk.index', compact('produk','priority','title'));
    }

	public function show($id)
    {
        $produk         = Produk::find($id);
        $produk         = Produk::with(['tenant','priority','produk_bisnis','produk_canvas','produk_ijin','produk_image','produk_ki','produk_riset','produk_sertifikasi'])->where('id', $id)->first();
        $image          = ProdukImage::where('produk_id',$id)->get();
        $produk_team    = ProdukTeam::with('profil_user.user')->where('produk_id', $id)->get();

        return view('produk.detailProduk', compact('produk','produk_team','image'));
    }

    public function create()
    {

        $user_id = ProfilUser::orderBy('nama')->get();
        $tenant = Tenant::orderBy('title')->get();
        // $penulis = profil_user::orderBy('nama')->get();

        return view('produk.formTambah', compact('user_id', 'tenant'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'foto'                  => 'required|image|mimes:jpg,png,jpeg',

        ]);

        // dd($validator);

        if ($request->hasFile('foto')) {

            $produk_id = Produk::orderBy('id','DESC')->first();
            $produks_id = $produk_id->id + 1;
            //$id = $produks_id + 1;
            //return $produks_id;
            $pd_image = $produks_id;
            $pd_team = $produks_id;

            $image = $request->file('foto');
            $filename = time() . Str::slug($request->tittle) . '.' . $image->getClientOriginalExtension();
            $image_resize = Image::make($image->getRealPath());
            $image_resize->resize(900,585);
            $image_resize->save(public_path('img/produk/'.$filename));

            $file = $request->file('proposal');
            $dokumen_file_proposal = time()."_".$file->getClientOriginalExtension();
            $file->storeAs('file/produk/dokumen_proposal/',$dokumen_file_proposal);

            $file = $request->file('file_sertifikasi');
            $dokumen_file_sertifikasi = time()."_".$file->getClientOriginalExtension();
            $file->storeAs('file/produk/dokumen_sertifikasi/',$dokumen_file_sertifikasi);

            $file = $request->file('file_ijin');
            $dokumen_file_ijin = time()."_".$file->getClientOriginalExtension();
            $file->storeAs('file/produk/dokumen_ijin/',$dokumen_file_ijin);
            
            $string = implode(",", $request->tag);
            
            // $produk = Produk::create([

            //     'id'                    => $produks_id,
            //     'tenant_id'             => $request->tenant_id,
            //     'inventor_id'           => $request->inventor_id,
            //     'priority_id'           => $request->priority_id,
            //     'title'                 => $request->title,
            //     'subtitle'              => $request->subtitle,
            //     'harga_pokok'           => $request->harga_pokok,
            //     'harga_jual'            => $request->harga_jual,
            //     'tag'                   => $string,
            //     'location'              => $request->location,
            //     'address'               => $request->address,
            //     'contact'               => $request->contact,
            //     'tentang'               => $request->tentang_produk,
            //     'latar'                 => $request->latar_produk,
            //     'keterbaharuan'         => $request->keterbaharuan,
            //     'spesifikasi'           => $request->spesifikasi,
            //     'manfaat'               => $request->manfaat,
            //     'keunggulan'            => $request->keunggulan,
            //     'teknologi'             => $request->teknologi,
            //     'pengembangan'          => $request->pengembangan,
            //     'proposal'              => $dokumen_file_proposal,
            //     'kategori_id'           => $request->kategori,
            // ]);
            
            // $produk_bisnis = ProdukBisnis::create([
            //     'produk_id'             => $produks_id,
            //     'kompetitor'            => $request->kompetitor,
            //     'target_pasar'          => $request->target_pasar,
            //     'dampak_sosek'          => $request->dampak_sosek,
            //     'produksi_harga'        => $request->produksi_harga,
            //     'pemasaran'             => $request->pemasaran,
            // ]);
            
            // $produk_canvas =ProdukCanvas::create([
            //     'produk_id'             => $produks_id,
            //     'canvas'                => $request->editor1,
            //     'kategori'              => $string,// $request->kategori_canvas,
            //     'tanggal'               => $request->tanggal_canvas,
            // ]);
            
            // $produk_image = ProdukImage::create([
            //     'produk_id'             => $produks_id,
            //     'image'                 => $filename,
            //     'judul'                 => $filename,
            //     'caption'               => 'null',
            // ]);

            // $produk_ki = ProdukKI::create([
            //     'produk_id'             => $produks_id,
            //     'jenis_ki'              => $request->jenis_ki,
            //     'status_ki'             => $request->status_ki,
            //     'permohonan'            => $request->permohonan_ki,
            //     'sertifikat'            => $request->sertifikat_ki,
            //     'berlaku_mulai'         => $request->berlaku_mulai,
            //     'berlaku_sampai'        => $request->berlaku_sampai,
            //     'pemilik_ki'            => $request->pemilik_ki,
            // ]);
            
            
            // dd($request->all());
            // $produk_team = ProdukTeam::create([
            //     'produk_id'             => $produks_id,
            //     'user_id'               => $request->user_id,
            //     'jabatan'               => $request->jabatan,
            //     'divisi'                => $request->divisi,
            //     'tugas'                 => $request->tugas,
            // ]);
            
            // $produk_riset = ProdukRiset::create([
            //     'produk_id'             => $produks_id,
            //     'nama_riset'            => $request->nama_riset,
            //     'pelaksana'             => $request->pelaksana_riset,
            //     'tahun'                 => $request->tahun_riset,
            //     'pendanaan'             => $request->pendanaan_riset,
            //     'skema'                 => $request->skema_riset,
            //     'nilai'                 => $request->nilai_riset,
            //     'aktifitas'             => $request->aktifitas_riset,
            //     'tujuan'                => $request->tujuan_riset,
            //     'hasil'                 => $request->hasil_riset,
            // ]);
            
            $produk_sertifikasi = ProdukSertifikasi::create([
                'produk_id'             => $produks_id,
                'jenis_sertif'          => $request->jenis_sertif,
                'pemberi_sertif'        => $request->pemberi_sertif,
                'status'                => $request->status_sertif,
                'tahun'                 => $request->tahun_sertif,
                'tanggal'               => $request->tanggal_sertif,
                'dokumen'               => $dokumen_file_sertifikasi,
            ]);
                dd('ok');

            $produk_ijin = ProdukIjin::create([
                'produk_id'             => $produks_id,
                'jenis_sertif'          => $request->jenis_ijin,
                'pemberi       '        => $request->pemberi_ijin,
                'status'                => $request->status_ijin,
                'tahun'                 => $request->tahun_ijin,
                'tanggal'               => $request->tanggal_ijin,
                'dokumen'               => $dokumen_file_ijin,
            ]);

            // $notification = array(
            //     'message' => 'Berita Berhasil Ditambahkan!',
            //     'alert-type' => 'success'
            // );

            // return view('tenant.produk');

            // return redirect(route('tenant.produk'));
        }
        return dd($validator);
        // return "ok";
    }

    public function destroy(Produk $produk)
    {
        $produk->delete();
        File::delete(storage_path('app/public/img/produk' . $produk->foto));

        return redirect()->back();
    }

    public function edit($id)
    {
        $produk = Produk::find($id);

        return $produk;
    }

    public function update($id, Request $request)
    {
        $produk = Produk::find($id);
    }
}
