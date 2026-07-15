<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Models\Badge;

class BadgeSeeder extends Seeder
{
    public function run()
    {
        $badges = [
            ['role_id' => 1,  'filename' => 'admin-badge.jpg'],
            ['role_id' => 2,  'filename' => 'buyer-badge.png'],
            ['role_id' => 3,  'filename' => 'biz-broker-icon-2.jpg'],
            ['role_id' => 4,  'filename' => 'broker-badge.jpeg'],
            ['role_id' => 5,  'filename' => 'attorney-badge.jpeg'],
            ['role_id' => 6,  'filename' => 'real-estate-agent-icon.jpg'],
            ['role_id' => 7,  'filename' => 'commercial-real-estate-agent-badge-icon.jpg'],
            ['role_id' => 8,  'filename' => 'attorney-badge-icon.jpg'],
            ['role_id' => 9,  'filename' => 'immigration-consultant-badge-icon.jpg'],
            ['role_id' => 10, 'filename' => 'moderator-badge.jpeg'],
            ['role_id' => 11, 'filename' => 'CPA-Accountant-badge-icon.jpg'],
            ['role_id' => 12, 'filename' => 'appraiser-home-and-business-badge-icon.jpg'],
            ['role_id' => 13, 'filename' => 'affiliate-service-badge-icon.jpg'],
            ['role_id' => 14, 'filename' => 'lender-and-loan-officer-badge-icon.jpg'],
            ['role_id' => 15, 'filename' => 'home-inspector-badge-icon.jpg'],
            ['role_id' => 16, 'filename' => 'insurance-badge-icon.jpg'],
            ['role_id' => 17, 'filename' => 'financial-advisor-badge-icon.jpg'],
            ['role_id' => 18, 'filename' => 'consultant-general-badge-icon.jpg'],
            ['role_id' => 19, 'filename' => 'title-company-badge-icon.jpg'],
        ];

        foreach ($badges as $badge) {
            $source = public_path('portal/assets/img/badges/' . $badge['filename']);
            if (!File::exists($source)) {
                continue;
            }

            // Skip if badge with this role_id already exists
            if (Badge::where('role_id', $badge['role_id'])->exists()) {
                continue;
            }

            $file = new \Illuminate\Http\File($source);
            $path = Storage::disk('public')->putFile('badges', $file);

            Badge::create([
                'role_id' => $badge['role_id'],
                'icon' => $path,
            ]);
        }
    }
}