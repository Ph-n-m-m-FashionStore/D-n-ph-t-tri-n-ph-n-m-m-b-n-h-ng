<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Requests\ProductRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    // ==================== FRONTEND METHODS ====================
    
    public function index(Request $request)
    {
        $query = Product::where('is_active', 1);
        // Tìm kiếm
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        // Lọc theo thương hiệu
        if ($request->has('brand')) {
            $query->where('brand', $request->brand);
        }
        // Lọc theo size
        if ($request->has('size')) {
            $query->where('size', $request->size);
        }
        // Lọc theo giá nhập vào
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        // Lọc theo giới tính (gender)
        if ($request->has('gender')) {
            $gender = strtolower($request->gender);
            if (in_array($gender, ['nam', 'nu', 'unisex'])) {
                $query->where('gender', $gender);
            }
        }
        // Lọc theo màu sắc
        if ($request->has('color')) {
            $query->where('color', $request->color);
        }
        $products = $query->paginate(12);
        return view('products.index', compact('products'));
    }
    
    public function show($id)
    {
        $product = Product::findOrFail($id);
        $product->image_url = 'images/' . basename($product->image_url);
        return view('product_detail', compact('product'));
    }

    // ==================== ADMIN METHODS ====================

    /**
     * Danh sách sản phẩm cho admin
     */
    public function adminIndex(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        $query = Product::with('reviews')->withCount('reviews');
        
        // Tìm kiếm
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        // Lọc theo trạng thái
        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status);
        }
        
        // Lọc theo loại sản phẩm
        if ($request->has('product_type') && $request->product_type) {
            $query->where('product_type', $request->product_type);
        }

        $products = $query->latest()->paginate(20);
        
        return view('admin.products.index', compact('products'));
    }

    /**
     * Form tạo sản phẩm mới
     */
    public function create()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }
        
        return view('admin.products.create');
    }

    /**
     * Lưu sản phẩm mới
     */
    public function store(ProductRequest $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

    $data = $request->all();
    $data['product_type'] = 'clothing'; // Luôn set là quần áo
        
        // Xử lý upload ảnh (hỗ trợ png, jpg, jpeg)
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            \Log::info('Upload image debug:', [
                'original_name' => $image->getClientOriginalName(),
                'mime_type' => $image->getMimeType(),
                'extension' => $image->getClientOriginalExtension(),
                'size' => $image->getSize(),
            ]);
            $ext = strtolower($image->getClientOriginalExtension());
            if (!in_array($ext, ['png', 'jpg', 'jpeg'])) {
                \Log::error('Image extension not allowed', ['ext' => $ext]);
                return back()->withErrors(['image' => 'Định dạng ảnh không hợp lệ!']);
            }
            $imageName = time() . '_' . Str::slug($request->name) . '.' . $ext;
            $image->move(public_path('images'), $imageName);
            \Log::info('Image moved to public/images', ['imageName' => $imageName]);
            $data['image_url'] = 'images/' . $imageName;
            \Log::info('DEBUG image_url before save', ['image_url' => $data['image_url']]);
        } else {
            \Log::error('No image file found in request');
        }

        $data['is_active'] = $request->has('is_active') ? 1 : 0;
        $data['reference_id'] = null;

        Product::create($data);

        return redirect()->route('admin.products.index')
            ->with('success', 'Sản phẩm đã được tạo thành công!');
    }

    /**
     * Form chỉnh sửa sản phẩm
     */
    public function edit(Product $product)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }
        
        return view('admin.products.edit', compact('product'));
    }

    /**
     * Cập nhật sản phẩm
     */
    public function update(ProductRequest $request, Product $product)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        $data = $request->all();
        
        // Xử lý upload ảnh mới
        if ($request->hasFile('image')) {
            // Xóa ảnh cũ
            if ($product->image_url && Storage::disk('public')->exists(str_replace('storage/', '', $product->image_url))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $product->image_url));
            }
            
            $image = $request->file('image');
            $imageName = time() . '_' . Str::slug($request->name) . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('products', $imageName, 'public');
            $data['image_url'] = 'storage/' . $imagePath;
        }

        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        $product->update($data);

        return redirect()->route('admin.products.index')
            ->with('success', 'Sản phẩm đã được cập nhật thành công!');
    }

    /**
     * Xóa sản phẩm
     */
    public function destroy(Product $product)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        // Xóa ảnh
        if ($product->image_url && Storage::disk('public')->exists(str_replace('storage/', '', $product->image_url))) {
            Storage::disk('public')->delete(str_replace('storage/', '', $product->image_url));
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Sản phẩm đã được xóa thành công!');
    }

    /**
     * Toggle trạng thái sản phẩm
     */
    public function toggleStatus(Product $product)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        $product->update(['is_active' => !$product->is_active]);
        
        $status = $product->is_active ? 'kích hoạt' : 'vô hiệu hóa';
        return redirect()->back()
            ->with('success', "Sản phẩm đã được {$status} thành công!");
    }
}

