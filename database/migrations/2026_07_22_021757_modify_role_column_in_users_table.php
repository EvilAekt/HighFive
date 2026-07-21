<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add 'user' and 'owner' to the ENUM while keeping 'pengunjung' temporarily
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('pengunjung', 'user', 'admin', 'owner') DEFAULT 'user'");
        
        // 2. Migrate existing 'pengunjung' data to 'user'
        \Illuminate\Support\Facades\DB::statement("UPDATE users SET role = 'user' WHERE role = 'pengunjung'");
        
        // 3. Remove 'pengunjung' from the ENUM list entirely
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('user', 'admin', 'owner') DEFAULT 'user'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('pengunjung', 'user', 'admin', 'owner') DEFAULT 'pengunjung'");
        \Illuminate\Support\Facades\DB::statement("UPDATE users SET role = 'pengunjung' WHERE role = 'user'");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('pengunjung', 'admin') DEFAULT 'pengunjung'");
    }
};
