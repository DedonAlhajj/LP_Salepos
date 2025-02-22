<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Product extends Model implements HasMedia
{
    use BelongsToTenant;
    use SoftDeletes;
    use InteractsWithMedia;

    protected $fillable =[
        "name", "code", "type", "slug", "barcode_symbology", "brand_id", "category_id", "unit_id", "purchase_unit_id", "sale_unit_id", "cost", "price", "wholesale_price", "qty", "alert_quantity", "daily_sale_objective", "promotion", "promotion_price", "starting_date", "last_date", "tax_id", "tax_method", "image", "file", "is_embeded", "is_batch", "is_variant", "is_diffPrice", "is_imei", "featured", "product_list", "variant_list", "qty_list", "price_list", "product_details", "short_description", "specification", "related_products", "variant_option", "variant_value", "is_active", "is_online", "in_stock", "track_inventory", "is_sync_disable", "woocommerce_product_id","woocommerce_media_id","tags","meta_title","meta_description"
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('product_images')
            ->useDisk('product_images')
            ->singleFile(); // لأن كل فئة لها صورة واحدة فقط

        $this->addMediaCollection('product_files')
            ->useDisk('product_files')
            ->singleFile(); // لأن كل فئة لها أيقونة واحدة فقط
    }

    public function customFields()
    {
        return $this->morphMany(CustomFieldValue::class, 'entity');
    }
    public function tax()
    {
        return $this->belongsTo(Tax::class, 'tax_id');
    }

    public function warehouses()
    {
        return $this->belongsToMany(Warehouse::class, 'product_warehouse')
            ->withPivot('qty','variant_id','imei_number','warehouse_id','product_id'); // جلب الكمية المخزنة لكل مستودع
    }

    public function category()
    {
    	return $this->belongsTo('App\Models\Category');
    }

    public function brand()
    {
    	return $this->belongsTo('App\Models\Brand');
    }

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit');
    }

    public function variants()
    {
        return $this->belongsToMany('App\Models\Variant', 'product_variants')->withPivot('id', 'item_code', 'additional_cost', 'additional_price','position');
    }

    public function scopeActiveStandard($query)
    {
        return $query->where('type', 'standard');
    }

    public function scopeActiveFeatured($query)
    {
        return $query->where('featured', 1);
    }

    public function scopeByCode($query, string $code)
    {
        return $query->where('code', $code);
    }

    public function scopeAvailable($query)
    {
        return $query->whereNull('deleted_at');
    }

    // 🔹 Scope لجلب المنتجات المتوفرة في المخزون
    public function scopeInStock($query)
    {
        return $query->where('in_stock', true);
    }

    // 🔹 Scope لجلب المنتجات المتاحة للبيع الإلكتروني
    public function scopeOnline($query)
    {
        return $query->where('is_online', true);
    }

    public function scopeWithVariantCode(Builder $query, string $code): Builder
    {
        return $query->leftJoin('product_variants', 'products.id', '=', 'product_variants.product_id')
            ->select('products.id', 'products.name', 'products.is_variant', 'products.code',
                'product_variants.id as product_variant_id', 'product_variants.item_code')
            ->where(function ($query) use ($code) {
                $query->where('products.code', $code)
                    ->orWhere('product_variants.item_code', $code);
            });
    }

}
