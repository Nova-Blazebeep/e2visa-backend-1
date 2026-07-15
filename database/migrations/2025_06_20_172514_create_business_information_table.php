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
        Schema::create('business_information', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable()->index();
            $table->unsignedBigInteger('sub_category_id')->nullable()->index();
            $table->unsignedBigInteger('country_id')->nullable()->index();
            $table->unsignedBigInteger('state_id')->nullable()->index();
            $table->unsignedBigInteger('county_id')->nullable()->index();
            $table->unsignedBigInteger('business_type_id')->nullable()->index();
            $table->string('business_name')->nullable();
            $table->string('is_featured')->default('No');
            $table->string('listing_type')->nullable();
            $table->integer('listing_type_id')->nullable()->index();
            $table->integer('contact_user_id')->nullable()->index();
            $table->string('zip_code')->nullable()->index();
            $table->string('street_address')->nullable();
            $table->boolean('is_confidential_state')->default(false)->index();
            $table->boolean('is_confidential_country')->default(false)->index();
            $table->boolean('is_confidential_city')->default(false)->index();
            $table->boolean('is_confidential_address')->default(false)->index();
            $table->string('contact_name')->nullable()->index();
            $table->string('contact_email')->nullable()->index();
            $table->string('contact_phone')->nullable()->index();
            $table->string('listing_number')->nullable()->index();
            $table->boolean('is_non_disclosure_agreement')->nullable();
            $table->string('listing_heading')->nullable()->index();
            $table->string('business_website')->nullable()->index();
            $table->boolean('is_website_confidential')->default(false);
            $table->text('listing_summary')->nullable();
            $table->decimal('asking_price', 15, 2)->nullable()->index();
            $table->decimal('cash_flow', 15, 2)->nullable()->index();
            $table->decimal('ebitdas', 15, 2)->nullable()->index();
            $table->decimal('gross_revenue', 15, 2)->nullable()->index();
            $table->decimal('inventory', 15, 2)->nullable()->index();
            $table->boolean('inventory_included_in_price')->default(false);
            $table->decimal('ffe', 15, 2)->nullable();
            $table->string('real_estate_status')->nullable()->index();
            $table->string('building_size')->nullable()->index();
            $table->boolean('is_seller_financing_available')->default(false)->index();
            $table->text('seller_note')->nullable();
            $table->integer('number_of_employees')->nullable();
            $table->string('year_established')->nullable();
            $table->text('facilities')->nullable();
            $table->text('support_and_training')->nullable();
            $table->text('reason_for_selling')->nullable();
            $table->text('competition_market_pros_and_cons')->nullable();
            $table->text('growth_and_expansion_pros_and_cons')->nullable();
            $table->boolean('is_required_buyer_telephone_number')->default(false);
            $table->boolean('is_required_buyer_zip_code')->default(false);
            $table->boolean('is_required_buyer_available_funds')->default(false);
            $table->boolean('is_required_buyer_timeframe')->default(false);
            $table->boolean('is_publish')->default(false)->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_information');
    }
};
