<?php

use App\Models\Admin;
use App\Models\Permission;
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
        // Drop the old table if it exists
        Schema::dropIfExists('role_permssions');

        // Create the new table with correct spelling
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Admin::class, 'admin_id');
            $table->foreignIdFor(Permission::class, 'permission_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_permissions');
    }
};
