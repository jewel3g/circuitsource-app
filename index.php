<?php
// ============================================================
// circuitsourcebd.com - COMPLETE INDEX PAGE
// Optimized | Bangladesh Pricing | Fully Functional
// ============================================================

session_start();
require_once 'config.php';

// Currency conversion (1 EUR = 120 BDT)
define('BDT_RATE', 120);
define('CURRENCY_SYMBOL', '৳');

function convertPrice($eur) {
    return $eur * BDT_RATE;
}

function formatPrice($eur) {
    return number_format(convertPrice($eur), 2);
}

// Fetch all data with OPTIMIZED JOIN (no subqueries)
$featuredProducts = [];
$newArrivals = [];
$bestsellers = [];
$categories = [];

try {
    // Fetch categories
    $stmt = $pdo->query("
        SELECT category_id, category_name, slug, icon_class 
        FROM categories 
        WHERE parent_category_id IS NULL 
        ORDER BY category_id 
        LIMIT 12
    ");
    $categories = $stmt->fetchAll();

    // Fetch featured products with JOIN for image (OPTIMIZED)
    $stmt = $pdo->query("
        SELECT p.*, c.category_name, pi.image_url as primary_image
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.category_id
        LEFT JOIN product_images pi ON p.product_id = pi.product_id AND pi.is_primary = 1
        WHERE p.is_active = 1 AND p.is_featured = 1
        GROUP BY p.product_id
        ORDER BY p.created_at DESC
        LIMIT 8
    ");
    $featuredProducts = $stmt->fetchAll();

    // Fetch new arrivals
    $stmt = $pdo->query("
        SELECT p.*, c.category_name, pi.image_url as primary_image
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.category_id
        LEFT JOIN product_images pi ON p.product_id = pi.product_id AND pi.is_primary = 1
        WHERE p.is_active = 1 AND p.is_new = 1
        GROUP BY p.product_id
        ORDER BY p.created_at DESC
        LIMIT 8
    ");
    $newArrivals = $stmt->fetchAll();

    // Fetch bestsellers
    $stmt = $pdo->query("
        SELECT p.*, c.category_name, pi.image_url as primary_image
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.category_id
        LEFT JOIN product_images pi ON p.product_id = pi.product_id AND pi.is_primary = 1
        WHERE p.is_active = 1
        GROUP BY p.product_id
        ORDER BY p.stock_quantity DESC
        LIMIT 8
    ");
    $bestsellers = $stmt->fetchAll();

} catch(PDOException $e) {
    $error = $e->getMessage();
}

$cartCount = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
?>

<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>CircuitSourceBD | বাংলাদেশের #১ ইলেকট্রনিক কম্পোনেন্ট স্টোর</title>
    <meta name="description" content="CircuitSourceBD - বাংলাদেশের সবচেয়ে বড় ইলেকট্রনিক কম্পোনেন্টের দোকান।">
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', 'Hind Siliguri', sans-serif; background: #f5f7fb; color: #1a2c3e; }
        
        /* Top Bar */
        .top-bar { background: #1e2a3a; color: #ccc; font-size: 12px; padding: 8px 5%; display: flex; justify-content: space-between; flex-wrap: wrap; gap: 10px; }
        .top-bar a { color: #ffcd7e; text-decoration: none; }
        .currency-badge { background: #c8102e; color: white; padding: 2px 8px; border-radius: 20px; font-weight: bold; margin-left: 10px; }
        
        /* Header */
        .header { background: white; padding: 15px 5%; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 20px; position: sticky; top: 0; z-index: 1000; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .logo h1 { font-size: 28px; font-weight: 800; color: #c8102e; }
        .logo span { font-size: 10px; color: #666; display: block; }
        .logo a { text-decoration: none; }
        
        /* Search Bar */
        .search-bar { flex: 1; max-width: 500px; display: flex; }
        .search-bar input { width: 100%; padding: 12px 18px; border: 2px solid #e2e8f0; border-radius: 40px 0 0 40px; outline: none; font-size: 14px; }
        .search-bar input:focus { border-color: #c8102e; }
        .search-bar button { background: #c8102e; border: none; padding: 0 24px; border-radius: 0 40px 40px 0; color: white; cursor: pointer; }
        
        /* Header Icons */
        .header-icons { display: flex; gap: 25px; font-size: 20px; }
        .header-icons a { color: #1e2a3a; position: relative; }
        .cart-count { position: absolute; top: -10px; right: -12px; background: #c8102e; color: white; font-size: 10px; padding: 2px 6px; border-radius: 50%; min-width: 18px; text-align: center; }
        
        /* Navigation */
        .nav { background: #c8102e; padding: 12px 5%; display: flex; gap: 25px; flex-wrap: wrap; overflow-x: auto; }
        .nav a { color: white; text-decoration: none; font-weight: 500; font-size: 14px; white-space: nowrap; }
        .nav a:hover { opacity: 0.85; }
        
        /* Hero Banner */
        .hero { background: linear-gradient(135deg, #0f2b3d 0%, #1a4a5f 100%); color: white; padding: 60px 5%; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 40px; }
        .hero-content { flex: 1; }
        .hero-badge { background: rgba(255,205,126,0.2); display: inline-block; padding: 6px 16px; border-radius: 50px; font-size: 12px; margin-bottom: 20px; border-left: 3px solid #ffcd7e; }
        .hero-content h1 { font-size: 42px; font-weight: 800; margin-bottom: 20px; line-height: 1.2; }
        .hero-content p { font-size: 18px; margin-bottom: 30px; opacity: 0.9; }
        .hero-buttons { display: flex; gap: 15px; flex-wrap: wrap; }
        .btn-primary { background: #ffcd7e; color: #1e2a3a; padding: 14px 35px; border-radius: 40px; text-decoration: none; font-weight: bold; display: inline-block; transition: 0.3s; }
        .btn-primary:hover { background: #e6b340; transform: translateY(-2px); }
        .btn-outline { border: 2px solid white; color: white; padding: 12px 33px; border-radius: 40px; text-decoration: none; font-weight: bold; display: inline-block; transition: 0.3s; }
        .btn-outline:hover { background: white; color: #1e2a3a; }
        .hero-stats { display: flex; gap: 40px; margin-top: 30px; flex-wrap: wrap; }
        .stat-number { font-size: 28px; font-weight: 800; color: #ffcd7e; }
        .hero-icons i { font-size: 100px; color: #ffcd7e; margin: 0 10px; }
        
        /* Section Title */
        .section-title { font-size: 28px; font-weight: 700; margin: 50px 5% 25px; border-left: 5px solid #c8102e; padding-left: 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 15px; }
        .section-title a { font-size: 14px; color: #c8102e; text-decoration: none; font-weight: 500; }
        
        /* Category Grid */
        .category-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 20px; padding: 0 5%; margin-bottom: 50px; }
        .category-card { background: white; padding: 25px 15px; text-align: center; border-radius: 16px; text-decoration: none; color: #1e2a3a; transition: 0.3s; border: 1px solid #e2e8f0; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        .category-card:hover { transform: translateY(-8px); box-shadow: 0 12px 24px rgba(0,0,0,0.1); border-color: #c8102e; }
        .category-icon { width: 70px; height: 70px; background: linear-gradient(135deg, #fff5f5, #ffe0e0); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; font-size: 32px; color: #c8102e; }
        .category-card h3 { font-size: 14px; font-weight: 600; }
        
        /* Product Grid */
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 25px; padding: 0 5%; margin-bottom: 50px; }
        .product-card { background: white; border-radius: 16px; overflow: hidden; transition: 0.3s; border: 1px solid #e2e8f0; position: relative; }
        .product-card:hover { transform: translateY(-8px); box-shadow: 0 12px 24px rgba(0,0,0,0.1); border-color: #c8102e; }
        .product-badge { position: absolute; top: 15px; left: 15px; background: #c8102e; color: white; font-size: 10px; padding: 4px 12px; border-radius: 20px; font-weight: 600; z-index: 2; }
        .product-badge.new { background: #2e7d32; }
        .product-badge.hot { background: #ff9800; }
        .product-img { width: 100%; height: 200px; object-fit: contain; padding: 20px; background: #fafafa; transition: 0.3s; }
        .product-card:hover .product-img { transform: scale(1.05); }
        .product-info { padding: 20px; border-top: 1px solid #e2e8f0; }
        .product-category { font-size: 11px; color: #c8102e; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; }
        .product-title { font-weight: 700; font-size: 16px; margin-bottom: 10px; line-height: 1.4; height: 44px; overflow: hidden; }
        .product-title a { color: #1e2a3a; text-decoration: none; }
        .product-price { font-size: 20px; font-weight: 800; color: #c8102e; margin: 10px 0; }
        .product-stock { font-size: 12px; margin-bottom: 15px; }
        .product-stock.in-stock { color: #2e7d32; }
        .btn-add { width: 100%; padding: 12px; background: #1e2a3a; color: white; border: none; border-radius: 40px; font-weight: 600; cursor: pointer; transition: 0.3s; }
        .btn-add:hover { background: #c8102e; }
        
        /* Features */
        .features { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; padding: 50px 5%; background: #1e2a3a; color: white; text-align: center; margin: 50px 0; }
        .feature i { font-size: 40px; color: #ffcd7e; margin-bottom: 15px; }
        .feature h3 { font-size: 18px; margin-bottom: 8px; }
        .feature p { font-size: 13px; opacity: 0.8; }
        
        /* Newsletter */
        .newsletter { background: linear-gradient(135deg, #0f2b3d, #1a4a5f); margin: 0 5% 50px; border-radius: 24px; padding: 50px; text-align: center; color: white; }
        .newsletter h3 { font-size: 28px; margin-bottom: 15px; }
        .newsletter p { margin-bottom: 25px; opacity: 0.9; }
        .newsletter-form { display: flex; max-width: 500px; margin: 0 auto; gap: 10px; flex-wrap: wrap; }
        .newsletter-form input { flex: 1; padding: 14px 20px; border: none; border-radius: 50px; outline: none; font-size: 14px; }
        .newsletter-form button { background: #c8102e; border: none; padding: 14px 30px; border-radius: 50px; color: white; font-weight: 600; cursor: pointer; }
        
        /* Footer */
        .footer { background: #0f1c2c; color: #b0c4de; padding: 50px 5% 30px; }
        .footer-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 40px; margin-bottom: 40px; }
        .footer h4 { color: white; margin-bottom: 20px; }
        .footer a { color: #b0c4de; text-decoration: none; display: block; margin: 10px 0; transition: 0.3s; font-size: 14px; }
        .footer a:hover { color: #ffcd7e; transform: translateX(5px); }
        .social-links { display: flex; gap: 15px; margin-top: 20px; }
        .social-links a { width: 40px; height: 40px; background: rgba(255,255,255,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 18px; }
        .copyright { text-align: center; padding-top: 30px; border-top: 1px solid rgba(255,255,255,0.1); font-size: 12px; }
        
        /* Floating Buttons */
        .whatsapp-float { position: fixed; bottom: 30px; left: 30px; background: #25D366; color: white; width: 55px; height: 55px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 30px; z-index: 100; box-shadow: 0 4px 12px rgba(0,0,0,0.2); transition: 0.3s; }
        .whatsapp-float:hover { transform: scale(1.1); color: white; }
        .back-to-top { position: fixed; bottom: 30px; right: 30px; background: #c8102e; color: white; width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 100; opacity: 0; transition: 0.3s; box-shadow: 0 4px 12px rgba(0,0,0,0.2); }
        .back-to-top.show { opacity: 1; }
        
        @media (max-width: 768px) {
            .hero-content h1 { font-size: 28px; }
            .hero-icons { display: none; }
            .header { flex-direction: column; }
            .search-bar { max-width: 100%; width: 100%; }
            .category-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 480px) {
            .product-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<!-- Top Bar -->
<div class="top-bar">
    <div>
        <i class="fas fa-truck"></i> সারা দেশে ফ্রি শিপিং অর্ডার ৳২৪,০০০+ 
        <span class="currency-badge">🇧🇩 BDT (৳)</span>
    </div>
    <div>
        <i class="fas fa-headset"></i> ২৪/৭ টেকনিক্যাল সাপোর্ট
        <a href="contact.php" style="margin-left: 10px;">যোগাযোগ করুন</a>
    </div>
</div>

<!-- Header -->
<div class="header">
    <div class="logo">
        <a href="index.php">
            <h1>CircuitSource<span>BD</span></h1>
            <span>ইলেকট্রনিক কম্পোনেন্ট ডিস্ট্রিবিউটর</span>
        </a>
    </div>
    <div class="search-bar">
        <form action="search.php" method="GET">
            <input type="text" name="q" placeholder="রেজিস্টর, ক্যাপাসিটর, টিভি, মোবাইল সার্চ করুন...">
            <button type="submit"><i class="fas fa-search"></i></button>
        </form>
    </div>
    <div class="header-icons">
        <a href="wishlist.php"><i class="far fa-heart"></i></a>
        <a href="cart.php">
            <i class="fas fa-shopping-cart"></i>
            <span class="cart-count"><?php echo $cartCount; ?></span>
        </a>
        <a href="account.php"><i class="fas fa-user"></i></a>
    </div>
</div>

<!-- Navigation -->
<div class="nav">
    <a href="index.php">হোম</a>
    <a href="catalog.php">সব পণ্য</a>
    <a href="catalog.php?cat=resistors">রেজিস্টর</a>
    <a href="catalog.php?cat=capacitors">ক্যাপাসিটর</a>
    <a href="catalog.php?cat=leds">এলইডি</a>
    <a href="catalog.php?cat=tvs">স্মার্ট টিভি</a>
    <a href="catalog.php?cat=mobiles">মোবাইল</a>
    <a href="catalog.php?cat=power">পাওয়ার সাপ্লাই</a>
    <a href="catalog.php?cat=new">নতুন পণ্য</a>
    <a href="contact.php">যোগাযোগ</a>
</div>

<!-- Hero Banner -->
<section class="hero">
    <div class="hero-content">
        <div class="hero-badge">
            <i class="fas fa-certificate"></i> বাংলাদেশের #১ ইলেকট্রনিক্স স্টোর
        </div>
        <h1>ইলেকট্রনিক কম্পোনেন্টের<br>বিশ্বস্ত উৎস</h1>
        <p>১০,০০০+ পণ্য | নির্ভরযোগ্য কোয়ালিটি | ফ্রি শিপিং | ২৪/৭ সাপোর্ট</p>
        <div class="hero-buttons">
            <a href="catalog.php" class="btn-primary">কিনুন এখন <i class="fas fa-arrow-right"></i></a>
            <a href="bulk-order.php" class="btn-outline">পাইকারি মূল্যে কিনুন <i class="fas fa-truck"></i></a>
        </div>
        <div class="hero-stats">
            <div><span class="stat-number">১০,০০০+</span><br>সন্তুষ্ট গ্রাহক</div>
            <div><span class="stat-number">২০,০০০+</span><br>পণ্য</div>
            <div><span class="stat-number">৯৯%</span><br>সন্তুষ্টি</div>
        </div>
    </div>
    <div class="hero-icons">
        <i class="fas fa-microchip"></i>
        <i class="fas fa-tv"></i>
        <i class="fas fa-mobile-alt"></i>
    </div>
</section>

<!-- Category Grid -->
<div class="category-grid">
    <?php foreach($categories as $cat): ?>
    <a href="catalog.php?cat=<?php echo htmlspecialchars($cat['slug']); ?>" class="category-card">
        <div class="category-icon">
            <i class="<?php echo htmlspecialchars($cat['icon_class'] ?? 'fa-microchip'); ?>"></i>
        </div>
        <h3><?php echo htmlspecialchars($cat['category_name']); ?></h3>
    </a>
    <?php endforeach; ?>
</div>

<!-- Featured Products -->
<?php if(!empty($featuredProducts)): ?>
<h2 class="section-title">
    <span><i class="fas fa-star" style="color: #c8102e;"></i> ফিচার্ড পণ্য</span>
    <a href="catalog.php?filter=featured">সব দেখুন →</a>
</h2>
<div class="product-grid">
    <?php foreach($featuredProducts as $product): ?>
    <div class="product-card">
        <div class="product-badge">ফিচার্ড</div>
        <img src="<?php echo htmlspecialchars($product['primary_image'] ?? 'https://placehold.co/300x200?text=' . urlencode(substr($product['product_name'], 0, 20))); ?>" 
             alt="<?php echo htmlspecialchars($product['product_name']); ?>" class="product-img">
        <div class="product-info">
            <div class="product-category"><?php echo htmlspecialchars($product['category_name'] ?? 'কম্পোনেন্ট'); ?></div>
            <div class="product-title"><a href="product-detail.php?id=<?php echo $product['product_id']; ?>"><?php echo htmlspecialchars($product['product_name']); ?></a></div>
            <div class="product-price">৳<?php echo formatPrice($product['unit_price_eur']); ?></div>
            <div class="product-stock in-stock"><i class="fas fa-check-circle"></i> স্টকে আছে</div>
            <button class="btn-add" onclick="addToCart(<?php echo $product['product_id']; ?>, '<?php echo addslashes($product['product_name']); ?>', <?php echo $product['unit_price_eur']; ?>)">
                <i class="fas fa-cart-plus"></i> কার্টে যোগ করুন
            </button>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- New Arrivals -->
<?php if(!empty($newArrivals)): ?>
<h2 class="section-title">
    <span><i class="fas fa-newspaper" style="color: #c8102e;"></i> নতুন পণ্য</span>
    <a href="catalog.php?sort=newest">সব দেখুন →</a>
</h2>
<div class="product-grid">
    <?php foreach($newArrivals as $product): ?>
    <div class="product-card">
        <div class="product-badge new">নতুন</div>
        <img src="<?php echo htmlspecialchars($product['primary_image'] ?? 'https://placehold.co/300x200?text=নতুন'); ?>" 
             alt="<?php echo htmlspecialchars($product['product_name']); ?>" class="product-img">
        <div class="product-info">
            <div class="product-category"><?php echo htmlspecialchars($product['category_name'] ?? 'কম্পোনেন্ট'); ?></div>
            <div class="product-title"><a href="product-detail.php?id=<?php echo $product['product_id']; ?>"><?php echo htmlspecialchars($product['product_name']); ?></a></div>
            <div class="product-price">৳<?php echo formatPrice($product['unit_price_eur']); ?></div>
            <div class="product-stock in-stock"><i class="fas fa-check-circle"></i> স্টকে আছে</div>
            <button class="btn-add" onclick="addToCart(<?php echo $product['product_id']; ?>, '<?php echo addslashes($product['product_name']); ?>', <?php echo $product['unit_price_eur']; ?>)">
                <i class="fas fa-cart-plus"></i> কার্টে যোগ করুন
            </button>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Best Sellers -->
<?php if(!empty($bestsellers)): ?>
<h2 class="section-title">
    <span><i class="fas fa-fire" style="color: #c8102e;"></i> বেস্ট সেলার</span>
    <a href="catalog.php?sort=popular">সব দেখুন →</a>
</h2>
<div class="product-grid">
    <?php foreach($bestsellers as $product): ?>
    <div class="product-card">
        <div class="product-badge hot">🔥 বেস্ট সেলার</div>
        <img src="<?php echo htmlspecialchars($product['primary_image'] ?? 'https://placehold.co/300x200?text=বেস্ট+সেলার'); ?>" 
             alt="<?php echo htmlspecialchars($product['product_name']); ?>" class="product-img">
        <div class="product-info">
            <div class="product-category"><?php echo htmlspecialchars($product['category_name'] ?? 'কম্পোনেন্ট'); ?></div>
            <div class="product-title"><a href="product-detail.php?id=<?php echo $product['product_id']; ?>"><?php echo htmlspecialchars($product['product_name']); ?></a></div>
            <div class="product-price">৳<?php echo formatPrice($product['unit_price_eur']); ?></div>
            <div class="product-stock in-stock"><i class="fas fa-check-circle"></i> স্টকে আছে</div>
            <button class="btn-add" onclick="addToCart(<?php echo $product['product_id']; ?>, '<?php echo addslashes($product['product_name']); ?>', <?php echo $product['unit_price_eur']; ?>)">
                <i class="fas fa-cart-plus"></i> কার্টে যোগ করুন
            </button>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Features Banner -->
<div class="features">
    <div class="feature"><i class="fas fa-shipping-fast"></i><h3>ফ্রি শিপিং</h3><p>৳২৪,০০০+ অর্ডারে</p></div>
    <div class="feature"><i class="fas fa-undo-alt"></i><h3>৩০ দিনে রিটার্ন</h3><p>সহজ নীতি</p></div>
    <div class="feature"><i class="fas fa-microchip"></i><h3>অরিজিনাল পণ্য</h3><p>১০০% নির্ভরযোগ্য</p></div>
    <div class="feature"><i class="fas fa-headset"></i><h3>২৪/৭ সাপোর্ট</h3><p>টেকনিক্যাল সাহায্য</p></div>
    <div class="feature"><i class="fas fa-shield-alt"></i><h3>১০০% নিরাপদ</h3><p>সুরক্ষিত পেমেন্ট</p></div>
</div>

<!-- Newsletter -->
<section class="newsletter">
    <h3>আমাদের নিউজলেটারে সাবস্ক্রাইব করুন</h3>
    <p>নতুন পণ্য এবং অফার সম্পর্কে সবার আগে আপডেট পেতে আপনার ইমেল দিন।</p>
    <form class="newsletter-form" method="POST" action="subscribe.php">
        <input type="email" name="email" placeholder="আপনার ইমেল এড্রেস..." required>
        <button type="submit">সাবস্ক্রাইব</button>
    </form>
</section>

<!-- Footer -->
<footer class="footer">
    <div class="footer-grid">
        <div>
            <h4>CircuitSourceBD</h4>
            <p>বাংলাদেশের সবচেয়ে বিশ্বস্ত ইলেকট্রনিক কম্পোনেন্ট ডিস্ট্রিবিউটর। আমরা কোয়ালিটি এবং সার্ভিসে বিশ্বাস করি।</p>
            <div class="social-links">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-youtube"></i></a>
                <a href="#"><i class="fab fa-linkedin-in"></i></a>
            </div>
        </div>
        <div>
            <h4>গুরুত্বপূর্ণ লিঙ্ক</h4>
            <a href="about.php">আমাদের সম্পর্কে</a>
            <a href="terms.php">শর্তাবলী</a>
            <a href="privacy.php">প্রাইভেসি পলিসি</a>
            <a href="contact.php">যোগাযোগ</a>
        </div>
        <div>
            <h4>পেমেন্ট মেথড</h4>
            <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-top: 15px;">
                <span style="background: white; color: #1e2a3a; padding: 5px 12px; border-radius: 8px; font-size: 12px;">Visa</span>
                <span style="background: white; color: #1e2a3a; padding: 5px 12px; border-radius: 8px; font-size: 12px;">Mastercard</span>
                <span style="background: white; color: #1e2a3a; padding: 5px 12px; border-radius: 8px; font-size: 12px;">bKash</span>
                <span style="background: white; color: #1e2a3a; padding: 5px 12px; border-radius: 8px; font-size: 12px;">Nagad</span>
                <span style="background: white; color: #1e2a3a; padding: 5px 12px; border-radius: 8px; font-size: 12px;">Rocket</span>
            </div>
        </div>
    </div>
    <div class="copyright">
        &copy; <?php echo date('Y'); ?> CircuitSourceBD. সর্বস্বত্ব সংরক্ষিত।
    </div>
</footer>

<!-- Floating UI -->
<a href="https://wa.me/8801XXXXXXXXX" class="whatsapp-float" target="_blank">
    <i class="fab fa-whatsapp"></i>
</a>
<div class="back-to-top" id="backToTop"><i class="fas fa-chevron-up"></i></div>

<script>
// Add to Cart Function
function addToCart(productId, name, price) {
    fetch('add-to-cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'product_id=' + productId + '&quantity=1'
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            alert(name + ' কার্টে যোগ করা হয়েছে!');
            location.reload();
        }
    })
    .catch(error => {
        // Fallback for demo - add to localStorage
        let cart = JSON.parse(localStorage.getItem('cart') || '[]');
        cart.push({ id: productId, name: name, price: price, quantity: 1 });
        localStorage.setItem('cart', JSON.stringify(cart));
        alert(name + ' কার্টে যোগ করা হয়েছে!');
        location.reload();
    });
}

// Back to Top
window.onscroll = function() {
    const btn = document.getElementById("backToTop");
    if (document.body.scrollTop > 300 || document.documentElement.scrollTop > 300) {
        btn.classList.add("show");
    } else {
        btn.classList.remove("show");
    }
};

document.getElementById("backToTop").onclick = function() {
    window.scrollTo({top: 0, behavior: 'smooth'});
};
</script>

</body>
</html>
