<php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->timestamp('status_changed_at')
                  ->nullable()
                  ->after('status');

            $table->index('status_changed_at');
        });

        // Backfill existing records (best guess)
        DB::table('appointments')
            ->whereNull('status_changed_at')
            ->update([
                'status_changed_at' => DB::raw('updated_at')
            ]);
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropIndex(['status_changed_at']);
            $table->dropColumn('status_changed_at');
        });
    }
};