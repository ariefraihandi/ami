<?php


namespace App\Http\Controllers\Portal;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\ValidationException;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\ItemInvoice;
use App\Models\FinancialTransaction;
use App\Models\Product;
use App\Models\Category;
use App\Models\Menu;
use App\Models\MenuSub;
use App\Models\MenuSubsChild;
use App\Models\UserRole;
use App\Models\AccessMenu;
use App\Models\AccessSub;
use App\Models\AccessSubChild;
use PDF;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $user = Auth::user();
        if (!$user) {
          
            return redirect('/login');
        }

        if ($request->ajax()) {
            $keuangan = Product::orderBy('created_at', 'desc')->get();
    
            $formattedKeuangan = $keuangan->map(function ($item) {
                return [
                    'id'                    => $item->id,
                    'name'                  => $item->name,
                    'kode'                  => $item->kode,
                    'deskripsi'             => $item->deskripsi,
                    'stock'                 => $item->stock,
                    'gambar'                => $item->gambar,
                    'status'                => $item->status,
                    'category'              => $item->category,
                    'transaction_amount'    => number_format($item->transaction_amount),
                    'payment_method'        => $item->payment_method,  
                    'created_at'            => Carbon::parse($item->created_at)->isoFormat('D MMM YY'),
                    'description'           => $item->description,  
                ];
            });
    
            return response()->json(['data' => $formattedKeuangan]);
        }
    
        $categories     = Category::all(); 
        $cateCount      = Category::count(); 
        $productData    = Product::all(); 
        $productCount   = Product::count();
        $productLow     = Product::where('stock', '<', 10)->count();
        $inProductCount = Product::where('status', '=', 'inactive')->count();
        $categor        = Category::all()->pluck('nama')->toArray();

        $accessMenus = AccessMenu::where('user_id', $user->role)->pluck('menu_id');
        $accessSubmenus = AccessSub::where('role_id', $user->role)->pluck('submenu_id');
        $accessChildren = AccessSubChild::where('role_id', $user->role)->pluck('childsubmenu_id');
    
        // Mengambil data berdasarkan hak akses
        $menus = Menu::whereIn('id', $accessMenus)->get();
        $subMenus = MenuSub::whereIn('id', $accessSubmenus)->get();
        $childSubMenus = MenuSubsChild::whereIn('id', $accessChildren)->get();
    
        $additionalData = [
            'title'         => 'Product & Stock',
            'subtitle'      => 'Index',
            'menus'         => $menus,
            'subMenus'      => $subMenus,
            'childSubMenus' => $childSubMenus,
            'categories'    => $categories, 
            'cateCount'     => $cateCount, 
            'productData'   => $productData,
            'productCount'  => $productCount,
            'productLow'    => $productLow,
            'inProductCount'    => $inProductCount,
            'categor'       => $categor,
        ];
    
        return view('Konten/Produk/indexProduk', $additionalData);
    }

    public function create(Request $request)
    {
        try {
            // Validasi data sesuai kebutuhan
            $request->validate([
                'productName' => 'required|string|max:255',
                'productCode' => 'required|string|max:255|unique:products,kode', // Ensure unique code
                'productStock' => 'required|integer',
                'productDescription' => 'nullable|string',
                // Tambahkan validasi lainnya sesuai kebutuhan
            ]);

            // Susun data untuk disimpan ke dalam database
            $productData = [
                'name' => $request->input('productName'),
                'kode' => $request->input('productCode'),
                'deskripsi' => $request->input('productDescription') ?? '', // Jika kosong, set menjadi string kosong
                'stock' => $request->input('productStock'),
                'category' => $request->input('productCategory'),
                'status' => $request->input('productStatus'),
                'harga_beli' => 0, // Atur sesuai kebutuhan Anda
                'harga_jual_individu' => 0,
                'harga_jual_biro' => 0,
                'harga_jual_instansi' => 0,
                'gambar' => 'product.png', // Ganti dengan nama gambar default yang Anda inginkan
            ];

            // Begin a database transaction
            DB::beginTransaction();

            // Simpan data produk ke database
            $product = Product::create($productData);

            // Commit the transaction if successful
            DB::commit();

            $response = [
                'success' => true,
                'message' => 'Data berhasil disimpan.',
            ];

            return redirect()->to('product')->with('response', $response);
        } catch (QueryException $e) {
            // Rollback the transaction in case of an error
            DB::rollback();

            // Catch the exception for duplicate entry violation
            $errorCode = $e->errorInfo[1];

            if ($errorCode == 1062) {
                // Duplicate entry error
                $response = [
                    'success' => false,
                    'message' => 'Duplicate entry. Product code already exists.',
                ];

                return redirect()->route('home')->with('response', $response);
            }

            // Re-throw other exceptions
            throw $e;
        }
    }

    public function updateProduct(Request $request)
    {
        $request->validate([
            'editProductName' => 'required',
            'editProductCode' => 'required',
            'editProductStock' => 'required',
            'editProductStatus' => 'required',
            'editProductCategory' => 'required',
            'editProductDescription' => 'required',
            'editProductImage' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        $id = $request->input('editProductId'); // Update the input name to 'editProductId'
        $product = Product::findOrFail($id);
        $product->name = $request->editProductName;
        $product->kode = $request->editProductCode;
        $product->stock = $request->editProductStock;
        $product->status = $request->editProductStatus;
        $product->category = $request->editProductCategory;
        $product->deskripsi = $request->editProductDescription;
    
        if ($request->hasFile('editProductImage')) {
            // Delete the existing image if it exists and it is not the default image
            if ($product->gambar && $product->gambar !== 'product.png') {
                $imagePath = public_path('assets/img/ecommerce-images/' . $product->gambar);
                if (File::exists($imagePath)) {
                    File::delete($imagePath);
                }
            }
    
            // Upload and save the new image
            $image = $request->file('editProductImage');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('assets/img/ecommerce-images'), $imageName);
            $product->gambar = $imageName;
        }
    
        $product->save();
        
        return redirect()->back()->with('response', [
            'success' => true,
            'message' => 'Product updated successfully.',
        ]);
    }
    
    public function indexCategory()
    {

        $user = Auth::user();
        if (!$user) {
          
            return redirect('/login');
        }

        $categories = Category::all();        

        $accessMenus = AccessMenu::where('user_id', $user->role)->pluck('menu_id');
        $accessSubmenus = AccessSub::where('role_id', $user->role)->pluck('submenu_id');
        $accessChildren = AccessSubChild::where('role_id', $user->role)->pluck('childsubmenu_id');
    
        // Mengambil data berdasarkan hak akses
        $menus = Menu::whereIn('id', $accessMenus)->get();
        $subMenus = MenuSub::whereIn('id', $accessSubmenus)->get();
        $childSubMenus = MenuSubsChild::whereIn('id', $accessChildren)->get();

        $additionalData = [
            'title'         => 'Product',
            'subtitle'      => 'Categories',
            'categories'    => $categories,
            'menus'         => $menus,
            'subMenus'      => $subMenus,
            'childSubMenus' => $childSubMenus,
        ];
    
        return view('Konten/Produk/categoryProduk', $additionalData);
    }

    public function storeCategory(Request $request)
    {
        // Validasi request jika diperlukan
        $request->validate([
            'categoryName' => 'required|string|max:255', // Sesuaikan dengan aturan validasi Anda
        ]);

        try {
            // Simpan kategori ke database
            $category = new Category();
            $category->nama = $request->input('categoryName');
            $category->save();

            // Berhasil, kirimkan respons ke view
            return redirect()->back()->with('response', [
                'success' => true,
                'message' => 'Kategori berhasil ditambahkan.',
            ]);
        } catch (\Exception $e) {
            // Gagal, kirimkan respons error ke view
            return redirect()->back()->with('response', [
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ]);
        }
    }

    public function updateCategory(Request $request)
    {
        // Validasi request jika diperlukan
        $request->validate([
            'editCategoryId' => 'required|exists:categories,id',
            'editCategoryName' => 'required|string|max:255', // Sesuaikan dengan aturan validasi Anda
        ]);

        try {
            // Ambil kategori dari database berdasarkan ID
            $category = Category::findOrFail($request->input('editCategoryId'));

            // Update nama kategori
            $category->nama = $request->input('editCategoryName');
            $category->save();

            // Berhasil, kirimkan respons ke view
            return redirect()->back()->with('response', [
                'success' => true,
                'message' => 'Kategori berhasil diperbarui.',
            ]);
        } catch (\Exception $e) {
            // Gagal, kirimkan respons error ke view
            return redirect()->back()->with('response', [
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ]);
        }
    }

    public function deleteCategory($id)
    {
        try {
            // Temukan kategori berdasarkan ID
            $category = Category::findOrFail($id);

            // Hapus kategori
            $category->delete();

            return redirect()->back()->with('response', [
                'success' => true,
                'message' => 'Kategori berhasil dihapus.',
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('response', [
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ]);
        }
    }

    public function store(Request $request)
    {
        //
    }

    public function getProduct($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        return response()->json($product);
    }

    // New method to get all products
    public function getProducts()
    {
        $products = Product::all();

        return response()->json($products);
    }

    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

     
    public function destroy(string $id)
    {
        //
    }
}
