<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $query = Product::where('is_active', 1);
        $request = request();
        // Tìm kiếm
        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            if ($search !== '') {
                $query->where('name', 'like', '%' . $search . '%');
            }
        }
        // Lọc theo giá tối thiểu
        if ($request->filled('min_price')) {
            $min = $request->input('min_price');
            if (is_numeric($min)) {
                $query->where('price', '>=', (float) $min);
            }
        }
        // Lọc theo giới tính (gender) - hỗ trợ chọn nhiều
        if ($request->has('gender')) {
            $genderInput = $request->input('gender');

            // Chuẩn hóa giá trị hợp lệ
            $normalize = function ($value) {
                if (!is_string($value)) return null;
                $v = strtolower(trim($value));
                if ($v === 'all' || $v === '' || $v === 'unisex') return null; // Bỏ unisex khỏi lọc
                // Chuẩn hóa cách viết
                $map = [
                    'nữ' => 'nu', 'nu~' => 'nu', 'female' => 'nu', 'f' => 'nu',
                    'nam' => 'nam', 'male' => 'nam', 'm' => 'nam',
                    'unisex' => 'unisex', 'uni' => 'unisex',
                ];
                // Nếu là giá trị đã đúng chuẩn, giữ nguyên
                if (in_array($v, ['nam', 'nu'])) return $v;
                // Thử map
                if (array_key_exists($v, $map)) return $map[$v];
                return null;
            };

            // Nếu là chuỗi nhiều giá trị ngăn cách bằng dấu phẩy hoặc dấu |
            if (!is_array($genderInput) && is_string($genderInput) && (str_contains($genderInput, ',') || str_contains($genderInput, '|'))) {
                $parts = preg_split('/[,|]/', $genderInput);
                $genderInput = array_map('trim', $parts);
            }

            if (is_array($genderInput)) {
                $genders = array_values(array_filter(array_map($normalize, $genderInput)));
                if (!empty($genders)) {
                    $query->whereIn('gender', $genders);
                }
            } else {
                $gender = $normalize($genderInput);
                if ($gender) {
                    $query->where('gender', $gender);
                }
            }
        }
        $products = $query->latest()->paginate(12);
        return view('home', compact('products'));
    }
}
