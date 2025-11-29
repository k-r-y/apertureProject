<?php

require_once 'includes/functions/config.php';
require_once 'includes/functions/auth.php';
require_once 'includes/functions/function.php';
require_once 'includes/functions/csrf.php';
require_once 'includes/functions/session.php';

$packages = [];
$query = "SELECT * FROM packages";
$result = $conn->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        // Fetch Inclusions for this package
        $incQuery = "SELECT Description FROM inclusion WHERE packageID = ?";
        $incStmt = $conn->prepare($incQuery);
        $incStmt->bind_param("s", $row['packageID']);
        $incStmt->execute();
        $incResult = $incStmt->get_result();
        $inclusions = [];
        while ($inc = $incResult->fetch_assoc()) {
            $inclusions[] = $inc['Description'];
        }
        $row['inclusions'] = $inclusions;
        $packages[] = $row;
        $incStmt->close();
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Discover Aperture's photography and videography services for weddings, corporate events, celebrations, and creative shoots. Transparent pricing, professional quality, guaranteed delivery.">
  <link rel="stylesheet" href="../bootstrap-5.3.8-dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="style.css">
  <link rel="icon" href="./assets/camera.png" type="image/x-icon">
  <link rel="stylesheet" href="../bootstrap-5.3.8-dist/font/bootstrap-icons.css">
  <title>Our Services - Professional Photography & Videography | Aperture</title>

  <style>
    
  </style>

</head>

<body>

<!-- @src/services.php  make the cta button on package cards align with other cta button on packages. also make the cards on add ons smaller by reducing the spacing and padding. also add a gallery section. then add border radius to everything. think longer and don't stop until you finish it. make sure that you follow the design system and the whole page is responsive, modern design, and luxury -->

  <?php include './includes/header.php'; ?>

  <!--------------------------------------- HERO SECTION ---------------------------------------->
  <section class="w-100 min-vh-100 d-flex justify-content-center align-items-center position-relative" style="background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 100%);">
    <div class="position-absolute w-100 h-100" style="opacity: 0.03; background-image: url('./assets/wp2815597-black-texture-wallpapers.jpg'); background-size: cover; background-position: center;"></div>

    <div class="container position-relative" style="z-index: 2;">
      <div class="row justify-content-center text-center">
        <div class="col-lg-10">
          <div class="mb-3">
            <span class="d-inline-block px-4 py-2 rounded-pill" style="background: rgba(212, 175, 55, 0.1); border: 1px solid rgba(212, 175, 55, 0.3); font-size: 0.75rem; letter-spacing: 3px; color: var(--gold); font-weight: 600;">APERTURE STUDIOS</span>
          </div>
          <h1 class="serif mb-4 text-light" style="font-size: clamp(2.5rem, 6vw, 5rem); font-weight: 300; line-height: 1.2; letter-spacing: -1px;">Capturing Moments,<br><span style="font-style: italic; color: var(--gold);">Crafting Legacies</span></h1>
          <p class="mb-5 mx-auto text-light" style="max-width: 650px; font-size: 1.05rem; line-height: 1.8; opacity: 0.8; font-weight: 300;">Bespoke photography and videography services for discerning clients who demand excellence. From intimate celebrations to grand corporate events, we transform fleeting moments into timeless art.</p>
          <div class="d-flex flex-column flex-md-row gap-3 justify-content-center">
            <a href="#packages" class="btn px-5 py-3" style="background-color: var(--gold); color: #000; font-weight: 500; letter-spacing: 0.5px; border-radius: 6px; font-size: 0.9rem; transition: all 0.4s ease;">View Packages</a>
            <a href="user/bookingForm.php" class="btn px-5 py-3" style="background: transparent; color: var(--gold); border: 1px solid var(--gold); font-weight: 500; letter-spacing: 0.5px; border-radius: 6px; font-size: 0.9rem; transition: all 0.4s ease;">Book now</a>
          </div>
        </div>
      </div>
    </div>

    <!-- Scroll Indicator -->
    <div class="position-absolute bottom-0 start-50 translate-middle-x mb-4" style="animation: bounce 2s infinite;">
      <div class="d-flex flex-column align-items-center">
        <span class="text-light mb-2" style="font-size: 0.7rem; letter-spacing: 2px; opacity: 0.6;">SCROLL</span>
        <i class="bi bi-arrow-down text-light" style="opacity: 0.6;"></i>
      </div>
    </div>
  </section>
  <!--------------------------------------- HERO SECTION ---------------------------------------->

  <!--------------------------------------- PACKAGES SECTION ---------------------------------------->
  <section id="packages" class="w-100 py-5" style="background: linear-gradient(to bottom, #fafafa 0%, #ffffff 100%);">
    <div class="container py-5">
      <div class="row justify-content-center mb-5">
        <div class="col-md-10 text-center">
          <span class="d-inline-block px-4 py-2 rounded-pill mb-3" style="background: rgba(212, 175, 55, 0.08); border: 1px solid rgba(212, 175, 55, 0.2); font-size: 0.7rem; letter-spacing: 3px; color: var(--gold); font-weight: 600;">OUR COLLECTIONS</span>
          <h2 class="serif mb-3" style="font-size: clamp(2rem, 4vw, 3rem); font-weight: 300; line-height: 1.3; letter-spacing: -0.5px; color: #1a1a1a;">Tailored to Your Vision</h2>
          <p class="mb-0 mx-auto" style="max-width: 600px; font-size: 0.95rem; line-height: 1.7; opacity: 0.7; font-weight: 300; color: #4a4a4a;">Three meticulously crafted tiers designed for discerning clients. Every collection includes professional coverage, expert post-production, and guaranteed delivery within 1-2 weeks.</p>
        </div>
      </div>

      <div class="row g-4 justify-content-center align-items-stretch">

      <?php foreach ($packages as $index => $package): ?>

        <div class="col-lg-4 col-md-6">
          <div class="package-card h-100 position-relative d-flex flex-column <?= ($index === 1) ? 'feature-card-black' : 'feature-card-light';?>" style="">
            <span class="position-absolute top-0 start-0 mt-3 ms-3 px-2 py-1" style="background: rgba(212, 175, 55, 0.1); font-size: 0.6rem; letter-spacing: 2px; color: var(--gold); font-weight: 600; border-radius: 4px;">INTIMATE</span>

            <?php if ($package['packageID'] == 'premium'): ?>
            <span class="position-absolute top-0 end-0 mt-3 me-3 px-2 py-1" style="background: var(--gold); font-size: 0.6rem; letter-spacing: 2px; color: #000; font-weight: 600; border-radius: 4px;">RECOMMENDED</span>
            <?php endif; ?>

            <div class="mb-3" style="padding-top: 1.5rem;">
              <h3 class="serif mb-1 <?= ($index === 1) ? 'text-light' : 'text-dark'; ?>" style="font-size: 1.5rem; font-weight: 300; letter-spacing: 0.5px; color: #1a1a1a;">
                <?= htmlspecialchars($package['packageName']); ?>
              </h3>
              <p class="mb-3 <?= ($index === 1) ? 'text-light' : 'text-dark'; ?>" style="font-size: 0.8rem; line-height: 1.5; opacity: 0.6; font-weight: 300;">
                <?= htmlspecialchars($package['description']); ?>
              </p>

              <div class="d-flex align-items-baseline mb-1">
                <span style="font-size: 0.7rem; color: #999; margin-right: 0.25rem; <?= ($index === 1) ? 'text-light' : 'text-dark'; ?>">₱</span>
                <span class="serif" style="font-size: 2rem; font-weight: 300; letter-spacing: -1px; color: var(--gold);">
                  <?= number_format($package['Price'], 2); ?>
                </span>
              </div>
              <div class="d-flex align-items-center gap-2 mb-2">
                <span style="font-size: 1rem; text-transform: uppercase; letter-spacing: 1px; color: #999; font-weight: 500; <?= ($index === 1) ? 'text-light' : 'text-dark'; ?>">
                  <?= htmlspecialchars($package['coverage_hours']); ?> hours of coverage
                </span>
              
              </div>
            </div>

            <div class="package-inclusions mb-auto">
              <h4 class="mb-2 <?= ($index === 1) ? 'text-light' : 'text-dark'; ?>" style="font-size: 0.8rem; font-weight: 500; letter-spacing: 1px; color: #1a1a1a;">What's Included</h4>
              <ul class="list-unstyled mb-0">
                <?php foreach ($package['inclusions'] as $inclusion): ?>
                  <li class="mb-2 <?= ($index === 1) ? 'text-light' : 'text-dark'; ?>" style="font-size: 0.7rem; opacity: 0.8; font-weight: 300; "> <i class="bi bi-check-circle-fill text-gold check-icon transition-all me-2"></i>
                    <?= htmlspecialchars($inclusion); ?>
                  </li>
                <?php endforeach; ?>
              </ul>
            </div>
             <div class="mt-auto" style="padding-top: 1.25rem;">
              <a href="user/bookingForm.php" class="btn w-100 py-2 btn-book-package <?= ($index === 1) ? 'gold-btn' : 'normal-btn'; ?>" data-id="<?= htmlspecialchars($package['packageID']); ?>" data-package="<?= htmlspecialchars($package['packageName']); ?>" data-price="<?= htmlspecialchars($package['Price']); ?>" style="">Book now</a>
              <p class="text-center mt-2 mb-0 <?= ($index === 1) ? 'text-light' : 'text-dark'; ?>" style="font-size: 0.65rem; opacity: 0.5; ">25% downpayment required</p>
            </div>
          </div>
        </div>

        <?php endforeach; ?>



      <!-- Comparison Button -->
      <div class="row mt-5">
        <div class="col text-center">
          <button id="toggleComparison" class="btn px-5 py-3" style="background: transparent; color: #1a1a1a; border: 1px solid rgba(0,0,0,0.2); font-size: 0.8rem; letter-spacing: 1.5px; font-weight: 500; border-radius: 6px; transition: all 0.3s ease;">DETAILED COMPARISON</button>
        </div>
      </div>

      <!-- Package Comparison Table -->
      <div id="comparisonTable" class="row mt-5" style="display: none;">
        <div class="col-12">
          <div class="table-responsive">
            <table class="table comparison-table bg-white" style="border: 1px solid rgba(0,0,0,0.08); border-radius: 8px; overflow: hidden;">
              <thead style="background: #fafafa; border-bottom: 1px solid rgba(0,0,0,0.08);">
                <tr>
                  <th class="py-4 ps-4" style="font-size: 0.75rem; font-weight: 600; letter-spacing: 1px; text-transform: uppercase; color: #999;">Feature</th>
                  <?php foreach ($packages as $index => $pkg): ?>
                    <th class="text-center py-4" style="font-size: 0.8rem; font-weight: 300; color: #1a1a1a; <?= ($index === 1) ? 'background-color: rgba(212, 175, 55, 0.05);' : '' ?>">
                      <?= htmlspecialchars($pkg['packageName']) ?><br>
                      <small style="font-size: 0.7rem; color: var(--gold);">₱<?= number_format($pkg['Price']) ?></small>
                    </th>
                  <?php endforeach; ?>
                </tr>
              </thead>
              <tbody>
                <?php
                $comparisonFeatures = [
                    'Coverage Hours' => ['type' => 'regex', 'pattern' => '/(\d+)\s*Hours/i', 'suffix' => ' hours', 'default' => '—'],
                    'Team Size' => ['type' => 'callback', 'callback' => function($inclusions) {
                        $photo = 0;
                        $video = 0;
                        foreach ($inclusions as $inc) {
                            if (preg_match('/(\d+)\s*Professional\s*Photographer/i', $inc, $m)) $photo += intval($m[1]);
                            if (preg_match('/(\d+)\s*Professional\s*Videographer/i', $inc, $m)) $video += intval($m[1]);
                        }
                        $parts = [];
                        if ($photo > 0) $parts[] = "$photo Photo";
                        if ($video > 0) $parts[] = "$video Video";
                        return empty($parts) ? '—' : implode(' + ', $parts);
                    }],
                    'Edited Photos' => ['type' => 'regex', 'pattern' => '/(\d+\+?)\s*(?:High-Quality|Expertly|Professionally)?\s*Edited Photos/i', 'default' => '—'],
                    'Highlight Video' => ['type' => 'regex', 'pattern' => '/(\d+[\-–]\d+\s*Minute)\s*(?:Cinematic)?\s*Highlight/i', 'default' => '—'],
                    'Full Event Film' => ['type' => 'regex', 'pattern' => '/(\d+[\-–]\d+\s*Minute)\s*Full Event/i', 'default' => '—'],
                    'Drone Coverage' => ['type' => 'bool', 'keyword' => 'Drone', 'true' => '<span style="width: 6px; height: 6px; background: var(--gold); border-radius: 50%; display: inline-block;"></span>', 'false' => '<span style="font-size: 0.75rem; font-weight: 300; color: #999;">Add-on</span>'],
                    'Same-Day Edit' => ['type' => 'bool', 'keyword' => 'Same-Day', 'true' => '<span style="width: 6px; height: 6px; background: var(--gold); border-radius: 50%; display: inline-block;"></span>', 'false' => '<span style="font-size: 0.75rem; font-weight: 300; color: #999;">Add-on</span>'],
                    'Gallery Access' => ['type' => 'regex', 'pattern' => '/Gallery\s*\(([^)]+)\)/i', 'default' => '—'],
                    'Cloud Storage' => ['type' => 'regex', 'pattern' => '/Storage.*?\(([^)]+)\)/i', 'default' => '—'],
                ];

                foreach ($comparisonFeatures as $label => $config):
                ?>
                  <tr style="border-bottom: 1px solid rgba(0,0,0,0.05);">
                    <td class="ps-4 py-3" style="font-size: 0.85rem; font-weight: 500; color: #1a1a1a;"><?= htmlspecialchars($label) ?></td>
                    <?php foreach ($packages as $index => $pkg): 
                        $value = $config['default'] ?? '—';
                        
                        if (isset($config['type']) && $config['type'] === 'column') {
                            $value = htmlspecialchars($pkg[$config['key']]) . ($config['suffix'] ?? '');
                        } elseif (isset($config['type']) && $config['type'] === 'regex') {
                            foreach ($pkg['inclusions'] as $inc) {
                                if (preg_match($config['pattern'], $inc, $matches)) {
                                    $value = htmlspecialchars($matches[1]) . ($config['suffix'] ?? '');
                                    break;
                                }
                            }
                        } elseif (isset($config['type']) && $config['type'] === 'bool') {
                            $found = false;
                            foreach ($pkg['inclusions'] as $inc) {
                                if (stripos($inc, $config['keyword']) !== false) {
                                    $found = true;
                                    break;
                                }
                            }
                            $value = $found ? $config['true'] : $config['false'];
                        } elseif (isset($config['type']) && $config['type'] === 'callback') {
                            $value = $config['callback']($pkg['inclusions']);
                        }
                    ?>
                      <td class="text-center py-3" style="font-size: 0.85rem; font-weight: 300; color: #4a4a4a; <?= ($index === 1) ? 'background-color: rgba(212, 175, 55, 0.03);' : '' ?>">
                        <?= $value ?>
                      </td>
                    <?php endforeach; ?>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
  </section>
  <!--------------------------------------- PACKAGES SECTION ---------------------------------------->

  <!--------------------------------------- ADD-ONS SECTION ---------------------------------------->
  <section class="w-100 py-5" style="background: #fafafa;">
    <div class="container py-5">
      <div class="row justify-content-center mb-5">
        <div class="col-md-10 text-center">
          <span class="d-inline-block px-4 py-2 rounded-pill mb-3" style="background: rgba(212, 175, 55, 0.08); border: 1px solid rgba(212, 175, 55, 0.2); font-size: 0.7rem; letter-spacing: 3px; color: var(--gold); font-weight: 600;">ENHANCEMENTS</span>
          <h2 class="serif mb-3" style="font-size: clamp(2rem, 4vw, 3rem); font-weight: 300; line-height: 1.3; letter-spacing: -0.5px; color: #1a1a1a;">Curated Add-Ons</h2>
          <p class="mb-0 mx-auto" style="max-width: 600px; font-size: 0.95rem; line-height: 1.7; opacity: 0.7; font-weight: 300; color: #4a4a4a;">Refine your collection with bespoke enhancements. Each add-on is designed to elevate your experience.</p>
        </div>
      </div>

      <div class="row g-4">

        <?php
        // Fetch distinct add-ons from the database
        $addonsQuery = "SELECT DISTINCT Description, Price FROM addons ORDER BY Price ASC";
        $addonsResult = $conn->query($addonsQuery);
        
        // Mapping for icons and descriptions based on add-on name keywords
        $addonDetails = [
            'Drone' => [
                'icon' => 'bi-camera-video-fill',
                'desc' => 'Cinematic aerial perspectives capturing the grandeur and scale of your event from above.'
            ],
            'Same-Day' => [
                'icon' => 'bi-lightning-charge-fill',
                'desc' => "Watch your event's highlight reel during your reception—a truly unforgettable experience."
            ],
            'Album' => [
                'icon' => 'bi-book-fill',
                'desc' => 'Museum-quality printed album featuring your finest moments. Available in 30 or 40-page editions.'
            ],
            'Extended' => [
                'icon' => 'bi-clock-history',
                'desc' => 'Additional hours to ensure every moment is documented from beginning to end.'
            ],
            'USB' => [
                'icon' => 'bi-usb-symbol',
                'desc' => 'All media delivered on a premium USB drive with custom presentation case.'
            ],
            'Streaming' => [
                'icon' => 'bi-broadcast',
                'desc' => 'Professional streaming service allowing distant loved ones to share in your celebration.'
            ],
            'Photographer' => [
                'icon' => 'bi-camera-fill',
                'desc' => 'An additional professional photographer to ensure no angle is missed.'
            ],
            '4K' => [
                'icon' => 'bi-film',
                'desc' => 'Upgrade your video quality to stunning 4K resolution for crystal clear memories.'
            ]
        ];

        if ($addonsResult && $addonsResult->num_rows > 0) {
            while ($addon = $addonsResult->fetch_assoc()) {
                $name = htmlspecialchars($addon['Description']);
                $price = number_format($addon['Price']);
                
                // Default values
                $icon = 'bi-star-fill';
                $description = 'Enhance your package with this premium addition.';
                
                // Find matching details
                foreach ($addonDetails as $keyword => $details) {
                    if (stripos($name, $keyword) !== false) {
                        $icon = $details['icon'];
                        $description = $details['desc'];
                        break;
                    }
                }
        ?>
        <div class="col-md-6 col-lg-4">
          <div class="addon-card h-100 position-relative" style="background: #ffffff; border: 1px solid rgba(0,0,0,0.08); border-radius: 8px; padding: 1.75rem 1.5rem; transition: all 0.4s ease;">
            <div class="mb-2">
              <i class="bi <?= $icon ?>" style="font-size: 1.75rem; color: var(--gold); opacity: 0.8;"></i>
            </div>
            <h4 class="serif mb-2" style="font-size: 1.35rem; font-weight: 300; letter-spacing: 0.3px; color: #1a1a1a;"><?= $name ?></h4>
            <p class="mb-3" style="font-size: 0.8rem; line-height: 1.6; opacity: 0.6; font-weight: 300; color: #4a4a4a;"><?= $description ?></p>
            <div class="d-flex align-items-baseline">
              <span style="font-size: 0.7rem; color: #999; margin-right: 0.25rem;">₱</span>
              <span class="serif" style="font-size: 1.6rem; font-weight: 300; letter-spacing: -0.5px; color: var(--gold);"><?= $price ?></span>
            </div>
          </div>
        </div>
        <?php 
            }
        } else {
            // Fallback if no add-ons found
            echo '<div class="col-12 text-center"><p class="text-muted">No add-ons available at the moment.</p></div>';
        }
        ?>

      </div>
    </div>
  </section>
  <!--------------------------------------- ADD-ONS SECTION ---------------------------------------->

  <!--------------------------------------- BOOKING DETAILS SECTION ---------------------------------------->
  <section class="w-100 py-5" style="background: linear-gradient(to bottom, #ffffff 0%, #fafafa 100%);">
    <div class="container py-5">
      <div class="row justify-content-center mb-5">
        <div class="col-md-10 text-center">
          <span class="d-inline-block px-4 py-2 rounded-pill mb-3" style="background: rgba(212, 175, 55, 0.08); border: 1px solid rgba(212, 175, 55, 0.2); font-size: 0.7rem; letter-spacing: 3px; color: var(--gold); font-weight: 600;">OUR COMMITMENT</span>
          <h2 class="serif mb-3" style="font-size: clamp(2rem, 4vw, 3rem); font-weight: 300; line-height: 1.3; letter-spacing: -0.5px; color: #1a1a1a;">Service Excellence</h2>
          <p class="mb-0 mx-auto" style="max-width: 600px; font-size: 0.95rem; line-height: 1.7; opacity: 0.7; font-weight: 300; color: #4a4a4a;">We prioritize transparency, flexibility, and exceptional service in every engagement.</p>
        </div>
      </div>

      <div class="row g-4">
        <div class="col-md-6 col-lg-3">
          <div class="text-center" style="padding: 2rem 1.5rem;">
            <div class="mb-3">
              <span style="width: 50px; height: 50px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid rgba(212, 175, 55, 0.3); color: var(--gold);">
                <i class="bi bi-shield-check" style="font-size: 1.5rem;"></i>
              </span>
            </div>
            <h5 class="mb-2" style="font-size: 0.95rem; font-weight: 500; letter-spacing: 0.5px; color: #1a1a1a;">Secure Booking</h5>
            <p style="font-size: 0.8rem; line-height: 1.6; opacity: 0.6; font-weight: 300; color: #4a4a4a;">20% deposit confirms your date. Balance due before the event.</p>
          </div>
        </div>

        

        <div class="col-md-6 col-lg-3">
          <div class="text-center" style="padding: 2rem 1.5rem;">
            <div class="mb-3">
              <span style="width: 50px; height: 50px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid rgba(212, 175, 55, 0.3); color: var(--gold);">
                <i class="bi bi-clock-history" style="font-size: 1.5rem;"></i>
              </span>
            </div>
            <h5 class="mb-2" style="font-size: 0.95rem; font-weight: 500; letter-spacing: 0.5px; color: #1a1a1a;">Timely Delivery</h5>
            <p style="font-size: 0.8rem; line-height: 1.6; opacity: 0.6; font-weight: 300; color: #4a4a4a;">Edited photos and videos delivered within 1-2 weeks guaranteed.</p>
          </div>
        </div>

        <div class="col-md-6 col-lg-3">
          <div class="text-center" style="padding: 2rem 1.5rem;">
            <div class="mb-3">
              <span style="width: 50px; height: 50px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid rgba(212, 175, 55, 0.3); color: var(--gold);">
                <i class="bi bi-credit-card" style="font-size: 1.5rem;"></i>
              </span>
            </div>
            <h5 class="mb-2" style="font-size: 0.95rem; font-weight: 500; letter-spacing: 0.5px; color: #1a1a1a;">Multiple Payment Options</h5>
            <p style="font-size: 0.8rem; line-height: 1.6; opacity: 0.6; font-weight: 300; color: #4a4a4a;">GCash, Maya, Bank Transfer, or PayPal accepted.</p>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!--------------------------------------- BOOKING DETAILS SECTION ---------------------------------------->

  <!--------------------------------------- GALLERY SECTION ---------------------------------------->
  <section class="w-100 py-5" style="background: #0a0a0a;" id="gallery">
    <div class="container py-5">
      <div class="row justify-content-center mb-5">
        <div class="col-md-10 text-center">
          <span class="d-inline-block px-4 py-2 rounded-pill mb-3" style="background: rgba(212, 175, 55, 0.1); border: 1px solid rgba(212, 175, 55, 0.3); font-size: 0.7rem; letter-spacing: 3px; color: var(--gold); font-weight: 600;">PORTFOLIO</span>
          <h2 class="serif mb-3 text-light" style="font-size: clamp(2rem, 4vw, 3rem); font-weight: 300; line-height: 1.3; letter-spacing: -0.5px;">Our Recent Work</h2>
          <p class="mb-0 mx-auto text-light" style="max-width: 600px; font-size: 0.95rem; line-height: 1.7; opacity: 0.7; font-weight: 300;">A glimpse into the moments we've captured and the stories we've told for our clients.</p>
        </div>
      </div>

      <div id="galleryCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
        <div class="carousel-indicators">
          <button type="button" data-bs-target="#galleryCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
          <button type="button" data-bs-target="#galleryCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
          <button type="button" data-bs-target="#galleryCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
          <button type="button" data-bs-target="#galleryCarousel" data-bs-slide-to="3" aria-label="Slide 4"></button>
          <button type="button" data-bs-target="#galleryCarousel" data-bs-slide-to="4" aria-label="Slide 5"></button>
          <button type="button" data-bs-target="#galleryCarousel" data-bs-slide-to="5" aria-label="Slide 6"></button>
        </div>
        <div class="carousel-inner" style="border-radius: 8px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
          
          <!-- Slide 1 -->
          <div class="carousel-item active" style="height: 600px;">
            <img src="./assets/pexels-emma-bauso-1183828-2253831.jpg" class="d-block w-100 h-100" alt="Wedding Photography" style="object-fit: cover;">
            <div class="carousel-caption d-block p-5" style="background: linear-gradient(to top, rgba(0,0,0,0.9), transparent); bottom: 0; left: 0; right: 0; width: 100%;">
              <p class="text-gold mb-2" style="font-size: 0.85rem; letter-spacing: 3px; text-transform: uppercase; font-weight: 600;">Weddings</p>
              <h3 class="text-light serif mb-0" style="font-size: 2.5rem; font-weight: 300;">Intimate Garden Ceremony</h3>
            </div>
          </div>

          <!-- Slide 2 -->
          <div class="carousel-item" style="height: 600px;">
            <img src="./assets/bride-groom-couple-wedding.jpg" class="d-block w-100 h-100" alt="Corporate Event" style="object-fit: cover;">
            <div class="carousel-caption d-block p-5" style="background: linear-gradient(to top, rgba(0,0,0,0.9), transparent); bottom: 0; left: 0; right: 0; width: 100%;">
              <p class="text-gold mb-2" style="font-size: 0.85rem; letter-spacing: 3px; text-transform: uppercase; font-weight: 600;">Corporate</p>
              <h3 class="text-light serif mb-0" style="font-size: 2.5rem; font-weight: 300;">Annual Gala Evening</h3>
            </div>
          </div>

          <!-- Slide 3 -->
          <div class="carousel-item" style="height: 600px;">
            <img src="./assets/pexels-mikhail-nilov-7534800.jpg" class="d-block w-100 h-100" alt="Birthday Celebration" style="object-fit: cover;">
            <div class="carousel-caption d-block p-5" style="background: linear-gradient(to top, rgba(0,0,0,0.9), transparent); bottom: 0; left: 0; right: 0; width: 100%;">
              <p class="text-gold mb-2" style="font-size: 0.85rem; letter-spacing: 3px; text-transform: uppercase; font-weight: 600;">Celebrations</p>
              <h3 class="text-light serif mb-0" style="font-size: 2.5rem; font-weight: 300;">Milestone Birthday</h3>
            </div>
          </div>

          <!-- Slide 4 -->
          <div class="carousel-item" style="height: 600px;">
            <img src="./assets/pexels-cottonbro-5077049.jpg" class="d-block w-100 h-100" alt="Debut Photography" style="object-fit: cover;">
            <div class="carousel-caption d-block p-5" style="background: linear-gradient(to top, rgba(0,0,0,0.9), transparent); bottom: 0; left: 0; right: 0; width: 100%;">
              <p class="text-gold mb-2" style="font-size: 0.85rem; letter-spacing: 3px; text-transform: uppercase; font-weight: 600;">Debuts</p>
              <h3 class="text-light serif mb-0" style="font-size: 2.5rem; font-weight: 300;">Elegant Coming of Age</h3>
            </div>
          </div>

          <!-- Slide 5 -->
          <div class="carousel-item" style="height: 600px;">
            <img src="./assets/close-up-teenage-boy-taking-photography-click-retro-vintage-photo-camera-against-white-background.jpg" class="d-block w-100 h-100" alt="Creative Shoot" style="object-fit: cover;">
            <div class="carousel-caption d-block p-5" style="background: linear-gradient(to top, rgba(0,0,0,0.9), transparent); bottom: 0; left: 0; right: 0; width: 100%;">
              <p class="text-gold mb-2" style="font-size: 0.85rem; letter-spacing: 3px; text-transform: uppercase; font-weight: 600;">Creative</p>
              <h3 class="text-light serif mb-0" style="font-size: 2.5rem; font-weight: 300;">Fashion Editorial</h3>
            </div>
          </div>

          <!-- Slide 6 -->
          <div class="carousel-item" style="height: 600px;">
            <img src="./assets/pexels-rdne-7648020.jpg" class="d-block w-100 h-100" alt="Product Photography" style="object-fit: cover;">
            <div class="carousel-caption d-block p-5" style="background: linear-gradient(to top, rgba(0,0,0,0.9), transparent); bottom: 0; left: 0; right: 0; width: 100%;">
              <p class="text-gold mb-2" style="font-size: 0.85rem; letter-spacing: 3px; text-transform: uppercase; font-weight: 600;">Commercial</p>
              <h3 class="text-light serif mb-0" style="font-size: 2.5rem; font-weight: 300;">Brand Campaign</h3>
            </div>
          </div>

        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#galleryCarousel" data-bs-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#galleryCarousel" data-bs-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Next</span>
        </button>
      </div>
    </div>
  </section>
  <!--------------------------------------- GALLERY SECTION ---------------------------------------->

  <!--------------------------------------- CTA SECTION ---------------------------------------->
  <section class="cta-section w-100 d-flex justify-content-center align-items-center position-relative" style="min-height: 60vh; background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 100%); padding: 6rem 0;">
    <div class="position-absolute w-100 h-100" style="opacity: 0.02; background-image: url('./assets/wp2815597-black-texture-wallpapers.jpg'); background-size: cover; background-position: center;"></div>

    <div class="container text-center position-relative" style="z-index: 2;">
      <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
          <span class="d-inline-block px-4 py-2 rounded-pill mb-4" style="background: rgba(212, 175, 55, 0.1); border: 1px solid rgba(212, 175, 55, 0.3); font-size: 0.7rem; letter-spacing: 3px; color: var(--gold); font-weight: 600;">BEGIN YOUR JOURNEY</span>
          <h2 class="serif mb-4 text-light" style="font-size: clamp(2rem, 5vw, 3.5rem); font-weight: 300; line-height: 1.2; letter-spacing: -0.5px;">Ready to Preserve<br><span style="font-style: italic; color: var(--gold);">Your Story?</span></h2>
          <p class="text-light mb-5 mx-auto" style="max-width: 650px; font-size: 0.95rem; line-height: 1.8; opacity: 0.7; font-weight: 300;">From intimate celebrations to grand occasions, we're here to document your moments with artistry, professionalism, and uncompromising attention to detail.</p>

          <div class="d-flex flex-column flex-md-row gap-3 justify-content-center mb-4">
            <a href="user/bookingForm.php" class="btn px-5 py-3" style="background-color: var(--gold); color: #000; font-weight: 500; letter-spacing: 1px; border-radius: 6px; font-size: 0.85rem; transition: all 0.4s ease;">Book now</a>
            <a href="aboutCompany.php" class="btn px-5 py-3" style="background: transparent; color: var(--gold); border: 1px solid var(--gold); font-weight: 500; letter-spacing: 1px; border-radius: 6px; font-size: 0.85rem; transition: all 0.4s ease;">About us</a>
          </div>

          <div class="mt-5 pt-4" style="border-top: 1px solid rgba(255,255,255,0.1);">
            <p class="text-light mb-3" style="font-size: 0.75rem; opacity: 0.5; letter-spacing: 1px; text-transform: uppercase;">Important Details</p>
            <div class="row g-4">
              <div class="col-md-4">
                <p class="text-light mb-1" style="font-size: 0.85rem; font-weight: 300; opacity: 0.8;">25% deposit to secure booking</p>
              </div>
              <div class="col-md-4">
                <p class="text-light mb-1" style="font-size: 0.85rem; font-weight: 300; opacity: 0.8;">Free reschedule with 5+ days notice</p>
              </div>
              <div class="col-md-4">
                <p class="text-light mb-1" style="font-size: 0.85rem; font-weight: 300; opacity: 0.8;">Delivery within 1-2 weeks</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!--------------------------------------- CTA SECTION ---------------------------------------->

  <?php include './includes/footer.php'; ?>

  <script src="../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
  <script src="script.js"></script>
  <script>
    // Package comparison toggle functionality
    const toggleComparisonBtn = document.getElementById('toggleComparison');
    const comparisonTable = document.getElementById('comparisonTable');

    if (toggleComparisonBtn && comparisonTable) {
      toggleComparisonBtn.addEventListener('click', function() {
        if (comparisonTable.style.display === 'none') {
          comparisonTable.style.display = 'block';
          this.textContent = 'CLOSE COMPARISON';
          comparisonTable.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        } else {
          comparisonTable.style.display = 'none';
          this.textContent = 'DETAILED COMPARISON';
        }
      });
    }

    // Store selected package in sessionStorage for booking page
    const bookingButtons = document.querySelectorAll('.btn-book-package');
    const isLoggedIn = <?= isset($_SESSION['userId']) ? 'true' : 'false' ?>;

    bookingButtons.forEach(button => {
      button.addEventListener('click', function(e) {
        e.preventDefault(); // Prevent default link behavior
        
        const packageId = this.dataset.id;
        const packageName = this.dataset.package;
        const packagePrice = this.dataset.price;
        
        if (packageId) {
             sessionStorage.setItem('selectedPackageId', packageId);
             sessionStorage.setItem('selectedPackageName', packageName);
             sessionStorage.setItem('selectedPrice', packagePrice);
        }

        if (isLoggedIn) {
            window.location.href = 'user/bookingForm.php';
        } else {
            window.location.href = 'logIn.php';
        }
      });
    });
  </script>

</body>

</html>
