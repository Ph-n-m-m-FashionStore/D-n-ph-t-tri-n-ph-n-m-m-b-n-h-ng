<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder {
    public function run() {
    // Xóa toàn bộ dữ liệu cũ để tránh lỗi trùng email, không dùng truncate vì có ràng buộc khóa ngoại
    DB::table('users')->delete();
        DB::table('users')->insert([
            [
                'name' => 'Nguyễn Văn A',
                'email' => 'a@example.com',
                'phone' => '0901234567',
                'password' => Hash::make('password'),
                'role' => 'customer',
            ],
            [
                'name' => 'Trần Thị B',
                'email' => 'b@example.com',
                'phone' => '0902345678',
                'password' => Hash::make('password'),
                'role' => 'customer',
            ],
            [
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'phone' => '0909999999',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ],
        ]);
    }
}
