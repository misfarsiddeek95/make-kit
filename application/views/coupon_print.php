<!DOCTYPE html>
<html lang="he" dir="rtl">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>הדפסת קופון - <?=htmlspecialchars($coupon->coupon_code)?></title>
    <style>
      * { margin: 0; padding: 0; box-sizing: border-box; }

      body {
        font-family: 'Segoe UI', Tahoma, Arial, sans-serif;
        background: #f5f5f5;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        direction: rtl;
      }

      .print-controls {
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 10;
      }

      .print-btn {
        background: #1d87e4;
        color: #fff;
        border: none;
        padding: 12px 40px;
        font-size: 18px;
        border-radius: 6px;
        cursor: pointer;
        font-family: inherit;
        direction: rtl;
      }

      .print-btn:hover { background: #1565c0; }

      .coupon-card {
        background: #fff;
        border: 3px dashed #1d87e4;
        border-radius: 16px;
        padding: 50px 40px;
        max-width: 520px;
        width: 100%;
        text-align: center;
        direction: rtl;
      }

      .coupon-logo {
        margin-bottom: 30px;
      }

      .coupon-logo img {
        max-width: 180px;
        height: auto;
      }

      .coupon-wish {
        font-size: 22px;
        color: #333;
        margin-bottom: 30px;
        line-height: 1.6;
      }

      .coupon-image {
        margin-bottom: 30px;
      }

      .coupon-image img {
        max-width: 180px;
        max-height: 180px;
        border-radius: 12px;
        border: 1px solid #e0e0e0;
      }

      .coupon-code-label {
        font-size: 14px;
        color: #888;
        margin-bottom: 8px;
      }

      .coupon-code {
        font-size: 48px;
        font-weight: 700;
        color: #1d87e4;
        letter-spacing: 4px;
        direction: ltr;
        margin-bottom: 20px;
      }

      .coupon-details {
        font-size: 14px;
        color: #666;
        border-top: 1px solid #eee;
        padding-top: 16px;
        margin-top: 10px;
      }

      .coupon-details span {
        display: inline-block;
        margin: 4px 12px;
      }

      .coupon-for-section {
        margin-top: 20px;
        border-top: 1px solid #eee;
        padding-top: 16px;
        text-align: center;
        direction: rtl;
      }

      .coupon-for-section h4 {
        font-size: 16px;
        color: #333;
        margin-bottom: 10px;
      }

      .coupon-for-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
        direction: rtl;
      }

      .coupon-for-table th {
        background: #1d87e4;
        color: #fff;
        padding: 8px 12px;
        text-align: right;
      }

      .coupon-for-table td {
        padding: 7px 12px;
        border-bottom: 1px solid #eee;
        text-align: right;
      }

      .coupon-for-table tr:nth-child(even) td {
        background: #f9f9f9;
      }

      @media print {
        body {
          background: #fff;
          min-height: auto;
          padding: 20px;
        }

        .print-controls {
          display: none !important;
        }

        .coupon-card {
          border: 2px dashed #1d87e4;
          box-shadow: none;
          margin: 0;
          padding: 30px;
          page-break-inside: avoid;
        }
      }
    </style>
  </head>
  <body>
    <div class="print-controls">
      <button class="print-btn" onclick="window.print();">הדפס קופון</button>
    </div>

    <div class="coupon-card" id="couponCard">
      <div class="coupon-logo">
        <img src="<?=base_url()?>assets/img/logoBottom.png" alt="Logo">
      </div>

      <div class="coupon-wish">
        !מזל טוב, קיבלת קופון
      </div>

      <?php if ($photo) { ?>
      <div class="coupon-image">
        <img src="<?=base_url()?>photos/coupons/<?=htmlspecialchars($photo->photo_path)?>-org.<?=htmlspecialchars($photo->extension)?>" alt="Coupon Image">
      </div>
      <?php } ?>

      <div class="coupon-code-label">קוד קופון</div>
      <div class="coupon-code"><?=htmlspecialchars($coupon->coupon_code)?></div>

      <div class="coupon-details">
        <?php
          $coupon_type = $coupon->coupon_type == 1 ? '%' : '₪';
        ?>
        <span>סכום: <?=$coupon->coupon_amount?> <?=$coupon_type?></span>
        <span>מתאריך: <?=htmlspecialchars($coupon->valid_from)?></span>
        <span>עד תאריך: <?=htmlspecialchars($coupon->valid_to)?></span>
      </div>

      <?php if (!empty($for_items)) { ?>
      <div class="coupon-for-section">
        <strong><?=$coupon->coupon_for == 0 ? 'מותג:' : 'קטגוריה:'?></strong>
        <?=htmlspecialchars($coupon->coupon_for == 0 ? $for_items[0]->brand : $for_items[0]->category)?>
      </div>
      <?php } ?>
    </div>
  </body>
</html>
